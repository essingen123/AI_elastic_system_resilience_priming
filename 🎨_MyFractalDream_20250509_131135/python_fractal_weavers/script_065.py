import sys, json, math, random
# Script ID 65: Op 'sine_transform', Modifiers: angle=0.2, amp=6
def perform_operation(val, current_depth, script_id, **modifiers):
    for key, value in modifiers.items(): globals()[key] = value 
    prev_val_placeholder = random.uniform(-0.1, 0.1) if "$op_name" == "henon_map_x" else 0 
    try: 
        if "sine_transform" == "logistic_map": 
             limit = modifiers.get("modifier_limit", 1)
             if limit == 0: limit = 1 
             val_norm = abs(val) 
             val = (val_norm % limit) / limit if limit != 0 else val_norm % 1.0
        elif "sine_transform" == "henon_map_x":
             pass # Placeholder for true Henon state logic

        return math.sin(val * modifier_angle) * modifier_amp
    except Exception as e: 
        return val + random.uniform(-1.0, 1.0) 

if __name__ == "__main__":
    if len(sys.argv) < 5: print(json.dumps({"error": "Insufficient args", "script_id": 65})); sys.exit(1)
    input_value, current_depth, num_total_scripts, max_allowed_depth = float(sys.argv[1]), int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])
    
    modifier_angle = 0.2
    modifier_amp = 6
    # PHP injects modifier assignments here. Note: Ensure it's indented correctly if it spans multiple lines.
    
    output_value = perform_operation(input_value, current_depth, 65, **({"modifier_angle": modifier_angle, "modifier_amp": modifier_amp}))
    output_value = max(-100000.0, min(100000.0, output_value)) 
    if math.isnan(output_value) or math.isinf(output_value): output_value = random.uniform(-50.0, 50.0)

    next_call_id = 39 if current_depth < max_allowed_depth and 1 else None
    if next_call_id is not None: # Ensure next_call_id from PHP is an integer
        try:
            next_call_id = int(next_call_id)
            if not (0 <= next_call_id < num_total_scripts): 
                 next_call_id = random.randint(0, num_total_scripts - 1)
        except ValueError: # If next_script_id_expr was 'None' string or other non-int
            next_call_id = random.randint(0, num_total_scripts - 1) if 1 else None # Fallback if parsing failed
    
    print(json.dumps({
        "script_id":65, "input_value":input_value, "op_type":"sine_transform", 
        "modifiers_used": {"modifier_angle": modifier_angle, "modifier_amp": modifier_amp}, "output_value":output_value, 
        "depth":current_depth, "next_call_id":next_call_id,
        "num_total_scripts":num_total_scripts
    }))