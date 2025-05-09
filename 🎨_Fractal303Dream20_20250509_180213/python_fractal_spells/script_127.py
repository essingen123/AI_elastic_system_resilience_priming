import sys, json, math, random
# Script ID 127: Op 'swirl_step', Modifiers: strength=3.4
op_name = "swirl_step" 

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
        result = val + math.sin(val * 0.1 + current_depth * 0.05 + script_id * 0.01) * modifier_strength + prev_val_placeholder * 0.1
        return result
    except Exception as e: 
        return val + random.uniform(-5.0, 5.0) 

if __name__ == "__main__":
    if len(sys.argv) < 5: print(json.dumps({"error": "Insufficient args", "script_id": 127})); sys.exit(1)
    input_value, current_depth, num_total_scripts, max_allowed_depth = float(sys.argv[1]), int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])
    modifier_strength = float(3.4)

    prev_val_placeholder_py = random.uniform(-0.5, 0.5) # General small random influence 
    output_value = perform_operation(input_value, current_depth, 127, prev_val_placeholder_py, **({"modifier_strength": modifier_strength}))
    output_value = max(-1e7, min(1e7, output_value)) 
    if math.isnan(output_value) or math.isinf(output_value): output_value = random.uniform(-200.0, 200.0)

    next_call_id_py_str = 121 
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
        "script_id":127, "input_value":input_value, "op_type":"swirl_step", 
        "modifiers_used": {"modifier_strength": modifier_strength}, "output_value":output_value, 
        "depth":current_depth, "next_call_id":next_call_id_final, 
        "num_total_scripts":num_total_scripts
    }))