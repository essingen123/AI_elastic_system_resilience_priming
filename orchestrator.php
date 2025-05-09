<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

$pythonProjectFolder = "python_fractal_test_bing_ting";
$numPythonScripts = 303;
$maxCallDepth = 5;

// Buffer all output
ob_implicit_flush(true);

function send_event($data) {
    echo "data: " . json_encode($data) . "\n\n";
    if (ob_get_level() > 0) {
        ob_flush();
    }
    flush();
}

// Queue for (script_id, input_value, current_depth)
$queue = [];

// Start with a few initial calls
for ($i = 0; $i < 3; ++$i) { // Start 3 parallel "branches"
    $initial_script_id = rand(0, $numPythonScripts - 1);
    $initial_value = (rand(10, 100) / 10.0) * (rand(0,1) == 0 ? 1 : -1) ; // Random float between -10 and 10
    $queue[] = ['id' => $initial_script_id, 'value' => $initial_value, 'depth' => 0];
}


$processed_count = 0;
$max_events = 500; // Limit total events for a single run to prevent browser overload

while (!empty($queue) && $processed_count < $max_events) {
    if (connection_aborted()) {
        error_log("Client disconnected, stopping orchestrator.");
        break;
    }

    $current_call = array_shift($queue); // Get the next call (BFS-like)
    $script_id_num = $current_call['id'];
    $input_value = $current_call['value'];
    $depth = $current_call['depth'];

    $script_name = sprintf("script_%03d.py", $script_id_num);
    $script_path = $pythonProjectFolder . DIRECTORY_SEPARATOR . $script_name;

    if (!file_exists($script_path)) {
        send_event(['error' => "Script not found: {$script_path}"]);
        continue;
    }

    // Escape arguments for security
    $escaped_script_path = escapeshellarg($script_path);
    $escaped_input_value = escapeshellarg((string)$input_value);
    $escaped_depth = escapeshellarg((string)$depth);
    $escaped_num_scripts = escapeshellarg((string)$numPythonScripts);
    $escaped_max_depth = escapeshellarg((string)$maxCallDepth);

    // Use python3 if available, otherwise python
    $python_executable = 'python3';
    @exec('python3 --version 2>&1', $py3_output, $py3_ret);
    if ($py3_ret !== 0) {
        $python_executable = 'python';
    }
    
    $command = sprintf(
        "%s %s %s %s %s %s", 
        $python_executable,
        $escaped_script_path, 
        $escaped_input_value, 
        $escaped_depth,
        $escaped_num_scripts,
        $escaped_max_depth
    );
    
    $output = null;
    $return_var = null;
    exec($command . ' 2>&1', $output_lines, $return_var); // Capture stderr as well
    $output_str = implode("\n", $output_lines);

    if ($return_var === 0) {
        // Python script should output one line of JSON per call it makes/processes
        foreach ($output_lines as $line) {
            $json_data = json_decode($line, true);
            if (is_array($json_data)) {
                send_event($json_data);
                // If this script decided to call another, add it to the queue
                if (isset($json_data['next_call_id']) && isset($json_data['output_value']) && $json_data['depth'] < $maxCallDepth) {
                    if ($json_data['next_call_id'] !== null && $json_data['next_call_id'] < $numPythonScripts){ // Ensure next_call_id is valid
                        $queue[] = [
                            'id' => $json_data['next_call_id'], 
                            'value' => $json_data['output_value'], 
                            'depth' => $json_data['depth'] + 1
                        ];
                    }
                }
                 $processed_count++;
                 if($processed_count >= $max_events) break;
            } else {
                // send_event(['warning' => "Non-JSON output from script {$script_name}: {$line}"]);
            }
        }
    } else {
        send_event(['error' => "Script {$script_name} execution failed. Ret: {$return_var}", 'details' => $output_str]);
    }
    
    usleep(50000); // 50ms delay to not overwhelm the browser and make it visible
}

send_event(['status' => 'Orchestration finished or max events reached.']);
error_log("Orchestrator finished. Processed events: {$processed_count}");

?>