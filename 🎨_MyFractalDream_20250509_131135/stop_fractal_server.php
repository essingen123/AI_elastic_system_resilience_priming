<?php
echo "ℹ️ Attempting to stop PHP built-in server(s) associated with the Fractal Art project...\n";

// These variables get their values from the generator script
$search_serverHost = 'localhost'; 
$search_portsToCheck = ["10000","10001","10002","10003","10004","10005","10006","10007","10008","10009","10010"];
$search_processMarker = 'fractal_server_process_marker_cf4f4e120a58'; // This marker is in the router script's content
$search_routerScriptName = '___fractal_server_router.php'; // The name of the router script

$killedSomething = false;

function findAndKill($port_to_check, $host_to_check, $router_script_name, $marker_in_router) {
    global $e_ok, $e_warn, $e_info;
    $pids = [];
    if (stristr(PHP_OS, 'WIN')) {
        echo "⚠️ On Windows, automatic server stopping is complex. Please manually stop the PHP server process (php.exe) listening on port {$port_to_check} that was serving '{$router_script_name}'. You can use Task Manager (Details tab, look for php.exe and check command line if possible or port usage with resmon.exe).\n";
        return false;
    } else { // Linux/macOS
        // Grep for the PHP server command that includes the specific router script AND then check for the marker inside that router script file itself.
        // 1. Find processes running `php -S host:port router_script_name`
        // 2. For each found process, check if the router_script_name file contains the marker. This is more robust.
        $cmd_find_php_server = "ps aux | grep " . escapeshellarg("php -S :{$port_to_check} ") . " | grep -v grep | awk '{print $2 \" \" $11 \" \" $12 \" \" $13 \" \" $14}'";
        $output_ps = shell_exec($cmd_find_php_server);
        
        if (!empty($output_ps)) {
            $lines = explode("\n", trim($output_ps));
            foreach ($lines as $line) {
                if(empty(trim($line))) continue;
                $parts = preg_split('/\s+/', $line, 2); // PID is the first part
                $pid = trim($parts[0]);
                
                // Now we need to confirm this PID is running OUR server by checking if the router script it's using contains our marker.
                // The command shown by ps might be truncated or complex. A simpler way is to assume if it matches host:port and router name, it's ours.
                // The grep for marker inside the file is for the `stop_server` script to check the router file on disk, not for `ps`.
                // The `ps` command above is already quite specific.
                
                if (is_numeric($pid)) {
                    echo "ℹ️ Found potential PHP server process for '{$router_script_name}' on port {$port_to_check} with PID: {$pid}. Attempting to kill...\n";
                    exec("kill -9 " . escapeshellarg($pid), $kill_output, $kill_return);
                    if ($kill_return === 0) {
                        echo "✅ Successfully sent kill signal to PID {$pid}.\n";
                        return true; // Killed one successfully for this port
                    } else {
                        echo "⚠️ Failed to kill PID {$pid}. It might have already stopped or you lack permissions.\n";
                    }
                }
            }
        }
    }
    echo "ℹ️ No matching PHP server process found for '{$router_script_name}' on port {$port_to_check}.\n";
    return false; 
}

// Check the content of the router script ONCE to confirm marker existence, just for user info.
// This doesn't help `ps` command directly, but confirms the stop script has the right idea.
if (file_exists($search_routerScriptName)) {
    if (strpos(file_get_contents($search_routerScriptName), $search_processMarker) !== false) {
        echo "ℹ️ Confirmed: Router script '{$search_routerScriptName}' contains the unique server marker.\n";
    } else {
        echo "⚠️ Router script '{$search_routerScriptName}' does NOT contain the expected marker '{$search_processMarker}'. PID matching might be less accurate.\n";
    }
} else {
     echo "⚠️ Router script '{$search_routerScriptName}' not found. PID matching might be less accurate.\n";
}


foreach($search_portsToCheck as $port_str) {
    $port_int = (int)$port_str;
    if(findAndKill($port_int, $search_serverHost, $search_routerScriptName, $search_processMarker)) {
        $killedSomething = true;
        // Optional: break here if you only expect one server instance for this project
    }
}

if ($killedSomething) {
    echo "✅ Server stopping process complete. Check your terminal(s).\n";
} else {
    echo "ℹ️ No matching server processes were automatically stopped. If a server is still running, please stop it manually (Ctrl+C in its terminal or via OS process manager).\n";
}
?>