<?php
$url = $_GET['url'] ?? null;

if ($url) {
    $fp = fopen($url, 'r');
    $meta_data = stream_get_meta_data($fp);
    $headers = $meta_data['wrapper_data'] ?? [];

    foreach ($headers as $header) {
        if (strpos($header, ':') !== false) {
            header($header);
        }
    }

    while (!feof($fp)) {
        $chunk = fread($fp, 8192);
        echo $chunk;
    }

    fclose($fp);
}
