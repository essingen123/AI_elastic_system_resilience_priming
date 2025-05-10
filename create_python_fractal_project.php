<?php
// create_python_fractal_project.php
// Version: 2023-10-28_FractalWeaver_v2.3 (3D Graph HTML, Simplified Python Gen)
// Generator Timestamp: <?php echo date('Y-m-d H:i:s T'); ? >

// --- Configuration ---
date_default_timezone_set('UTC');
$scriptVersion = "FractalWeaver_v2.3 (" . date('Y-m-d') . ")";

$numPythonScripts = 50; // Reduced for faster testing with simpler Python
$randomNumberForName = rand(10, 99);
// For simpler folder structure as requested - always use python_fractal_gen
$mainProjectFolderName = "python_fractal_gen"; // FIXED FOLDER NAME

$pythonSubfolderName = "py_scripts"; // Simpler subfolder name
$serverScriptsSubfolderName = "_server_utils";
$maxCallDepth = 3; // Reduced for simpler Python
$defaultMaxOrchestratorRuntime = 30;

$serverHost = "localhost";
$startPort = 10021; // New port range
$endPort = 10030;
$maxPortRetries = 5;

// Emojis
$e_sparkle = "✨"; $e_folder = "📁"; $e_script_py = "🐍"; $e_script_php = "🐘";
$e_html = "📄"; $e_rocket = "🚀"; $e_palette = "🎨"; $e_gear = "⚙️";
$e_warn = "⚠️"; $e_info = "ℹ️"; $e_ok = "✅"; $e_party = "🎉";
$e_link = "🔗"; $e_terminal = "💻"; $e_eyes = "👀"; $e_stop = "🛑"; $e_timer = "⏱️"; $e_wrench = "🔧"; $e_magic = "🪄"; $e_broom = "🧹"; $e_log = "📜"; $e_conduct = "📜";

$serverProcessMarker = "fractal_server_process_marker_" . bin2hex(random_bytes(8));

// Manifesto (content assumed to be the same)
$manifesto_q1 = <<<MANIFESTO
kilian v0.1x CODE OF CONDUCT CODING GUIDE
KEEP CONCISE SELF-EXPLANATORY CODE READABLE FOR EXPERTS.
NO CODE COMMENTS EXCEPT: THIS CODE GUIDE, FILE HEADERS (SHEBANG/LICENSE/CRUCIAL SELF-EVIDENT STUFF THAT MAY ARISE), TODOs, AND PAIRED # CODE_BLOCK_ONLY_CHANGED_BY_AUTHOR_START / # CODE_BLOCK_ONLY_CHANGED_BY_AUTHOR_END.
SINGLE SOURCE OF TRUTH (SSOT) DYNAMIC GENERATION AND FACTS ONCE ONLY IF POSSIBLE.
SELF-CONTAINED RELOCATABLE FUNCTIONS IN RELATION TO THE REST OF THE FILE.
USE SHORTHANDS FOR VARIABLES AND NAMES IN SINGLE-FILE CONTEXTS TO REDUCE CODE LENGTH.
BLANK LINES FOR FUNCTION SEPARATION AND LOGICAL BLOCKS (LOOPS, CONDITIONALS INCLUDING REGEX) ENSURING CORRECT PARSING.
PRINCIPAL BASED
"Transform force to power"; decrease stress, increase support
be transaprent but avoid weakness in games of "unfair advantage"
"ALWAYS RISE TO THE OCCASION"; meaning - "keep a mind like water" meaning respond (effectivly) adequeatly (dont react affectivly overengineer)"
be fair and kind
copy with pride, but never steal
care for the small, transform compete to synergy with partners
Measure time
MÄT TID FÖR SKAPANDE; docker + start och nedtid.. samt prestanda-krav
hade google chrome en lokal ai med gemini GEMMA? ! .. visar att det går ! finns detta etablerat? dedikerade typ "noder" för ai med browser !?!? supercoolt! alltså typ playwright stuk
MANTRA

CONSIDER: BE AGILE; DONT CHANGE PRINCIPAL CORE - BUD ADAPT TO THE LATEST CHANGES; MEANING CHANGE BUSINESSS IDEAS IDEA DAILY(?)
pitchdeck becomes obselete as soon as pitched to investors (thats why we have automation tools for it)
failure just happens at 
keep it simple yet effective
imagine wished outcome (stay at center)
how to reach x valuation; keep focus
build tests
do benchmarks; have dashboard
now timeline; break it down
zeroday-thinking; best songs are made in an hour - not 3 weeks - they get over saturated; how about products?

PHP snabbare än python i prototyper? kör php! är php typ lika portabelt som python; kanske ännu mer eftersom det mer sällan kanske kopplar in en massa bibliotek(?) .. eller så var det en gång i allafall..
php + sqlite (text-files?)
MANIFESTO;
$manifesto_q2_expected_hash = 'ceaf9d0f';
function calculate_manifesto_hash($text) { return substr(sha1($text), 0, 8); }
$manifesto_q1_current_hash = calculate_manifesto_hash($manifesto_q1);

echo "$e_magic Create Python Fractal Project - Generator v{$scriptVersion} $e_magic\n";
echo "Generator run timestamp: " . date('Y-m-d H:i:s T') . "\n";
if ($manifesto_q1_current_hash === $manifesto_q2_expected_hash) {
    echo "$e_conduct Manifesto Check: $e_ok UNCHANGED ($manifesto_q1_current_hash)\n";
} else {
    echo "$e_conduct Manifesto Check: $e_warn CHANGED! Expected '$manifesto_q2_expected_hash', got '$manifesto_q1_current_hash'.\n";
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

// --- Helper: Content for index.html (NEW VERSION with 3D Force Graph) ---
function getIndexHtmlContent() {
    // This HTML is self-contained for the 3D graph example.
    // PHP variables are not interpolated into the <script> part here.
    return <<<'§§kilian_html_delimiter§§'
<!DOCTYPE html>
<html lang="en">
<head>
  <style>
    body { margin: 0; }
    #controls {
      position: absolute;
      top: 10px;
      left: 10px;
      z-index: 100;
      background: rgba(0,0,0,0.5);
      padding: 10px;
      border-radius: 5px;
    }
    #controls button, #controls input, #controls label {
      margin: 5px;
      color: white; /* Basic styling for controls */
    }
    #status {
        position: absolute;
        bottom: 10px;
        left: 10px;
        color: lightgrey;
        font-family: sans-serif;
        font-size: 0.8em;
        background: rgba(0,0,0,0.5);
        padding: 5px;
        border-radius: 3px;
    }
  </style>
  <script src="//cdn.jsdelivr.net/npm/3d-force-graph"></script>
</head>
<body>
  <div id="3d-graph"></div>
  <div id="controls">
    <button id="emit-particles-btn">Emit 10 Particles</button>
    <button id="reset-graph-btn">New Random Graph</button>
    <label for="num-nodes">Nodes:</label>
    <input type="number" id="num-nodes" value="50" min="10" max="300" style="width: 60px;">
  </div>
  <div id="status">Graph Ready. Click links or Emit Particles.</div>

  <script>
    const graphElem = document.getElementById('3d-graph');
    const statusElem = document.getElementById('status');
    let N = 50; // Default number of nodes
    let gData = { nodes: [], links: [] };

    const Graph = ForceGraph3D()(graphElem)
      .linkDirectionalParticles(2)
      .linkDirectionalParticleWidth(2.5)
      .linkDirectionalParticleColor(() => 'rgba(255,0,0,0.8)')
      .linkHoverPrecision(10)
      .onLinkClick(link => Graph.emitParticle(link));

    function generateRandomGraph(numNodes) {
      gData.nodes = [...Array(numNodes).keys()].map(i => ({ 
        id: i,
        val: Math.random() * 5 + 1 // Random value for node size/color later
      }));
      gData.links = [...Array(numNodes).keys()]
        .filter(id => id > 0) // Node 0 has no source target in this simple model
        .map(id => ({
          source: id,
          target: Math.round(Math.random() * (id - 1)),
          value: Math.random() // For particle speed or link strength
        }));
      Graph.graphData(gData)
           .nodeVal('val') // Use 'val' for node size
           .nodeColor(node => { // Color based on ID or val
                const hue = (node.id * 360 / numNodes) % 360;
                return `hsl(${hue}, 80%, 60%)`;
           })
           .linkWidth(link => 0.2 + link.value * 1.5)
           .linkDirectionalParticleSpeed(link => link.value * 0.01 + 0.005);
      statusElem.textContent = `Generated graph with ${numNodes} nodes.`;
    }
    
    document.getElementById('emit-particles-btn').addEventListener('click', () => {
      if (!gData.links.length) return;
      let count = 0;
      const interval = setInterval(() => {
        if (count >= 10) {
          clearInterval(interval);
          return;
        }
        const link = gData.links[Math.floor(Math.random() * gData.links.length)];
        Graph.emitParticle(link);
        count++;
      }, 100); // Emit particles with a slight delay
      statusElem.textContent = 'Emitting 10 random particles...';
    });

    document.getElementById('reset-graph-btn').addEventListener('click', () => {
        N = parseInt(document.getElementById('num-nodes').value) || 50;
        generateRandomGraph(N);
    });
    
    // Initial graph
    N = parseInt(document.getElementById('num-nodes').value) || 50;
    generateRandomGraph(N);

    // Adjust graph size to window
    Graph.width(window.innerWidth);
    Graph.height(window.innerHeight);
    window.addEventListener('resize', () => {
        Graph.width(window.innerWidth);
        Graph.height(window.innerHeight);
    });

    // Optional: camera orbit
    // let angle = 0;
    // setInterval(() => {
    //   Graph.cameraPosition({
    //     x: 200 * Math.sin(angle),
    //     z: 200 * Math.cos(angle)
    //   });
    //   angle += Math.PI / 300;
    // }, 40);

  </script>
</body>
</html>
§§kilian_html_delimiter§§;
}

// --- Helper: Content for fractal_orchestrator.php ---
// This orchestrator will now be simpler as the new HTML doesn't use its SSE for drawing.
// We can keep it as a placeholder or adapt it later if the 3D graph needs external data.
// For now, let's make it very basic, as it won't be directly used by the new index.html.
function getFractalOrchestratorPhpContent($pythonSubfolderName, $numPythonScripts, $maxCallDepth, $maxRuntime) {
    // For this version, the orchestrator is simplified as the 3D graph is self-contained.
    // It can still run Python scripts if we want to log their output or use them for something else later.
    return <<<PHP
<?php
header('Content-Type: text/event-stream'); // Still set for potential future use
header('Cache-Control: no-cache');
header('Connection: keep-alive');
set_time_limit(0); 

\$pythonProjectFolder = "$pythonSubfolderName"; 
\$numPythonScripts = $numPythonScripts;
\$maxCallDepth = $maxCallDepth;
\$maxOrchestratorRuntime = $maxRuntime; 

ob_implicit_flush(true);
\$startTime = time();
\$eventCount = 0;

function send_event_orchestrator(\$data) { // Renamed to avoid conflict if this file is included
    echo "data: " . json_encode(\$data) . "\\n\\n";
    if (ob_get_level() > 0) ob_flush();
    flush();
}

error_log("Fractal Orchestrator (v2.3 - 3D Graph Version) started. Max runtime: {\$maxOrchestratorRuntime}s.");
send_event_orchestrator(['status' => 'Orchestrator alive, but index.html uses self-generated graph data.']);

// Minimal loop just to keep alive for the duration or if we want to add Python interaction later
while(true) {
    if ((time() - \$startTime) >= \$maxOrchestratorRuntime) {
        send_event_orchestrator(['status' => 'Orchestrator max runtime reached.']);
        error_log("Fractal Orchestrator reached max runtime.");
        break;
    }
    if (connection_aborted()) {
        error_log("Client disconnected from orchestrator.");
        break;
    }
    // Send a heartbeat or a simple message periodically if desired
    // send_event_orchestrator(['heartbeat' => date('H:i:s'), 'message' => 'Orchestrator is idling.']);
    \$eventCount++;
    sleep(10); // Check every 10 seconds
}

error_log("Fractal Orchestrator finished.");
?>
PHP;
}

// --- Helper: Content for individual Python scripts (SIMPLIFIED) ---
function getPythonScriptContent($scriptId, $numTotalScripts, $maxCallDepth) {
    // Drastically simplified Python for now to ensure generator stability
    $ops = ['add', 'subtract', 'multiply'];
    $chosen_op = $ops[array_rand($ops)];
    $modifier = rand(1, 5);
    
    $python_code = "";
    $python_code .= "import sys, json, random, math\n\n";
    $python_code .= "def my_operation(val, mod):\n";
    if ($chosen_op === 'add') {
        $python_code .= "    return val + mod\n";
    } elseif ($chosen_op === 'subtract') {
        $python_code .= "    return val - mod\n";
    } else { // multiply
        $python_code .= "    return val * mod\n";
    }
    $python_code .= "\n";
    $python_code .= "if __name__ == \"__main__\":\n";
    $python_code .= "    input_value = float(sys.argv[1]) if len(sys.argv) > 1 else 1.0\n";
    $python_code .= "    # Other args (depth, num_scripts, max_depth) are not used by this simple version\n";
    $python_code .= "    output_value = my_operation(input_value, $modifier)\n";
    $python_code .= "    # Ensure output is finite and within a reasonable range for the 3D graph if used later\n";
    $python_code .= "    if math.isnan(output_value) or math.isinf(output_value): output_value = random.uniform(-100, 100)\n";
    $python_code .= "    output_value = max(-1e5, min(1e5, output_value))\n";
    $python_code .= "    result = {\n";
    $python_code .= "        \"script_id\": $scriptId,\n";
    $python_code .= "        \"input_value\": input_value,\n";
    $python_code .= "        \"op_type\": \"$chosen_op\",\n";
    $python_code .= "        \"modifier_used\": $modifier,\n";
    $python_code .= "        \"output_value\": output_value,\n";
    $python_code .= "        \"message\": \"Simple Python script $scriptId executed.\"\n";
    $python_code .= "    }\n";
    $python_code .= "    print(json.dumps(result))\n";

    return $python_code;
}

function getStopServerPhpContent($outer_serverHost, $outer_portRangeStart, $outer_portRangeEnd, $outer_serverProcessMarker, $outer_routerScriptNameForGrep) {
    global $e_info, $e_ok, $e_warn, $e_stop; 
    $ports_to_check_str_array = [];
    for ($p = $outer_portRangeStart; $p <= $outer_portRangeEnd; $p++) {
        $ports_to_check_str_array[] = (string)$p;
    }
    $ports_to_check_json_for_stop_script = json_encode($ports_to_check_str_array);
    $embedded_serverHost = addslashes($outer_serverHost);
    $embedded_ports_json = addslashes($ports_to_check_json_for_stop_script); 
    $embedded_marker = addslashes($outer_serverProcessMarker);
    $embedded_router_name = addslashes($outer_routerScriptNameForGrep);
    $stop_e_info = $e_info; $stop_e_ok = $e_ok; $stop_e_warn = $e_warn; $stop_e_stop_glyph = $e_stop;

    // Using Nowdoc for the stop script to avoid any escaping issues with its internal PHP code
    $stop_script_template = <<<'PHP_STOP_SCRIPT_NOWDOC'
<?php
// stop_fractal_server.php - Generated
// These placeholders will be replaced by the generator script
$_E_INFO_ = '__PHP_E_INFO__'; $_E_OK_ = '__PHP_E_OK__'; 
$_E_WARN_ = '__PHP_E_WARN__'; $_E_STOP_GLYPH_ = '__PHP_E_STOP_GLYPH__';

echo "\$_E_INFO_ Attempting to stop PHP built-in server(s) for the Fractal Art project...\n";

\$php_serverHost_val = '__PHP_EMBEDDED_SERVER_HOST__'; 
\$php_portsToCheck_val = json_decode('__PHP_EMBEDDED_PORTS_JSON__', true);
\$php_processMarker_val = '__PHP_EMBEDDED_MARKER__'; 
\$php_routerScriptName_val = '__PHP_EMBEDDED_ROUTER_NAME__'; 

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
PHP_STOP_SCRIPT_NOWDOC;

    $replacements = [
        '__PHP_E_INFO__' => $stop_e_info,
        '__PHP_E_OK__' => $stop_e_ok,
        '__PHP_E_WARN__' => $stop_e_warn,
        '__PHP_E_STOP_GLYPH__' => $stop_e_stop_glyph,
        '__PHP_EMBEDDED_SERVER_HOST__' => $outer_serverHost, // No addslashes for str_replace value
        '__PHP_EMBEDDED_PORTS_JSON__' => $ports_to_check_json_for_stop_script,
        '__PHP_EMBEDDED_MARKER__' => $outer_serverProcessMarker,
        '__PHP_EMBEDDED_ROUTER_NAME__' => $outer_routerScriptNameForGrep,
    ];
    $final_stop_script_code = $stop_script_template;
    foreach($replacements as $placeholder => $value){
        $final_stop_script_code = str_replace($placeholder, $value, $final_stop_script_code);
    }
    return $final_stop_script_code;
}


// --- Main Generation Logic ---
// Always use the fixed folder name, and attempt to clean it first.
$fixedProjectFolderName = "python_fractal_gen"; // As requested
$mainProjectFolderName = $fixedProjectFolderName; // Use this throughout

// Attempt to stop any old server instance if the project folder exists from a previous run
$potentialOldStopScript = $mainProjectFolderName . DIRECTORY_SEPARATOR . $serverScriptsSubfolderName . DIRECTORY_SEPARATOR . "stop_fractal_server.php";
if (is_dir($mainProjectFolderName) && file_exists($potentialOldStopScript)) {
    echo "$e_broom Attempting to stop server from existing '$mainProjectFolderName' (if any)...\n";
    $original_cwd = getcwd();
    $stopScriptDir = $mainProjectFolderName . DIRECTORY_SEPARATOR . $serverScriptsSubfolderName;
    if (is_dir($stopScriptDir) && chdir($stopScriptDir)) {
        echo "$e_info Executing: php stop_fractal_server.php (in context of $serverScriptsSubfolderName)\n";
        system("php stop_fractal_server.php"); 
        chdir($original_cwd);
        echo "$e_info Old server stop attempt finished.\n";
        if (PHP_OS_FAMILY !== 'Windows') sleep(1);
    } else {
        echo "$e_warn Could not change to '$serverScriptsSubfolderName' directory ('$stopScriptDir'). Skipping auto-stop.\n";
    }
    echo "---------------------------------------------------\n";
}

// Delete contents of the project folder if it exists, then recreate
if (is_dir($mainProjectFolderName)) {
    echo "$e_broom Directory '$mainProjectFolderName' exists. Clearing contents for a fresh generation...\n";
    // More robust directory clearing
    function deleteDirectory($dir) {
        if (!file_exists($dir)) return true;
        if (!is_dir($dir)) return unlink($dir);
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
        }
        return rmdir($dir);
    }
    if (!deleteDirectory($mainProjectFolderName)) {
        die("$e_warn ABORT: Could not clear existing project directory: $mainProjectFolderName. Check permissions.\n");
    }
    echo "$e_ok Contents of '$mainProjectFolderName' cleared.\n";
}

if (!mkdir($mainProjectFolderName, 0755, true)) { // Recreate after clearing
    die("$e_warn ABORT: Failed to create main project directory: $mainProjectFolderName\n");
}
echo "$e_ok $e_folder Created main project directory: $mainProjectFolderName\n";


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
    if ($i > 0 && ($i+1) % 30 == 0) echo ".";
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
// Corrected stop command instruction to reflect the new structure
$stopCommandInstruction = "php " . escapeshellarg($projectFullPath . DIRECTORY_SEPARATOR . $serverScriptsSubfolderName . DIRECTORY_SEPARATOR . $stopServerScriptName);
$serverLogFile = $projectFullPath . DIRECTORY_SEPARATOR . "php_server.log";


echo "$e_info $e_terminal TO RUN THE FRACTAL ART VISUALIZATION:\n";
echo "1. This script will ATTEMPT to start the server in the background.\n";
echo "   $e_log Server output (if any) will be logged to: " . escapeshellarg(basename($projectFullPath) . DIRECTORY_SEPARATOR ."php_server.log") . "\n";
echo "2. $e_link If successful, your browser should open to: $urlToOpen\n";
echo "3. Click 'Start Visualization' $e_rocket on the webpage.\n";
echo "$e_info $e_timer Orchestrator default runtime: ~$defaultMaxOrchestratorRuntime seconds.\n";
echo "---------------------------------------------------\n";
echo "$e_info $e_stop TO STOP THE SERVER:\n";
echo "   Run this exact command from THIS directory ($e_eyes " . getcwd() . " ):\n";
echo "   $stopCommandInstruction\n";
echo "   (Alternatively, cd into '$mainProjectFolderName/$serverScriptsSubfolderName' and run 'php $stopServerScriptName')\n";
echo "   OR stop 'php -S ... $routerScriptName' manually (e.g., Ctrl+C or OS process manager).\n";
echo "---------------------------------------------------\n";

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
echo "---------------------------------------------------\n";
echo "$e_party Script finished! $e_palette Check browser and '$mainProjectFolderName'.\n";

?>