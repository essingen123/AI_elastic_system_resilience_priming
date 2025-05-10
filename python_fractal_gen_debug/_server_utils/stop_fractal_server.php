<?php
$_E_INFO_ = 'ℹ️'; $_E_OK_ = '✅'; $_E_WARN_ = '⚠️'; $_E_STOP_ = '🛑';
echo "\$_E_INFO_ Debug Stop Server Script... (Simplified)\n";
\$host = 'localhost'; \$ports = json_decode('[10031,10032,10033,10034,10035]', true); \$router = '___fractal_server_router.php';
echo "Would check for server on {\$host} for router '{\$router}' on ports: " . implode(", ", \$ports) . "\n";
echo "\$_E_OK_ Debug stop script finished.\n";
?>