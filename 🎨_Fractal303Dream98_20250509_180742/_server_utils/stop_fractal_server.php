<?php
// stop_fractal_server.php
$e_info = "ℹ️"; $e_ok = "✅"; $e_warn = "⚠️"; $e_stop_glyph = "🛑";

echo "$e_info Attempting to stop PHP built-in server(s) for the Fractal Art project...\n";

$php_serverHost = 'localhost'; 
$php_portsToCheck = json_decode('["10011","10012","10013","10014","10015","10016","10017","10018","10019","10020"]', true);
$php_processMarker = 'fractal_server_process_marker_3b74d6efe4af7420'; 
$php_routerScriptName = '___fractal_server_router.php'; 

$killedSomething = false;

function findAndKillProcess($port_to_check, $host_to_check, $router_script_name_to_grep_param) { 
    global $e_ok, $e_warn, $e_info; 
    if (stristr(PHP_OS, 'WIN')) {
        echo "$e_warn On Windows, please manually stop the PHP server (php.exe) listening on port {$port_to_check} that was serving '{$router_script_name_to_grep_param}'. Use Task Manager or Resource Monitor.\n";
        return false;
    } else { 
        $grep_pattern = escapeshellarg("php -S :{$port_to_check} ");
        $cmd_find_php_server = "ps aux | grep " . $grep_pattern . " | grep -v grep | awk '{print $2}'";
        $output_ps = shell_exec($cmd_find_php_server);
        
        if (!empty($output_ps)) {
            $found_pids = array_filter(explode("\n", trim($output_ps)));
            if (empty($found_pids) || (count($found_pids) == 1 && empty(trim($found_pids[0]))) ) {
                 echo "$e_info No specific PHP server process found for '{$router_script_name_to_grep_param}' on port {$port_to_check} via ps command.\n";
                 return false;
            }
            foreach ($found_pids as $pid_str) {
                $pid = trim($pid_str);
                if (is_numeric($pid) && $pid > 0) {
                    echo "$e_info Found potential PHP server (PID: {$pid}) for '{$router_script_name_to_grep_param}' on port {$port_to_check}. Attempting to kill...\n";
                    exec("kill -9 " . escapeshellarg($pid), $kill_output, $kill_return);
                    if ($kill_return === 0) {
                        echo "$e_ok Successfully sent SIGKILL to PID {$pid}.\n";
                        return true; 
                    } else {
                        echo "$e_warn Failed to kill PID {$pid} (return: {$kill_return}). Already stopped or no permissions?\n";
                    }
                }
            }
        } else {
             echo "$e_info No matching PHP server process found for '{$router_script_name_to_grep_param}' on port {$port_to_check} (empty ps output).\n";
        }
    }
    return false; 
}

$routerPathForCheck = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $php_routerScriptName;
if (file_exists($routerPathForCheck)) {
    if (strpos(file_get_contents($routerPathForCheck), $php_processMarker) !== false) {
        echo "$e_info Confirmed: Router '{$php_routerScriptName}' contains marker '{$php_processMarker}'.\n";
    } else {
        echo "$e_warn Router '{$php_routerScriptName}' does NOT contain expected marker. PID matching on command args only.\n";
    }
} else {
     echo "$e_warn Router '{$php_routerScriptName}' not found for marker check. PID matching by command args only.\n";
}

foreach($php_portsToCheck as $port_str) {
    $port_int = (int)$port_str;
    if(findAndKillProcess($port_int, $php_serverHost, $php_routerScriptName)) {
        $killedSomething = true;
    }
}

if ($killedSomething) {
    echo "$e_ok Server stopping process complete. Please verify.\n";
} else {
    echo "$e_info $e_stop_glyph No matching server processes were automatically stopped. Please stop manually if needed.\n";
}
?>