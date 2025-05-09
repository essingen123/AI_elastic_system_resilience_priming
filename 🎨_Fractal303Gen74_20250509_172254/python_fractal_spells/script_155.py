import sys, json, math, random
# Script ID 155: Op 'clifford_attractor_y', Modifiers: b=0.34, d=0.95
op_name = "clifford_attractor_y" 

def perform_operation(val, current_depth, script_id, prev_val_placeholder, **modifiers):
    for key, value in modifiers.items(): globals()[key] = float(value) # Ensure modifiers are float
    
    # For specific map preparations
    current_val_for_map = val 
    if op_name == "logistic_growth": # Logistic map often expects input in [0,1] normalized by K
         k_val = modifiers.get("modifier_k", 1.0)
         if k_val == 0: k_val = 1.0
         current_val_for_map = (abs(val) % k_val) / k_val # Map val to [0,1) for the lambda
         # The lambda for logistic_growth will use 'val', so reassign 'val' to the normalized one for this op
         # This is a common pattern for map functions if they expect a specific input range.
         val = current_val_for_map

    elif op_name.startswith("ikeda_map") or op_name.startswith("clifford_attractor") or op_name.startswith("de_jong_attractor") or op_name.startswith("coupled_logistic"):
        # These are 2D maps, 'val' is one component, 'prev_val_placeholder' is a proxy for the other or previous state.
        # This is a simplification; a true 2D map would pass (x,y) state.
        # For Ikeda, tn depends on both x (val) and y (prev_val_placeholder)
        if op_name.startswith("ikeda_map"):
            tn = 0.4 - 6.0 / (1.0 + val**2 + prev_val_placeholder**2) # tn is now local to perform_operation

    try: 
        result = math.sin(modifier_b * val) + modifier_d * math.cos(modifier_b * prev_val_placeholder)
        return result
    except Exception as e: 
        # import traceback; print(f"Error in op {op_name} (ID {script_id}): {e}\nval={val}, prev_val={prev_val_placeholder}, mods={modifiers}\n{traceback.format_exc()}", file=sys.stderr)
        return val + random.uniform(-5.0, 5.0) # Larger perturbation on math error

if __name__ == "__main__":
    if len(sys.argv) < 5: print(json.dumps({"error": "Insufficient args", "script_id": 155})); sys.exit(1)
    input_value, current_depth, num_total_scripts, max_allowed_depth = float(sys.argv[1]), int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])
    
    modifier_b = float(0.34)
    modifier_d = float(0.95)
 # PHP injects modifier assignments here.
    
    # prev_val_placeholder is a simple random value passed from Python itself now for statelessness.
    # A more complex system would involve the orchestrator managing state between calls.
    prev_val_placeholder_py = random.uniform(-0.5, 0.5) # General small random influence 
    
    output_value = perform_operation(input_value, current_depth, 155, prev_val_placeholder_py, **({"modifier_b": modifier_b, "modifier_d": modifier_d}))
    
    # More aggressive capping to prevent extreme values but allow wider dynamics
    output_value = max(-1e7, min(1e7, output_value)) 
    if math.isnan(output_value) or math.isinf(output_value): output_value = random.uniform(-200.0, 200.0)

    next_call_id_py_str = 118 
    next_call_id_final = None 
    if next_call_id_py_str != 'None' and current_depth < (max_allowed_depth -1) and 1: # Check depth before increment
        try:
            parsed_next_id = int(next_call_id_py_str)
            if 0 <= parsed_next_id < num_total_scripts: 
                 next_call_id_final = parsed_next_id
            else: 
                 next_call_id_final = random.randint(0, num_total_scripts - 1)
        except (ValueError, TypeError): 
            next_call_id_final = random.randint(0, num_total_scripts - 1) 
    
    print(json.dumps({
        "script_id":155, "input_value":input_value, "op_type":"clifford_attractor_y", 
        "modifiers_used": {"modifier_b": modifier_b, "modifier_d": modifier_d}, "output_value":output_value, 
        "depth":current_depth, "next_call_id":next_call_id_final, 
        "num_total_scripts":num_total_scripts
    }))