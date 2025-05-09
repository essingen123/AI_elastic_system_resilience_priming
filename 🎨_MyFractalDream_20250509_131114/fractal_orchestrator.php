<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
set_time_limit(0); 

$pythonProjectFolder = "python_fractal_weavers"; 
$numPythonScripts = 303;
$maxCallDepth = 5;
$maxOrchestratorRuntime = 120; 

ob_implicit_flush(true);
$startTime = time();

function send_event($data) {
    echo "data: " . json_encode($data) . "\n\n";
    if (ob_get_level() > 0) ob_flush();
    flush();
}

$queue = [];
for ($i = 0; $i < 5; ++$i) {
    $queue[] = ['id' => rand(0, $numPythonScripts - 1), 'value' => (rand(-1000, 1000) / 100.0), 'depth' => 0];
}

$processed_count = 0;
$max_events_per_burst = 700; 

while (!empty($queue) && $processed_count < $max_events_per_burst) { // Loop condition simplified for clarity, time check inside
    if ((time() - $startTime) >= $maxOrchestratorRuntime) {
        send_event(['status' => 'Orchestrator reached max runtime (' . $maxOrchestratorRuntime . 's). Processed: ' . $processed_count]);
        error_log("Fractal_Orchestrator reached max runtime. Processed: {$processed_count}");
        exit(0);
    }
    if (connection_aborted()) {
        error_log("Client disconnected, stopping fractal_orchestrator.");
        break;
    }

    $current_call = array_shift($queue);
    $script_id_num = $current_call['id']; $input_value = $current_call['value']; $depth = $current_call['depth'];
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
                if (isset($json_data['next_call_id']) && $json_data['next_call_id'] !== null && $json_data['depth'] < $maxCallDepth && $json_data['next_call_id'] >= 0 && $json_data['next_call_id'] < $numPythonScripts) { // Added bounds check for next_call_id
                    $next_val = $json_data['output_value'];
                    if (abs($next_val - $input_value) < 0.1 && rand(0,2) == 0) $next_val += (rand(-50,50)/100.0);
                    $queue[] = ['id' => $json_data['next_call_id'], 'value' => $next_val, 'depth' => $json_data['depth'] + 1];
                }
                 $processed_count++;
                 if($processed_count >= $max_events_per_burst && (time() - $startTime) < $maxOrchestratorRuntime) {
                    $max_events_per_burst += 100; 
                 }
            }
        }
    } else {
        send_event(['error' => "Script {$script_name} failed. Ret: {$return_var}", 'details' => $output_str]);
    }
    usleep(20000); 
}
send_event(['status' => 'Orchestration loop finished. Processed total: ' . $processed_count]);
error_log("Fractal_Orchestrator loop finished. Processed events: {$processed_count}");
?>