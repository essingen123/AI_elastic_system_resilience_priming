<?php
// create_python_fractal_project.php
// Version: 2023-10-28_FractalWeaver_v2.5_Debug_Heredoc
// Generator Timestamp: <?php echo date('Y-m-d H:i:s T'); ? > 

// --- Configuration ---
date_default_timezone_set('UTC');
$scriptVersion = "FractalWeaver_v2.5_Debug (" . date('Y-m-d') . ")";

$numPythonScripts = 5; // Drastically reduced for faster testing
$randomNumberForName = rand(10, 99);
$mainProjectFolderName = "python_fractal_gen_debug"; // Fixed for debugging

$pythonSubfolderName = "py_scripts";
$serverScriptsSubfolderName = "_server_utils";
$maxCallDepth = 2;
$defaultMaxOrchestratorRuntime = 10; // Short runtime

$serverHost = "localhost";
$startPort = 10031; // Different port for debug
$endPort = 10035;
$maxPortRetries = 3;

// Emojis
$e_sparkle = "✨"; $e_folder = "📁"; $e_script_py = "🐍"; $e_script_php = "🐘";
$e_html = "📄"; $e_rocket = "🚀"; $e_palette = "🎨"; $e_gear = "⚙️";
$e_warn = "⚠️"; $e_info = "ℹ️"; $e_ok = "✅"; $e_party = "🎉";
$e_link = "🔗"; $e_terminal = "💻"; $e_eyes = "👀"; $e_stop = "🛑"; $e_timer = "⏱️"; $e_wrench = "🔧"; $e_magic = "🪄"; $e_broom = "🧹"; $e_log = "📜"; $e_conduct = "📜";

$serverProcessMarker = "fractal_server_debug_marker_" . bin2hex(random_bytes(4));

// Manifesto (kept for structure, but not the cause of parse error)
$manifesto_q1 = "Debug Manifesto Content"; // Simplified
$manifesto_q2_expected_hash = substr(sha1($manifesto_q1),0,8); // Recalculate for debug
function calculate_manifesto_hash($text) { return substr(sha1($text), 0, 8); }
$manifesto_q1_current_hash = calculate_manifesto_hash($manifesto_q1);

echo "$e_magic Create Python Fractal Project - DEBUG v{$scriptVersion} $e_magic\n";
echo "Generator run timestamp: " . date('Y-m-d H:i:s T') . "\n";
if ($manifesto_q1_current_hash === $manifesto_q2_expected_hash) {
    echo "$e_conduct Manifesto Check: $e_ok UNCHANGED ($manifesto_q1_current_hash)\n";
} else {
    echo "$e_conduct Manifesto Check: $e_warn MANIFESTO CONTENT HAS CHANGED IN DEBUG VERSION!\n";
}
echo "---------------------------------------------------\n";


function isPortAvailable($host, $port, $timeout = 0.5) {
    $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if ($socket) {
        fclose($socket);
        return false;
    }
    return true;
}

// --- Helper: Content for index.html (SIMPLIFIED) ---
function getIndexHtmlContent($paletteEmoji, $scriptPyEmojiGlobal, $scriptPhpEmojiGlobal, $rocketEmojiGlobal, $stopEmojiGlobal) {
    // Using single-quoted Nowdoc to ensure no PHP parsing within the HTML/JS content
    // Any PHP variables needed must be replaced using str_replace AFTER this block.
    $html_nowdoc_content = <<<'HTML_DEBUG_NOWDOC'
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Debug Fractal HTML (__PALETTE_EMOJI__)</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
<style>body{padding:20px; font-family: sans-serif; background:#333; color:#fff;} h1{text-align:center;}</style>
</head>
<body><h1>Debug HTML Generated (__PALETTE_EMOJI__)</h1><p>This is a placeholder. Python: __SCRIPT_PY_EMOJI__, PHP: __SCRIPT_PHP_EMOJI__</p>
<button>Start __ROCKET_EMOJI__</button> <button>Stop __STOP_EMOJI__</button>
<div id="3d-graph" style="width:100%; height:400px; border:1px solid #555;">3D Graph Placeholder</div>
<script src="//cdn.jsdelivr.net/npm/3d-force-graph"></script>
<script>
    console.log("Debug JavaScript loaded.");
    const N = 10;
    const gData = {
      nodes: [...Array(N).keys()].map(i => ({ id: i })),
      links: [...Array(N).keys()].filter(id => id).map(id => ({ source: id, target: Math.round(Math.random() * (id-1)) }))
    };
    try {
        const Graph = ForceGraph3D()(document.getElementById('3d-graph')).graphData(gData);
        console.log("3D Force Graph initialized with dummy data.");
    } catch (e) {
        console.error("Error initializing 3D Force Graph (is the library loaded?):", e);
        document.getElementById('3d-graph').textContent = "Error initializing 3D graph. Check console.";
    }
</script>
</body></html>
HTML_DEBUG_NOWDOC;

    // Replace placeholders
    $replacements = [
        '__PALETTE_EMOJI__' => $paletteEmoji,
        '__SCRIPT_PY_EMOJI__' => $scriptPyEmojiGlobal,
        '__SCRIPT_PHP_EMOJI__' => $scriptPhpEmojiGlobal,
        '__ROCKET_EMOJI__' => $rocketEmojiGlobal,
        '__STOP_EMOJI__' => $stopEmojiGlobal,
    ];
    $final_html = $html_nowdoc_content;
    foreach ($replacements as $placeholder => $value) {
        $final_html = str_replace($placeholder, $value, $final_html);
    }
    return $final_html;
}

// --- Helper: Content for fractal_orchestrator.php (SIMPLIFIED) ---
function getFractalOrchestratorPhpContent($pythonSubfolderName, $numPythonScripts, $maxCallDepth, $maxRuntime) {
    $template = <<<'ORCH_DEBUG_NOWDOC'
<?php
header('Content-Type: text/event-stream'); header('Cache-Control: no-cache');
set_time_limit(0); 
$maxRuntime = __MAX_RUNTIME__; $startTime = time();
ob_implicit_flush(true);
function send_event_debug($data) { echo "data: " . json_encode($data) . "\n\n"; if(ob_get_level()>0) ob_flush(); flush(); }
error_log("Debug Orchestrator started. Max runtime: " . $maxRuntime . "s.");
send_event_debug(['status' => 'Debug Orchestrator alive. Will send pings. Max runtime: ' . $maxRuntime . 's.']);
$c = 0;
while(true){
    if((time() - $startTime) >= $maxRuntime) { send_event_debug(['status' => 'Debug Orchestrator max runtime.']); break; }
    if(connection_aborted()) { error_log("Client disconnected orchestrator."); break; }
    send_event_debug(['ping' => $c++, 'time' => date('H:i:s')]);
    sleep(5);
}
error_log("Debug Orchestrator finished.");
?>
ORCH_DEBUG_NOWDOC;
    return str_replace('__MAX_RUNTIME__', $maxRuntime, $template);
}

// --- Helper: Content for individual Python scripts (SIMPLIFIED) ---
function getPythonScriptContent($scriptId, $numTotalScripts, $maxCallDepth) {
    $op_name = "debug_op";
    $modifier = rand(1,5);
    $python_template = <<<'PYTHON_DEBUG_NOWDOC'
import sys, json, random
# Script ID __SCRIPT_ID__: Op '__OP_NAME__'
if __name__ == "__main__":
    input_value = float(sys.argv[1]) if len(sys.argv) > 1 else 1.0
    output_value = input_value * __MODIFIER__ + random.uniform(-0.1,0.1)
    print(json.dumps({
        "script_id":__SCRIPT_ID__, "input_value":input_value, "op_type":"__OP_NAME__", 
        "output_value":output_value, "depth": int(sys.argv[2]) if len(sys.argv) > 2 else 0,
        "next_call_id": (random.randint(0, __NUM_SCRIPTS__ - 1) if random.random() < 0.5 else None) if (int(sys.argv[2] if len(sys.argv) > 2 else 0) < __MAX_DEPTH__ -1) else None,
        "num_total_scripts": __NUM_SCRIPTS__
    }))
PYTHON_DEBUG_NOWDOC;
    
    $replacements = [
        '__SCRIPT_ID__' => $scriptId,
        '__OP_NAME__' => $op_name,
        '__MODIFIER__' => $modifier,
        '__NUM_SCRIPTS__' => $numTotalScripts,
        '__MAX_DEPTH__' => $maxCallDepth
    ];
    $final_code = $python_template;
    foreach ($replacements as $placeholder => $value) {
        $final_code = str_replace($placeholder, (string)$value, $final_code);
    }
    return $final_code;
}

// --- Helper: Content for stop_server.php (SIMPLIFIED for debug, focusing on structure) ---
function getStopServerPhpContent($outer_serverHost, $outer_portRangeStart, $outer_portRangeEnd, $outer_serverProcessMarker, $outer_routerScriptNameForGrep) {
    global $e_info, $e_ok, $e_warn, $e_stop;
    $ports_to_check_json = json_encode(range($outer_portRangeStart, $outer_portRangeEnd));

    $template = <<<'STOP_DEBUG_NOWDOC'
<?php
$_E_INFO_ = '__E_INFO__'; $_E_OK_ = '__E_OK__'; $_E_WARN_ = '__E_WARN__'; $_E_STOP_ = '__E_STOP__';
echo "\$_E_INFO_ Debug Stop Server Script... (Simplified)\n";
\$host = '__HOST__'; \$ports = json_decode('__PORTS_JSON__', true); \$router = '__ROUTER_NAME__';
echo "Would check for server on {\$host} for router '{\$router}' on ports: " . implode(", ", \$ports) . "\n";
echo "\$_E_OK_ Debug stop script finished.\n";
?>
STOP_DEBUG_NOWDOC;

    $replacements = [
        '__E_INFO__' => $e_info, '__E_OK__' => $e_ok, '__E_WARN__' => $e_warn, '__E_STOP__' => $e_stop,
        '__HOST__' => addslashes($outer_serverHost),
        '__PORTS_JSON__' => addslashes($ports_to_check_json),
        '__ROUTER_NAME__' => addslashes($outer_routerScriptNameForGrep)
    ];
    $final_code = $template;
    foreach ($replacements as $placeholder => $value) {
        $final_code = str_replace($placeholder, $value, $final_code);
    }
    return $final_code;
}


// --- Main Generation Logic ---
// (Cleanup old server from same day's base name - this logic can stay)
$potentialOldProjectFolder = $mainProjectFolderName; // Fixed folder name
$potentialOldStopScript = $potentialOldProjectFolder . DIRECTORY_SEPARATOR . $serverScriptsSubfolderName . DIRECTORY_SEPARATOR . "stop_fractal_server.php";
if (is_dir($potentialOldProjectFolder)) {
    echo "$e_broom Project folder '$potentialOldProjectFolder' exists. Attempting to stop any running server and clear specific contents...\n";
    if (file_exists($potentialOldStopScript)) {
        $original_cwd = getcwd();
        $stopScriptDir = dirname($potentialOldStopScript);
        if (is_dir($stopScriptDir) && chdir($stopScriptDir)) {
            echo "$e_info Executing: php stop_fractal_server.php (in $stopScriptDir)\n";
            system("php stop_fractal_server.php"); 
            chdir($original_cwd);
            echo "$e_info Old server stop attempt finished.\n";
            if (PHP_OS_FAMILY !== 'Windows') sleep(1);
        } else {
            echo "$e_warn Could not change to '$stopScriptDir' for old stop script. Skipping auto-stop.\n";
        }
    } else {
         echo "$e_info No existing stop script found at '$potentialOldStopScript' to run for cleanup.\n";
    }
    // Clear contents robustly
    function deleteDirectoryContentsRecursive($dir) {
        if (!is_dir($dir)) return;
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                deleteDirectoryContentsRecursive($path);
                @rmdir($path); // Try to remove subdir if empty
            } else {
                @unlink($path);
            }
        }
    }
    // Specifically clear subfolders we create, and top-level files we create
    $subfolders_to_clear = [$pythonSubfolderName, $serverScriptsSubfolderName];
    $files_to_clear = ["index.html", "fractal_orchestrator.php", "___fractal_server_router.php", "php_server.log"];
    foreach($subfolders_to_clear as $subfolder) {
        $path_to_clear = $mainProjectFolderName . DIRECTORY_SEPARATOR . $subfolder;
        if (is_dir($path_to_clear)) deleteDirectoryContentsRecursive($path_to_clear);
        @rmdir($path_to_clear); // Attempt to remove the subfolder itself
    }
    foreach($files_to_clear as $file) {
        $path_to_clear = $mainProjectFolderName . DIRECTORY_SEPARATOR . $file;
        if (file_exists($path_to_clear)) @unlink($path_to_clear);
    }
    echo "$e_ok Specific contents of '$mainProjectFolderName' cleared for fresh generation.\n";
    echo "---------------------------------------------------\n";
}


if (!is_dir($mainProjectFolderName)) {
    if (!mkdir($mainProjectFolderName, 0755, true)) {
        die("$e_warn ABORT: Failed to create main project directory: $mainProjectFolderName\n");
    }
    echo "$e_ok $e_folder Created main project directory: $mainProjectFolderName\n";
}

$actualServerPort = null;
echo "$e_gear Searching for an available port in range $startPort-$endPort...\n";
for ($portAttempt = 0; $portAttempt < $maxPortRetries; $portAttempt++) {
    $currentTryPort = $startPort + $portAttempt;
    if ($currentTryPort > $endPort) {
        echo "$e_warn Exceeded port range $startPort-$endPort during retries.\n";
        break;
    }
    if (isPortAvailable($serverHost, $currentTryPort)) {
        $actualServerPort = $currentTryPort;
        echo "$e_ok Port $actualServerPort is available.\n";
        break;
    } else {
        echo "$e_info Port $currentTryPort is currently in use.\n";
    }
}
if ($actualServerPort === null) {
    $actualServerPort = $startPort; 
    echo "$e_warn Could not find an available port. Defaulting to $actualServerPort. Server start may fail.\n";
}

$serverScriptsPath = $mainProjectFolderName . DIRECTORY_SEPARATOR . $serverScriptsSubfolderName;
if (!is_dir($serverScriptsPath)) {
    if (!mkdir($serverScriptsPath, 0755, true)) {
        echo "$e_warn Warning: Failed to create server tools subfolder: $serverScriptsPath\n";
        $serverScriptsPath = $mainProjectFolderName; 
    } else {
         echo "$e_ok $e_folder Created server tools subfolder: $serverScriptsPath\n";
    }
}

$pythonScriptsFullPath = $mainProjectFolderName . DIRECTORY_SEPARATOR . $pythonSubfolderName;
if (!is_dir($pythonScriptsFullPath)) {
    if (mkdir($pythonScriptsFullPath, 0755, true)) {
        echo "$e_ok $e_folder Created Python scripts subfolder: $pythonScriptsFullPath\n";
    } else {
        die("$e_warn ABORT: Failed to create Python scripts subfolder: $pythonScriptsFullPath\n");
    }
}
echo "$e_gear Generating $numPythonScripts Python scripts ($e_script_py)...";
for ($i = 0; $i < $numPythonScripts; $i++) {
    $scriptName = sprintf("script_%03d.py", $i);
    $scriptPath = $pythonScriptsFullPath . DIRECTORY_SEPARATOR . $scriptName;
    $pythonContent = getPythonScriptContent($i, $numPythonScripts, $maxCallDepth);
    if (!file_put_contents($scriptPath, $pythonContent)) {
        echo "\n$e_warn Warning: Failed to write Python script: $scriptPath\n";
    }
    if ($i > 0 && ($i+1) % 10 == 0) echo "."; // More frequent progress for fewer scripts
}
echo " $e_ok Done.\n";

$indexHtmlPath = $mainProjectFolderName . DIRECTORY_SEPARATOR . "index.html";
if (file_put_contents($indexHtmlPath, getIndexHtmlContent($e_palette, $e_script_py, $e_script_php, $e_rocket, $e_stop))) {
    echo "$e_ok $e_html Generated $indexHtmlPath.\n";
} else {
    echo "$e_warn Warning: Failed to write $indexHtmlPath.\n";
}

$fractalOrchestratorName = "fractal_orchestrator.php";
$orchestratorPath = $mainProjectFolderName . DIRECTORY_SEPARATOR . $fractalOrchestratorName;
if (file_put_contents($orchestratorPath, getFractalOrchestratorPhpContent($pythonSubfolderName, $numPythonScripts, $maxCallDepth, $defaultMaxOrchestratorRuntime))) {
    echo "$e_ok $e_script_php Generated $orchestratorPath ($e_timer will run for max ~$defaultMaxOrchestratorRuntime seconds).\n";
} else {
    echo "$e_warn Warning: Failed to write $orchestratorPath.\n";
}

$routerScriptName = "___fractal_server_router.php";
$routerScriptContent = "<?php\n// DO NOT DELETE - Process Marker: {$serverProcessMarker}\n// Serves files from current dir or index.html for directories.\n\$docRoot = __DIR__;\n\$reqUri = \$_SERVER['REQUEST_URI'];\n\$reqPath = \$docRoot . preg_replace('/\?.*/', '', \$reqUri);\nif (is_file(\$reqPath) && file_exists(\$reqPath) && basename(\$reqPath) !== '$routerScriptName') {\n    return false;\n} elseif (file_exists(\$docRoot . '/index.html')) {\n    require_once \$docRoot . '/index.html';\n} else {\n    http_response_code(404);\n    echo '404 Not Found - index.html missing in ' . \$docRoot;\n}\n?>";
$routerPath = $mainProjectFolderName . DIRECTORY_SEPARATOR . $routerScriptName;
if (!file_put_contents($routerPath, $routerScriptContent)) {
     echo "$e_warn Warning: Failed to write server router script '$routerPath'.\n";
} else {
    echo "$e_ok $e_wrench Generated server router: " . basename($routerPath) . "\n";
}

$stopServerScriptName = "stop_fractal_server.php";
$stopServerPath = $serverScriptsPath . DIRECTORY_SEPARATOR . $stopServerScriptName;
if (file_put_contents($stopServerPath, getStopServerPhpContent($serverHost, $startPort, $endPort, $serverProcessMarker, $routerScriptName))) {
    chmod($stopServerPath, 0755);
    echo "$e_ok $e_stop Generated server stopper script: " . basename($serverScriptsPath) . DIRECTORY_SEPARATOR . $stopServerScriptName . "\n";
} else {
    echo "$e_warn Warning: Failed to write $stopServerPath.\n";
}

echo "---------------------------------------------------\n";
echo "$e_party Project generation complete! $e_party\n";
echo "---------------------------------------------------\n";

$serverCommand = sprintf("php -S %s:%d %s", escapeshellarg($serverHost), $actualServerPort, escapeshellarg($routerScriptName) );
$urlToOpen = "http://$serverHost:$actualServerPort/";
$projectFullPath = getcwd() . DIRECTORY_SEPARATOR . $mainProjectFolderName;
$stopCommandInstruction = "php " . escapeshellarg($projectFullPath . DIRECTORY_SEPARATOR . $serverScriptsSubfolderName . DIRECTORY_SEPARATOR . $stopServerScriptName);
$serverLogFile = $projectFullPath . DIRECTORY_SEPARATOR . "php_server.log";

echo "$e_info $e_terminal TO RUN THE FRACTAL ART VISUALIZATION:\n";
echo "1. This script will ATTEMPT to start the server in the background.\n";
echo "   $e_log Server output (if any) will be logged to: " . escapeshellarg(basename($projectFullPath) . DIRECTORY_SEPARATOR ."php_server.log") . "\n";
echo "2. $e_link If successful, your browser should open to: $urlToOpen\n";
echo "3. Click 'Start Visualization' $e_rocket on the webpage (if using the SSE version of HTML).\n";
echo "   For the 3D Force Graph version, it should load automatically.\n";
echo "$e_info $e_timer Orchestrator default runtime: ~$defaultMaxOrchestratorRuntime seconds (if SSE version).\n";
echo "---------------------------------------------------\n";
echo "$e_info $e_stop TO STOP THE SERVER:\n";
echo "   Run this exact command from THIS directory ($e_eyes " . getcwd() . " ):\n";
echo "   $stopCommandInstruction\n";
echo "   (Alternatively, cd into '" . basename($projectFullPath) . DIRECTORY_SEPARATOR . basename($serverScriptsPath) ."' and run 'php $stopServerScriptName')\n";
echo "   OR stop 'php -S ... $routerScriptName' manually (e.g., Ctrl+C or OS process manager).\n";
echo "---------------------------------------------------\n";

// Logic to prevent auto-server start if script is run by a web server (like your 'r' alias)
if (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') {
    $backgroundCmd = '';
    $serverOutputRedirect = ' > ' . escapeshellarg($serverLogFile) . ' 2>&1';

    if (PHP_OS_FAMILY === 'Windows') {
        $phpExe = (shell_exec('where php')) ? 'php' : ( (shell_exec('where php.exe')) ? 'php.exe' : 'php' );
        $serverCommandWin = sprintf("%s -S %s:%d %s", $phpExe, escapeshellarg($serverHost), $actualServerPort, escapeshellarg($routerScriptName) );
        $backgroundCmd = "start /B \"PHP Fractal Server\" cmd /c \"cd /D " . escapeshellarg($projectFullPath) . " && $serverCommandWin $serverOutputRedirect\"";
    } else { 
        $backgroundCmd = "(cd " . escapeshellarg($projectFullPath) . " && exec $serverCommand $serverOutputRedirect) &";
    }

    echo "$e_rocket Attempting to start PHP server in background ($serverHost:$actualServerPort)...\n";
    shell_exec($backgroundCmd); 
    sleep(3); 

    echo "$e_eyes Checking if server started on $urlToOpen ...\n";
    $context_options = ['http' => ['timeout' => 3.0, 'ignore_errors' => true]];
    if (defined('HHVM_VERSION')) { 
        $context_options['http']['user_agent'] = 'PHP script';
    }
    $context = stream_context_create($context_options); 
    $headers = @get_headers($urlToOpen, 0, $context); 

    if ($headers && isset($headers[0]) && (strpos($headers[0], '200') !== false || strpos($headers[0], '301') !== false || strpos($headers[0], '302') !== false) ) {
        echo "$e_ok Server seems to have started successfully (responded with: $headers[0])!\n";
    } else {
        echo "$e_warn Server might not have started or is not reachable at $urlToOpen.\n";
        echo "$e_warn Response headers: " . ($headers ? implode("|", $headers) : "No response") . "\n";
        echo "$e_warn Check the server log: " . escapeshellarg(basename($projectFullPath) . DIRECTORY_SEPARATOR ."php_server.log") . "\n";
        echo "$e_warn If it failed, try starting it manually from the terminal:\n";
        echo "   cd " . escapeshellarg($projectFullPath) . "\n";
        echo "   php -S $serverHost:$actualServerPort " . escapeshellarg($routerScriptName) . "\n";
    }

    echo "$e_eyes Attempting to open '$urlToOpen' in your default browser...\n";
    $opener = '';
    if (PHP_OS_FAMILY === 'Darwin') $opener = 'open';
    elseif (PHP_OS_FAMILY === 'Windows') $opener = 'start ""';
    elseif (PHP_OS_FAMILY === 'Linux') $opener = 'xdg-open';

    if ($opener) {
        $browserCmd = ($opener === 'start ""') ? $opener . ' ' . escapeshellarg($urlToOpen) : $opener . ' ' . escapeshellarg($urlToOpen);
        @system($browserCmd . ($opener === 'start ""' ? '' : ' > /dev/null 2>&1'));
        echo "$e_link If browser didn't open, please navigate to the URL manually.\n";
    } else {
        echo "$e_warn Could not determine OS to auto-open browser. Please open: $urlToOpen\n";
    }
} else {
    echo "$e_warn Script is not running in CLI mode. Skipping auto-start of server and browser.\n";
    echo "$e_info Please run this script from your command line: php " . basename(__FILE__) . "\n";
}


echo "---------------------------------------------------\n";
echo "$e_party Script finished! $e_palette Check browser and '$mainProjectFolderName'.\n";

?>