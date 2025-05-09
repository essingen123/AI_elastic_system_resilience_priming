#!/bin/bash

# Directory to save raw HTML content
output_dir="raw_articles"

# List of Wikipedia URLs to download (Order 1)
# Ensure these are raw URLs, not markdown links
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
    mkdir -p "$output_dir" # -p creates parent dirs if needed and doesn't error if exists
    echo "Created directory: '$output_dir'"
fi

echo "Starting to download ${#wikipedia_urls[@]} articles..."
echo "------------------------------"

successful_downloads=0
failed_downloads=0

for url in "${wikipedia_urls[@]}"; do
    echo "Downloading: $url"
    # Use wget to download the URL into the output directory
    # -P specifies prefix directory
    # -nv for less verbose output
    # --timeout and --tries for robustness
    # -O to specify output filename based on URL part, avoiding issues with query strings if any
    filename=$(basename "$url")
    wget -P "$output_dir" -O "$output_dir/$filename.html" --timeout=15 --tries=2 "$url"
    if [ $? -eq 0 ]; then
        echo "  -> Downloaded successfully as $filename.html"
        successful_downloads=$((successful_downloads + 1))
    else
        echo "  -> Error downloading $url. Status: $?"
        failed_downloads=$((failed_downloads + 1))
    fi
done

echo "------------------------------"
echo "Download complete."
echo "Successfully downloaded: $successful_downloads article(s)."
echo "Failed to download: $failed_downloads article(s)."
echo "Raw HTML files (attempted) in the '$output_dir' directory."
echo "Note: runner.py is needed to parse these into plain text."