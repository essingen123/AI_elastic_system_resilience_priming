<?php
header('Content-Type: text/event-stream'); // Still set for potential future use
header('Cache-Control: no-cache');
header('Connection: keep-alive');
set_time_limit(0); 

$pythonProjectFolder = "py_scripts"; 
$numPythonScripts = 50;
$maxCallDepth = 3;
$maxOrchestratorRuntime = 30; 

ob_implicit_flush(true);
$startTime = time();
$eventCount = 0;

function send_event_orchestrator($data) { // Renamed to avoid conflict if this file is included
    echo "data: " . json_encode($data) . "\n\n";
    if (ob_get_level() > 0) ob_flush();
    flush();
}

error_log("Fractal Orchestrator (v2.3 - 3D Graph Version) started. Max runtime: {$maxOrchestratorRuntime}s.");
send_event_orchestrator(['status' => 'Orchestrator alive, but index.html uses self-generated graph data.']);

// Minimal loop just to keep alive for the duration or if we want to add Python interaction later
while(true) {
    if ((time() - $startTime) >= $maxOrchestratorRuntime) {
        send_event_orchestrator(['status' => 'Orchestrator max runtime reached.']);
        error_log("Fractal Orchestrator reached max runtime.");
        break;
    }
    if (connection_aborted()) {
        error_log("Client disconnected from orchestrator.");
        break;
    }
    // Send a heartbeat or a simple message periodically if desired
    // send_event_orchestrator(['heartbeat' => date('H:i:s'), 'message' => 'Orchestrator is idling.']);
    $eventCount++;
    sleep(10); // Check every 10 seconds
}

error_log("Fractal Orchestrator finished.");
?>