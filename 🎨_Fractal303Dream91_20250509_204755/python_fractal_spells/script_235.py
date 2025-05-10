import sys, json, math, random
# Script ID 235: Op 'sine_power_mix', Modifiers: freq1=0.4, pow1=2, amp1=2, freq2=2.39, pow2=3, amp2=4
op_name = "sine_power_mix" 

def perform_operation(val, current_depth, script_id, prev_val_placeholder, **modifiers):
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
        tn = 0.4 - 6.0 / (1.0 + val**2 + prev_val_placeholder**2)
    try:
        result = (math.sin(val * modifiers.get("modifier_freq1", 1.0)) ** int(modifiers.get("modifier_pow1", 1))) * modifiers.get("modifier_amp1", 1.0) + (math.cos(prev_val_placeholder * modifiers.get("modifier_freq2", 1.0)) ** int(modifiers.get("modifier_pow2", 1))) * modifiers.get("modifier_amp2", 1.0)
        return result
    except Exception as e:
        return val + random.uniform(-5.0, 5.0)


if __name__ == "__main__":
    if len(sys.argv) < 5: print(json.dumps({"error": "Insufficient args", "script_id": 235})); sys.exit(1)
    input_value, current_depth, num_total_scripts, max_allowed_depth = float(sys.argv[1]), int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])
    freq1 = float(0.4) # PHP generated
    pow1 = float(2) # PHP generated
    amp1 = float(2) # PHP generated
    freq2 = float(2.39) # PHP generated
    pow2 = float(3) # PHP generated
    amp2 = float(4) # PHP generated

    prev_val_placeholder_py = random.uniform(-0.5, 0.5) 
    python_modifiers_dict = {"freq1": freq1, "pow1": pow1, "amp1": amp1, "freq2": freq2, "pow2": pow2, "amp2": amp2}
    output_value = perform_operation(input_value, current_depth, 235, prev_val_placeholder_py, **python_modifiers_dict)
    output_value = max(-1e7, min(1e7, output_value)) 
    if math.isnan(output_value) or math.isinf(output_value): output_value = random.uniform(-200.0, 200.0)

    next_call_id_py_str = "267" 
    next_call_id_final = None 
    if next_call_id_py_str != 'None' and current_depth < (max_allowed_depth -1) and True: # Use Python literal True/False
        try:
            parsed_next_id = int(next_call_id_py_str)
            if 0 <= parsed_next_id < num_total_scripts: 
                 next_call_id_final = parsed_next_id
            else: 
                 next_call_id_final = random.randint(0, num_total_scripts - 1)
        except (ValueError, TypeError): 
            next_call_id_final = random.randint(0, num_total_scripts - 1) 
    
    print(json.dumps({
        "script_id":235, "input_value":input_value, "op_type":op_name, 
        "modifiers_used": python_modifiers_dict, "output_value":output_value, 
        "depth":current_depth, "next_call_id":next_call_id_final, 
        "num_total_scripts":num_total_scripts
    }))