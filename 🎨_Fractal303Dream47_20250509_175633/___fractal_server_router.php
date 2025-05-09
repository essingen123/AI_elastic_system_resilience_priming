<?php
// DO NOT DELETE - Process Marker: fractal_server_process_marker_62533905510e2e0e
// Serves files from current dir or index.html for directories.
$docRoot = __DIR__;
$reqUri = $_SERVER['REQUEST_URI'];
$reqPath = $docRoot . preg_replace('/\?.*/', '', $reqUri);
if (is_file($reqPath) && file_exists($reqPath) && basename($reqPath) !== '___fractal_server_router.php') {
    return false;
} elseif (file_exists($docRoot . '/index.html')) {
    require_once $docRoot . '/index.html';
} else {
    http_response_code(404);
    echo '404 Not Found - index.html missing in ' . $docRoot;
}
?>