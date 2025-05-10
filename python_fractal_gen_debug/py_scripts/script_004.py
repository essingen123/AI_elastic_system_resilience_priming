import sys, json, random
# Script ID 4: Op 'debug_op'
if __name__ == "__main__":
    input_value = float(sys.argv[1]) if len(sys.argv) > 1 else 1.0
    output_value = input_value * 3 + random.uniform(-0.1,0.1)
    print(json.dumps({
        "script_id":4, "input_value":input_value, "op_type":"debug_op", 
        "output_value":output_value, "depth": int(sys.argv[2]) if len(sys.argv) > 2 else 0,
        "next_call_id": (random.randint(0, 5 - 1) if random.random() < 0.5 else None) if (int(sys.argv[2] if len(sys.argv) > 2 else 0) < 2 -1) else None,
        "num_total_scripts": 5
    }))