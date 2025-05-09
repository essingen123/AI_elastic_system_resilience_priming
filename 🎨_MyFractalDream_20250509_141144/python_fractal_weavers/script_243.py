import sys, json, math, random
# Script ID 243: Op 'sine_transform', Modifiers: angle=3.8, amp=4.2
op_name = "sine_transform" # Make op_name available in Python global scope

def perform_operation(val, current_depth, script_id, **modifiers):
    for key, value in modifiers.items(): globals()[key] = value # Make individual modifiers global
    prev_val_placeholder = random.uniform(-0.1, 0.1) if op_name == "henon_map_x" else 0.0 
    try: 
        if op_name == "logistic_map": 
             limit = modifiers.get("modifier_limit", 1.0)
             if limit == 0: limit = 1.0
             val_norm = abs(val) 
             # val must be in [0,1] for standard logistic map, here we map it to [0,1] based on limit
             val_for_map = (val_norm % limit) / limit if limit != 0.0 else val_norm % 1.0
             # The lambda for logistic_map will use 'val', so we reassign it here for that specific op
             val = val_for_map 
        elif op_name == "henon_map_x":
             pass

        return math.sin(val * modifier_angle) * modifier_amp
    except Exception as e: 
        # import traceback
        # print(f"Error in op {op_name} (ID {script_id}): {e}\n{traceback.format_exc()}", file=sys.stderr)
        return val + random.uniform(-1.0, 1.0) 

if __name__ == "__main__":
    if len(sys.argv) < 5: print(json.dumps({"error": "Insufficient args", "script_id": 243})); sys.exit(1)
    input_value, current_depth, num_total_scripts, max_allowed_depth = float(sys.argv[1]), int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])
    
    modifier_angle = 3.8
    modifier_amp = 4.2

    
    output_value = perform_operation(input_value, current_depth, 243, **({"modifier_angle": modifier_angle, "modifier_amp": modifier_amp}))
    output_value = max(-100000.0, min(100000.0, output_value)) 
    if math.isnan(output_value) or math.isinf(output_value): output_value = random.uniform(-50.0, 50.0)

    next_call_id_py = 267 # This will be 'None' string or a number string
    
    if next_call_id_py != 'None' and current_depth < max_allowed_depth and 1:
        try:
            next_call_id = int(next_call_id_py)
            if not (0 <= next_call_id < num_total_scripts): 
                 next_call_id = random.randint(0, num_total_scripts - 1)
        except (ValueError, TypeError): 
            next_call_id = random.randint(0, num_total_scripts - 1) # Fallback if string was not int-like
    else:
        next_call_id = None # Python None
            
    print(json.dumps({
        "script_id":243, "input_value":input_value, "op_type":"sine_transform", 
        "modifiers_used": {"modifier_angle": modifier_angle, "modifier_amp": modifier_amp}, "output_value":output_value, 
        "depth":current_depth, "next_call_id":next_call_id, # This will be Python int or None
        "num_total_scripts":num_total_scripts
    }))