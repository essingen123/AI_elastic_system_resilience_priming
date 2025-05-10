<?php
// stop_fractal_server.php - Generated
// These placeholders will be replaced by the generator script
$_E_INFO_ = 'ℹ️'; $_E_OK_ = '✅'; 
$_E_WARN_ = '⚠️'; $_E_STOP_GLYPH_ = '🛑';

echo "\$_E_INFO_ Attempting to stop PHP built-in server(s) for the Fractal Art project...\n";

\$php_serverHost_val = 'localhost'; 
\$php_portsToCheck_val = json_decode('["10021","10022","10023","10024","10025","10026","10027","10028","10029","10030"]', true);
\$php_processMarker_val = 'fractal_server_process_marker_e8f5d3da53774143'; 
\$php_routerScriptName_val = '___fractal_server_router.php'; 

\$killedSomething = false;

function findAndKillProcessForStopScript(\$port_to_check, \$host_param, \$router_script_name_param) { 
    // Emojis need to be available here, e.g., by defining them globally in this script
    // or passing them into this function if they were part of the generator's global scope.
    // For simplicity, let's assume they are defined above or use plain text.
    // Using the placeholders that will be replaced by actual emoji characters.
    global \$_E_OK_, \$_E_WARN_, \$_E_INFO_; 

    if (stristr(PHP_OS, 'WIN')) {
        echo "\$_E_WARN_ On Windows, please manually stop PHP server on port {\$port_to_check} for '{\$router_script_name_param}'.\n";
        return false;
    } else { 
        \$grep_pattern = escapeshellarg("php -S {\$host_param}:{\$port_to_check} {\$router_script_name_param}");
        \$cmd_find_php_server = "ps aux | grep " . \$grep_pattern . " | grep -v grep | awk '{print \$2}'";
        \$output_ps = shell_exec(\$cmd_find_php_server);
        if (!empty(\$output_ps)) {
            \$found_pids = array_filter(explode("\\n", trim(\$output_ps)));
            if (empty(\$found_pids) || (count(\$found_pids) == 1 && empty(trim(\$found_pids[0]))) ) {
                 echo "\$_E_INFO_ No specific server for '{\$router_script_name_param}' on port {\$port_to_check} (ps).\\n";
                 return false;
            }
            foreach (\$found_pids as \$pid_str) {
                \$pid = trim(\$pid_str);
                if (is_numeric(\$pid) && \$pid > 0) {
                    echo "\$_E_INFO_ Found server (PID: {\$pid}) for '{\$router_script_name_param}' on port {\$port_to_check}. Killing...\\n";
                    exec("kill -9 " . escapeshellarg(\$pid), \$kill_output, \$kill_return);
                    if (\$kill_return === 0) { echo "\$_E_OK_ Killed PID {\$pid}.\\n"; return true; }
                    else { echo "\$_E_WARN_ Failed to kill PID {\$pid}.\\n"; }
                }
            }
        } else { echo "\$_E_INFO_ No server for '{\$router_script_name_param}' on port {\$port_to_check} (empty ps output).\\n"; }
    }
    return false; 
}
\$routerPathForCheck = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . \$php_routerScriptName_val;
if (file_exists(\$routerPathForCheck)) {
    if (strpos(file_get_contents(\$routerPathForCheck), \$php_processMarker_val) !== false) { echo "\$_E_INFO_ Router '{\$php_routerScriptName_val}' contains marker '{\$php_processMarker_val}'.\\n"; }
    else { echo "\$_E_WARN_ Router '{\$php_routerScriptName_val}' does NOT contain marker!\\n"; }
} else { echo "\$_E_WARN_ Router '{\$php_routerScriptName_val}' not found for marker check.\\n"; }
foreach(\$php_portsToCheck_val as \$port_str) {
    \$port_int = (int)\$port_str;
    if(findAndKillProcessForStopScript(\$port_int, \$php_serverHost_val, \$php_routerScriptName_val)) { \$killedSomething = true; }
}
if (\$killedSomething) { echo "\$_E_OK_ Server stopping finished.\\n"; }
else { echo "\$_E_INFO_ \$_E_STOP_GLYPH_ No servers automatically stopped. Stop manually if needed.\\n"; }
?>