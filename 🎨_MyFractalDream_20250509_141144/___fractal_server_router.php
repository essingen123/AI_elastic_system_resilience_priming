<?php
// DO NOT DELETE - Process Marker: fractal_server_process_marker_9f207ef90868
// Serves files from the current directory or index.html for directories.
$requestedPath = __DIR__ . $_SERVER['REQUEST_URI'];
if (strpos($_SERVER['REQUEST_URI'], '?') !== false) { $requestedPath = substr($requestedPath, 0, strpos($requestedPath, '?')); } // Ignore query string for file check
if (is_file($requestedPath) && file_exists($requestedPath)) {
    return false; // Serve the requested file as-is.
} elseif (file_exists(__DIR__ . '/index.html')) {
    require_once __DIR__ . '/index.html'; // Serve index.html for directory requests or non-existent files.
} else {
    http_response_code(404);
    echo '404 Not Found - Main index.html missing from ' . __DIR__;
}
?>