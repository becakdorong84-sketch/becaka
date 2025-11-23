<?php
// wp-backdoor-fullscan-simple.php
// Full-read, high-sensitivity, read-only WP backdoor scanner.
// Output: plain list of relative paths (one per line) and a "Copy All" button in browser.
// Browser usage: https://site/wp-backdoor-fullscan-simple.php?key=RAHASIASENPAI
// CLI usage: php wp-backdoor-fullscan-simple.php
// IMPORTANT: Read-only. Does NOT modify/delete files.

//////////// CONFIG ////////////
$BROWSER_KEY        = 'RAHASIASENPAI';    // required for browser access
$EXTS               = ['php','php5','php7','phtml','inc','tpl','html'];
$MAX_FILES          = 200000;             // safety cap on number of files scanned
$MAX_TOTAL_BYTES    = 4 * 1024 * 1024 * 1024; // 4 GB total read cap (stop if exceeded)
$SLEEP_EVERY        = 2000;
$SLEEP_US           = 15000;              // 15 ms sleep every SLEEP_EVERY files
$SCORE_THRESHOLD    = 1;                  // be generous: >=1 marks candidate (high-sensitivity)
/////////////////////////////////

ini_set('memory_limit','1536M');
set_time_limit(0);

// Browser key check (CLI bypass)
if (php_sapi_name() !== 'cli') {
    $provided = $_GET['key'] ?? '';
    if (!function_exists('hash_equals') || !hash_equals($BROWSER_KEY, $provided)) {
        if (isset($_SERVER['SERVER_PROTOCOL'])) header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden'); else header('HTTP/1.1 403 Forbidden');
        header('Content-Type: text/plain; charset=utf-8');
        echo "403 Forbidden\n";
        exit;
    }
}

// Find WP root by walking up for wp-config.php; fallback to current dir
function find_wp_root($start) {
    $p = realpath($start);
    if ($p === false) return false;
    $tries = 50;
    for ($i=0;$i<$tries;$i++) {
        if (file_exists($p . DIRECTORY_SEPARATOR . 'wp-config.php')) return $p;
        $parent = dirname($p);
        if ($parent === $p) break;
        $p = $parent;
    }
    return realpath($start);
}
$WP_ROOT = find_wp_root(__DIR__);
if (!$WP_ROOT) $WP_ROOT = find_wp_root(getcwd());
if (!$WP_ROOT) $WP_ROOT = realpath(__DIR__);

if ($WP_ROOT === false || !is_dir($WP_ROOT)) {
    $msg = "ERROR: WP root not found. Please place this file inside your WP folder or adjust script.\n";
    if (php_sapi_name() === 'cli') { echo $msg; } else { header('Content-Type: text/plain; charset=utf-8'); echo $msg; }
    exit(1);
}

// Heuristics & patterns (very broad and sensitive)
$PATTERNS = [
    // obfuscation & eval combos
    '/eval\s*\(\s*base64_decode\s*\(/i',
    '/eval\s*\(\s*gzinflate\s*\(\s*base64_decode\s*\(/i',
    '/gzinflate\s*\(\s*base64_decode\s*\(/i',
    '/base64_decode\s*\(/i',
    '/gzinflate\s*\(/i',
    '/gzuncompress\s*\(/i',
    '/str_rot13\s*\(/i',
    // dangerous code execution
    '/\b(shell_exec|exec|system|passthru|popen|proc_open)\s*\(/i',
    '/\bassert\s*\(/i',
    '/\bcreate_function\s*\(/i',
    '/file_put_contents\s*\(/i',
    '/curl_exec\s*\(/i',
    '/fsockopen\s*\(/i',
    // remote include / file_get_contents remote
    '/(include|require|include_once|require_once)[^\n;]*https?:\/\//i',
    '/fopen\s*\(\s*[\'"]https?:\/\//i',
    '/file_get_contents\s*\(\s*[\'"]https?:\/\//i',
    // preg /e obfuscation
    '/preg_replace\s*\(\s*[\'"].+\/e[\'"]/i',
    // hex escapes common in obf
    '/(\\\\x[0-9A-Fa-f]{2}){6,}/',
    // many non-alnum (symbol heavy) indicator (we'll treat separately)
];

// filename heuristics (catch random names like prv88..., random-letter-digit combos, typical disguises)
$NAME_TOKENS = '/(^|[\/\._\-])((prv[a-z0-9]{2,}|tmp[a-z0-9]{1,}|cache_|error_|stats_|\.cache_|\.local_|\.wp-admin_|wp-admin_|wp-login_|xmlrpc|xmlrpcs|wp-cron|backdoor|webshell|shell|sx|mrec|hidden|adminer|phpinfo)|([a-z0-9]{4,}\d+[a-z0-9]{1,}\.php$))/i';

// Safe read full file (read entire file)
function safe_read_full($file) {
    // try file_get_contents; if fails, try fopen/fread
    $s = @file_get_contents($file);
    if ($s !== false) return $s;
    $fp = @fopen($file, 'rb');
    if (!$fp) return false;
    $data = '';
    while (!feof($fp)) {
        $chunk = @fread($fp, 8192);
        if ($chunk === false) break;
        $data .= $chunk;
        // small micro-sleep to avoid starving IO on extremely large files
        if (strlen($data) > 1024*1024*8) usleep(1000);
    }
    fclose($fp);
    return $data;
}

// iterate and detect
$results = [];
$scanned = 0;
$totalRead = 0;
$unreadable = [];

try {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($WP_ROOT, RecursiveDirectoryIterator::SKIP_DOTS));
} catch (Exception $e) {
    $err = "Iterator error: " . $e->getMessage();
    if (php_sapi_name() === 'cli') { echo $err . PHP_EOL; } else { header('Content-Type: text/plain; charset=utf-8'); echo $err; }
    exit(1);
}

foreach ($rii as $fileinfo) {
    if (!$fileinfo->isFile()) continue;
    $scanned++;
    if ($scanned > $MAX_FILES) break;

    $ext = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
    if (!in_array($ext, $EXTS, true)) continue;

    // total cap
    if ($totalRead >= $MAX_TOTAL_BYTES) break;

    $real = $fileinfo->getRealPath();
    if ($real === false) continue;

    // try read full file
    $content = safe_read_full($real);
    if ($content === false) {
        $unreadable[] = $real;
        continue;
    }

    $totalRead += strlen($content);

    $score = 0;

    // direct pattern matches
    foreach ($PATTERNS as $pat) {
        if (preg_match($pat, $content)) {
            $score += 1;
        }
    }

    // symbol-heavy content check (non-alphanumeric ratio)
    $len = strlen($content);
    if ($len > 128) {
        $nonalnum = preg_match_all('/[^a-zA-Z0-9\s]/', $content, $m);
        if ($nonalnum !== false) {
            $ratio = $nonalnum / max(1, $len);
            if ($ratio > 0.30) $score += 1;
        }
    }

    // suspicious filename
    if (preg_match($NAME_TOKENS, $fileinfo->getFilename())) $score += 1;

    // tiny files that are single-line long-encoded content
    if ($len < 8000 && preg_match('/^[A-Za-z0-9+\/=\\s]+$/', trim($content)) && preg_match('/={1,2}$/', trim($content))) {
        // looks like a big base64 blob file
        $score += 2;
    }

    if ($score >= $SCORE_THRESHOLD) {
        $rel = substr($real, strlen($WP_ROOT));
        if ($rel === '') $rel = '/';
        if ($rel[0] !== '/') $rel = '/'.$rel;
        $results[$real] = $rel; // unique
    }

    if (($scanned % $SLEEP_EVERY) === 0) usleep($SLEEP_US);
}

// Prepare output list (sorted)
$paths = array_values($results);
sort($paths, SORT_NATURAL | SORT_FLAG_CASE);

// CLI output
if (php_sapi_name() === 'cli') {
    if (empty($paths)) {
        echo "No likely backdoor/shell files detected.\n";
    } else {
        foreach ($paths as $p) echo $p . PHP_EOL;
    }
    if (!empty($unreadable)) {
        fwrite(STDERR, "Note: some files unreadable by this user (count=" . count($unreadable) . ").\n");
    }
    exit(0);
}

// Browser output — simple copy UI (only paths, one per line)
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>WP Fullscan Backdoor List — Simple</title>
<style>
  body{font-family:system-ui,Segoe UI,Arial;background:#f5f7fb;color:#111;padding:18px}
  .wrap{max-width:980px;margin:0 auto}
  h1{font-size:18px;margin:0 0 6px}
  .meta{color:#444;margin-bottom:12px}
  button{background:#2563eb;color:#fff;padding:8px 12px;border:0;border-radius:6px;cursor:pointer;margin-right:8px}
  pre{background:#0b1220;color:#dff5ff;padding:12px;border-radius:6px;white-space:pre-wrap;word-break:break-word;max-height:66vh;overflow:auto;font-family:monospace}
  .empty{color:#0a8a00}
  .note{color:#666;font-size:13px;margin-top:12px}
</style>
</head>
<body>
  <div class="wrap">
    <h1>WP Fullscan Backdoor / Shell Finder — Simple List</h1>
    <div class="meta">WP Root: <strong><?php echo htmlspecialchars($WP_ROOT) ?></strong> — Candidates: <?php echo count($paths) ?></div>

    <div style="margin-bottom:10px">
      <button id="copyBtn">Copy ALL results</button>
      <button id="selectBtn">Select All</button>
      <span class="note">Output is read-only; remove this script after use. Prefer CLI for big sites.</span>
    </div>

    <?php if (empty($paths)): ?>
      <div class="empty">No likely backdoor/shell files detected.</div>
      <pre id="results" aria-hidden="true"></pre>
    <?php else: ?>
      <pre id="results"><?php echo htmlspecialchars(implode("\n",$paths)); ?></pre>
    <?php endif; ?>

    <?php if (!empty($unreadable)): ?>
      <div class="note">Note: <?php echo count($unreadable) ?> files could not be read by this user and were skipped.</div>
    <?php endif; ?>

    <div class="note">Read-only scanner — does NOT modify or delete files. After cleanup, remove this file from server.</div>
  </div>

<script>
(function(){
  var copyBtn = document.getElementById('copyBtn');
  var selectBtn = document.getElementById('selectBtn');
  var results = document.getElementById('results');
  copyBtn.addEventListener('click', function(){
    var text = results ? results.textContent : '';
    if (!text) { alert('No results to copy'); return; }
    navigator.clipboard.writeText(text).then(function(){ alert('All results copied ✅'); }, function(){ alert('Copy failed — try select manual'); });
  });
  selectBtn.addEventListener('click', function(){
    if (!results) return;
    var range = document.createRange();
    range.selectNodeContents(results);
    var sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);
  });
})();
</script>
</body>
</html>
