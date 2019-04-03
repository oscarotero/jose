<?php
$url = $_GET['url'] ?? null;

if ($url) {
    $types = [
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'webp' => 'image/webp',
        'mp3' => 'audio/mpeg',
        'mp4' => 'video/mp4',
        'mov' => 'video/quicktime',
        'ogg' => 'audio/ogg',
        'webm' => 'video/webm',
    ];

    $path = parse_url($url, PHP_URL_PATH);
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    if (isset($types[$ext])) {
        header(sprintf('Content-Type: %s', $types[$ext]));
        readfile($url);
    }
}
