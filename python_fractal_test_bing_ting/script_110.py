import sys
import json
import math
import random

def perform_operation(val, modifier):
    try:
        # This specific script (ID 110) uses: modulo_shift with modifier 4
        # Lambda: (val + modifier) % 10 if modifier != 0 else val % 10
        return (val + modifier) % 10 if modifier != 0 else val % 10
    except Exception as e:
        # Fallback if math operation fails (e.g. overflow, domain error)
        return val + random.uniform(-0.1, 0.1) # Slight perturbation

if __name__ == "__main__":
    if len(sys.argv) < 5:
        print(json.dumps({"error": "Insufficient arguments", "script_id": 110}))
        sys.exit(1)

    input_value = float(sys.argv[1])
    current_depth = int(sys.argv[2])
    num_total_scripts = int(sys.argv[3]) # Passed by orchestrator
    max_allowed_depth = int(sys.argv[4]) # Passed by orchestrator

    modifier_val = 4
    output_value = perform_operation(input_value, modifier_val)

    # Cap output value to prevent extreme explosion, helps visualization
    output_value = max(-1000.0, min(1000.0, output_value))
    if math.isnan(output_value) or math.isinf(output_value):
        output_value = random.uniform(-1.0, 1.0) # Reset if problematic

    next_call_id = None
    if current_depth < max_allowed_depth:
        will_call_next_py =  # True or False from PHP
        if will_call_next_py:
            # This calculation is now deterministic based on PHP's generation for this script
            next_call_id = None 
            # Ensure it's within bounds (PHP should also do this, but double check)
            if next_call_id is not None and (next_call_id < 0 or next_call_id >= num_total_scripts):
                 next_call_id = random.randint(0, num_total_scripts - 1) # Fallback random if calculated is bad
    
    result = {
        "script_id": 110,
        "input_value": input_value,
        "op_type": "modulo_shift",
        "modifier_used": modifier_val,
        "output_value": output_value,
        "depth": current_depth,
        "next_call_id": next_call_id, # Python script tells orchestrator who to call next
        "num_total_scripts": num_total_scripts # For context
    }
    print(json.dumps(result))
