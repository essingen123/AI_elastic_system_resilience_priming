import sys, json, random, math

def my_operation(val, mod):
    return val - mod

if __name__ == "__main__":
    input_value = float(sys.argv[1]) if len(sys.argv) > 1 else 1.0
    # Other args (depth, num_scripts, max_depth) are not used by this simple version
    output_value = my_operation(input_value, 4)
    # Ensure output is finite and within a reasonable range for the 3D graph if used later
    if math.isnan(output_value) or math.isinf(output_value): output_value = random.uniform(-100, 100)
    output_value = max(-1e5, min(1e5, output_value))
    result = {
        "script_id": 9,
        "input_value": input_value,
        "op_type": "subtract",
        "modifier_used": 4,
        "output_value": output_value,
        "message": "Simple Python script 9 executed."
    }
    print(json.dumps(result))
