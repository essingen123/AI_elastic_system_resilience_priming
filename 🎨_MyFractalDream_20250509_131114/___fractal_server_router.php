<?php
// DO NOT DELETE - Process Marker: fractal_server_process_marker_d4b56eef3344
// This router file helps identify and stop the correct PHP server process.
chdir(__DIR__); // Ensure relative paths work from here
if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . $_SERVER['REQUEST_URI']) && !is_dir(__DIR__ . DIRECTORY_SEPARATOR . $_SERVER['REQUEST_URI'])) {
    return false;
} elseif (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'index.html')) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'index.html';
} else {
    http_response_code(404);
    echo '404 Not Found - index.html missing in ' . __DIR__;
}
?>