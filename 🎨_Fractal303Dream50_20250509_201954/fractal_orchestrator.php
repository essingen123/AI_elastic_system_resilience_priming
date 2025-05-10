<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
set_time_limit(0); 

$pythonProjectFolder = "python_fractal_spells"; 
$numPythonScripts = 303;
$maxCallDepth = 6;
$maxOrchestratorRuntime = 30; 

ob_implicit_flush(true);
$startTime = time();

function send_event($data) {
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
        send_event(['status' => 'Orchestrator reached max runtime (' . $maxOrchestratorRuntime . 's). Total events: ' . $total_events_sent_this_run]);
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
            send_event(['error' => "Script not found: {$script_path} from " . getcwd(), 'id' => $script_id_num]);
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
                    send_event($json_data);
                    $total_events_sent_this_run++;
                    if (isset($json_data['next_call_id']) && $json_data['next_call_id'] !== null && $json_data['depth'] < ($maxCallDepth -1) && is_numeric($json_data['next_call_id']) && $json_data['next_call_id'] >= 0 && $json_data['next_call_id'] < $numPythonScripts) {
                        $next_val = $json_data['output_value'];
                        if (abs($next_val - $input_value) < 0.01 || abs($next_val) < 0.01 ) {
                            $next_val += (rand(-100,100)/50.0) * ($depth + 1); 
                            if (abs($next_val) < 0.01 && $next_val != 0) $next_val = $next_val > 0 ? 0.1 : -0.1;
                        }
                        if(count($queue) < 200) { 
                           $queue[] = ['id' => (int)$json_data['next_call_id'], 'value' => $next_val, 'depth' => $json_data['depth'] + 1];
                        }
                    }
                }
            }
        } else {
            send_event(['error' => "Script {$script_name} failed. Ret: {$return_var}", 'details' => $output_str, 'id' => $script_id_num]);
        }
    } 
    usleep(10000); 
}
send_event(['status' => 'Orchestration loop finished or queue empty. Total events: ' . $total_events_sent_this_run]);
error_log("Fractal_Orchestrator loop finished. Total events: {$total_events_sent_this_run}, Iterations: {$loop_iterations}");
?>