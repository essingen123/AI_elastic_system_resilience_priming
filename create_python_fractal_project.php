<?php

// --- Configuration ---
$pythonProjectFolder = "python_fractal_test_bing_ting";
$numPythonScripts = 303; // As requested
$maxCallDepth = 5; // To prevent infinite recursion in Python calls

// --- Helper: Content for index.html ---
function getIndexHtmlContent() {
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Python Fractal Math Art 🎨</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
    <style>
        body { margin: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; background-color: #f0f0f0; }
        canvas { border: 1px solid #ccc; background-color: #fff; max-width: 90vw; max-height: 80vh; }
        header, footer { text-align: center; margin: 1em; }
        .controls { margin-bottom: 1em; }
    </style>
</head>
<body>
    <header>
        <h1>Python Fractal Math Art 🎨</h1>
        <p>Visualizing chained math operations from Python scripts.</p>
    </header>
    <main class="container">
        <div class="controls">
            <button id="startButton">Start Visualization</button>
            <button id="stopButton" disabled>Stop</button>
            <button id="clearButton">Clear Canvas</button>
        </div>
        <canvas id="fractalCanvas" width="800" height="600"></canvas>
    </main>
    <footer>
        <p>Powered by Python, PHP, and Pico.css</p>
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
        let hue = 0;

        function clearCanvas() {
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            lastX = canvas.width / 2; // Reset starting point
            lastY = canvas.height / 2;
            hue = 0;
        }
        clearCanvas(); // Initial clear

        startButton.addEventListener('click', () => {
            if (eventSource) eventSource.close(); // Close existing if any
            clearCanvas();
            
            eventSource = new EventSource('orchestrator.php');
            startButton.disabled = true;
            stopButton.disabled = false;

            eventSource.onmessage = function(event) {
                try {
                    const data = JSON.parse(event.data);
                    console.log(data); // Log for debugging

                    // Simple visualization: move a point and change its color
                    const value = parseFloat(data.output_value);
                    const depth = parseInt(data.depth);

                    // Map value to canvas coordinates (very basic example)
                    // Normalize value to be somewhat predictable for drawing
                    let normValue = (value % 100) / 100; // Normalize to 0-1 range after modulo
                    if (isNaN(normValue)) normValue = 0.5;


                    const angle = normValue * Math.PI * 2 * (data.op_type === 'multiply' ? 1.5 : 1); // Different angle for different ops
                    const distance = 10 + (depth * 5) + (Math.abs(normValue - 0.5) * 20) ; // Move further based on depth and value deviation

                    const newX = lastX + Math.cos(angle) * distance;
                    const newY = lastY + Math.sin(angle) * distance;
                    
                    ctx.beginPath();
                    ctx.moveTo(lastX, lastY);

                    hue = (hue + 5) % 360; // Cycle hue
                    const saturation = 70 + (depth * 5); // More saturated for deeper calls
                    const lightness = 50 + (normValue - 0.5) * 20; // Lighter/darker based on value

                    ctx.strokeStyle = `hsl(\${hue}, \${saturation}%, \${lightness}%)`;
                    ctx.lineWidth = 1 + depth * 0.5; // Thicker lines for deeper calls
                    ctx.lineTo(newX, newY);
                    ctx.stroke();

                    // Draw a small circle at the new point
                    ctx.beginPath();
                    ctx.arc(newX, newY, 2 + depth, 0, Math.PI * 2);
                    ctx.fillStyle = `hsl(\${hue}, \${saturation}%, \${lightness - 10}%)`;
                    ctx.fill();

                    lastX = newX > canvas.width || newX < 0 ? canvas.width / 2 : newX; // Reset if out of bounds
                    lastY = newY > canvas.height || newY < 0 ? canvas.height / 2 : newY;

                } catch (e) {
                    console.error("Error parsing or drawing data:", e, event.data);
                }
            };

            eventSource.onerror = function(err) {
                console.error("EventSource failed:", err);
                eventSource.close();
                startButton.disabled = false;
                stopButton.disabled = true;
            };
        });

        stopButton.addEventListener('click', () => {
            if (eventSource) {
                eventSource.close();
                console.log("EventSource closed.");
            }
            startButton.disabled = false;
            stopButton.disabled = true;
        });
        
        clearButton.addEventListener('click', clearCanvas);

    </script>
</body>
</html>
HTML;
}

// --- Helper: Content for orchestrator.php ---
function getOrchestratorPhpContent($pythonProjectFolder, $numPythonScripts, $maxCallDepth) {
    return <<<PHP
<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

\$pythonProjectFolder = "$pythonProjectFolder";
\$numPythonScripts = $numPythonScripts;
\$maxCallDepth = $maxCallDepth;

// Buffer all output
ob_implicit_flush(true);

function send_event(\$data) {
    echo "data: " . json_encode(\$data) . "\\n\\n";
    if (ob_get_level() > 0) {
        ob_flush();
    }
    flush();
}

// Queue for (script_id, input_value, current_depth)
\$queue = [];

// Start with a few initial calls
for (\$i = 0; \$i < 3; ++\$i) { // Start 3 parallel "branches"
    \$initial_script_id = rand(0, \$numPythonScripts - 1);
    \$initial_value = (rand(10, 100) / 10.0) * (rand(0,1) == 0 ? 1 : -1) ; // Random float between -10 and 10
    \$queue[] = ['id' => \$initial_script_id, 'value' => \$initial_value, 'depth' => 0];
}


\$processed_count = 0;
\$max_events = 500; // Limit total events for a single run to prevent browser overload

while (!empty(\$queue) && \$processed_count < \$max_events) {
    if (connection_aborted()) {
        error_log("Client disconnected, stopping orchestrator.");
        break;
    }

    \$current_call = array_shift(\$queue); // Get the next call (BFS-like)
    \$script_id_num = \$current_call['id'];
    \$input_value = \$current_call['value'];
    \$depth = \$current_call['depth'];

    \$script_name = sprintf("script_%03d.py", \$script_id_num);
    \$script_path = \$pythonProjectFolder . DIRECTORY_SEPARATOR . \$script_name;

    if (!file_exists(\$script_path)) {
        send_event(['error' => "Script not found: {\$script_path}"]);
        continue;
    }

    // Escape arguments for security
    \$escaped_script_path = escapeshellarg(\$script_path);
    \$escaped_input_value = escapeshellarg((string)\$input_value);
    \$escaped_depth = escapeshellarg((string)\$depth);
    \$escaped_num_scripts = escapeshellarg((string)\$numPythonScripts);
    \$escaped_max_depth = escapeshellarg((string)\$maxCallDepth);

    // Use python3 if available, otherwise python
    \$python_executable = 'python3';
    @exec('python3 --version 2>&1', \$py3_output, \$py3_ret);
    if (\$py3_ret !== 0) {
        \$python_executable = 'python';
    }
    
    \$command = sprintf(
        "%s %s %s %s %s %s", 
        \$python_executable,
        \$escaped_script_path, 
        \$escaped_input_value, 
        \$escaped_depth,
        \$escaped_num_scripts,
        \$escaped_max_depth
    );
    
    \$output = null;
    \$return_var = null;
    exec(\$command . ' 2>&1', \$output_lines, \$return_var); // Capture stderr as well
    \$output_str = implode("\\n", \$output_lines);

    if (\$return_var === 0) {
        // Python script should output one line of JSON per call it makes/processes
        foreach (\$output_lines as \$line) {
            \$json_data = json_decode(\$line, true);
            if (is_array(\$json_data)) {
                send_event(\$json_data);
                // If this script decided to call another, add it to the queue
                if (isset(\$json_data['next_call_id']) && isset(\$json_data['output_value']) && \$json_data['depth'] < \$maxCallDepth) {
                    if (\$json_data['next_call_id'] !== null && \$json_data['next_call_id'] < \$numPythonScripts){ // Ensure next_call_id is valid
                        \$queue[] = [
                            'id' => \$json_data['next_call_id'], 
                            'value' => \$json_data['output_value'], 
                            'depth' => \$json_data['depth'] + 1
                        ];
                    }
                }
                 \$processed_count++;
                 if(\$processed_count >= \$max_events) break;
            } else {
                // send_event(['warning' => "Non-JSON output from script {\$script_name}: {\$line}"]);
            }
        }
    } else {
        send_event(['error' => "Script {\$script_name} execution failed. Ret: {\$return_var}", 'details' => \$output_str]);
    }
    
    usleep(50000); // 50ms delay to not overwhelm the browser and make it visible
}

send_event(['status' => 'Orchestration finished or max events reached.']);
error_log("Orchestrator finished. Processed events: {\$processed_count}");

?>
PHP;
}

// --- Helper: Content for individual Python scripts ---
function getPythonScriptContent($scriptId, $numTotalScripts, $maxCallDepth) {
    // Define a few math operations
    $ops = [
        ['name' => 'add', 'lambda' => 'val + modifier', 'mod_range' => [-5, 5]],
        ['name' => 'subtract', 'lambda' => 'val - modifier', 'mod_range' => [-5, 5]],
        ['name' => 'multiply', 'lambda' => 'val * modifier', 'mod_range' => [0.5, 2.0, 0.1]], // [min, max, step for float]
        ['name' => 'divide', 'lambda' => 'val / modifier if modifier != 0 else val + 0.1', 'mod_range' => [0.5, 2.0, 0.1]],
        ['name' => 'sine_wave', 'lambda' => 'math.sin(val) * modifier', 'mod_range' => [1, 5]],
        ['name' => 'cosine_wave', 'lambda' => 'math.cos(val) * modifier', 'mod_range' => [1, 5]],
        ['name' => 'tangent_clip', 'lambda' => 'max(-10, min(10, math.tan(val / modifier))) if modifier != 0 else val', 'mod_range' => [1, 5]], // Clipped tan
        ['name' => 'power', 'lambda' => 'math.copysign(pow(abs(val), modifier), val) if val != 0 else 0', 'mod_range' => [0.5, 1.5, 0.1]], // Keep sign
        ['name' => 'modulo_shift', 'lambda' => '(val + modifier) % 10 if modifier != 0 else val % 10', 'mod_range' => [1, 5]],
    ];

    $chosen_op_data = $ops[array_rand($ops)];
    $op_name = $chosen_op_data['name'];
    $op_lambda = $chosen_op_data['lambda'];
    
    if (count($chosen_op_data['mod_range']) === 3) { // float range
        $mod_min = $chosen_op_data['mod_range'][0];
        $mod_max = $chosen_op_data['mod_range'][1];
        $mod_step = $chosen_op_data['mod_range'][2];
        $modifier = round(rand($mod_min / $mod_step, $mod_max / $mod_step) * $mod_step, 2);
    } else { // int range
        $modifier = rand($chosen_op_data['mod_range'][0], $chosen_op_data['mod_range'][1]);
    }
    if ($modifier == 0 && ($op_name == 'divide' || $op_name == 'tangent_clip' || $op_name == 'modulo_shift')) {
        $modifier = 0.1; // Avoid division by zero for specific ops
    }


    // Decide if this script makes a subsequent call
    $will_call_next = (rand(1, 100) <= 60); // 60% chance to call another script if depth allows
    $next_script_id_expr = 'None';
    if ($will_call_next) {
        // Try to call a script "nearby" or a bit further, with some randomness
        $offset_direction = rand(0,1) == 0 ? -1 : 1;
        $offset_amount = rand(1, (int)($numTotalScripts / 20) + 5); // Smaller, more local jumps generally
        $next_id_raw = ($scriptId + ($offset_direction * $offset_amount)) % $numTotalScripts;
        if ($next_id_raw < 0) $next_id_raw += $numTotalScripts;
        $next_script_id_expr = (string)$next_id_raw;
    }
    
    return <<<PYTHON
import sys
import json
import math
import random

def perform_operation(val, modifier):
    try:
        # This specific script (ID $scriptId) uses: $op_name with modifier $modifier
        # Lambda: $op_lambda
        return $op_lambda
    except Exception as e:
        # Fallback if math operation fails (e.g. overflow, domain error)
        return val + random.uniform(-0.1, 0.1) # Slight perturbation

if __name__ == "__main__":
    if len(sys.argv) < 5:
        print(json.dumps({"error": "Insufficient arguments", "script_id": $scriptId}))
        sys.exit(1)

    input_value = float(sys.argv[1])
    current_depth = int(sys.argv[2])
    num_total_scripts = int(sys.argv[3]) # Passed by orchestrator
    max_allowed_depth = int(sys.argv[4]) # Passed by orchestrator

    modifier_val = $modifier
    output_value = perform_operation(input_value, modifier_val)

    # Cap output value to prevent extreme explosion, helps visualization
    output_value = max(-1000.0, min(1000.0, output_value))
    if math.isnan(output_value) or math.isinf(output_value):
        output_value = random.uniform(-1.0, 1.0) # Reset if problematic

    next_call_id = None
    if current_depth < max_allowed_depth:
        will_call_next_py = $will_call_next # True or False from PHP
        if will_call_next_py:
            # This calculation is now deterministic based on PHP's generation for this script
            next_call_id = $next_script_id_expr 
            # Ensure it's within bounds (PHP should also do this, but double check)
            if next_call_id is not None and (next_call_id < 0 or next_call_id >= num_total_scripts):
                 next_call_id = random.randint(0, num_total_scripts - 1) # Fallback random if calculated is bad
    
    result = {
        "script_id": $scriptId,
        "input_value": input_value,
        "op_type": "$op_name",
        "modifier_used": modifier_val,
        "output_value": output_value,
        "depth": current_depth,
        "next_call_id": next_call_id, # Python script tells orchestrator who to call next
        "num_total_scripts": num_total_scripts # For context
    }
    print(json.dumps(result))

PYTHON;
}


// --- Main Generation Logic ---
echo "Starting project generation...\n";

// Create Python project folder
if (!is_dir($pythonProjectFolder)) {
    if (mkdir($pythonProjectFolder, 0755, true)) {
        echo "Created directory: $pythonProjectFolder\n";
    } else {
        die("Failed to create directory: $pythonProjectFolder\n");
    }
} else {
    echo "Directory $pythonProjectFolder already exists.\n";
}

// Create Python scripts
for ($i = 0; $i < $numPythonScripts; $i++) {
    $scriptName = sprintf("script_%03d.py", $i);
    $scriptPath = $pythonProjectFolder . DIRECTORY_SEPARATOR . $scriptName;
    $pythonContent = getPythonScriptContent($i, $numPythonScripts, $maxCallDepth);
    if (file_put_contents($scriptPath, $pythonContent)) {
        if ($i % 50 == 0 && $i > 0) echo "."; // Progress indicator
    } else {
        echo "Failed to write Python script: $scriptPath\n";
    }
}
echo "\nGenerated $numPythonScripts Python scripts in $pythonProjectFolder.\n";

// Create index.html
$indexHtmlPath = "index.html";
if (file_put_contents($indexHtmlPath, getIndexHtmlContent())) {
    echo "Generated $indexHtmlPath.\n";
} else {
    echo "Failed to write $indexHtmlPath.\n";
}

// Create orchestrator.php
$orchestratorPath = "orchestrator.php";
if (file_put_contents($orchestratorPath, getOrchestratorPhpContent($pythonProjectFolder, $numPythonScripts, $maxCallDepth))) {
    echo "Generated $orchestratorPath.\n";
} else {
    echo "Failed to write $orchestratorPath.\n";
}

echo "Project generation complete!\n";
echo "To run:\n";
echo "1. Open index.html in your browser.\n";
echo "2. Click the 'Start Visualization' button.\n";
echo "(This will start orchestrator.php in the background via EventSource).\n";
echo "Ensure PHP and Python (preferably Python 3) are installed and in your system's PATH.\n";

?>