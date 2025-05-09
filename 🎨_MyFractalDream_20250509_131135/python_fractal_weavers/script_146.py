import sys, json, math, random
# Script ID 146: Op 'logistic_map', Modifiers: r=3.795, limit=1
def perform_operation(val, current_depth, script_id, **modifiers):
    for key, value in modifiers.items(): globals()[key] = value 
    prev_val_placeholder = random.uniform(-0.1, 0.1) if "$op_name" == "henon_map_x" else 0 
    try: 
        if "logistic_map" == "logistic_map": 
             limit = modifiers.get("modifier_limit", 1)
             if limit == 0: limit = 1 
             val_norm = abs(val) 
             val = (val_norm % limit) / limit if limit != 0 else val_norm % 1.0
        elif "logistic_map" == "henon_map_x":
             pass # Placeholder for true Henon state logic

        return modifier_r * val * (1 - val / modifier_limit if modifier_limit !=0 else 1-val)
    except Exception as e: 
        return val + random.uniform(-1.0, 1.0) 

if __name__ == "__main__":
    if len(sys.argv) < 5: print(json.dumps({"error": "Insufficient args", "script_id": 146})); sys.exit(1)
    input_value, current_depth, num_total_scripts, max_allowed_depth = float(sys.argv[1]), int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])
    
    modifier_r = 3.795
    modifier_limit = 1
    # PHP injects modifier assignments here. Note: Ensure it's indented correctly if it spans multiple lines.
    
    output_value = perform_operation(input_value, current_depth, 146, **({"modifier_r": modifier_r, "modifier_limit": modifier_limit}))
    output_value = max(-100000.0, min(100000.0, output_value)) 
    if math.isnan(output_value) or math.isinf(output_value): output_value = random.uniform(-50.0, 50.0)

    next_call_id = None if current_depth < max_allowed_depth and  else None
    if next_call_id is not None: # Ensure next_call_id from PHP is an integer
        try:
            next_call_id = int(next_call_id)
            if not (0 <= next_call_id < num_total_scripts): 
                 next_call_id = random.randint(0, num_total_scripts - 1)
        except ValueError: # If next_script_id_expr was 'None' string or other non-int
            next_call_id = random.randint(0, num_total_scripts - 1) if  else None # Fallback if parsing failed
    
    print(json.dumps({
        "script_id":146, "input_value":input_value, "op_type":"logistic_map", 
        "modifiers_used": {"modifier_r": modifier_r, "modifier_limit": modifier_limit}, "output_value":output_value, 
        "depth":current_depth, "next_call_id":next_call_id,
        "num_total_scripts":num_total_scripts
    }))