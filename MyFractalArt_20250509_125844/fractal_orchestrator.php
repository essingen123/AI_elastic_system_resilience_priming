<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
set_time_limit(0); // Allow script to run indefinitely

$pythonProjectFolder = "python_fractal_test_bing_ting"; // This is now just the subfolder name
$numPythonScripts = 303;
$maxCallDepth = 5;

ob_implicit_flush(true);

function send_event($data) {
    echo "data: " . json_encode($data) . "\n\n";
    if (ob_get_level() > 0) ob_flush();
    flush();
}

$queue = [];
for ($i = 0; $i < 3; ++$i) {
    $queue[] = ['id' => rand(0, $numPythonScripts - 1), 'value' => (rand(10, 100) / 10.0) * (rand(0,1) == 0 ? 1 : -1), 'depth' => 0];
}

$processed_count = 0;
$max_events = 500; 

while (!empty($queue) && $processed_count < $max_events) {
    if (connection_aborted()) {
        error_log("Client disconnected, stopping fractal_orchestrator.");
        break;
    }

    $current_call = array_shift($queue);
    $script_id_num = $current_call['id'];
    $input_value = $current_call['value'];
    $depth = $current_call['depth'];

    // Path to script is now relative to this orchestrator script
    $script_name = sprintf("script_%03d.py", $script_id_num);
    $script_path = $pythonProjectFolder . DIRECTORY_SEPARATOR . $script_name;

    if (!file_exists($script_path)) {
        send_event(['error' => "Script not found: {$script_path} from " . getcwd()]);
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
            if (is_array($json_data)) {
                send_event($json_data);
                if (isset($json_data['next_call_id']) && $json_data['next_call_id'] !== null && $json_data['depth'] < $maxCallDepth && $json_data['next_call_id'] < $numPythonScripts) {
                    $queue[] = ['id' => $json_data['next_call_id'], 'value' => $json_data['output_value'], 'depth' => $json_data['depth'] + 1];
                }
                 $processed_count++;
                 if($processed_count >= $max_events) break;
            }
        }
    } else {
        send_event(['error' => "Script {$script_name} failed. Ret: {$return_var}", 'details' => $output_str]);
    }
    usleep(50000); 
}
send_event(['status' => 'Orchestration finished. Processed: ' . $processed_count]);
error_log("Fractal_Orchestrator finished. Processed events: {$processed_count}");
?>