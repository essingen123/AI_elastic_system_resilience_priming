import sys, json, math, random
def perform_operation(val, modifier):
    try: return math.copysign(pow(abs(val), modifier), val) if val != 0 else 0
    except Exception: return val + random.uniform(-0.1, 0.1)
if __name__ == "__main__":
    if len(sys.argv) < 5: print(json.dumps({"error": "Insufficient args", "script_id": 168})); sys.exit(1)
    input_value, current_depth, num_total_scripts, max_allowed_depth = float(sys.argv[1]), int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])
    output_value = perform_operation(input_value, 1)
    output_value = max(-1000.0, min(1000.0, output_value))
    if math.isnan(output_value) or math.isinf(output_value): output_value = random.uniform(-1.0, 1.0)
    next_call_id = 185 if current_depth < max_allowed_depth and 1 else None
    if next_call_id is not None and (next_call_id < 0 or next_call_id >= num_total_scripts): next_call_id = random.randint(0,num_total_scripts-1)
    print(json.dumps({"script_id":168,"input_value":input_value,"op_type":"power","modifier_used":1,"output_value":output_value,"depth":current_depth,"next_call_id":next_call_id,"num_total_scripts":num_total_scripts}))