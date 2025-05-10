<?php
// stop_fractal_server.php
$e_info = "ℹ️"; $e_ok = "✅"; $e_warn = "⚠️"; $e_stop_glyph = "🛑";
echo "$e_info Attempting to stop PHP built-in server(s) for the Fractal Art project...\n";
$php_serverHost = 'localhost'; 
$php_portsToCheck = json_decode('[\"10011\",\"10012\",\"10013\",\"10014\",\"10015\",\"10016\",\"10017\",\"10018\",\"10019\",\"10020\"]', true);
$php_processMarker = 'fractal_server_process_marker_700ea2957704a7eb'; 
$php_routerScriptName = '___fractal_server_router.php'; 
$killedSomething = false;
function findAndKillProcess($port_to_check, $host_to_check, $router_script_name_to_grep_param) { 
    global $e_ok, $e_warn, $e_info; 
    if (stristr(PHP_OS, 'WIN')) {
        echo "$e_warn On Windows, please manually stop PHP server on port {$port_to_check} for '{$router_script_name_to_grep_param}'.\n";
        return false;
    } else { 
        $grep_pattern = escapeshellarg("php -S :{$port_to_check} ");
        $cmd_find_php_server = "ps aux | grep " . $grep_pattern . " | grep -v grep | awk '{print $2}'";
        $output_ps = shell_exec($cmd_find_php_server);
        if (!empty($output_ps)) {
            $found_pids = array_filter(explode("\n", trim($output_ps)));
            if (empty($found_pids) || (count($found_pids) == 1 && empty(trim($found_pids[0]))) ) {
                 echo "$e_info No specific PHP server process found for '{$router_script_name_to_grep_param}' on port {$port_to_check} (ps).\n";
                 return false;
            }
            foreach ($found_pids as $pid_str) {
                $pid = trim($pid_str);
                if (is_numeric($pid) && $pid > 0) {
                    echo "$e_info Found server (PID: {$pid}) for '{$router_script_name_to_grep_param}' on port {$port_to_check}. Killing...\n";
                    exec("kill -9 " . escapeshellarg($pid), $kill_output, $kill_return);
                    if ($kill_return === 0) { echo "$e_ok Killed PID {$pid}.\n"; return true; }
                    else { echo "$e_warn Failed to kill PID {$pid}.\n"; }
                }
            }
        } else { echo "$e_info No server process for '{$router_script_name_to_grep_param}' on port {$port_to_check} (empty ps output).\n"; }
    }
    return false; 
}
$routerPathForCheck = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $php_routerScriptName;
if (file_exists($routerPathForCheck)) {
    if (strpos(file_get_contents($routerPathForCheck), $php_processMarker) !== false) { echo "$e_info Router '{$php_routerScriptName}' contains marker '{$php_processMarker}'.\n"; }
    else { echo "$e_warn Router '{$php_routerScriptName}' does NOT contain marker! PID matching by command args only.\n"; }
} else { echo "$e_warn Router '{$php_routerScriptName}' not found for marker check. PID matching by command args only.\n"; }
foreach($php_portsToCheck as $port_str) {
    $port_int = (int)$port_str;
    if(findAndKillProcess($port_int, $php_serverHost, $php_routerScriptName)) { $killedSomething = true; }
}
if ($killedSomething) { echo "$e_ok Server stopping finished.\n"; }
else { echo "$e_info $e_stop_glyph No servers automatically stopped. Stop manually if needed.\n"; }
?>