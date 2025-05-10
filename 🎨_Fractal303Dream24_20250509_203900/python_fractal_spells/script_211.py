import sys, json, math, random
# Script ID 211: Op 'sine_power_mix', Modifiers: freq1=2.52, pow1=2, amp1=1.5, freq2=1.24, pow2=3, amp2=4.2
op_name = "sine_power_mix" # Make op_name global in this Python script's context

# Define the perform_operation function with PHP-generated content
def perform_operation(val, current_depth, script_id, prev_val_placeholder, **modifiers):
    # Unpack modifiers into local scope for the lambda
    # This makes them accessible directly by name in the lambda string
    # Example: if modifiers = {'modifier_a': 1.2}, then modifier_a = 1.2
    for key, value in modifiers.items():
        locals()[key] = float(value)

    _internal_prev_norm = (prev_val_placeholder % 1.0 if prev_val_placeholder is not None else 0.5)

    current_val_for_map = val
    if op_name == "logistic_growth":
         k_val = modifiers.get("modifier_k", 1.0)
         if k_val == 0: k_val = 1.0
         current_val_for_map = (abs(val) % k_val) / k_val
         val = current_val_for_map
    elif op_name.startswith("ikeda_map"):
        # tn is calculated based on current val and prev_val_placeholder
        # It needs to be available in the scope where lambda is evaluated
        tn = 0.4 - 6.0 / (1.0 + val**2 + prev_val_placeholder**2)
    try:
        result = (math.sin(val * modifiers.get("modifier_freq1", 1.0)) ** int(modifiers.get("modifier_pow1", 1))) * modifiers.get("modifier_amp1", 1.0) + (math.cos(prev_val_placeholder * modifiers.get("modifier_freq2", 1.0)) ** int(modifiers.get("modifier_pow2", 1))) * modifiers.get("modifier_amp2", 1.0)
        return result
    except Exception as e:
        # import traceback; print(f"Error in op {op_name} (ID {script_id}): {e}\nval={val}, prev_val={prev_val_placeholder}, mods={modifiers}\n{traceback.format_exc()}", file=sys.stderr)
        return val + random.uniform(-5.0, 5.0)


if __name__ == "__main__":
    if len(sys.argv) < 5: print(json.dumps({"error": "Insufficient args", "script_id": 211})); sys.exit(1)
    input_value, current_depth, num_total_scripts, max_allowed_depth = float(sys.argv[1]), int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])

    # These definitions will create global variables in Python for modifiers
    freq1 = float(2.52) # PHP generated
    pow1 = float(2) # PHP generated
    amp1 = float(1.5) # PHP generated
    freq2 = float(1.24) # PHP generated
    pow2 = float(3) # PHP generated
    amp2 = float(4.2) # PHP generated

    prev_val_placeholder_py = random.uniform(-0.5, 0.5) 
    
    # The modifiers dict for Python is constructed using these Python global variables
    python_modifiers_dict = {"freq1": freq1, "pow1": pow1, "amp1": amp1, "freq2": freq2, "pow2": pow2, "amp2": amp2}
    
    output_value = perform_operation(input_value, current_depth, 211, prev_val_placeholder_py, **python_modifiers_dict)
    output_value = max(-1e7, min(1e7, output_value)) 
    if math.isnan(output_value) or math.isinf(output_value): output_value = random.uniform(-200.0, 200.0)

    next_call_id_py_str = "245" 
    
    next_call_id_final = None 
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
        "script_id":211, "input_value":input_value, "op_type":op_name, # Use Python op_name
        "modifiers_used": python_modifiers_dict, "output_value":output_value, 
        "depth":current_depth, "next_call_id":next_call_id_final, 
        "num_total_scripts":num_total_scripts
    }))