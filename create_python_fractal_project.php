<?php
// create_python_fractal_project.php
// Version: 2023-10-28_1000
// Generator Timestamp: <?php echo date('Y-m-d H:i:s T'); ? > 

// --- Configuration ---
date_default_timezone_set('UTC'); // Set a default timezone for consistency
$scriptVersion = "1.2.0 (" . date('Y-m-d') . ")"; // Version for the generated project itself

// New Naming Scheme
$randomNumber = rand(10, 99); // For "Fractal303Dream42" style
$mainProjectFolderNameBase = "🎨_Fractal{$numPythonScripts}Dream{$randomNumber}_" . date('Ymd');
$mainProjectFolderName = $mainProjectFolderNameBase . "_" . date('His');

$pythonSubfolderName = "python_fractal_weavers";
$serverScriptsSubfolderName = "_server_tools"; // For stop script etc.
$numPythonScripts = 303; // Keep this if you want Fractal303
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
$e_link = "🔗"; $e_terminal = "💻"; $e_eyes = "👀"; $e_stop = "🛑"; $e_timer = "⏱️"; $e_wrench = "🔧";

$serverProcessMarker = "fractal_server_process_marker_" . bin2hex(random_bytes(6));

echo "$e_sparkle Create Python Fractal Project - Generator v{$scriptVersion} $e_sparkle\n";
echo "Generator run timestamp: " . date('Y-m-d H:i:s T') . "\n";
echo "---------------------------------------------------\n";


function isPortAvailable($host, $port, $timeout = 1) {
    $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if ($socket) {
        fclose($socket);
        return false;
    }
    return true;
}

// --- Helper: Content for index.html ---
function getIndexHtmlContent($paletteEmoji, $scriptPyEmojiGlobal, $scriptPhpEmojiGlobal, $rocketEmojiGlobal, $stopEmojiGlobal) {
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Python Fractal Math Art $paletteEmoji</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
    <style>
        body { margin: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; background-color: #222; color: #eee; font-family: 'Segoe UI', sans-serif; }
        canvas { border: 1px solid #444; background-color: #111; max-width: 90vw; max-height: 80vh; box-shadow: 0 0 20px rgba(0,200,255,0.3); touch-action: none; }
        header, footer { text-align: center; margin: 1em; }
        .controls { margin-bottom: 1em; padding: 0.5em; background-color: rgba(255,255,255,0.05); border-radius: 5px;}
        button { margin: 0.3em; font-size: 0.9em;}
        .container { padding: 1em; max-width: 900px; width: 100%;}
        #statusMessage { font-style: italic; color: #aaa; font-size: 0.85em; min-height: 1.2em; }
    </style>
</head>
<body>
    <header>
        <h1>Python Fractal Math Art $paletteEmoji</h1>
        <p>Visualizing chained math operations from Python scripts.</p>
    </header>
    <main class="container">
        <div class="controls">
            <button id="startButton">Start Visualization $rocketEmojiGlobal</button>
            <button id="stopButton" disabled>Stop Stream $stopEmojiGlobal</button>
            <button id="clearButton">Clear Canvas 🧹</button>
        </div>
        <div id="statusMessage">Ready to start...</div>
        <canvas id="fractalCanvas" width="800" height="600"></canvas>
    </main>
    <footer>
        <p>Powered by Python $scriptPyEmojiGlobal, PHP $scriptPhpEmojiGlobal, and Pico.css</p>
    </footer>
    <script>
        const canvas = document.getElementById('fractalCanvas');
        const ctx = canvas.getContext('2d');
        const startButton = document.getElementById('startButton');
        const stopButton = document.getElementById('stopButton');
        const clearButton = document.getElementById('clearButton');
        const statusMessage = document.getElementById('statusMessage');
        let eventSource = null;
        let lastX = canvas.width / 2;
        let lastY = canvas.height / 2;
        let hue = Math.random() * 360;
        let pointCounter = 0;
        let animationFrameId = null;
        let dataBuffer = [];
        const MAX_BUFFER_SIZE = 5; // Process up to 5 data points per frame

        function clearCanvas() {
            if (animationFrameId) cancelAnimationFrame(animationFrameId);
            dataBuffer = [];
            ctx.fillStyle = '#111111';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            lastX = canvas.width / 2; 
            lastY = canvas.height / 2;
            hue = Math.random() * 360;
            pointCounter = 0;
            statusMessage.textContent = "Canvas cleared. Ready.";
        }
        clearCanvas(); 

        function drawDataPoint(data) {
            const value = parseFloat(data.output_value);
            const depth = parseInt(data.depth);
            const scriptId = parseInt(data.script_id);
            let normValue = (value % 200 - 100) / 100;
            if (isNaN(normValue)) normValue = 0;

            const angle = (normValue * Math.PI) + (scriptId % 360 * Math.PI / 180) + (depth * 0.1 * (pointCounter % 2 === 0 ? 1 : -1));
            const distance = 3 + (Math.abs(normValue) * 12) + (depth * 2.5) + (Math.sin(pointCounter * 0.07) * 4); 
            
            let newX = lastX + Math.cos(angle) * distance;
            let newY = lastY + Math.sin(angle) * distance;
            
            ctx.beginPath(); ctx.moveTo(lastX, lastY);
            hue = (hue + Math.abs(scriptId % 7 - 3.5) + (normValue * 7) + (depth * 0.5) ) % 360;
            const saturation = Math.min(100, 40 + (depth * 8) + Math.abs(normValue)*40); 
            const lightness = Math.min(85, Math.max(30, 55 + (normValue * 25) - (depth*2) )); 
            
            ctx.strokeStyle = `hsla(\${hue}, \${saturation}%, \${lightness}%, 0.55)`;
            ctx.lineWidth = Math.max(0.1, 0.3 + depth * 0.15 + Math.abs(normValue) * 0.3); 
            ctx.lineTo(newX, newY); ctx.stroke();
            
            if (pointCounter % 5 === 0) { // Draw "stars" less frequently
                ctx.beginPath(); 
                ctx.arc(newX, newY, Math.max(0.3, 0.8 + depth * 0.1), 0, Math.PI * 2);
                ctx.fillStyle = `hsla(\${(hue + 40) % 360}, \${saturation}%, \${Math.min(95, lightness + 15)}%, 0.75)`;
                ctx.fill();
            }

            if (newX > canvas.width || newX < 0) newX = Math.random() * canvas.width; 
            if (newY > canvas.height || newY < 0) newY = Math.random() * canvas.height;
            lastX = newX; lastY = newY;
            pointCounter++;
        }

        function processBuffer() {
            let processedCount = 0;
            while(dataBuffer.length > 0 && processedCount < MAX_BUFFER_SIZE) {
                const data = dataBuffer.shift();
                drawDataPoint(data);
                processedCount++;
            }
            if (dataBuffer.length > 0 || (eventSource && eventSource.readyState !== EventSource.CLOSED)) {
                 animationFrameId = requestAnimationFrame(processBuffer);
            }
        }

        startButton.addEventListener('click', () => {
            if (eventSource) eventSource.close(); 
            clearCanvas();
            statusMessage.textContent = "Connecting to orchestrator...";
            eventSource = new EventSource('fractal_orchestrator.php'); 
            startButton.disabled = true;
            stopButton.disabled = false;

            if (animationFrameId) cancelAnimationFrame(animationFrameId); // Clear previous animation loop
            animationFrameId = requestAnimationFrame(processBuffer); // Start new animation loop

            eventSource.onmessage = function(event) {
                try {
                    const data = JSON.parse(event.data);
                    if (data.status && data.status.includes('finished')) {
                        statusMessage.textContent = "Stream finished: " + data.status;
                        console.log("Orchestrator reported:", data.status);
                        if (eventSource) eventSource.close();
                        startButton.disabled = false;
                        stopButton.disabled = true;
                        // No more data, but let animation frame process remaining buffer
                        return;
                    }
                    if(data.script_id !== undefined) { // Check if it's a data point
                        dataBuffer.push(data);
                        statusMessage.textContent = `Received data from script #\${data.script_id} (depth: \${data.depth}). Buffer: \${dataBuffer.length}`;
                    } else if (data.error) {
                        console.error("Orchestrator error:", data.error, data.details || '');
                        statusMessage.textContent = "Error from orchestrator: " + data.error;
                    }
                } catch (e) { console.error("Error parsing SSE data:", e, event.data); statusMessage.textContent = "Error parsing data.";}
            };
            eventSource.onerror = function(err) {
                console.error("EventSource failed:", err);
                statusMessage.textContent = "EventSource connection failed or closed by server.";
                if(eventSource) eventSource.close();
                startButton.disabled = false; stopButton.disabled = true;
                if (animationFrameId) cancelAnimationFrame(animationFrameId);
                 if (err.target && err.target.readyState === EventSource.CLOSED) {
                    // Alert moved to status message for less intrusiveness
                }
            };
        });
        stopButton.addEventListener('click', () => {
            if (eventSource) { eventSource.close(); console.log("EventSource closed."); statusMessage.textContent = "Stream stopped by user.";}
            if (animationFrameId) cancelAnimationFrame(animationFrameId);
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
for (\$i = 0; \$i < 7; ++\$i) { // Increased initial branches for more starting points
    \$queue[] = ['id' => rand(0, \$numPythonScripts - 1), 'value' => (rand(-1500, 1500) / 100.0), 'depth' => 0];
}

\$processed_count = 0;
\$event_burst_limit = 100; // Process this many events before explicit time check
\$total_events_sent_this_run = 0;

while (!empty(\$queue)) {
    if ((time() - \$startTime) >= \$maxOrchestratorRuntime) {
        send_event(['status' => 'Orchestrator reached max runtime (' . \$maxOrchestratorRuntime . 's). Processed: ' . \$total_events_sent_this_run]);
        error_log("Fractal_Orchestrator reached max runtime. Total events: {\$total_events_sent_this_run}");
        exit(0);
    }
    if (connection_aborted()) {
        error_log("Client disconnected, stopping fractal_orchestrator.");
        break;
    }

    \$current_burst_processed = 0;
    while(!empty(\$queue) && \$current_burst_processed < \$event_burst_limit){
        if (connection_aborted() || (time() - \$startTime) >= \$maxOrchestratorRuntime) break 2; // Break outer loop too

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
                    \$total_events_sent_this_run++;
                    if (isset(\$json_data['next_call_id']) && \$json_data['next_call_id'] !== null && \$json_data['depth'] < \$maxCallDepth && is_numeric(\$json_data['next_call_id']) && \$json_data['next_call_id'] >= 0 && \$json_data['next_call_id'] < \$numPythonScripts) {
                        \$next_val = \$json_data['output_value'];
                        if (abs(\$next_val - \$input_value) < 0.05 && rand(0,3) == 0) \$next_val += (rand(-75,75)/100.0); // More perturbation
                        \$queue[] = ['id' => (int)\$json_data['next_call_id'], 'value' => \$next_val, 'depth' => \$json_data['depth'] + 1];
                    }
                }
            }
        } else {
            send_event(['error' => "Script {\$script_name} failed. Ret: {\$return_var}", 'details' => \$output_str]);
        }
        \$current_burst_processed++;
        usleep(15000); // 15ms, even faster
    }
    // After a burst, or if queue emptied within a burst, explicitly check time / connection again
    if (empty(\$queue)) break; // No more items to process
}
send_event(['status' => 'Orchestration loop finished. Processed total: ' . \$total_events_sent_this_run]);
error_log("Fractal_Orchestrator loop finished. Total events: {\$total_events_sent_this_run}");
?>
PHP;
}

function getPythonScriptContent($scriptId, $numTotalScripts, $maxCallDepth) {
    $ops = [
        ['name' => 'add', 'lambda' => 'val + modifier', 'mod_range' => [-8.0, 8.0, 0.1]],
        ['name' => 'subtract', 'lambda' => 'val - modifier', 'mod_range' => [-8.0, 8.0, 0.1]],
        ['name' => 'multiply', 'lambda' => 'val * modifier', 'mod_range' => [0.2, 3.0, 0.01]],
        ['name' => 'divide', 'lambda' => 'val / modifier if modifier != 0 else val + random.uniform(0.001, 0.01)', 'mod_range' => [0.2, 3.0, 0.01]],
        ['name' => 'sine_transform', 'lambda' => 'math.sin(val * modifier_angle) * modifier_amp', 'mod_range' => [], 'custom_modifiers' => ['angle' => [0.05, (float)(2 * M_PI), 0.01], 'amp' => [0.2, 12.0, 0.1]]],
        ['name' => 'cosine_transform', 'lambda' => 'math.cos(val * modifier_angle) * modifier_amp', 'mod_range' => [], 'custom_modifiers' => ['angle' => [0.05, (float)(2 * M_PI), 0.01], 'amp' => [0.2, 12.0, 0.1]]],
        ['name' => 'spiral_step_x', 'lambda' => 'val + math.cos(current_depth * modifier_angle_rate + script_id * 0.05) * modifier_step_size', 'mod_range' => [], 'custom_modifiers' => ['angle_rate' => [0.02, 0.6, 0.005], 'step_size' => [0.2, 7.0, 0.05]]],
        ['name' => 'spiral_step_y', 'lambda' => 'val + math.sin(current_depth * modifier_angle_rate + script_id * 0.05) * modifier_step_size', 'mod_range' => [], 'custom_modifiers' => ['angle_rate' => [0.02, 0.6, 0.005], 'step_size' => [0.2, 7.0, 0.05]]],
        ['name' => 'power_clip', 'lambda' => 'max(-75.0, min(75.0, math.copysign(pow(abs(val) + 1e-9, modifier), val))) if val != 0 else 0.0', 'mod_range' => [0.3, 1.9, 0.01]], // Added 1e-9 for stability near 0
        ['name' => 'modulo_chaos', 'lambda' => '(val * modifier_factor + modifier_add) % modifier_mod if modifier_mod != 0 else val', 'mod_range' => [], 'custom_modifiers' => ['factor' => [0.3, 3.0, 0.01], 'add' => [-7.0, 7.0, 0.1], 'mod' => [3.0, 25.0, 0.1]]],
        ['name' => 'logistic_map', 'lambda' => 'modifier_r * current_val_for_map * (1.0 - current_val_for_map)', 'mod_range' => [], 'custom_modifiers' => ['r' => [3.55, 4.0, 0.001], 'limit_dummy' => [1.0,1.0,0.01]]], // Limit is implicit (0-1), param for consistency
        ['name' => 'ikeda_map_x', 'lambda' => '1.0 + modifier_u * (val * math.cos(tn) - prev_val_placeholder * math.sin(tn))', 'mod_range' => [], 'custom_modifiers' => ['u' => [0.7, 0.95, 0.005]]], // tn needs to be calculated
        ['name' => 'ikeda_map_y', 'lambda' => 'modifier_u * (val * math.sin(tn) + prev_val_placeholder * math.cos(tn))', 'mod_range' => [], 'custom_modifiers' => ['u' => [0.7, 0.95, 0.005]]],
    ];
    $chosen_op_data = $ops[array_rand($ops)];
    $op_name = $chosen_op_data['name']; $op_lambda = $chosen_op_data['lambda'];
    $modifier_definitions = ""; $modifier_values_dict_content = ""; $modifier_display_values = "";

    if (isset($chosen_op_data['custom_modifiers'])) {
        $temp_mod_dict_parts = [];
        foreach($chosen_op_data['custom_modifiers'] as $mod_name => $range) {
            $min_r = (float)$range[0]; $max_r = (float)$range[1];
            $step_r = isset($range[2]) ? (float)$range[2] : (($max_r - $min_r > 1) ? 1.0 : 0.01); // Default step
            if ($step_r == 0) $step_r = ($max_r - $min_r > 1) ? 0.1 : 0.001;

            $rand_min_scaled = (int)floor($min_r / $step_r); // Use floor for min
            $rand_max_scaled = (int)floor($max_r / $step_r); // Use floor for max as well to ensure range
            if ($rand_min_scaled > $rand_max_scaled) list($rand_min_scaled, $rand_max_scaled) = [$rand_max_scaled, $rand_min_scaled]; // Swap if out of order
            if ($rand_min_scaled == $rand_max_scaled) $mod_val = $min_r; // If range is too small for step
            else $mod_val = round(rand($rand_min_scaled, $rand_max_scaled) * $step_r, 4); // Increased precision

            $modifier_definitions .= "    modifier_{$mod_name} = {$mod_val}\n";
            $temp_mod_dict_parts[] = "\"modifier_{$mod_name}\": modifier_{$mod_name}";
            $modifier_display_values .= "{$mod_name}={$mod_val}, ";
        }
        $modifier_values_dict_content = implode(", ", $temp_mod_dict_parts);
        $modifier_display_values = rtrim($modifier_display_values, ", ");
    } else {
        $min_r_single = (float)$chosen_op_data['mod_range'][0]; $max_r_single = (float)$chosen_op_data['mod_range'][1];
        if (isset($chosen_op_data['mod_range'][2])) {
            $step_r_single = (float)$chosen_op_data['mod_range'][2];
            if ($step_r_single == 0) $step_r_single = 0.1;
            $rand_min_s_scaled = (int)floor($min_r_single / $step_r_single);
            $rand_max_s_scaled = (int)floor($max_r_single / $step_r_single);
            if ($rand_min_s_scaled > $rand_max_s_scaled) list($rand_min_s_scaled, $rand_max_s_scaled) = [$rand_max_s_scaled, $rand_min_s_scaled];
            if ($rand_min_s_scaled == $rand_max_s_scaled) $modifier = $min_r_single;
            else $modifier = round(rand($rand_min_s_scaled, $rand_max_s_scaled) * $step_r_single, 3);
        } else { $modifier = rand((int)$min_r_single, (int)$max_r_single); }
        if ($modifier == 0 && ($op_name == 'divide')) $modifier = (rand(0,1)==0 ? 0.01 : -0.01);
        $modifier_definitions = "    modifier = {$modifier}";
        $modifier_values_dict_content = "\"modifier\": modifier";
        $modifier_display_values = "modifier={$modifier}";
    }
    $modifier_values_dict = "{".$modifier_values_dict_content."}";

    $will_call_next = (rand(1, 100) <= 75); // Increased branching
    $next_script_id_expr = 'None';
    if ($will_call_next) {
        $offset_direction = rand(0,1) == 0 ? -1 : 1; $offset_amount = rand(1, max(1, (int)($numTotalScripts / 8))); // Even wider jumps
        $next_id_raw = ($scriptId + ($offset_direction * $offset_amount)) % $numTotalScripts;
        if ($next_id_raw < 0) $next_id_raw += $numTotalScripts;
        $next_script_id_expr = (string)$next_id_raw;
    }
    $prev_val_placeholder_code = 'random.uniform(-0.3, 0.3) if op_name.startswith("henon_map") or op_name.startswith("ikeda_map") else 0.0';
    if(!empty($modifier_definitions) && substr($modifier_definitions, -strlen("\n")) !== "\n") {
        $modifier_definitions .= "\n";
    }

    return <<<PYTHON
import sys, json, math, random
# Script ID $scriptId: Op '$op_name', Modifiers: $modifier_display_values
op_name = "$op_name" 

def perform_operation(val, current_depth, script_id, **modifiers):
    for key, value in modifiers.items(): globals()[key] = value 
    prev_val_placeholder = $prev_val_placeholder_code 
    current_val_for_map = val # Used by logistic map after normalization
    try: 
        if op_name == "logistic_map": 
             limit = modifiers.get("modifier_limit_dummy", 1.0) # This limit is just for normalization here
             if limit == 0: limit = 1.0
             val_norm = abs(val) 
             current_val_for_map = (val_norm % limit) / limit if limit != 0.0 else val_norm % 1.0 # val for map is 0-1
        elif op_name.startswith("ikeda_map"):
            # Simplified Ikeda: tn depends on val (x_n) and prev_val_placeholder (y_n)
            # This is not a true 2D map without passing both x,y state.
            # prev_val_placeholder here acts as a proxy for y_n or some other related term.
            tn = 0.4 - 6.0 / (1.0 + val**2 + prev_val_placeholder**2)


        return $op_lambda
    except Exception as e: 
        # import traceback; print(f"Error in op {op_name} (ID {script_id}): {e}\\n{traceback.format_exc()}", file=sys.stderr)
        return val + random.uniform(-2.0, 2.0) # Larger perturbation on error

if __name__ == "__main__":
    if len(sys.argv) < 5: print(json.dumps({"error": "Insufficient args", "script_id": $scriptId})); sys.exit(1)
    input_value, current_depth, num_total_scripts, max_allowed_depth = float(sys.argv[1]), int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])
    
$modifier_definitions
    
    output_value = perform_operation(input_value, current_depth, $scriptId, **($modifier_values_dict))
    # Cap output value less aggressively to allow for more dynamic range, but still prevent true infinity
    output_value = max(-1e6, min(1e6, output_value)) 
    if math.isnan(output_value) or math.isinf(output_value): output_value = random.uniform(-100.0, 100.0)

    next_call_id_py_str = $next_script_id_expr 
    
    next_call_id_final = None # Python None
    if next_call_id_py_str != 'None' and current_depth < max_allowed_depth and $will_call_next:
        try:
            parsed_next_id = int(next_call_id_py_str)
            if 0 <= parsed_next_id < num_total_scripts: 
                 next_call_id_final = parsed_next_id
            else: # Fallback if parsed int is out of bounds
                 next_call_id_final = random.randint(0, num_total_scripts - 1)
        except (ValueError, TypeError): # Fallback if string was not int-like (e.g. 'None' or corrupted)
            next_call_id_final = random.randint(0, num_total_scripts - 1) 
    
    print(json.dumps({
        "script_id":$scriptId, "input_value":input_value, "op_type":"$op_name", 
        "modifiers_used": $modifier_values_dict, "output_value":output_value, 
        "depth":current_depth, "next_call_id":next_call_id_final, 
        "num_total_scripts":num_total_scripts
    }))
PYTHON;
}

function getStopServerPhpContent($serverHost, $portRangeStart, $portRangeEnd, $marker, $routerScriptNameForGrep) {
    // Using global emojis
    $e_info_stop = $GLOBALS['e_info']; $e_ok_stop = $GLOBALS['e_ok']; 
    $e_warn_stop = $GLOBALS['e_warn']; $e_stop_stop = $GLOBALS['e_stop'];
    
    $ports_to_check_str_array = [];
    for ($p = $portRangeStart; $p <= $portRangeEnd; $p++) {
        $ports_to_check_str_array[] = (string)$p;
    }
    $ports_to_check_json = json_encode($ports_to_check_str_array);

    return <<<PHP
<?php
// stop_fractal_server.php
\$e_info = "{$e_info_stop}"; \$e_ok = "{$e_ok_stop}"; \$e_warn = "{$e_warn_stop}"; \$e_stop_glyph = "{$e_stop_stop}";

echo "\$e_info Attempting to stop PHP built-in server(s) for the Fractal Art project...\\n";

\$search_serverHost = '$serverHost'; 
\$search_portsToCheck = $ports_to_check_json;
\$search_processMarker = '$marker'; 
\$search_routerScriptName = '$routerScriptNameForGrep'; 

\$killedSomething = false;

function findAndKillProcess(\$port_to_check, \$host_to_check, \$router_script_name_to_grep) {
    global \$e_ok, \$e_warn, \$e_info;
    if (stristr(PHP_OS, 'WIN')) {
        echo "\$e_warn On Windows, please manually stop the PHP server (php.exe) listening on port {\$port_to_check} that was serving '{\$router_script_name_to_grep}'. Use Task Manager or Resource Monitor (resmon.exe -> Network -> Listening Ports).\\n";
        return false;
    } else { 
        // Grep for `php -S host:port router_script_name`
        \$grep_pattern = escapeshellarg("php -S {$host_to_check}:{\$port_to_check} {$router_script_name_to_grep}");
        // Use -f with pgrep to match full command line, more robust if available
        // However, ps aux | grep is more portable.
        \$cmd_find_php_server = "ps aux | grep " . \$grep_pattern . " | grep -v grep | awk '{print \$2}'";
        
        \$output_ps = shell_exec(\$cmd_find_php_server);
        
        if (!empty(\$output_ps)) {
            \$found_pids = array_filter(explode("\\n", trim(\$output_ps)));
            if (empty(\$found_pids)) { // Double check, as grep might return empty string that explode makes an array with one empty element
                 echo "\$e_info No matching PHP server process found for '{\$router_script_name_to_grep}' on port {\$port_to_check} via ps command.\\n";
                 return false;
            }
            foreach (\$found_pids as \$pid_str) {
                \$pid = trim(\$pid_str);
                if (is_numeric(\$pid) && \$pid > 0) { // Ensure PID is a positive number
                    echo "\$e_info Found potential PHP server (PID: {\$pid}) for '{\$router_script_name_to_grep}' on port {\$port_to_check}. Attempting to kill...\\n";
                    exec("kill -9 " . escapeshellarg(\$pid), \$kill_output, \$kill_return);
                    if (\$kill_return === 0) {
                        echo "\$e_ok Successfully sent SIGKILL to PID {\$pid}.\\n";
                        return true; 
                    } else {
                        echo "\$e_warn Failed to kill PID {\$pid} (return: {\$kill_return}). It might have already stopped or you lack permissions.\\n";
                    }
                }
            }
        } else {
             echo "\$e_info No matching PHP server process found for '{\$router_script_name_to_grep}' on port {\$port_to_check} via ps command (empty output).\\n";
        }
    }
    return false; 
}

// This check is informational, done when stop_fractal_server.php is run
// It assumes the stop script is in the same directory as the router script.
if (file_exists(\$search_routerScriptName)) {
    if (strpos(file_get_contents(\$search_routerScriptName), \$search_processMarker) !== false) {
        echo "\$e_info Confirmed: Local router script '{\$search_routerScriptName}' contains the unique server marker '{\$search_processMarker}'.\\n";
    } else {
        echo "\$e_warn Local router script '{\$search_routerScriptName}' does NOT contain the expected marker. PID matching relies on command line arguments only.\\n";
    }
} else {
     echo "\$e_warn Router script '{\$search_routerScriptName}' not found in current directory. Cannot verify marker. PID matching relies on command line args only.\\n";
}

foreach(\$search_portsToCheck as \$port_str) {
    \$port_int = (int)\$port_str;
    if(findAndKillProcess(\$port_int, \$search_serverHost, \$search_routerScriptName)) { // Removed marker from here as ps doesn't see file content
        \$killedSomething = true;
        // break; // Uncomment if you only expect one server instance for this project
    }
}

if (\$killedSomething) {
    echo "\$e_ok Server stopping process complete. Please verify in your terminal(s).\\n";
} else {
    echo "\$e_info \$e_stop_glyph No matching server processes were automatically stopped. If a server is still running, please stop it manually (Ctrl+C in its terminal or via OS process manager).\\n";
}
?>
PHP;
}

// --- Main Generation Logic ---
echo "$e_sparkle Create Python Fractal Project - Generator v{$scriptVersion} $e_sparkle\n";
echo "Generator run timestamp: " . date('Y-m-d H:i:s T') . "\n";
echo "---------------------------------------------------\n";

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

// Create server tools subfolder
$serverScriptsPath = $mainProjectFolderName . DIRECTORY_SEPARATOR . $serverScriptsSubfolderName;
if (!is_dir($serverScriptsPath)) {
    if (!mkdir($serverScriptsPath, 0755, true)) {
        echo "$e_warn Warning: Failed to create server tools subfolder: $serverScriptsPath\n";
        $serverScriptsPath = $mainProjectFolderName; // Fallback to main project folder for stop script
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
$routerScriptContent = "<?php\n// DO NOT DELETE - Process Marker: {$serverProcessMarker}\n// Serves files from current dir or index.html for directories.\n\$docRoot = __DIR__;\n\$reqUri = \$_SERVER['REQUEST_URI'];\n\$reqPath = \$docRoot . preg_replace('/\?.*/', '', \$reqUri); // Remove query string for file check\nif (is_file(\$reqPath)) {\n    return false;\n} elseif (file_exists(\$docRoot . '/index.html')) {\n    require_once \$docRoot . '/index.html';\n} else {\n    http_response_code(404);\n    echo '404 Not Found - index.html missing in ' . \$docRoot;\n}\n?>";
$routerPath = $mainProjectFolderName . DIRECTORY_SEPARATOR . $routerScriptName; // Router in main project folder
if (!file_put_contents($routerPath, $routerScriptContent)) {
     echo "$e_warn Warning: Failed to write server router script '$routerPath'.\n";
} else {
    echo "$e_ok $e_wrench Generated server router: " . basename($routerPath) . "\n";
}


$stopServerScriptName = "stop_fractal_server.php";
// Place stop script inside the server tools subfolder
$stopServerPath = $serverScriptsPath . DIRECTORY_SEPARATOR . $stopServerScriptName;
if (file_put_contents($stopServerPath, getStopServerPhpContent($serverHost, $startPort, $endPort, $serverProcessMarker, $routerScriptName))) {
    echo "$e_ok $e_stop Generated server stopper script: " . $serverScriptsSubfolderName . DIRECTORY_SEPARATOR . $stopServerScriptName . "\n";
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
echo "   - Navigate to: cd " . escapeshellarg(basename($mainProjectFolderName)) . DIRECTORY_SEPARATOR . escapeshellarg(basename($serverScriptsSubfolderName)) . "\n";
echo "   - Then run: php $stopServerScriptName\n";
echo "   - OR stop 'php -S ... $routerScriptName' manually (e.g., Ctrl+C if in foreground).\n";
echo "---------------------------------------------------\n";

$backgroundCmd = ''; $projectFullPath = getcwd() . DIRECTORY_SEPARATOR . $mainProjectFolderName;
$outputRedirect = (stristr(PHP_OS, 'WIN') ? ' > NUL 2>&1' : ' > /dev/null 2>&1');

if (stristr(PHP_OS, 'WIN')) {
    $backgroundCmd = "start /B \"PHP Fractal Server\" cmd /c \"cd /D " . escapeshellarg($projectFullPath) . " && $serverCommand \"";
} else { // macOS / Linux
    // For macOS/Linux, ensure 'exec' is used so the PHP server process replaces the shell, making it easier to manage if parent shell exits.
    $backgroundCmd = "(cd " . escapeshellarg($projectFullPath) . " && exec $serverCommand $outputRedirect) &";
}

echo "$e_rocket Attempting to start PHP server in background ($serverHost:$actualServerPort)...\n";
shell_exec($backgroundCmd); 
sleep(3); // Give server time to start

// Simple check if server started (very basic, not foolproof)
echo "$e_eyes Checking if server started on $urlToOpen ...\n";
$headers = @get_headers($urlToOpen);
if ($headers && strpos($headers[0], '200 OK') !== false) {
    echo "$e_ok Server seems to have started successfully!\n";
} else {
    echo "$e_warn Server might not have started or is not reachable at $urlToOpen.\n";
    echo "$e_warn Please check manually. If it failed, try starting it from the terminal:\n";
    echo "   cd " . escapeshellarg($projectFullPath) . "\n";
    echo "   php -S $serverHost:$actualServerPort " . escapeshellarg($routerScriptName) . "\n";
}


echo "$e_eyes Attempting to open '$urlToOpen' in your default browser...\n";
$opener = '';
// Explicitly check for macOS (PHP_OS can be 'Darwin')
if (PHP_OS_FAMILY === 'Darwin') $opener = 'open'; // PHP_OS_FAMILY available PHP 7.2+
elseif (PHP_OS_FAMILY === 'Windows') $opener = 'start ""';
elseif (PHP_OS_FAMILY === 'Linux') $opener = 'xdg-open';
else { // Fallback for older PHP or other OS
    if (stristr(PHP_OS, 'DAR')) $opener = 'open';
    elseif (stristr(PHP_OS, 'WIN')) $opener = 'start ""'; 
    elseif (stristr(PHP_OS, 'LINUX')) $opener = 'xdg-open';
}


if ($opener) {
    @system($opener . ' ' . escapeshellarg($urlToOpen) . ($opener === 'start ""' ? '' : ' > /dev/null 2>&1'));
    echo "$e_link If browser didn't open, please navigate to the URL manually.\n";
} else {
    echo "$e_warn Could not determine OS to auto-open browser. Please open: $urlToOpen\n";
}
echo "---------------------------------------------------\n";
echo "$e_party Script finished! $e_palette Check browser and '$mainProjectFolderName'.\n";

?>