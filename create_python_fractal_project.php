<?php

// --- Configuration ---
date_default_timezone_set('UTC');
$mainProjectFolderNameBase = "🎨_MyFractalDream_" . date('Ymd');
$mainProjectFolderName = $mainProjectFolderNameBase . "_" . date('His');
$pythonSubfolderName = "python_fractal_weavers";
$numPythonScripts = 303;
$maxCallDepth = 5;
$defaultMaxOrchestratorRuntime = 120;

$serverHost = "localhost";
$startPort = 10000;
$endPort = 10010;
$maxPortRetries = 5;

// Emojis
$e_sparkle = "✨"; $e_folder = "📁"; $e_script_py = "🐍"; $e_script_php = "🐘";
$e_html = "📄"; $e_rocket = "🚀"; $e_palette = "🎨"; $e_gear = "⚙️";
$e_warn = "⚠️"; $e_info = "ℹ️"; $e_ok = "✅"; $e_party = "🎉";
$e_link = "🔗"; $e_terminal = "💻"; $e_eyes = "👀"; $e_stop = "🛑"; $e_timer = "⏱️";

$serverProcessMarker = "fractal_server_process_marker_" . bin2hex(random_bytes(6));

function isPortAvailable($host, $port, $timeout = 1) {
    $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if ($socket) {
        fclose($socket);
        return false;
    }
    return true;
}

function getIndexHtmlContent($paletteEmoji) {
    // Using $GLOBALS to access global emojis inside heredoc if needed, or pass as param
    $rocketEmoji = $GLOBALS['e_rocket'];
    $stopEmoji = $GLOBALS['e_stop'];
    $scriptPyEmoji = $GLOBALS['e_script_py'];
    $scriptPhpEmoji = $GLOBALS['e_script_php'];

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Python Fractal Math Art $paletteEmoji</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
    <style>
        body { margin: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; background-color: #222; color: #eee; }
        canvas { border: 1px solid #444; background-color: #111; max-width: 90vw; max-height: 80vh; box-shadow: 0 0 20px rgba(0,200,255,0.3); }
        header, footer { text-align: center; margin: 1em; }
        .controls { margin-bottom: 1em; padding: 0.5em; background-color: rgba(255,255,255,0.05); border-radius: 5px;}
        button { margin: 0.2em; }
        .container { padding: 1em; }
    </style>
</head>
<body>
    <header>
        <h1>Python Fractal Math Art $paletteEmoji</h1>
        <p>Visualizing chained math operations from Python scripts.</p>
    </header>
    <main class="container">
        <div class="controls">
            <button id="startButton">Start Visualization $rocketEmoji</button>
            <button id="stopButton" disabled>Stop Stream $stopEmoji</button>
            <button id="clearButton">Clear Canvas 🧹</button>
        </div>
        <canvas id="fractalCanvas" width="800" height="600"></canvas>
    </main>
    <footer>
        <p>Powered by Python $scriptPyEmoji, PHP $scriptPhpEmoji, and Pico.css</p>
    </footer>
    <script>
        const canvas = document.getElementById('fractalCanvas');
        const ctx = canvas.getContext('2d');
        const startButton = document.getElementById('startButton');
        const stopButton = document.getElementById('stopButton');
        const clearButton = document.getElementById('clearButton');
        let eventSource = null;
        let lastX = canvas.width / 2;
        let lastY = canvas.height / 2;
        let hue = Math.random() * 360;
        let pointCounter = 0;

        function clearCanvas() {
            ctx.fillStyle = '#111111';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            lastX = canvas.width / 2; 
            lastY = canvas.height / 2;
            hue = Math.random() * 360;
            pointCounter = 0;
        }
        clearCanvas(); 

        startButton.addEventListener('click', () => {
            if (eventSource) eventSource.close(); 
            clearCanvas();
            eventSource = new EventSource('fractal_orchestrator.php'); 
            startButton.disabled = true;
            stopButton.disabled = false;

            eventSource.onmessage = function(event) {
                try {
                    const data = JSON.parse(event.data);
                    if (data.status && data.status.includes('finished')) {
                        console.log("Orchestrator reported:", data.status);
                        if (eventSource) eventSource.close();
                        startButton.disabled = false;
                        stopButton.disabled = true;
                        return;
                    }
                    const value = parseFloat(data.output_value);
                    const depth = parseInt(data.depth);
                    const scriptId = parseInt(data.script_id);
                    let normValue = (value % 200 - 100) / 100;
                    if (isNaN(normValue)) normValue = 0;
                    const angle = (normValue * Math.PI) + (scriptId % 360 * Math.PI / 180) + (depth * 0.1);
                    const distance = 5 + (Math.abs(normValue) * 15) + (depth * 3) + (Math.sin(pointCounter * 0.05) * 5); 
                    let newX = lastX + Math.cos(angle) * distance;
                    let newY = lastY + Math.sin(angle) * distance;
                    ctx.beginPath(); ctx.moveTo(lastX, lastY);
                    hue = (hue + Math.abs(scriptId % 10 - 5) + (normValue * 10) ) % 360;
                    const saturation = Math.min(100, 50 + (depth * 10) + Math.abs(normValue)*30); 
                    const lightness = Math.min(85, Math.max(35, 60 + (normValue * 20))); 
                    ctx.strokeStyle = `hsla(\${hue}, \${saturation}%, \${lightness}%, 0.6)`;
                    ctx.lineWidth = Math.max(0.2, 0.5 + depth * 0.2 + Math.abs(normValue) * 0.5); 
                    ctx.lineTo(newX, newY); ctx.stroke();
                    ctx.beginPath(); 
                    ctx.arc(newX, newY, Math.max(0.5, 1 + depth * 0.1), 0, Math.PI * 2);
                    ctx.fillStyle = `hsla(\${(hue + 30) % 360}, \${saturation}%, \${Math.min(95, lightness + 10)}%, 0.8)`;
                    ctx.fill();
                    if (newX > canvas.width || newX < 0) newX = canvas.width / (1 + Math.random()); else if (newX < 0) newX = canvas.width / (1 + Math.random()) ; // Reset with jitter
                    if (newY > canvas.height || newY < 0) newY = canvas.height / (1 + Math.random()); else if (newY < 0) newY = canvas.height / (1 + Math.random());
                    lastX = newX; lastY = newY;
                    pointCounter++;
                } catch (e) { console.error("Error parsing/drawing:", e, event.data); }
            };
            eventSource.onerror = function(err) {
                console.error("EventSource failed:", err);
                if(eventSource) eventSource.close();
                startButton.disabled = false; stopButton.disabled = true;
                 if (err.target && err.target.readyState === EventSource.CLOSED) {
                    alert("Connection to server lost or stream ended. Please ensure 'fractal_orchestrator.php' is running via the PHP server. It might have timed out.");
                }
            };
        });
        stopButton.addEventListener('click', () => {
            if (eventSource) { eventSource.close(); console.log("EventSource closed."); }
            startButton.disabled = false; stopButton.disabled = true;
        });
        clearButton.addEventListener('click', clearCanvas);
    </script>
</body>
</html>
HTML;
}

function getFractalOrchestratorPhpContent($pythonSubfolderName, $numPythonScripts, $maxCallDepth, $maxRuntime) {
    return <<<PHP
<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
set_time_limit(0); 

\$pythonProjectFolder = "$pythonSubfolderName"; 
\$numPythonScripts = $numPythonScripts;
\$maxCallDepth = $maxCallDepth;
\$maxOrchestratorRuntime = $maxRuntime; 

ob_implicit_flush(true);
\$startTime = time();

function send_event(\$data) {
    echo "data: " . json_encode(\$data) . "\\n\\n";
    if (ob_get_level() > 0) ob_flush();
    flush();
}

\$queue = [];
for (\$i = 0; \$i < 5; ++\$i) {
    \$queue[] = ['id' => rand(0, \$numPythonScripts - 1), 'value' => (rand(-1000, 1000) / 100.0), 'depth' => 0];
}

\$processed_count = 0;
\$current_burst_event_limit = 700; 

while (!empty(\$queue)) { // Primary loop condition
    if ((time() - \$startTime) >= \$maxOrchestratorRuntime) {
        send_event(['status' => 'Orchestrator reached max runtime (' . \$maxOrchestratorRuntime . 's). Processed: ' . \$processed_count]);
        error_log("Fractal_Orchestrator reached max runtime. Processed: {\$processed_count}");
        exit(0);
    }
    if (connection_aborted()) {
        error_log("Client disconnected, stopping fractal_orchestrator.");
        break;
    }
     if (\$processed_count >= \$current_burst_event_limit && (time() - \$startTime) < \$maxOrchestratorRuntime) {
        // If we hit burst limit but still have time, increase limit for next burst.
        // This is to prevent extremely long single loops if Python scripts are very fast.
        \$current_burst_event_limit += 100; // Continue with a larger burst
    }
    if (\$processed_count >= \$current_burst_event_limit && (time() - \$startTime) >= \$maxOrchestratorRuntime) {
        // If burst limit AND time limit hit simultaneously.
        send_event(['status' => 'Orchestrator loop finishing due to event burst and time limit. Processed: ' . \$processed_count]);
        error_log("Fractal_Orchestrator loop finishing. Processed: {\$processed_count}");
        break;
    }


    \$current_call = array_shift(\$queue);
    \$script_id_num = \$current_call['id']; \$input_value = \$current_call['value']; \$depth = \$current_call['depth'];
    \$script_name = sprintf("script_%03d.py", \$script_id_num);
    \$script_path = \$pythonProjectFolder . DIRECTORY_SEPARATOR . \$script_name;

    if (!file_exists(\$script_path)) {
        send_event(['error' => "Script not found: {\$script_path} from " . getcwd()]);
        continue;
    }

    \$escaped_script_path = escapeshellarg(\$script_path);
    \$escaped_input_value = escapeshellarg((string)\$input_value);
    \$escaped_depth = escapeshellarg((string)\$depth);
    \$escaped_num_scripts = escapeshellarg((string)\$numPythonScripts);
    \$escaped_max_depth = escapeshellarg((string)\$maxCallDepth);

    \$python_executable = 'python3';
    @exec('python3 --version 2>&1', \$py3_output_dummy, \$py3_ret_dummy);
    if (\$py3_ret_dummy !== 0) \$python_executable = 'python';
    
    \$command = sprintf("%s %s %s %s %s %s", \$python_executable, \$escaped_script_path, \$escaped_input_value, \$escaped_depth, \$escaped_num_scripts, \$escaped_max_depth);
    
    \$output_lines = []; \$return_var = null;
    exec(\$command . ' 2>&1', \$output_lines, \$return_var);
    \$output_str = implode("\\n", \$output_lines);

    if (\$return_var === 0) {
        foreach (\$output_lines as \$line) {
            \$json_data = json_decode(\$line, true);
            if (is_array(\$json_data)) {
                send_event(\$json_data);
                if (isset(\$json_data['next_call_id']) && \$json_data['next_call_id'] !== null && \$json_data['depth'] < \$maxCallDepth && is_numeric(\$json_data['next_call_id']) && \$json_data['next_call_id'] >= 0 && \$json_data['next_call_id'] < \$numPythonScripts) {
                    \$next_val = \$json_data['output_value'];
                    if (abs(\$next_val - \$input_value) < 0.1 && rand(0,2) == 0) \$next_val += (rand(-50,50)/100.0);
                    \$queue[] = ['id' => (int)\$json_data['next_call_id'], 'value' => \$next_val, 'depth' => \$json_data['depth'] + 1];
                }
                 \$processed_count++;
            }
        }
    } else {
        send_event(['error' => "Script {\$script_name} failed. Ret: {\$return_var}", 'details' => \$output_str]);
    }
    usleep(20000); 
}
send_event(['status' => 'Orchestration loop finished. Processed total: ' . \$processed_count]);
error_log("Fractal_Orchestrator loop finished. Processed events: {\$processed_count}");
?>
PHP;
}

function getPythonScriptContent($scriptId, $numTotalScripts, $maxCallDepth) {
    $ops = [
        ['name' => 'add', 'lambda' => 'val + modifier', 'mod_range' => [-7, 7]],
        ['name' => 'subtract', 'lambda' => 'val - modifier', 'mod_range' => [-7, 7]],
        ['name' => 'multiply', 'lambda' => 'val * modifier', 'mod_range' => [0.3, 2.5, 0.05]],
        ['name' => 'divide', 'lambda' => 'val / modifier if modifier != 0 else val + random.uniform(0.01, 0.1)', 'mod_range' => [0.3, 2.5, 0.05]],
        ['name' => 'sine_transform', 'lambda' => 'math.sin(val * modifier_angle) * modifier_amp', 'mod_range' => [], 'custom_modifiers' => ['angle' => [0.1, (float)(2 * M_PI), 0.1], 'amp' => [0.5, 10.0, 0.1]]], // Use M_PI for PHP's float range
        ['name' => 'cosine_transform', 'lambda' => 'math.cos(val * modifier_angle) * modifier_amp', 'mod_range' => [], 'custom_modifiers' => ['angle' => [0.1, (float)(2 * M_PI), 0.1], 'amp' => [0.5, 10.0, 0.1]]],
        ['name' => 'spiral_step_x', 'lambda' => 'val + math.cos(current_depth * modifier_angle_rate + script_id * 0.1) * modifier_step_size', 'mod_range' => [], 'custom_modifiers' => ['angle_rate' => [0.05, 0.5, 0.01], 'step_size' => [0.5, 5.0, 0.1]]],
        ['name' => 'spiral_step_y', 'lambda' => 'val + math.sin(current_depth * modifier_angle_rate + script_id * 0.1) * modifier_step_size', 'mod_range' => [], 'custom_modifiers' => ['angle_rate' => [0.05, 0.5, 0.01], 'step_size' => [0.5, 5.0, 0.1]]],
        ['name' => 'power_clip', 'lambda' => 'max(-50.0, min(50.0, math.copysign(pow(abs(val), modifier), val))) if val != 0 else 0.0', 'mod_range' => [0.4, 1.8, 0.05]],
        ['name' => 'modulo_chaos', 'lambda' => '(val * modifier_factor + modifier_add) % modifier_mod if modifier_mod != 0 else val', 'mod_range' => [], 'custom_modifiers' => ['factor' => [0.5, 2.5, 0.1], 'add' => [-5.0, 5.0, 0.1], 'mod' => [5.0, 20.0, 0.1]]],
        ['name' => 'logistic_map', 'lambda' => 'modifier_r * val * (1.0 - val / modifier_limit if modifier_limit !=0 else 1.0-val)', 'mod_range' => [], 'custom_modifiers' => ['r' => [3.5, 4.0, 0.005], 'limit' => [1.0,1.0, 0.01]]], // Ensure limit is float for consistency
        ['name' => 'henon_map_x', 'lambda' => '1.0 - modifier_a * val**2 + prev_val_placeholder', 'mod_range' => [], 'custom_modifiers' => ['a' => [1.0, 1.4, 0.01]]],
    ];
    $chosen_op_data = $ops[array_rand($ops)];
    $op_name = $chosen_op_data['name']; $op_lambda = $chosen_op_data['lambda'];
    $modifier_definitions = ""; $modifier_values_dict_content = ""; $modifier_display_values = "";

    if (isset($chosen_op_data['custom_modifiers'])) {
        $temp_mod_dict_parts = [];
        foreach($chosen_op_data['custom_modifiers'] as $mod_name => $range) {
            // Ensure range elements are float for calculations if step is present
            $min_r = (float)$range[0];
            $max_r = (float)$range[1]; // This was the '2*math.pi' string, now M_PI float
            $step_r = isset($range[2]) ? (float)$range[2] : 1.0;

            if ($step_r == 0) $step_r = 0.1; // Avoid division by zero if step is 0 by mistake

            // Explicitly cast to int for rand() arguments after scaling
            $rand_min_scaled = (int)round($min_r / $step_r);
            $rand_max_scaled = (int)round($max_r / $step_r);
            
            if ($rand_min_scaled > $rand_max_scaled) $rand_min_scaled = $rand_max_scaled; // Ensure min <= max

            $mod_val = round(rand($rand_min_scaled, $rand_max_scaled) * $step_r, 3);

            $modifier_definitions .= "    modifier_{$mod_name} = {$mod_val}\n";
            $temp_mod_dict_parts[] = "\"modifier_{$mod_name}\": modifier_{$mod_name}";
            $modifier_display_values .= "{$mod_name}={$mod_val}, ";
        }
        $modifier_values_dict_content = implode(", ", $temp_mod_dict_parts);
        $modifier_display_values = rtrim($modifier_display_values, ", ");
    } else { // Original single modifier logic
        $min_r_single = (float)$chosen_op_data['mod_range'][0];
        $max_r_single = (float)$chosen_op_data['mod_range'][1];
        
        if (isset($chosen_op_data['mod_range'][2])) { // Float range with step
            $step_r_single = (float)$chosen_op_data['mod_range'][2];
            if ($step_r_single == 0) $step_r_single = 0.1;

            $rand_min_s_scaled = (int)round($min_r_single / $step_r_single);
            $rand_max_s_scaled = (int)round($max_r_single / $step_r_single);
            if ($rand_min_s_scaled > $rand_max_s_scaled) $rand_min_s_scaled = $rand_max_s_scaled;

            $modifier = round(rand($rand_min_s_scaled, $rand_max_s_scaled) * $step_r_single, 2);
        } else { // Int range
            $modifier = rand((int)$min_r_single, (int)$max_r_single);
        }
        
        if ($modifier == 0 && ($op_name == 'divide')) $modifier = (rand(0,1)==0 ? 0.1 : -0.1);
        $modifier_definitions = "    modifier = {$modifier}";
        $modifier_values_dict_content = "\"modifier\": modifier";
        $modifier_display_values = "modifier={$modifier}";
    }
    $modifier_values_dict = "{".$modifier_values_dict_content."}";

    $will_call_next = (rand(1, 100) <= 70); 
    $next_script_id_expr = 'None'; // Default to string 'None' for Python
    if ($will_call_next) {
        $offset_direction = rand(0,1) == 0 ? -1 : 1; $offset_amount = rand(1, max(1, (int)($numTotalScripts / 10)));
        $next_id_raw = ($scriptId + ($offset_direction * $offset_amount)) % $numTotalScripts;
        if ($next_id_raw < 0) $next_id_raw += $numTotalScripts;
        $next_script_id_expr = (string)$next_id_raw; // Python will get this as a string, then int() it
    }
    $prev_val_placeholder_code = 'random.uniform(-0.1, 0.1) if op_name == "henon_map_x" else 0.0'; // Pass op_name to python global scope
    if(!empty($modifier_definitions) && substr($modifier_definitions, -strlen("\n")) !== "\n") {
        $modifier_definitions .= "\n";
    }

    return <<<PYTHON
import sys, json, math, random
# Script ID $scriptId: Op '$op_name', Modifiers: $modifier_display_values
op_name = "$op_name" # Make op_name available in Python global scope

def perform_operation(val, current_depth, script_id, **modifiers):
    for key, value in modifiers.items(): globals()[key] = value # Make individual modifiers global
    prev_val_placeholder = $prev_val_placeholder_code 
    try: 
        if op_name == "logistic_map": 
             limit = modifiers.get("modifier_limit", 1.0)
             if limit == 0: limit = 1.0
             val_norm = abs(val) 
             # val must be in [0,1] for standard logistic map, here we map it to [0,1] based on limit
             val_for_map = (val_norm % limit) / limit if limit != 0.0 else val_norm % 1.0
             # The lambda for logistic_map will use 'val', so we reassign it here for that specific op
             val = val_for_map 
        elif op_name == "henon_map_x":
             pass

        return $op_lambda
    except Exception as e: 
        # import traceback
        # print(f"Error in op {op_name} (ID {script_id}): {e}\\n{traceback.format_exc()}", file=sys.stderr)
        return val + random.uniform(-1.0, 1.0) 

if __name__ == "__main__":
    if len(sys.argv) < 5: print(json.dumps({"error": "Insufficient args", "script_id": $scriptId})); sys.exit(1)
    input_value, current_depth, num_total_scripts, max_allowed_depth = float(sys.argv[1]), int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])
    
$modifier_definitions
    
    output_value = perform_operation(input_value, current_depth, $scriptId, **($modifier_values_dict))
    output_value = max(-100000.0, min(100000.0, output_value)) 
    if math.isnan(output_value) or math.isinf(output_value): output_value = random.uniform(-50.0, 50.0)

    next_call_id_py = $next_script_id_expr # This will be 'None' string or a number string
    
    if next_call_id_py != 'None' and current_depth < max_allowed_depth and $will_call_next:
        try:
            next_call_id = int(next_call_id_py)
            if not (0 <= next_call_id < num_total_scripts): 
                 next_call_id = random.randint(0, num_total_scripts - 1)
        except (ValueError, TypeError): 
            next_call_id = random.randint(0, num_total_scripts - 1) # Fallback if string was not int-like
    else:
        next_call_id = None # Python None
            
    print(json.dumps({
        "script_id":$scriptId, "input_value":input_value, "op_type":"$op_name", 
        "modifiers_used": $modifier_values_dict, "output_value":output_value, 
        "depth":current_depth, "next_call_id":next_call_id, # This will be Python int or None
        "num_total_scripts":num_total_scripts
    }))
PYTHON;
}

function getStopServerPhpContent($serverHost, $portRangeStart, $portRangeEnd, $marker, $routerScriptNameForGrep) {
    global $e_info, $e_ok, $e_warn, $e_stop;
    $ports_to_check_str_array = [];
    for ($p = $portRangeStart; $p <= $portRangeEnd; $p++) {
        $ports_to_check_str_array[] = (string)$p;
    }
    $ports_to_check_json = json_encode($ports_to_check_str_array);
    // Note: $routerScriptNameForGrep is the *name* of the script, not its path, for use in `ps`
    // $marker is the unique string inside the router script's content

    return <<<PHP
<?php
// These emojis are for the stop script's output
\$e_info = "{$e_info}"; \$e_ok = "{$e_ok}"; \$e_warn = "{$e_warn}"; \$e_stop = "{$e_stop}";

echo "\$e_info Attempting to stop PHP built-in server(s) associated with the Fractal Art project...\\n";

\$search_serverHost = '$serverHost'; 
\$search_portsToCheck = $ports_to_check_json;
\$search_processMarker = '$marker'; 
\$search_routerScriptName = '$routerScriptNameForGrep'; 

\$killedSomething = false;

function findAndKill(\$port_to_check, \$host_to_check, \$router_script_name_to_grep, \$marker_in_router_content) {
    global \$e_ok, \$e_warn, \$e_info;
    \$pids = [];
    if (stristr(PHP_OS, 'WIN')) {
        echo "\$e_warn On Windows, automatic server stopping is complex. Please manually stop the PHP server (php.exe) listening on port {\$port_to_check} that was serving '{\$router_script_name_to_grep}'. Use Task Manager (Details tab) or Resource Monitor (resmon.exe -> Network -> Listening Ports).\\n";
        return false;
    } else { 
        // Construct a robust grep pattern. We want lines from `ps aux` that:
        // 1. Contain 'php -S'
        // 2. Contain 'host:port'
        // 3. Contain the specific router script name
        // We can't easily grep for the *content* of the router script from `ps` output.
        // The marker is more for manual verification or if the router script path was directly in ps output.
        // The most reliable ps grep is for `php -S host:port specific_router.php`
        \$grep_pattern = escapeshellarg("php -S {$host_to_check}:{\$port_to_check} {$router_script_name_to_grep}");
        \$cmd_find_php_server = "ps aux | grep " . \$grep_pattern . " | grep -v grep | awk '{print \$2}'";
        
        \$output_ps = shell_exec(\$cmd_find_php_server);
        
        if (!empty(\$output_ps)) {
            \$found_pids = array_filter(explode("\\n", trim(\$output_ps)));
            foreach (\$found_pids as \$pid_str) {
                \$pid = trim(\$pid_str);
                if (is_numeric(\$pid)) {
                    echo "\$e_info Found potential PHP server (PID: {\$pid}) for '{\$router_script_name_to_grep}' on port {\$port_to_check}. Attempting to kill...\\n";
                    exec("kill -9 " . escapeshellarg(\$pid), \$kill_output, \$kill_return);
                    if (\$kill_return === 0) {
                        echo "\$e_ok Successfully sent kill signal to PID {\$pid}.\\n";
                        return true; 
                    } else {
                        echo "\$e_warn Failed to kill PID {\$pid}. It might have already stopped or you lack permissions.\\n";
                    }
                }
            }
        }
    }
    echo "\$e_info No matching PHP server process found for '{\$router_script_name_to_grep}' on port {\$port_to_check}.\\n";
    return false; 
}

// Informational check of the router script itself (if it exists where stop_server expects it)
// This script (stop_server.php) is in the same directory as the router script.
if (file_exists(\$search_routerScriptName)) {
    if (strpos(file_get_contents(\$search_routerScriptName), \$search_processMarker) !== false) {
        echo "\$e_info Confirmed: Router script '{\$search_routerScriptName}' contains the unique server marker '{\$search_processMarker}'.\\n";
    } else {
        echo "\$e_warn Router script '{\$search_routerScriptName}' does NOT contain the expected marker '{\$search_processMarker}'. PID matching relies on command line arguments.\\n";
    }
} else {
     echo "\$e_warn Router script '{\$search_routerScriptName}' not found in current directory. PID matching might be less accurate if server was started differently.\\n";
}


foreach(\$search_portsToCheck as \$port_str) {
    \$port_int = (int)\$port_str;
    if(findAndKill(\$port_int, \$search_serverHost, \$search_routerScriptName, \$search_processMarker)) {
        \$killedSomething = true;
        // break; // Uncomment if you only expect one server instance for this project on any of the checked ports
    }
}

if (\$killedSomething) {
    echo "\$e_ok Server stopping process complete. Please verify in your terminal(s).\\n";
} else {
    echo "\$e_info No matching server processes were automatically stopped. If a server is still running, please stop it manually (Ctrl+C in its terminal or via OS process manager).\\n";
}
?>
PHP;
}

// --- Main Generation Logic ---
echo "$e_sparkle Starting Python Fractal Art project generation...\n";
echo "$e_info Main project will be created in: $mainProjectFolderName\n";

if (!is_dir($mainProjectFolderName)) {
    if (!mkdir($mainProjectFolderName, 0755, true)) {
        die("$e_warn ABORT: Failed to create main project directory: $mainProjectFolderName\n");
    }
    echo "$e_ok $e_folder Created main project directory: $mainProjectFolderName\n";
} else {
    echo "$e_info $e_folder Main project directory $mainProjectFolderName already exists.\n";
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
if (file_put_contents($indexHtmlPath, getIndexHtmlContent($e_palette))) {
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
$routerScriptContent = "<?php\n// DO NOT DELETE - Process Marker: {$serverProcessMarker}\n// Serves files from the current directory or index.html for directories.\n\$requestedPath = __DIR__ . \$_SERVER['REQUEST_URI'];\nif (strpos(\$_SERVER['REQUEST_URI'], '?') !== false) { \$requestedPath = substr(\$requestedPath, 0, strpos(\$requestedPath, '?')); } // Ignore query string for file check\nif (is_file(\$requestedPath) && file_exists(\$requestedPath)) {\n    return false; // Serve the requested file as-is.\n} elseif (file_exists(__DIR__ . '/index.html')) {\n    require_once __DIR__ . '/index.html'; // Serve index.html for directory requests or non-existent files.\n} else {\n    http_response_code(404);\n    echo '404 Not Found - Main index.html missing from ' . __DIR__;\n}\n?>";
$routerPath = $mainProjectFolderName . DIRECTORY_SEPARATOR . $routerScriptName;
if (!file_put_contents($routerPath, $routerScriptContent)) {
     echo "$e_warn Warning: Failed to write server router script '$routerPath'.\n";
} else {
    echo "$e_ok $e_gear Generated server router: $routerPath\n";
}

$stopServerScriptName = "stop_fractal_server.php";
$stopServerPath = $mainProjectFolderName . DIRECTORY_SEPARATOR . $stopServerScriptName;
if (file_put_contents($stopServerPath, getStopServerPhpContent($serverHost, $startPort, $endPort, $serverProcessMarker, $routerScriptName))) {
    echo "$e_ok $e_stop Generated server stopper script: $stopServerPath\n";
} else {
    echo "$e_warn Warning: Failed to write $stopServerPath.\n";
}

echo "---------------------------------------------------\n";
echo "$e_party Project generation complete! $e_party\n";
echo "---------------------------------------------------\n";

$serverCommand = sprintf("php -S %s:%d %s", escapeshellarg($serverHost), $actualServerPort, escapeshellarg($routerScriptName) );
$urlToOpen = "http://$serverHost:$actualServerPort/";

echo "$e_info $e_terminal TO RUN THE FRACTAL ART VISUALIZATION:\n";
echo "1. This script will ATTEMPT to start the server in the background.\n";
echo "2. $e_link If successful, your browser should open to: $urlToOpen\n";
echo "3. Click 'Start Visualization' $e_rocket on the webpage.\n";
echo "$e_info $e_timer Orchestrator default runtime: ~$defaultMaxOrchestratorRuntime seconds.\n";
echo "---------------------------------------------------\n";
echo "$e_info $e_stop TO STOP THE SERVER:\n";
echo "   - Navigate to: cd " . escapeshellarg(basename($mainProjectFolderName)) . "\n";
echo "   - Then run: php $stopServerScriptName\n";
echo "   - OR stop 'php -S ... $routerScriptName' manually.\n";
echo "---------------------------------------------------\n";

$backgroundCmd = ''; $projectFullPath = getcwd() . DIRECTORY_SEPARATOR . $mainProjectFolderName;
$outputRedirect = (stristr(PHP_OS, 'WIN') ? ' > NUL 2>&1' : ' > /dev/null 2>&1');

if (stristr(PHP_OS, 'WIN')) {
    $backgroundCmd = "start /B \"PHP Fractal Server\" cmd /c \"cd /D " . escapeshellarg($projectFullPath) . " && $serverCommand \"";
} else { 
    $backgroundCmd = "(cd " . escapeshellarg($projectFullPath) . " && exec $serverCommand $outputRedirect) &";
}

echo "$e_rocket Attempting to start PHP server in background ($serverHost:$actualServerPort)...\n";
shell_exec($backgroundCmd); 
sleep(3); 

echo "$e_eyes Attempting to open '$urlToOpen' in your default browser...\n";
$opener = '';
if (stristr(PHP_OS, 'DAR')) $opener = 'open';
elseif (stristr(PHP_OS, 'WIN')) $opener = 'start ""'; 
elseif (stristr(PHP_OS, 'LINUX')) $opener = 'xdg-open';

if ($opener) {
    @system($opener . ' ' . escapeshellarg($urlToOpen) . ($opener === 'start ""' ? '' : ' > /dev/null 2>&1'));
    echo "$e_link If browser didn't open, please navigate to the URL manually.\n";
} else {
    echo "$e_warn Could not determine OS to auto-open browser. Please open: $urlToOpen\n";
}
echo "---------------------------------------------------\n";
echo "$e_party Script finished! $e_palette Check browser and '$mainProjectFolderName'.\n";

?>