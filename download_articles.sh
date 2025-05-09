#!/bin/bash

# Directory to save raw HTML content
output_dir="raw_articles"

# List of Wikipedia URLs to download
wikipedia_urls=(
    "https://en.wikipedia.org/wiki/Worse_is_better"
    "https://en.wikipedia.org/wiki/Rapid_application_development"
    "https://en.wikipedia.org/wiki/Procedural_programming"
    "https://en.wikipedia.org/wiki/Monolithic_application"
    "https://en.wikipedia.org/wiki/Dynamic_typing"
    "https://en.wikipedia.org/wiki/Polyglot_programming"
    "https://en.wikipedia.org/wiki/WebAssembly"
    "https://en.wikipedia.org/wiki/Prompt_engineering"
    "https://en.wikipedia.org/wiki/Model_Context_protocol"
    "https://en.wikipedia.org/wiki/Event_driven_architecture"
    "https://en.wikipedia.org/wiki/Concurrency_(computer_science)"
    "https://en.wikipedia.org/wiki/Finite_state_machine"
    "https://en.wikipedia.org/wiki/Idempotence"
    "https://en.wikipedia.org/wiki/Fault_tolerance"
)

# Create the output directory if it doesn't exist
if [ ! -d "$output_dir" ]; then
    mkdir "$output_dir"
    echo "Created directory: '$output_dir'"
fi

echo "Starting to download ${#wikipedia_urls[@]} articles..."
echo "------------------------------"

for url in "${wikipedia_urls[@]}"; do
    echo "Downloading: $url"
    wget -P "$output_dir" "$url"
    if [ $? -eq 0 ]; then
        echo "  -> Downloaded successfully."
    else
        echo "  -> Error downloading."
    fi
done

echo "------------------------------"
echo "Download complete."
echo "Raw HTML files saved in the '$output_dir' directory."
echo "Note: These are raw HTML files. runner.py or a similar script is needed to parse them into plain text."