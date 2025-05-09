import sys, json, math, random
# Script ID 57: Op 'clifford_attractor_y', Modifiers: b=1.28, d=1.64
op_name = "clifford_attractor_y" 

def perform_operation(val, current_depth, script_id, prev_val_placeholder, **modifiers):
    for key, value in modifiers.items(): globals()[key] = float(value) 
    current_val_for_map = val 
    if op_name == "logistic_growth": 
         k_val = modifiers.get("modifier_k", 1.0)
         if k_val == 0: k_val = 1.0
         current_val_for_map = (abs(val) % k_val) / k_val 
         val = current_val_for_map 
    elif op_name.startswith("ikeda_map"):
        tn = 0.4 - 6.0 / (1.0 + val**2 + prev_val_placeholder**2)
    try: 
        result = math.sin(modifier_b * val) + modifier_d * math.cos(modifier_b * prev_val_placeholder)
        return result
    except Exception as e: 
        return val + random.uniform(-5.0, 5.0) 

if __name__ == "__main__":
    if len(sys.argv) < 5: print(json.dumps({"error": "Insufficient args", "script_id": 57})); sys.exit(1)
    input_value, current_depth, num_total_scripts, max_allowed_depth = float(sys.argv[1]), int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])
    modifier_b = float(1.28)
    modifier_d = float(1.64)

    prev_val_placeholder_py = random.uniform(-0.5, 0.5) # General small random influence 
    output_value = perform_operation(input_value, current_depth, 57, prev_val_placeholder_py, **({"modifier_b": modifier_b, "modifier_d": modifier_d}))
    output_value = max(-1e7, min(1e7, output_value)) 
    if math.isnan(output_value) or math.isinf(output_value): output_value = random.uniform(-200.0, 200.0)

    next_call_id_py_str = 64 
    next_call_id_final = None 
    if next_call_id_py_str != 'None' and current_depth < (max_allowed_depth -1) and 1:
        try:
            parsed_next_id = int(next_call_id_py_str)
            if 0 <= parsed_next_id < num_total_scripts: 
                 next_call_id_final = parsed_next_id
            else: 
                 next_call_id_final = random.randint(0, num_total_scripts - 1)
        except (ValueError, TypeError): 
            next_call_id_final = random.randint(0, num_total_scripts - 1) 
    
    print(json.dumps({
        "script_id":57, "input_value":input_value, "op_type":"clifford_attractor_y", 
        "modifiers_used": {"modifier_b": modifier_b, "modifier_d": modifier_d}, "output_value":output_value, 
        "depth":current_depth, "next_call_id":next_call_id_final, 
        "num_total_scripts":num_total_scripts
    }))