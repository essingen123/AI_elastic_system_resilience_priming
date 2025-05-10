// --- Helper: Content for index.html (NEW VERSION with 3D Force Graph + SSE) ---
function getIndexHtmlContent($paletteEmoji, $scriptPyEmojiGlobal, $scriptPhpEmojiGlobal, $rocketEmojiGlobal, $stopEmojiGlobal) {
    // Nowdoc for maximum literalness of JS/HTML
    return <<<'§§kilian_html_nowdoc_delimiter§§'
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Python 3D Fractal Graph %PALETTE_EMOJI%</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
  <style>
    :root { 
        --background-color: #0c1014; --color: #ccc; --h1-color: #eef; --muted-color: #9ab;
        --primary: #00e0ff; --primary-hover: #00c0dd; --card-background-color: rgba(20, 30, 40, 0.9);
        --card-border-color: rgba(40, 60, 80, 0.8);
    }
    html, body { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; background-color: var(--background-color); color: var(--color); font-family: 'Segoe UI', sans-serif; display: flex; flex-direction: column; }
    #graph-container { flex-grow: 1; width: 100%; height: 100%; position: relative; }
    #controls-overlay {
      position: absolute; top: 10px; left: 10px; z-index: 100;
      background: var(--card-background-color); padding: 10px; border-radius: 8px;
      border: 1px solid var(--card-border-color); box-shadow: 0 3px 15px rgba(0,0,0,0.4);
      display: flex; flex-direction: column; gap: 8px; max-width: 280px;
    }
    #controls-overlay h2 { font-size: 1.1em; margin: 0 0 5px 0; color: var(--h1-color); text-align: center; }
    #controls-overlay button, #controls-overlay select, #controls-overlay label, #controls-overlay input { font-size: 0.85em; margin: 2px 0; }
    #controls-overlay .control-group { display: flex; flex-direction: column; margin-bottom: 8px; }
    #controls-overlay .control-row { display: flex; align-items: center; gap: 5px; }
    #controls-overlay input[type="range"] { flex-grow: 1; }
    #statusMessage { 
        font-style: italic; color: var(--muted-color); font-size: 0.8em; margin-top: 5px; 
        padding: 5px; border-radius: 4px; text-align: center;
        max-height: 50px; overflow-y: auto;
    }
    #3d-graph { width: 100%; height: 100%; } 
  </style>
  <script src="//cdn.jsdelivr.net/npm/3d-force-graph"></script>
</head>
<body>
  <div id="graph-container">
    <div id="3d-graph"></div>
  </div>

  <div id="controls-overlay">
    <h2>%PALETTE_EMOJI% Fractal Weaver</h2>
    <div class="control-group">
        <button id="startButton" class="primary">Start Weaving %ROCKET_EMOJI%</button>
        <button id="stopButton" class="secondary" disabled>Stop Stream %STOP_EMOJI%</button>
        <button id="clearGraphButton" class="contrast">Clear Graph 🧹</button>
    </div>
    <div class="control-group">
        <label for="particleEmissionRate">Particle Emission (0-100%):</label>
        <input type="range" id="particleEmissionRate" min="0" max="100" value="15">
    </div>
     <div class="control-group">
        <label for="nodeSizeFactor">Node Size Factor (0.1-3):</label>
        <input type="range" id="nodeSizeFactor" min="1" max="30" value="8">
    </div>
    <div id="statusMessage">Ready to weave the digital tapestry...</div>
  </div>

  <script>
    const graphElem = document.getElementById('3d-graph');
    const statusElem = document.getElementById('statusMessage');
    const startButton = document.getElementById('startButton');
    const stopButton = document.getElementById('stopButton');
    const clearGraphButton = document.getElementById('clearGraphButton');
    const particleEmissionRateSlider = document.getElementById('particleEmissionRate');
    const nodeSizeFactorSlider = document.getElementById('nodeSizeFactor');

    let eventSource = null;
    let N_SCRIPTS_JS = 303; // Default, updated from first SSE data
    
    const graphData = { nodes: [], links: [] };
    const nodeMap = new Map(); // For quick node lookup and update

    const Graph = ForceGraph3D({ controlType: 'orbit' })(graphElem)
      .backgroundColor('#0c1014')
      .showNavInfo(false) // Keep it clean
      .nodeLabel(node => `ID: ${node.id}<br>Op: ${node.op_type || 'N/A'}<br>Val: ${node.output_value ? node.output_value.toFixed(2) : 'N/A'}<br>Depth: ${node.depth || 0}`)
      .linkLabel(link => `From: ${link.source_id} -> To: ${link.target_id}<br>Depth: ${link.depth || 0}`)
      .linkWidth(link => 0.2 + (link.depth || 0) * 0.05)
      .linkDirectionalParticles(0) // Start with 0 particles
      .linkDirectionalParticleWidth(link => 1.0 + (link.depth || 0) * 0.2)
      .linkDirectionalParticleColor(link => link.color || 'rgba(255,0,0,0.7)')
      .linkDirectionalParticleResolution(6)
      .linkDirectionalParticleSpeed(link => (link.value || 0.01) * 0.005 + 0.005) // Use link value if available
      .onNodeClick(node => {
        Graph.cameraPosition({ x: node.x + 50, y: node.y + 50, z: node.z + 50 }, node, 800);
      });

    let particleEmissionRate = 0.15; // Default 15% chance per eligible link
    let nodeSizeFactor = 0.8;

    function updateGraphParams() {
        particleEmissionRate = parseInt(particleEmissionRateSlider.value) / 100.0;
        nodeSizeFactor = parseFloat(nodeSizeFactorSlider.value) / 10.0;
    }
    particleEmissionRateSlider.addEventListener('input', updateGraphParams);
    nodeSizeFactorSlider.addEventListener('input', updateGraphParams);
    updateGraphParams(); // Initial call

    function clearGraph() {
        graphData.nodes = [];
        graphData.links = [];
        nodeMap.clear();
        Graph.graphData(graphData).resumeAnimation(); // Clear and resume physics
        statusElem.textContent = "Graph cleared. Ready.";
    }

    function addOrUpdateNodeFromSSE(data) {
        N_SCRIPTS_JS = data.num_total_scripts || N_SCRIPTS_JS;
        const nodeId = data.script_id;
        let node = nodeMap.get(nodeId);
        let isNew = false;
        if (!node) {
            isNew = true;
            node = { 
                id: nodeId, 
                fx: (Math.random() - 0.5) * 150, // Initial random positions
                fy: (Math.random() - 0.5) * 150,
                fz: (Math.random() - 0.5) * 150
            };
            graphData.nodes.push(node);
            nodeMap.set(nodeId, node);
        }
        
        node.output_value = data.output_value;
        node.op_type = data.op_type;
        node.depth = data.depth;
        
        // Dynamic node size
        node.val = nodeSizeFactor * (1.5 + (data.depth || 0) * 0.3 + Math.min(5, Math.abs(data.output_value % 20)/5) );
        
        // Dynamic node color
        const hue = (nodeId * 360 / N_SCRIPTS_JS + (data.depth || 0) * 30 + (data.output_value || 0) * 5) % 360;
        const saturation = Math.min(100, 60 + (data.depth || 0) * 7 + Math.abs(data.output_value % 100)/2 );
        const lightness = Math.min(80, Math.max(30, 50 - (data.depth || 0) * 3 + (data.output_value % 100)/5 ));
        node.color = `hsl(${hue.toFixed(0)}, ${saturation.toFixed(0)}%, ${lightness.toFixed(0)}%)`;

        return { node, isNew };
    }

    function handleSSEMessage(data) {
        if (data.script_id === undefined) return;

        const { node: sourceNode, isNew: isSourceNew } = addOrUpdateNodeFromSSE(data);
        let graphNeedsUpdate = isSourceNew;

        if (data.next_call_id !== null && data.next_call_id !== undefined) {
            const targetNodeData = { 
                script_id: data.next_call_id, 
                depth: (data.depth || 0) + 1, 
                num_total_scripts: N_SCRIPTS_JS 
            };
            const { node: targetNode, isNew: isTargetNew } = addOrUpdateNodeFromSSE(targetNodeData);
            
            const linkExists = graphData.links.some(l => l.source === sourceNode.id && l.target === targetNode.id);
            if (!linkExists) {
                const newLink = {
                    source: sourceNode.id,
                    target: targetNode.id,
                    depth: sourceNode.depth, // Link depth from source
                    value: Math.abs(sourceNode.output_value % 10) / 10 + 0.1, // For particle speed
                    color: `hsla(${(sourceNode.id * 360 / N_SCRIPTS_JS + 180)%360}, 70%, 50%, 0.5)` // Link color
                };
                graphData.links.push(newLink);
                graphNeedsUpdate = true;

                if (Math.random() < particleEmissionRate) {
                    setTimeout(() => Graph.emitParticle(newLink), Math.random() * 500); // Delayed particle
                }
            }
        }
        
        if (graphNeedsUpdate) {
            Graph.graphData(graphData); // Update the graph
        }
        statusElem.textContent = `Processed: Op '${data.op_type}' by #${data.script_id} (Depth: ${data.depth}). Nodes: ${graphData.nodes.length}, Links: ${graphData.links.length}`;
    }

    function startStreaming() {
        if (eventSource && eventSource.readyState !== EventSource.CLOSED) eventSource.close();
        clearGraph();
        Graph.resumeAnimation();

        statusElem.textContent = "Connecting to orchestrator...";
        eventSource = new EventSource('fractal_orchestrator.php');
        startButton.disabled = true;
        stopButton.disabled = false;

        eventSource.onopen = function() {
            console.log("Connection to SSE stream opened.");
            statusElem.textContent = "Data stream connected. Weaving the tapestry...";
        };

        eventSource.onmessage = function(event) {
            try {
                const data = JSON.parse(event.data);
                if (data.status && data.status.includes('finished')) {
                    statusElem.textContent = "Stream finished: " + data.status + ` Total nodes: ${graphData.nodes.length}`;
                    if (eventSource) eventSource.close();
                    startButton.disabled = false;
                    stopButton.disabled = true;
                    return;
                }
                handleSSEMessage(data);
            } catch (e) {
                console.error("Error parsing SSE data:", e, "Data:", event.data);
                statusElem.textContent = "Error parsing incoming data.";
            }
        };

        eventSource.onerror = function(err) {
            console.error("EventSource failed:", err);
            statusElem.textContent = "Stream connection error or server stream ended.";
            if (eventSource) eventSource.close();
            startButton.disabled = false;
            stopButton.disabled = true;
        };
    }

    startButton.addEventListener('click', startStreaming);
    stopButton.addEventListener('click', () => {
        if (eventSource) {
            eventSource.close();
            console.log("EventSource closed by user.");
            statusElem.textContent = "Stream stopped by user.";
        }
        startButton.disabled = false;
        stopButton.disabled = true;
    });
    clearGraphButton.addEventListener('click', clearGraph);
    
    // Initial graph setup & resize handling
    Graph.graphData({ nodes: [], links: [] }); // Start with an empty graph
    function resizeGraphDisplay() {
        Graph.width(graphElem.offsetWidth);
        Graph.height(graphElem.offsetHeight);
    }
    window.addEventListener('resize', resizeGraphDisplay);
    resizeGraphDisplay(); // Initial size
    statusElem.textContent = 'Ready. Click "Start Weaving".';
  </script>
</body>
</html>
§§kilian_html_nowdoc_delimiter§§;

    // Replace placeholders in the Nowdoc template
    $replacements = [
        '%PALETTE_EMOJI%' => $paletteEmoji,
        '%ROCKET_EMOJI%' => $rocketEmojiGlobal,
        '%STOP_EMOJI%' => $stopEmojiGlobal,
        '%SCRIPT_PY_EMOJI%' => $scriptPyEmojiGlobal,
        '%SCRIPT_PHP_EMOJI%' => $scriptPhpEmojiGlobal,
    ];
    $final_html = $html_template;
    foreach ($replacements as $placeholder => $value) {
        $final_html = str_replace($placeholder, $value, $final_html);
    }
    return $final_html;
}

// (The rest of the PHP: getFractalOrchestratorPhpContent, getPythonScriptContent, getStopServerPhpContent, Main Logic)
// ... should be the same as the previous version that successfully ran without PHP parse errors ...
// ... with the PYTHON SYNTAX FIXES from your error log incorporated into getPythonScriptContent ...

function getFractalOrchestratorPhpContent($pythonSubfolderName, $numPythonScripts, $maxCallDepth, $maxRuntime) {
    // Using Nowdoc to avoid PHP parsing issues with internal strings.
    // Placeholders like __PYTHON_SUBFOLDER_NAME__ will be replaced.
    $orchestrator_template = <<<'§§kilian_orchestrator_nowdoc§§'
<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
set_time_limit(0); 

$pythonProjectFolder = "__PYTHON_SUBFOLDER_NAME__"; 
$numPythonScripts = __NUM_PYTHON_SCRIPTS__;
$maxCallDepth = __MAX_CALL_DEPTH__;
$maxOrchestratorRuntime = __MAX_RUNTIME__; 

ob_implicit_flush(true);
$startTime = time();

function send_event_orchestrator($data) { // Renamed to avoid conflict
    echo "data: " . json_encode($data) . "\n\n";
    if (ob_get_level() > 0) ob_flush();
    flush();
}

$queue = [];
for ($i = 0; $i < 7; ++$i) { 
    $start_id = rand(0, $numPythonScripts - 1);
    $start_val = (rand(-2000, 2000) / 100.0); 
    $queue[] = ['id' => $start_id, 'value' => $start_val, 'depth' => 0];
}

$total_events_sent_this_run = 0;
$loop_iterations = 0;

while (!empty($queue)) {
    $loop_iterations++;
    if ((time() - $startTime) >= $maxOrchestratorRuntime) {
        send_event_orchestrator(['status' => 'Orchestrator reached max runtime (' . $maxOrchestratorRuntime . 's). Total events: ' . $total_events_sent_this_run]);
        error_log("Fractal_Orchestrator max runtime. Events: {$total_events_sent_this_run}, Iterations: {$loop_iterations}");
        exit(0);
    }
    if (connection_aborted()) {
        error_log("Client disconnected, stopping fractal_orchestrator. Events: {$total_events_sent_this_run}");
        break;
    }

    $batch_size = min(count($queue), 10); 
    for($b = 0; $b < $batch_size; $b++) {
        if (empty($queue)) break;
        $current_call = array_shift($queue);
        $script_id_num = $current_call['id']; $input_value = $current_call['value']; $depth = $current_call['depth'];
        $script_name = sprintf("script_%03d.py", $script_id_num);
        $script_path = $pythonProjectFolder . DIRECTORY_SEPARATOR . $script_name;

        if (!file_exists($script_path)) {
            send_event_orchestrator(['error' => "Script not found: {$script_path} from " . getcwd(), 'id' => $script_id_num]);
            continue;
        }

        $escaped_script_path = escapeshellarg($script_path);
        $escaped_input_value = escapeshellarg((string)$input_value);
        $escaped_depth = escapeshellarg((string)$depth);
        $escaped_num_scripts = escapeshellarg((string)$numPythonScripts);
        $escaped_max_depth = escapeshellarg((string)$maxCallDepth);

        $python_executable = 'python3';
        @exec('python3 --version 2>&1', $py3_output_dummy, $py3_ret_dummy);
        if ($py3_ret_dummy !== 0) $python_executable = 'python';
        
        $command = sprintf("%s %s %s %s %s %s", $python_executable, $escaped_script_path, $escaped_input_value, $escaped_depth, $escaped_num_scripts, $escaped_max_depth);
        
        $output_lines = []; $return_var = null;
        exec($command . ' 2>&1', $output_lines, $return_var);
        $output_str = implode("\n", $output_lines);

        if ($return_var === 0) {
            foreach ($output_lines as $line) {
                $json_data = json_decode($line, true);
                if (is_array($json_data) && isset($json_data['script_id'])) { 
                    send_event_orchestrator($json_data);
                    $total_events_sent_this_run++;
                    if (isset($json_data['next_call_id']) && $json_data['next_call_id'] !== null && $json_data['depth'] < ($maxCallDepth -1) && is_numeric($json_data['next_call_id']) && $json_data['next_call_id'] >= 0 && $json_data['next_call_id'] < $numPythonScripts) {
                        $next_val = $json_data['output_value'];
                        if (abs($next_val - $input_value) < 0.01 || abs($next_val) < 0.01 ) {
                            $next_val += (rand(-100,100)/50.0) * ($depth + 1); 
                            if (abs($next_val) < 0.01 && $next_val != 0) $next_val = ($next_val > 0 ? 0.1 : -0.1);
                        }
                        if(count($queue) < 250) { 
                           $queue[] = ['id' => (int)$json_data['next_call_id'], 'value' => $next_val, 'depth' => $json_data['depth'] + 1];
                        }
                    }
                }
            }
        } else {
            send_event_orchestrator(['error' => "Script {$script_name} failed. Ret: {$return_var}", 'details' => $output_str, 'id' => $script_id_num]);
        }
    } 
    usleep(5000); 
}
send_event_orchestrator(['status' => 'Orchestration loop finished or queue empty. Total events: ' . $total_events_sent_this_run]);
error_log("Fractal_Orchestrator loop finished. Total events: {$total_events_sent_this_run}, Iterations: {$loop_iterations}");
?>
§§kilian_orchestrator_nowdoc§§;

    $replacements = [
        '__PYTHON_SUBFOLDER_NAME__' => $pythonSubfolderName,
        '__NUM_PYTHON_SCRIPTS__' => $numPythonScripts,
        '__MAX_CALL_DEPTH__' => $maxCallDepth,
        '__MAX_RUNTIME__' => $maxRuntime,
    ];
    $final_code = $orchestrator_template;
    foreach($replacements as $placeholder => $value) {
        $final_code = str_replace($placeholder, $value, $final_code);
    }
    return $final_code;
}

function getPythonScriptContent($scriptId, $numTotalScripts, $maxCallDepth) {
    $python_template = <<<'§§kilian_python_nowdoc§§'
import sys, json, math, random
# Script ID __SCRIPT_ID__: Op '__OP_NAME__', Modifiers: __MODIFIER_DISPLAY__
op_name = "__OP_NAME__" 

# Define the perform_operation function with PHP-generated content
__PYTHON_PERFORM_OP_FUNC_STR__

if __name__ == "__main__":
    if len(sys.argv) < 5: print(json.dumps({"error": "Insufficient args", "script_id": __SCRIPT_ID__})); sys.exit(1)
    input_value, current_depth, num_total_scripts, max_allowed_depth = float(sys.argv[1]), int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])

    # These definitions will create global variables in Python for modifiers
__MODIFIER_DEFINITIONS__
    prev_val_placeholder_py = __PREV_VAL_PLACEHOLDER_CODE__ 
    
    python_modifiers_dict = __MODIFIER_VALUES_DICT__
    
    output_value = perform_operation(input_value, current_depth, __SCRIPT_ID__, prev_val_placeholder_py, **python_modifiers_dict)
    output_value = max(-1e7, min(1e7, output_value)) 
    if math.isnan(output_value) or math.isinf(output_value): output_value = random.uniform(-200.0, 200.0)

    next_call_id_py_str = "__NEXT_SCRIPT_ID_EXPR__" 
    
    next_call_id_final = None 
    # Use the Python boolean literal that was prepared in PHP
    if next_call_id_py_str != 'None' and current_depth < (max_allowed_depth -1) and __PYTHON_WILL_CALL_NEXT_LITERAL__: # This placeholder is key
        try:
            parsed_next_id = int(next_call_id_py_str)
            if 0 <= parsed_next_id < num_total_scripts: 
                 next_call_id_final = parsed_next_id
            else: 
                 next_call_id_final = random.randint(0, num_total_scripts - 1)
        except (ValueError, TypeError): 
            next_call_id_final = random.randint(0, num_total_scripts - 1) 
    
    print(json.dumps({
        "script_id":__SCRIPT_ID__, "input_value":input_value, "op_type":op_name, 
        "modifiers_used": python_modifiers_dict, "output_value":output_value, 
        "depth":current_depth, "next_call_id":next_call_id_final, 
        "num_total_scripts":num_total_scripts
    }))
§§kilian_python_nowdoc§§;

    // PHP logic to prepare Python code parts
    $ops = [
        ['name' => 'clifford_attractor_x', 'lambda' => 'math.sin(modifiers.get("modifier_a", 0.0) * prev_val_placeholder) + modifiers.get("modifier_c", 0.0) * math.cos(modifiers.get("modifier_a", 0.0) * val)'],
        ['name' => 'clifford_attractor_y', 'lambda' => 'math.sin(modifiers.get("modifier_b", 0.0) * val) + modifiers.get("modifier_d", 0.0) * math.cos(modifiers.get("modifier_b", 0.0) * prev_val_placeholder)'],
        ['name' => 'logistic_growth', 'lambda' => 'modifiers.get("modifier_r", 3.0) * val * (1.0 - val / (modifiers.get("modifier_k", 1.0) if modifiers.get("modifier_k", 1.0) != 0.0 else 1.0))'],
        ['name' => 'lyapunov_exponent_approx', 
         'lambda' => 'val + math.log(abs(modifiers.get("modifier_r", 3.57) - 2.0 * modifiers.get("modifier_r", 3.57) * _internal_prev_norm)) if abs(modifiers.get("modifier_r", 3.57) - 2.0 * modifiers.get("modifier_r", 3.57) * _internal_prev_norm) > 1e-9 else val',
        ],
    ];
    $ops[0]['custom_modifiers'] = ['a' => [-2.0, 2.0, 0.01], 'c' => [-2.0, 2.0, 0.01]];
    $ops[1]['custom_modifiers'] = ['b' => [-2.0, 2.0, 0.01], 'd' => [-2.0, 2.0, 0.01]];
    $ops[2]['custom_modifiers'] = ['r' => [2.8, 4.0, 0.001], 'k' => [10.0, 100.0, 1.0]];
    $ops[3]['custom_modifiers'] = ['r' => [3.5, 4.0, 0.001]]; // For Lyapunov

    $chosen_op_data = $ops[array_rand($ops)];
    $op_name_php = $chosen_op_data['name']; 
    $op_lambda_template_php = $chosen_op_data['lambda'];
    $modifier_definitions_for_py = ""; 
    $modifier_values_dict_content_for_py = ""; 
    $modifier_display_values_for_py = "";

    if (isset($chosen_op_data['custom_modifiers'])) {
        $temp_mod_dict_parts = [];
        foreach($chosen_op_data['custom_modifiers'] as $mod_name => $range) {
            $min_r = (float)$range[0]; $max_r = (float)$range[1];
            $step_r = isset($range[2]) ? (float)$range[2] : (($max_r - $min_r > 1.0) ? 1.0 : 0.01);
            if ($step_r == 0) $step_r = ($max_r - $min_r > 1.0) ? 0.1 : 0.001;
            $rand_min_scaled = (int)floor($min_r / $step_r);
            $rand_max_scaled = (int)floor($max_r / $step_r);
            if ($rand_min_scaled > $rand_max_scaled) list($rand_min_scaled, $rand_max_scaled) = [$rand_max_scaled, $rand_min_scaled];
            $mod_val = ($rand_min_scaled == $rand_max_scaled) ? $min_r : round(rand($rand_min_scaled, $rand_max_scaled) * $step_r, 4);
            $modifier_definitions_for_py .= "    {$mod_name} = float({$mod_val}) # PHP generated\n";
            $temp_mod_dict_parts[] = "\"{$mod_name}\": {$mod_name}";
            $modifier_display_values_for_py .= "{$mod_name}={$mod_val}, ";
        }
        $modifier_values_dict_content_for_py = implode(", ", $temp_mod_dict_parts);
        $modifier_display_values_for_py = rtrim($modifier_display_values_for_py, ", ");
    } else if (isset($chosen_op_data['mod_range'])) { 
        // Fallback for simpler ops if any are added without custom_modifiers but with mod_range
        $min_r_single = (float)$chosen_op_data['mod_range'][0]; $max_r_single = (float)$chosen_op_data['mod_range'][1];
        $modifier = (float)rand((int)$min_r_single, (int)$max_r_single);
        $modifier_definitions_for_py = "    modifier = float({$modifier})\n";
        $modifier_values_dict_content_for_py = "\"modifier\": modifier";
        $modifier_display_values_for_py = "modifier={$modifier}";
    }
    $modifier_values_dict_py_final = "{".$modifier_values_dict_content_for_py."}";

    $will_call_next_php = (rand(1, 100) <= 70);
    $python_will_call_next_literal_php = ($will_call_next_php ? 'True' : 'False'); 
    $next_script_id_expr_php_val = 'None'; 
    if ($will_call_next_php) {
        $offset_direction = rand(0,1) == 0 ? -1 : 1; $offset_amount = rand(1, max(1, (int)($numTotalScripts / 10)));
        $next_id_raw = ($scriptId + ($offset_direction * $offset_amount) + $numTotalScripts) % $numTotalScripts;
        $next_script_id_expr_php_val = (string)$next_id_raw;
    }
    $prev_val_placeholder_code_py_val = 'random.uniform(-0.5, 0.5)'; 
    if(!empty($modifier_definitions_for_py) && substr($modifier_definitions_for_py, -strlen("\n")) !== "\n") {
        $modifier_definitions_for_py .= "\n";
    }

    $python_perform_op_func_str_php = "def perform_operation(val, current_depth, script_id, prev_val_placeholder, **modifiers):\n";
    $python_perform_op_func_str_php .= "    for key, value in modifiers.items(): locals()[key] = float(value)\n";
    $python_perform_op_func_str_php .= "    _internal_prev_norm = (prev_val_placeholder % 1.0 if prev_val_placeholder is not None else 0.5)\n";
    $python_perform_op_func_str_php .= "    current_val_for_map = val\n";
    $python_perform_op_func_str_php .= "    if op_name == \"logistic_growth\":\n"; 
    $python_perform_op_func_str_php .= "         k_val = modifiers.get(\"modifier_k\", 1.0)\n";
    $python_perform_op_func_str_php .= "         if k_val == 0: k_val = 1.0\n";
    $python_perform_op_func_str_php .= "         current_val_for_map = (abs(val) % k_val) / k_val\n";
    $python_perform_op_func_str_php .= "         val = current_val_for_map\n";
    $python_perform_op_func_str_php .= "    elif op_name.startswith(\"ikeda_map\"):\n"; // Not currently in simplified ops
    $python_perform_op_func_str_php .= "        tn = 0.4 - 6.0 / (1.0 + val**2 + prev_val_placeholder**2)\n";
    $python_perform_op_func_str_php .= "    try:\n";
    $python_perform_op_func_str_php .= "        return " . $op_lambda_template_php . "\n";
    $python_perform_op_func_str_php .= "    except Exception as e:\n";
    $python_perform_op_func_str_php .= "        return val + random.uniform(-1.0, 1.0)\n";

    $replacements = [
        '__SCRIPT_ID__' => $scriptId,
        '__OP_NAME__' => $op_name_php,
        '__MODIFIER_DISPLAY__' => $modifier_display_values_for_py,
        '__PYTHON_PERFORM_OP_FUNC_STR__' => $python_perform_op_func_str_php,
        '__MODIFIER_DEFINITIONS__' => $modifier_definitions_for_py,
        '__PREV_VAL_PLACEHOLDER_CODE__' => $prev_val_placeholder_code_py_val,
        '__MODIFIER_VALUES_DICT__' => $modifier_values_dict_py_final,
        '__NEXT_SCRIPT_ID_EXPR__' => $next_script_id_expr_php_val,
        '__PYTHON_WILL_CALL_NEXT_LITERAL__' => $python_will_call_next_literal_php,
    ];
    $final_python_code = $python_template;
    foreach ($replacements as $placeholder => $value) {
        $final_python_code = str_replace($placeholder, (string)$value, $final_python_code);
    }
    return $final_python_code;
}


function getStopServerPhpContent($outer_serverHost, $outer_portRangeStart, $outer_portRangeEnd, $outer_serverProcessMarker, $outer_routerScriptNameForGrep) {
    global $e_info, $e_ok, $e_warn, $e_stop; 
    $ports_to_check_str_array = [];
    for ($p = $outer_portRangeStart; $p <= $outer_portRangeEnd; $p++) {
        $ports_to_check_str_array[] = (string)$p;
    }
    $ports_to_check_json_for_stop_script = json_encode($ports_to_check_str_array);
    $stop_script_template = <<<'PHP_STOP_SCRIPT_NOWDOC'
<?php
$_E_INFO_ = '__PHP_E_INFO__'; $_E_OK_ = '__PHP_E_OK__'; 
$_E_WARN_ = '__PHP_E_WARN__'; $_E_STOP_GLYPH_ = '__PHP_E_STOP_GLYPH__';
echo "\$_E_INFO_ Attempting to stop PHP server(s)...\\n";
\$php_serverHost_val = '__PHP_EMBEDDED_SERVER_HOST__'; 
\$php_portsToCheck_val = json_decode('__PHP_EMBEDDED_PORTS_JSON__', true);
\$php_processMarker_val = '__PHP_EMBEDDED_MARKER__'; 
\$php_routerScriptName_val = '__PHP_EMBEDDED_ROUTER_NAME__'; 
\$killedSomething = false;
function findAndKillProcessForStopScript(\$port_to_check, \$host_param, \$router_script_name_param) { 
    global \$_E_OK_, \$_E_WARN_, \$_E_INFO_; 
    if (stristr(PHP_OS, 'WIN')) {
        echo "\$_E_WARN_ On Windows, please manually stop PHP server on port {\$port_to_check} for '{\$router_script_name_param}'.\\n";
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
        '__PHP_E_INFO__' => $e_info, '__PHP_E_OK__' => $e_ok,
        '__PHP_E_WARN__' => $e_warn, '__PHP_E_STOP_GLYPH__' => $e_stop,
        '__PHP_EMBEDDED_SERVER_HOST__' => $outer_serverHost, // No addslashes needed here
        '__PHP_EMBEDDED_PORTS_JSON__' => $ports_to_check_json_for_stop_script, // Already JSON string
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
$potentialOldProjectFolder = $mainProjectFolderName; 
$potentialOldStopScript = $potentialOldProjectFolder . DIRECTORY_SEPARATOR . $serverScriptsSubfolderName . DIRECTORY_SEPARATOR . "stop_fractal_server.php";

if (is_dir($potentialOldProjectFolder)) {
    echo "$e_broom Project folder '$potentialOldProjectFolder' exists. Attempting to stop any running server and clear contents...\n";
    if (file_exists($potentialOldStopScript)) {
        $original_cwd = getcwd();
        $stopScriptDir = dirname($potentialOldStopScript);
        if (is_dir($stopScriptDir) && chdir($stopScriptDir)) {
            echo "$e_info Executing: php stop_fractal_server.php (in $stopScriptDir)\n";
            system("php stop_fractal_server.php"); 
            chdir($original_cwd);
            echo "$e_info Server stop attempt finished.\n";
            if (PHP_OS_FAMILY !== 'Windows') sleep(1);
        } else {
            echo "$e_warn Could not change to '$stopScriptDir' for old stop script. Skipping auto-stop.\n";
        }
    } else {
         echo "$e_info No existing stop script found at '$potentialOldStopScript' to run for cleanup.\n";
    }
    function deleteDirectoryContentsRecursive($dir) {
        if (!is_dir($dir)) return;
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                deleteDirectoryContentsRecursive($path);
                @rmdir($path); 
            } else {
                @unlink($path);
            }
        }
    }
    $subfolders_to_clear = [$pythonSubfolderName, $serverScriptsSubfolderName];
    $files_to_clear = ["index.html", "fractal_orchestrator.php", "___fractal_server_router.php", "php_server.log"];
    foreach($subfolders_to_clear as $subfolder) {
        $path_to_clear = $mainProjectFolderName . DIRECTORY_SEPARATOR . $subfolder;
        if (is_dir($path_to_clear)) deleteDirectoryContentsRecursive($path_to_clear);
        @rmdir($path_to_clear); 
    }
    foreach($files_to_clear as $file) {
        $path_to_clear = $mainProjectFolderName . DIRECTORY_SEPARATOR . $file;
        if (file_exists($path_to_clear)) @unlink($path_to_clear);
    }
    echo "$e_ok Contents of '$mainProjectFolderName' (known subfolders/files) cleared for fresh generation.\n";
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
$stopCommandInstruction = "php " . escapeshellarg($projectFullPath . DIRECTORY_SEPARATOR . $serverScriptsSubfolderName . DIRECTORY_SEPARATOR . $stopServerScriptName);
$serverLogFile = $projectFullPath . DIRECTORY_SEPARATOR . "php_server.log";


echo "$e_info $e_terminal TO RUN THE FRACTAL ART VISUALIZATION:\n";
echo "1. This script will ATTEMPT to start the server in the background.\n";
echo "   $e_log Server output (if any) will be logged to: " . escapeshellarg(basename($projectFullPath) . DIRECTORY_SEPARATOR ."php_server.log") . "\n";
echo "2. $e_link If successful, your browser should open to: $urlToOpen\n";
echo "3. Click 'Start Visualization' $e_rocket on the webpage (if using the SSE version of HTML).\n";
echo "   For the 3D Force Graph version, it should load data via SSE when you click 'Start Weaving'.\n";
echo "$e_info $e_timer Orchestrator default runtime: ~$defaultMaxOrchestratorRuntime seconds.\n";
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