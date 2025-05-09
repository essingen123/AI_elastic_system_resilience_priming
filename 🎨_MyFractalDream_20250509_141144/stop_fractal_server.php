<?php
// These emojis are for the stop script's output
$e_info = "ℹ️"; $e_ok = "✅"; $e_warn = "⚠️"; $e_stop = "🛑";

echo "$e_info Attempting to stop PHP built-in server(s) associated with the Fractal Art project...\n";

$search_serverHost = 'localhost'; 
$search_portsToCheck = ["10000","10001","10002","10003","10004","10005","10006","10007","10008","10009","10010"];
$search_processMarker = 'fractal_server_process_marker_9f207ef90868'; 
$search_routerScriptName = '___fractal_server_router.php'; 

$killedSomething = false;

function findAndKill($port_to_check, $host_to_check, $router_script_name_to_grep, $marker_in_router_content) {
    global $e_ok, $e_warn, $e_info;
    $pids = [];
    if (stristr(PHP_OS, 'WIN')) {
        echo "$e_warn On Windows, automatic server stopping is complex. Please manually stop the PHP server (php.exe) listening on port {$port_to_check} that was serving '{$router_script_name_to_grep}'. Use Task Manager (Details tab) or Resource Monitor (resmon.exe -> Network -> Listening Ports).\n";
        return false;
    } else { 
        // Construct a robust grep pattern. We want lines from `ps aux` that:
        // 1. Contain 'php -S'
        // 2. Contain 'host:port'
        // 3. Contain the specific router script name
        // We can't easily grep for the *content* of the router script from `ps` output.
        // The marker is more for manual verification or if the router script path was directly in ps output.
        // The most reliable ps grep is for `php -S host:port specific_router.php`
        $grep_pattern = escapeshellarg("php -S :{$port_to_check} ");
        $cmd_find_php_server = "ps aux | grep " . $grep_pattern . " | grep -v grep | awk '{print $2}'";
        
        $output_ps = shell_exec($cmd_find_php_server);
        
        if (!empty($output_ps)) {
            $found_pids = array_filter(explode("\n", trim($output_ps)));
            foreach ($found_pids as $pid_str) {
                $pid = trim($pid_str);
                if (is_numeric($pid)) {
                    echo "$e_info Found potential PHP server (PID: {$pid}) for '{$router_script_name_to_grep}' on port {$port_to_check}. Attempting to kill...\n";
                    exec("kill -9 " . escapeshellarg($pid), $kill_output, $kill_return);
                    if ($kill_return === 0) {
                        echo "$e_ok Successfully sent kill signal to PID {$pid}.\n";
                        return true; 
                    } else {
                        echo "$e_warn Failed to kill PID {$pid}. It might have already stopped or you lack permissions.\n";
                    }
                }
            }
        }
    }
    echo "$e_info No matching PHP server process found for '{$router_script_name_to_grep}' on port {$port_to_check}.\n";
    return false; 
}

// Informational check of the router script itself (if it exists where stop_server expects it)
// This script (stop_server.php) is in the same directory as the router script.
if (file_exists($search_routerScriptName)) {
    if (strpos(file_get_contents($search_routerScriptName), $search_processMarker) !== false) {
        echo "$e_info Confirmed: Router script '{$search_routerScriptName}' contains the unique server marker '{$search_processMarker}'.\n";
    } else {
        echo "$e_warn Router script '{$search_routerScriptName}' does NOT contain the expected marker '{$search_processMarker}'. PID matching relies on command line arguments.\n";
    }
} else {
     echo "$e_warn Router script '{$search_routerScriptName}' not found in current directory. PID matching might be less accurate if server was started differently.\n";
}


foreach($search_portsToCheck as $port_str) {
    $port_int = (int)$port_str;
    if(findAndKill($port_int, $search_serverHost, $search_routerScriptName, $search_processMarker)) {
        $killedSomething = true;
        // break; // Uncomment if you only expect one server instance for this project on any of the checked ports
    }
}

if ($killedSomething) {
    echo "$e_ok Server stopping process complete. Please verify in your terminal(s).\n";
} else {
    echo "$e_info No matching server processes were automatically stopped. If a server is still running, please stop it manually (Ctrl+C in its terminal or via OS process manager).\n";
}
?>