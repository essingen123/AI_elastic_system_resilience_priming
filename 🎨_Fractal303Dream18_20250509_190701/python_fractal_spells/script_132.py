import sys, json, math, random
# Script ID 132: Op 'coupled_logistic_x', Modifiers: rx=3.684, cxy=0.148
op_name = "coupled_logistic_x" 

def perform_operation(val, current_depth, script_id, prev_val_placeholder, **modifiers):
    for key, value in modifiers.items(): globals()[key] = float(value) 
    current_val_for_map = val 
    # Define prev_val_placeholder_norm for Lyapunov based on prev_val_placeholder
    prev_val_placeholder_norm = ((prev_val_placeholder % 1.0 if prev_val_placeholder is not None else 0.5))

    if op_name == "logistic_growth": 
         k_val = modifiers.get("modifier_k", 1.0)
         if k_val == 0: k_val = 1.0
         current_val_for_map = (abs(val) % k_val) / k_val 
         val = current_val_for_map 
    elif op_name.startswith("ikeda_map"):
        tn = 0.4 - 6.0 / (1.0 + val**2 + prev_val_placeholder**2)
    try: 
        result = modifier_rx * val * (1.0 - val) - modifier_cxy * val * prev_val_placeholder
        return result
    except Exception as e: 
        return val + random.uniform(-5.0, 5.0) 

if __name__ == "__main__":
    if len(sys.argv) < 5: print(json.dumps({"error": "Insufficient args", "script_id": 132})); sys.exit(1)
    input_value, current_depth, num_total_scripts, max_allowed_depth = float(sys.argv[1]), int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])
    modifier_rx = float(3.684)
    modifier_cxy = float(0.148)

    prev_val_placeholder_py = random.uniform(-0.5, 0.5) 
    output_value = perform_operation(input_value, current_depth, 132, prev_val_placeholder_py, **({"modifier_rx": modifier_rx, "modifier_cxy": modifier_cxy}))
    output_value = max(-1e7, min(1e7, output_value)) 
    if math.isnan(output_value) or math.isinf(output_value): output_value = random.uniform(-200.0, 200.0)

    next_call_id_py_str = "104" 
    
    next_call_id_final = None 
    # CORRECTED Python 'if' condition to use Python boolean literal
    if next_call_id_py_str != 'None' and current_depth < (max_allowed_depth -1) and True:
        try:
            parsed_next_id = int(next_call_id_py_str)
            if 0 <= parsed_next_id < num_total_scripts: 
                 next_call_id_final = parsed_next_id
            else: 
                 next_call_id_final = random.randint(0, num_total_scripts - 1)
        except (ValueError, TypeError): 
            next_call_id_final = random.randint(0, num_total_scripts - 1) 
    
    print(json.dumps({
        "script_id":132, "input_value":input_value, "op_type":"coupled_logistic_x", 
        "modifiers_used": {"modifier_rx": modifier_rx, "modifier_cxy": modifier_cxy}, "output_value":output_value, 
        "depth":current_depth, "next_call_id":next_call_id_final, 
        "num_total_scripts":num_total_scripts
    }))