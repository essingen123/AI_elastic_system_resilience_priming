<?php
header('Content-Type: text/event-stream'); header('Cache-Control: no-cache');
set_time_limit(0); 
$maxRuntime = 10; $startTime = time();
ob_implicit_flush(true);
function send_event_debug($data) { echo "data: " . json_encode($data) . "\n\n"; if(ob_get_level()>0) ob_flush(); flush(); }
error_log("Debug Orchestrator started. Max runtime: " . $maxRuntime . "s.");
send_event_debug(['status' => 'Debug Orchestrator alive. Will send pings. Max runtime: ' . $maxRuntime . 's.']);
$c = 0;
while(true){
    if((time() - $startTime) >= $maxRuntime) { send_event_debug(['status' => 'Debug Orchestrator max runtime.']); break; }
    if(connection_aborted()) { error_log("Client disconnected orchestrator."); break; }
    send_event_debug(['ping' => $c++, 'time' => date('H:i:s')]);
    sleep(5);
}
error_log("Debug Orchestrator finished.");
?>