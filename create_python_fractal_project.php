<?php
// create_python_fractal_project.php
// Version: 2023-10-28_FractalWeaver_v1.6
// Generator Timestamp: <?php echo date('Y-m-d H:i:s T'); ? > 

// --- Configuration ---
date_default_timezone_set('UTC');
$scriptVersion = "FractalWeaver_v1.6 (" . date('Y-m-d') . ")";

$numPythonScripts = 303; // Used in folder name
$randomNumberForName = rand(10, 99);
$mainProjectFolderNameBase = "🎨_Fractal{$numPythonScripts}Dream{$randomNumberForName}_" . date('Ymd');
// The ACTUAL folder name will append _His to this base AFTER trying to stop old server
// $mainProjectFolderName = $mainProjectFolderNameBase . "_" . date('His'); // This is set later

$pythonSubfolderName = "python_fractal_spells";
$serverScriptsSubfolderName = "_server_utils";
$maxCallDepth = 6;
$defaultMaxOrchestratorRuntime = 150;

$serverHost = "localhost";
$startPort = 10011;
$endPort = 10020;
$maxPortRetries = 5;

// Emojis
$e_sparkle = "✨"; $e_folder = "📁"; $e_script_py = "🐍"; $e_script_php = "🐘";
$e_html = "📄"; $e_rocket = "🚀"; $e_palette = "🎨"; $e_gear = "⚙️";
$e_warn = "⚠️"; $e_info = "ℹ️"; $e_ok = "✅"; $e_party = "🎉";
$e_link = "🔗"; $e_terminal = "💻"; $e_eyes = "👀"; $e_stop = "🛑"; $e_timer = "⏱️"; $e_wrench = "🔧"; $e_magic = "🪄"; $e_broom = "🧹";

$serverProcessMarker = "fractal_server_process_marker_" . bin2hex(random_bytes(8));

echo "$e_magic Create Python Fractal Project - Generator v{$scriptVersion} $e_magic\n";
echo "Generator run timestamp: " . date('Y-m-d H:i:s T') . "\n";
echo "---------------------------------------------------\n";


function isPortAvailable($host, $port, $timeout = 0.5) {
    $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if ($socket) {
        fclose($socket);
        return false;
    }
    return true;
}

// --- Helper: Content for index.html ---
function getIndexHtmlContent($paletteEmoji, $scriptPyEmojiGlobal, $scriptPhpEmojiGlobal, $rocketEmojiGlobal, $stopEmojiGlobal) {
    // CORRECTED: Using \${variable} for JS template literals inside PHP heredoc
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Python Fractal Math Art $paletteEmoji</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
    <style>
        :root { 
            --background-color: #11191f; --color: #bbb; --h1-color: #fff; --muted-color: #789;
            --primary: #00bcd4; --primary-hover: #00acc1; --card-background-color: #1a242f;
            --card-border-color: #2c3a47; --form-element-background-color: #1a242f;
            --form-element-border-color: #2c3a47; --form-element-focus-color: var(--primary);
        }
        body { margin: 0; display: flex; flex-direction: column; align-items: center; min-height: 100vh; background-color: var(--background-color); color: var(--color); font-family: 'Segoe UI', sans-serif; }
        canvas { border: 1px solid var(--card-border-color); background-color: #0d1117; max-width: 90vw; max-height: 75vh; box-shadow: 0 0 25px rgba(0, 200, 255, 0.25); touch-action: none; }
        header, footer { text-align: center; margin: 1em; }
        header h1 { color: var(--h1-color); }
        footer p { color: var(--muted-color); font-size: 0.9em;}
        .controls { margin-bottom: 1em; padding: 0.75em; background-color: var(--card-background-color); border-radius: 8px; display: flex; gap: 0.5em; flex-wrap: wrap; justify-content: center;}
        button { margin: 0.2em; font-size: 0.9em;}
        .container { padding: 1em; max-width: 1000px; width: 100%;}
        #statusMessage { font-style: italic; color: var(--muted-color); font-size: 0.9em; min-height: 1.3em; margin-top: 0.5em; text-align: center; }
    </style>
</head>
<body>
    <header>
        <h1>Python Fractal Math Art $paletteEmoji</h1>
        <p>Emergent patterns from chained Python math operations.</p>
    </header>
    <main class="container">
        <div class="controls">
            <button id="startButton" class="primary">Start Visualization $rocketEmojiGlobal</button>
            <button id="stopButton" class="secondary" disabled>Stop Stream $stopEmojiGlobal</button>
            <button id="clearButton" class="contrast">Clear Canvas 🧹</button>
        </div>
        <div id="statusMessage">Ready to weave some math magic...</div>
        <canvas id="fractalCanvas" width="900" height="650"></canvas>
    </main>
    <footer>
        <p>Crafted with Python $scriptPyEmojiGlobal, PHP $scriptPhpEmojiGlobal, JavaScript, and Pico.css</p>
    </footer>
    <script>
        const canvas = document.getElementById('fractalCanvas');
        const ctx = canvas.getContext('2d');
        const startButton = document.getElementById('startButton');
        const stopButton = document.getElementById('stopButton');
        const clearButton = document.getElementById('clearButton');
        const statusMessage = document.getElementById('statusMessage');
        let eventSource = null;
        
        let lastPos = { x: canvas.width / 2, y: canvas.height / 2 };
        let hue = Math.random() * 360;
        let pointCounter = 0;
        let animationFrameId = null;
        let dataBuffer = [];
        const MAX_BUFFER_SIZE = 8; 
        const TRAIL_EFFECT = true; // JS const for trail effect
        const FADE_ALPHA = 0.03; 

        function clearCanvas(fullClear = true) {
            if (animationFrameId) cancelAnimationFrame(animationFrameId);
            dataBuffer = [];
            if (fullClear) {
                ctx.fillStyle = '#0d1117';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
            }
            lastPos = { x: canvas.width * (0.4 + Math.random() * 0.2), y: canvas.height * (0.4 + Math.random() * 0.2) };
            hue = Math.random() * 360;
            pointCounter = 0;
            statusMessage.textContent = "Canvas cleared. Ready for a new vision.";
        }
        clearCanvas(); 

        function drawDataPoint(data) {
            const value = parseFloat(data.output_value);
            const depth = parseInt(data.depth);
            const scriptId = parseInt(data.script_id);
            const opType = data.op_type || 'unknown';

            let normValue = (value % 200 - 100) / 100;
            if (isNaN(normValue)) normValue = Math.random() * 2 - 1;

            let angleOffset = 0;
            if (opType.includes('sine') || opType.includes('cos')) angleOffset = Math.PI / 4;
            if (opType.includes('spiral')) angleOffset = scriptId * 0.01;

            const angle = (normValue * Math.PI * 1.5) + (scriptId % 180 * Math.PI / 90) + (depth * 0.15 * (pointCounter % 3 === 0 ? 1 : -1)) + angleOffset;
            const distance = 2 + (Math.abs(normValue) * 18) + (depth * 3.0) + (Math.cos(pointCounter * 0.03) * 5) + (scriptId % 5); 
            
            const newX = lastPos.x + Math.cos(angle) * distance;
            const newY = lastPos.y + Math.sin(angle) * distance;
            
            ctx.beginPath(); ctx.moveTo(lastPos.x, lastPos.y);
            
            hue = (hue + Math.abs(scriptId % 15 - 7.5) + (normValue * 12) + (depth * 0.7) ) % 360;
            const saturation = Math.min(100, 45 + (depth * 9) + Math.abs(normValue)*35); 
            const lightness = Math.min(90, Math.max(25, 60 + (normValue * 20) - (depth*2.5) )); 
            
            ctx.strokeStyle = \`hsla(\${hue}, \${saturation}%, \${lightness}%, \${TRAIL_EFFECT ? 0.45 : 0.65})\`;
            ctx.lineWidth = Math.max(0.1, 0.2 + depth * 0.18 + Math.abs(normValue) * 0.4); 
            ctx.lineTo(newX, newY); ctx.stroke();
            
            if (pointCounter % (TRAIL_EFFECT ? 8 : 4) === 0) {
                ctx.beginPath(); 
                ctx.arc(newX, newY, Math.max(0.2, 0.5 + depth * 0.12), 0, Math.PI * 2);
                ctx.fillStyle = \`hsla(\${(hue + 35) % 360}, \${saturation}%, \${Math.min(95, lightness + 10)}%, \${TRAIL_EFFECT ? 0.6 : 0.8})\`;
                ctx.fill();
            }

            if (newX > canvas.width || newX < 0) lastPos.x = Math.random() * canvas.width; 
            else lastPos.x = newX;

            if (newY > canvas.height || newY < 0) lastPos.y = Math.random() * canvas.height;
            else lastPos.y = newY;
            
            pointCounter++;
        }

        function animationLoop() {
            if (TRAIL_EFFECT) {
                ctx.fillStyle = \`rgba(17, 17, 23, \${FADE_ALPHA})\`;
                ctx.fillRect(0, 0, canvas.width, canvas.height);
            }
            let processedCount = 0;
            while(dataBuffer.length > 0 && processedCount < MAX_BUFFER_SIZE) {
                const data = dataBuffer.shift();
                drawDataPoint(data);
                processedCount++;
            }
            if (dataBuffer.length > 0 || (eventSource && eventSource.readyState !== EventSource.CLOSED)) {
                 animationFrameId = requestAnimationFrame(animationLoop);
            } else {
                animationFrameId = null; 
            }
        }

        startButton.addEventListener('click', () => {
            if (eventSource) eventSource.close(); 
            if (animationFrameId) cancelAnimationFrame(animationFrameId);
            clearCanvas(true); 
            statusMessage.textContent = "Conjuring data stream from orchestrator...";
            eventSource = new EventSource('fractal_orchestrator.php'); 
            startButton.disabled = true;
            stopButton.disabled = false;
            animationFrameId = requestAnimationFrame(animationLoop); 

            eventSource.onmessage = function(event) {
                try {
                    const data = JSON.parse(event.data);
                    if (data.status && data.status.includes('finished')) {
                        statusMessage.textContent = "Stream finished: " + data.status;
                        console.log("Orchestrator reported:", data.status);
                        if (eventSource) eventSource.close();
                        startButton.disabled = false;
                        stopButton.disabled = true;
                        return;
                    }
                    if(data.script_id !== undefined) {
                        dataBuffer.push(data);
                        if(pointCounter % 20 === 0) statusMessage.textContent = \`Processing script #\${data.script_id} (depth: \${data.depth}). Buffer: \${dataBuffer.length}\`;
                    } else if (data.error) {
                        console.error("Orchestrator error:", data.error, data.details || '');
                        statusMessage.textContent = "Error from orchestrator: " + data.error;
                    }
                } catch (e) { console.error("Error parsing SSE data:", e, event.data); statusMessage.textContent = "Error parsing data.";}
            };
            eventSource.onerror = function(err) {
                console.error("EventSource failed:", err);
                statusMessage.textContent = "EventSource connection failed or server stream ended.";
                if(eventSource) eventSource.close();
                startButton.disabled = false; stopButton.disabled = true;
                if (animationFrameId) cancelAnimationFrame(animationFrameId);
            };
        });
        stopButton.addEventListener('click', () => {
            if (eventSource) { eventSource.close(); console.log("EventSource closed."); statusMessage.textContent = "Stream stopped by user.";}
            if (animationFrameId) cancelAnimationFrame(animationFrameId);
            startButton.disabled = false; stopButton.disabled = true;
        });
        clearButton.addEventListener('click', () => clearCanvas(true));
    </script>
</body>
</html>
HTML;
}

// --- Helper: Content for fractal_orchestrator.php ---
// (No changes needed here based on the errors, but using $GLOBALS for emojis passed as params)
function getFractalOrchestratorPhpContent($pythonSubfolderName, $numPythonScripts, $maxCallDepth, $maxRuntime) {
    // (Content from previous fully correct version)
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
for (\$i = 0; \$i < 7; ++\$i) { 
    \$start_id = rand(0, \$numPythonScripts - 1);
    \$start_val = (rand(-2000, 2000) / 100.0); 
    \$queue[] = ['id' => \$start_id, 'value' => \$start_val, 'depth' => 0];
}

\$total_events_sent_this_run = 0;
\$loop_iterations = 0;

while (!empty(\$queue)) {
    \$loop_iterations++;
    if ((time() - \$startTime) >= \$maxOrchestratorRuntime) {
        send_event(['status' => 'Orchestrator reached max runtime (' . \$maxOrchestratorRuntime . 's). Total events: ' . \$total_events_sent_this_run]);
        error_log("Fractal_Orchestrator max runtime. Events: {\$total_events_sent_this_run}, Iterations: {\$loop_iterations}");
        exit(0);
    }
    if (connection_aborted()) {
        error_log("Client disconnected, stopping fractal_orchestrator. Events: {\$total_events_sent_this_run}");
        break;
    }

    \$batch_size = min(count(\$queue), 10); 
    for(\$b = 0; \$b < \$batch_size; \$b++) {
        if (empty(\$queue)) break;
        \$current_call = array_shift(\$queue);
        \$script_id_num = \$current_call['id']; \$input_value = \$current_call['value']; \$depth = \$current_call['depth'];
        \$script_name = sprintf("script_%03d.py", \$script_id_num);
        \$script_path = \$pythonProjectFolder . DIRECTORY_SEPARATOR . \$script_name;

        if (!file_exists(\$script_path)) {
            send_event(['error' => "Script not found: {\$script_path} from " . getcwd(), 'id' => \$script_id_num]);
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
                if (is_array(\$json_data) && isset(\$json_data['script_id'])) { 
                    send_event(\$json_data);
                    \$total_events_sent_this_run++;
                    if (isset(\$json_data['next_call_id']) && \$json_data['next_call_id'] !== null && \$json_data['depth'] < (\$maxCallDepth -1) && is_numeric(\$json_data['next_call_id']) && \$json_data['next_call_id'] >= 0 && \$json_data['next_call_id'] < \$numPythonScripts) {
                        \$next_val = \$json_data['output_value'];
                        if (abs(\$next_val - \$input_value) < 0.01 || abs(\$next_val) < 0.01 ) {
                            \$next_val += (rand(-100,100)/50.0) * (\$depth + 1); 
                            if (abs(\$next_val) < 0.01 && \$next_val != 0) \$next_val = \$next_val > 0 ? 0.1 : -0.1;
                        }
                        if(count(\$queue) < 200) { 
                           \$queue[] = ['id' => (int)\$json_data['next_call_id'], 'value' => \$next_val, 'depth' => \$json_data['depth'] + 1];
                        }
                    }
                }
            }
        } else {
            send_event(['error' => "Script {\$script_name} failed. Ret: {\$return_var}", 'details' => \$output_str, 'id' => \$script_id_num]);
        }
    } 
    usleep(10000); 
}
send_event(['status' => 'Orchestration loop finished or queue empty. Total events: ' . \$total_events_sent_this_run]);
error_log("Fractal_Orchestrator loop finished. Total events: {\$total_events_sent_this_run}, Iterations: {\$loop_iterations}");
?>
PHP;
}

// --- Helper: Content for individual Python scripts ---
// (Using the version from previous response, with M_PI fix and float casting in rand ranges)
function getPythonScriptContent($scriptId, $numTotalScripts, $maxCallDepth) {
    $ops = [
        ['name' => 'clifford_attractor_x', 'lambda' => 'math.sin(modifier_a * prev_val_placeholder) + modifier_c * math.cos(modifier_a * val)', 'mod_range' => [], 'custom_modifiers' => ['a' => [-2.0, 2.0, 0.01], 'c' => [-2.0, 2.0, 0.01]]],
        ['name' => 'clifford_attractor_y', 'lambda' => 'math.sin(modifier_b * val) + modifier_d * math.cos(modifier_b * prev_val_placeholder)', 'mod_range' => [], 'custom_modifiers' => ['b' => [-2.0, 2.0, 0.01], 'd' => [-2.0, 2.0, 0.01]]],
        ['name' => 'de_jong_attractor_x', 'lambda' => 'math.sin(modifier_a * prev_val_placeholder) - math.cos(modifier_b * val)', 'mod_range' => [], 'custom_modifiers' => ['a' => [-3.0, 3.0, 0.01], 'b' => [-3.0, 3.0, 0.01]]],
        ['name' => 'de_jong_attractor_y', 'lambda' => 'math.sin(modifier_c * val) - math.cos(modifier_d * prev_val_placeholder)', 'mod_range' => [], 'custom_modifiers' => ['c' => [-3.0, 3.0, 0.01], 'd' => [-3.0, 3.0, 0.01]]],
        ['name' => 'sine_power_mix', 'lambda' => '(math.sin(val * modifier_freq1) ** int(modifier_pow1)) * modifier_amp1 + (math.cos(prev_val_placeholder * modifier_freq2) ** int(modifier_pow2)) * modifier_amp2', 'mod_range' => [],
            'custom_modifiers' => [
                'freq1' => [0.1, 3.0, 0.01], 'pow1' => [1, 3], 'amp1' => [0.5, 5.0, 0.1],
                'freq2' => [0.1, 3.0, 0.01], 'pow2' => [1, 3], 'amp2' => [0.5, 5.0, 0.1]
            ]],
        ['name' => 'swirl_step', 'lambda' => 'val + math.sin(val * 0.1 + current_depth * 0.05 + script_id * 0.01) * modifier_strength + prev_val_placeholder * 0.1', 'mod_range' => [], 'custom_modifiers' => ['strength' => [0.5, 8.0, 0.1]]],
        ['name' => 'logistic_growth', 'lambda' => 'modifier_r * val * (1.0 - val / modifier_k if modifier_k != 0 else 1.0 - val)', 'mod_range' => [], 'custom_modifiers' => ['r' => [2.8, 4.0, 0.001], 'k' => [10.0, 100.0, 1.0]]],
        ['name' => 'coupled_logistic_x', 'lambda' => 'modifier_rx * val * (1.0 - val) - modifier_cxy * val * prev_val_placeholder', 'mod_range' => [], 'custom_modifiers' => ['rx' => [3.5, 4.0, 0.001], 'cxy' => [0.01, 0.2, 0.001]]],
        ['name' => 'lyapunov_exponent_approx', 'lambda' => 'val + math.log(abs(modifier_r - 2.0 * modifier_r * prev_val_placeholder_norm if prev_val_placeholder_norm is not None else modifier_r)) if (modifier_r - 2.0 * modifier_r * (prev_val_placeholder_norm if prev_val_placeholder_norm is not None else 0.5)) != 0 else val',
            'mod_range' => [], 'custom_modifiers' => ['r' => [3.5, 4.0, 0.001]]],
    ];
    $chosen_op_data = $ops[array_rand($ops)];
    $op_name = $chosen_op_data['name']; $op_lambda = $chosen_op_data['lambda'];
    $modifier_definitions = ""; $modifier_values_dict_content = ""; $modifier_display_values = "";

    if (isset($chosen_op_data['custom_modifiers'])) {
        $temp_mod_dict_parts = [];
        foreach($chosen_op_data['custom_modifiers'] as $mod_name => $range) {
            $min_r = (float)$range[0]; 
            $max_r = (float)$range[1]; // M_PI fix applied here already
            
            $step_r = isset($range[2]) ? (float)$range[2] : (($max_r - $min_r > 1.0) ? 1.0 : 0.01);
            if ($step_r == 0) $step_r = ($max_r - $min_r > 1.0) ? 0.1 : 0.001;

            $rand_min_scaled = (int)floor($min_r / $step_r);
            $rand_max_scaled = (int)floor($max_r / $step_r);
            if ($rand_min_scaled > $rand_max_scaled) list($rand_min_scaled, $rand_max_scaled) = [$rand_max_scaled, $rand_min_scaled];
            
            $mod_val = ($rand_min_scaled == $rand_max_scaled) ? $min_r : round(rand($rand_min_scaled, $rand_max_scaled) * $step_r, 4);

            $modifier_definitions .= "    modifier_{$mod_name} = float({$mod_val})\n";
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
            $modifier = ($rand_min_s_scaled == $rand_max_s_scaled) ? $min_r_single :round(rand($rand_min_s_scaled, $rand_max_s_scaled) * $step_r_single, 3);
        } else { $modifier = (float)rand((int)$min_r_single, (int)$max_r_single); }
        if ($modifier == 0 && ($op_name == 'divide')) $modifier = (rand(0,1)==0 ? 0.01 : -0.01);
        $modifier_definitions = "    modifier = float({$modifier})";
        $modifier_values_dict_content = "\"modifier\": modifier";
        $modifier_display_values = "modifier={$modifier}";
    }
    $modifier_values_dict = "{".$modifier_values_dict_content."}";

    $will_call_next = (rand(1, 100) <= 78);
    $next_script_id_expr = 'None';
    if ($will_call_next) {
        $offset_direction = rand(0,1) == 0 ? -1 : 1; $offset_amount = rand(1, max(1, (int)($numTotalScripts / 8)));
        $next_id_raw = ($scriptId + ($offset_direction * $offset_amount) + $numTotalScripts) % $numTotalScripts;
        $next_script_id_expr = (string)$next_id_raw;
    }
    $prev_val_placeholder_code = 'random.uniform(-0.5, 0.5) # General small random influence';
    $prev_val_norm_code = '(prev_val_placeholder % 1.0) if prev_val_placeholder is not None else 0.5 # Normalized for Lyapunov';
    if(!empty($modifier_definitions) && substr($modifier_definitions, -strlen("\n")) !== "\n") {
        $modifier_definitions .= "\n";
    }
    $op_lambda_final = str_replace('prev_val_placeholder_norm', $prev_val_norm_code, $op_lambda);

    return <<<PYTHON
import sys, json, math, random
# Script ID $scriptId: Op '$op_name', Modifiers: $modifier_display_values
op_name = "$op_name" 

def perform_operation(val, current_depth, script_id, prev_val_placeholder, **modifiers):
    for key, value in modifiers.items(): globals()[key] = float(value) 
    current_val_for_map = val 
    if op_name == "logistic_growth": 
         k_val = modifiers.get("modifier_k", 1.0)
         if k_val == 0: k_val = 1.0
         current_val_for_map = (abs(val) % k_val) / k_val 
         val = current_val_for_map 
    elif op_name.startswith("ikeda_map"):
        tn = 0.4 - 6.0 / (1.0 + val**2 + prev_val_placeholder**2)
    try: 
        result = $op_lambda_final
        return result
    except Exception as e: 
        return val + random.uniform(-5.0, 5.0) 

if __name__ == "__main__":
    if len(sys.argv) < 5: print(json.dumps({"error": "Insufficient args", "script_id": $scriptId})); sys.exit(1)
    input_value, current_depth, num_total_scripts, max_allowed_depth = float(sys.argv[1]), int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])
$modifier_definitions
    prev_val_placeholder_py = $prev_val_placeholder_code 
    output_value = perform_operation(input_value, current_depth, $scriptId, prev_val_placeholder_py, **($modifier_values_dict))
    output_value = max(-1e7, min(1e7, output_value)) 
    if math.isnan(output_value) or math.isinf(output_value): output_value = random.uniform(-200.0, 200.0)

    next_call_id_py_str = $next_script_id_expr 
    next_call_id_final = None 
    if next_call_id_py_str != 'None' and current_depth < (max_allowed_depth -1) and $will_call_next:
        try:
            parsed_next_id = int(next_call_id_py_str)
            if 0 <= parsed_next_id < num_total_scripts: 
                 next_call_id_final = parsed_next_id
            else: 
                 next_call_id_final = random.randint(0, num_total_scripts - 1)
        except (ValueError, TypeError): 
            next_call_id_final = random.randint(0, num_total_scripts - 1) 
    
    print(json.dumps({
        "script_id":$scriptId, "input_value":input_value, "op_type":"$op_name", 
        "modifiers_used": $modifier_values_dict, "output_value":output_value, 
        "depth":current_depth, "next_call_id":next_call_id_final, 
        "num_total_scripts":num_total_scripts
    }))
PYTHON;
}

// --- Helper: Content for stop_server.php ---
function getStopServerPhpContent($outer_serverHost, $outer_portRangeStart, $outer_portRangeEnd, $outer_serverProcessMarker, $outer_routerScriptNameForGrep) {
    global $e_info, $e_ok, $e_warn, $e_stop; 
    
    $ports_to_check_str_array = [];
    for ($p = $outer_portRangeStart; $p <= $outer_portRangeEnd; $p++) {
        $ports_to_check_str_array[] = (string)$p;
    }
    $ports_to_check_json_for_stop_script = json_encode($ports_to_check_str_array);

    $embedded_serverHost = $outer_serverHost;
    $embedded_ports_json = $ports_to_check_json_for_stop_script;
    $embedded_marker = $outer_serverProcessMarker;
    $embedded_router_name = $outer_routerScriptNameForGrep;

    $stop_e_info = $e_info; $stop_e_ok = $e_ok; $stop_e_warn = $e_warn; $stop_e_stop_glyph = $e_stop;

    return <<<PHP
<?php
// stop_fractal_server.php
\$e_info = "{$stop_e_info}"; \$e_ok = "{$stop_e_ok}"; \$e_warn = "{$stop_e_warn}"; \$e_stop_glyph = "{$stop_e_stop_glyph}";

echo "\$e_info Attempting to stop PHP built-in server(s) for the Fractal Art project...\\n";

\$php_serverHost = '$embedded_serverHost'; 
\$php_portsToCheck = json_decode('$embedded_ports_json', true);
\$php_processMarker = '$embedded_marker'; 
\$php_routerScriptName = '$embedded_router_name'; 

\$killedSomething = false;

function findAndKillProcess(\$port_to_check, \$host_to_check, \$router_script_name_to_grep_param) { // Renamed param
    global \$e_ok, \$e_warn, \$e_info; 
    if (stristr(PHP_OS, 'WIN')) {
        echo "\$e_warn On Windows, please manually stop the PHP server (php.exe) listening on port {\$port_to_check} that was serving '{\$router_script_name_to_grep_param}'. Use Task Manager or Resource Monitor.\\n";
        return false;
    } else { 
        \$grep_pattern = escapeshellarg("php -S {$host_to_check}:{\$port_to_check} {$router_script_name_to_grep_param}");
        \$cmd_find_php_server = "ps aux | grep " . \$grep_pattern . " | grep -v grep | awk '{print \$2}'";
        \$output_ps = shell_exec(\$cmd_find_php_server);
        
        if (!empty(\$output_ps)) {
            \$found_pids = array_filter(explode("\\n", trim(\$output_ps)));
            if (empty(\$found_pids) || (count(\$found_pids) == 1 && empty(trim(\$found_pids[0]))) ) {
                 echo "\$e_info No specific PHP server process found for '{\$router_script_name_to_grep_param}' on port {\$port_to_check} via ps command.\\n";
                 return false;
            }
            foreach (\$found_pids as \$pid_str) {
                \$pid = trim(\$pid_str);
                if (is_numeric(\$pid) && \$pid > 0) {
                    echo "\$e_info Found potential PHP server (PID: {\$pid}) for '{\$router_script_name_to_grep_param}' on port {\$port_to_check}. Attempting to kill...\\n";
                    exec("kill -9 " . escapeshellarg(\$pid), \$kill_output, \$kill_return);
                    if (\$kill_return === 0) {
                        echo "\$e_ok Successfully sent SIGKILL to PID {\$pid}.\\n";
                        return true; 
                    } else {
                        echo "\$e_warn Failed to kill PID {\$pid} (return: {\$kill_return}). Already stopped or no permissions?\\n";
                    }
                }
            }
        } else {
             echo "\$e_info No matching PHP server process found for '{\$router_script_name_to_grep_param}' on port {\$port_to_check} (empty ps output).\\n";
        }
    }
    return false; 
}

\$routerPathForCheck = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . \$php_routerScriptName;
if (file_exists(\$routerPathForCheck)) {
    if (strpos(file_get_contents(\$routerPathForCheck), \$php_processMarker) !== false) {
        echo "\$e_info Confirmed: Router '{\$php_routerScriptName}' contains marker '{\$php_processMarker}'.\\n";
    } else {
        echo "\$e_warn Router '{\$php_routerScriptName}' does NOT contain expected marker. PID matching on command args only.\\n";
    }
} else {
     echo "\$e_warn Router '{\$php_routerScriptName}' not found for marker check. PID matching by command args only.\\n";
}

foreach(\$php_portsToCheck as \$port_str) {
    \$port_int = (int)\$port_str;
    if(findAndKillProcess(\$port_int, \$php_serverHost, \$php_routerScriptName)) {
        \$killedSomething = true;
    }
}

if (\$killedSomething) {
    echo "\$e_ok Server stopping process complete. Please verify.\\n";
} else {
    echo "\$e_info \$e_stop_glyph No matching server processes were automatically stopped. Please stop manually if needed.\\n";
}
?>
PHP;
}

// --- Main Generation Logic ---
// Attempt to stop any old server instance from a previous run if a stop script exists
// This check is for a folder with the *same base name* from a *previous run today*.
$potentialOldProjectFolder = $mainProjectFolderNameBase; // Today's base name
$potentialOldStopScript = $potentialOldProjectFolder . DIRECTORY_SEPARATOR . $serverScriptsSubfolderName . DIRECTORY_SEPARATOR . "stop_fractal_server.php";

if (is_dir($potentialOldProjectFolder) && file_exists($potentialOldStopScript)) {
    echo "$e_broom Attempting to stop server from a previous run of '$potentialOldProjectFolder' (if any)...\n";
    // We need to execute this stop script from within its correct relative directory context if it uses relative paths.
    // Or, if the stop script is self-contained with absolute paths or robust relative paths, we can run from here.
    // The current stop script is designed to be run from its own folder (_server_utils) and find router one level up.
    // For now, let's just try to run it if it exists, user can cd if needed.
    // More robust would be to `cd` into the old project's _server_utils and run it.
    // This is a best-effort cleanup.
    if (is_executable($potentialOldStopScript) || !stristr(PHP_OS_FAMILY, "Windows")) { // Check if executable on non-Windows
        echo "$e_info Executing: php " . escapeshellarg($potentialOldStopScript) . "\n";
        system("php " . escapeshellarg($potentialOldStopScript));
        echo "$e_info Old server stop attempt finished.\n";
    } else {
        echo "$e_warn Found old stop script '$potentialOldStopScript' but it's not executable or on Windows. Skipping auto-stop.\n";
    }
    echo "---------------------------------------------------\n";
}


// Now, create the *new* unique project folder name for *this* run
$mainProjectFolderName = $mainProjectFolderNameBase . "_" . date('His'); // This makes it unique per second

echo "$e_info Main project will be created in: $mainProjectFolderName\n";

if (!is_dir($mainProjectFolderName)) {
    if (!mkdir($mainProjectFolderName, 0755, true)) {
        die("$e_warn ABORT: Failed to create main project directory: $mainProjectFolderName\n");
    }
    echo "$e_ok $e_folder Created main project directory: $mainProjectFolderName\n";
} else {
    echo "$e_info $e_folder Main project directory $mainProjectFolderName already exists (should be rare with timestamp).\n";
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
$stopScriptRelativePath = $serverScriptsSubfolderName . DIRECTORY_SEPARATOR . $stopServerScriptName;

echo "$e_info $e_terminal TO RUN THE FRACTAL ART VISUALIZATION:\n";
echo "1. This script will ATTEMPT to start the server in the background.\n";
echo "2. $e_link If successful, your browser should open to: $urlToOpen\n";
echo "3. Click 'Start Visualization' $e_rocket on the webpage.\n";
echo "$e_info $e_timer Orchestrator default runtime: ~$defaultMaxOrchestratorRuntime seconds.\n";
echo "---------------------------------------------------\n";
echo "$e_info $e_stop TO STOP THE SERVER:\n";
echo "   Run this command from the directory where this generator script is located:\n";
echo "   ( cd " . escapeshellarg($projectFullPath . DIRECTORY_SEPARATOR . $serverScriptsSubfolderName) . " && php $stopServerScriptName )\n";
echo "   OR stop 'php -S ... $routerScriptName' manually (e.g., Ctrl+C or OS process manager).\n";
echo "---------------------------------------------------\n";

$backgroundCmd = '';
$outputRedirect = (PHP_OS_FAMILY === 'Windows' ? ' > NUL 2>&1' : ' > /dev/null 2>&1');

if (PHP_OS_FAMILY === 'Windows') {
    $backgroundCmd = "start /B \"PHP Fractal Server\" cmd /c \"cd /D " . escapeshellarg($projectFullPath) . " && $serverCommand \"";
} else { 
    $backgroundCmd = "(cd " . escapeshellarg($projectFullPath) . " && exec $serverCommand $outputRedirect) &";
}

echo "$e_rocket Attempting to start PHP server in background ($serverHost:$actualServerPort)...\n";
shell_exec($backgroundCmd); 
sleep(3); 

echo "$e_eyes Checking if server started on $urlToOpen ...\n";
$context = stream_context_create(['http' => ['timeout' => 2.5, 'ignore_errors' => true]]); 
$headers = @get_headers($urlToOpen, 0, $context); 
if ($headers && isset($headers[0]) && strpos($headers[0], '200') !== false) {
    echo "$e_ok Server seems to have started successfully!\n";
} else {
    echo "$e_warn Server might not have started or is not reachable at $urlToOpen.\n";
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
    @system($opener . ' ' . escapeshellarg($urlToOpen) . ($opener === 'start ""' ? '' : ' > /dev/null 2>&1'));
    echo "$e_link If browser didn't open, please navigate to the URL manually.\n";
} else {
    echo "$e_warn Could not determine OS to auto-open browser. Please open: $urlToOpen\n";
}
echo "---------------------------------------------------\n";
echo "$e_party Script finished! $e_palette Check browser and '$mainProjectFolderName'.\n";

?>