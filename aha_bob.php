<?php

$triple_backtick = '```'; // Variable to hold triple backticks
$bash_lang = 'bash';     // Variable for bash language specifier

// --- Content for README.md ---
// Using Heredoc (double quotes around EOD) to allow variable interpolation for $triple_backtick and $bash_lang
$readme_content = <<<"EOD"
# Resilient Elastic System "Mind" Priming for LLMs
*Unlocking Dynamic Capabilities in Browser-Based AI Agents*

**Suggested Container/Repository Name:** AI_elastic_system_resilience_priming

Dive into the core concepts powering the next generation of AI systems that don't just run, but adapt, heal, and orchestrate complex tasks directly within the browser. This repository presents a unique approach to 'priming' Artificial Intelligence models, especially those with limited context or complexity handling, by curating and ordering foundational technical and philosophical concepts vital for building resilient, dynamic, and incredibly flexible AI agents.

---

## Getting Started

To set up the project and generate the knowledge monolith:

1.  **Clone this repository:**
    {$triple_backtick}{$bash_lang}
    git clone https://github.com/essingen123/AI_elastic_system_resilience_priming.git
    cd AI_elastic_system_resilience_priming
    {$triple_backtick}
    *(If you used a generation script like the one that created these files, you can skip the clone and `cd` steps and start from step 2 in your current directory).*

2.  **Install Required Python Libraries:**
    Ensure you have Python installed (3.6+ recommended). Open your terminal and run:
    {$triple_backtick}{$bash_lang}
    pip install requests beautifulsoup4
    {$triple_backtick}

3.  **Download and Parse Articles:**
    Run the Python script to fetch and parse the Wikipedia articles:
    {$triple_backtick}{$bash_lang}
    python runner.py
    {$triple_backtick}
    This will create a `parsed_articles` directory containing the text content.

4.  **Generate the Knowledge Monolith:**
    Run the Python script to create the interactive HTML file:
    {$triple_backtick}{$bash_lang}
    python smart_monolith.py
    {$triple_backtick}
    This will create the `rendered_results` directory containing `smart_monolith.html`.

5.  **View the Monolith:**
    Open the `rendered_results/smart_monolith.html` file in your web browser.

---

## The Concept: Mind Priming for LLMs

The sequence in which knowledge is acquired profoundly shapes understanding. For AI, this 'priming' can influence how it interprets and applies subsequent information. This section unveils three strategically designed orderings of crucial Wikipedia articles. Each order is a tailored journey through concepts vital for developing AI systems that are not only functional but embody resilience and elasticity, particularly within the cutting-edge environment of browser-based containers. These orderings are presented with attention weights in mind, suggesting that concepts presented earlier may have a more significant initial impact, though a more nuanced bell curve of attention might apply.

---

## Order 1: Priming from Philosophy and Methodology to Technical Practice

* **Effect:** Ignite a **pragmatic revolution** in your development process. This order starts by embedding the powerful philosophies of prioritizing working solutions and methodologies for rapid iteration ('Worse is Better', RAD). It strategically positions subsequent technical articles on paradigms and flexibility as essential tools to manifest this mindset, guiding you to build fast, 'just working,' and elastic systems. **Prepare to tackle problems like:** Crafting a Minimum Viable Self-Healing Browser AI Agent built for relentless iteration.

    * [https://en.wikipedia.org/wiki/Worse_is_better](https://en.wikipedia.org/wiki/Worse_is_better)
    * [https://en.wikipedia.org/wiki/Rapid_application_development](https://en.wikipedia.org/wiki/Rapid_application_development)
    * [https://en.wikipedia.org/wiki/Procedural_programming](https://en.wikipedia.org/wiki/Procedural_programming)
    * [https://en.wikipedia.org/wiki/Monolithic_application](https://en.wikipedia.org/wiki/Monolithic_application)
    * [https://en.wikipedia.org/wiki/Dynamic_typing](https://en.wikipedia.org/wiki/Dynamic_typing)
    * [https://en.wikipedia.org/wiki/Polyglot_programming](https://en.wikipedia.org/wiki/Polyglot_programming)
    * [https://en.wikipedia.org/wiki/WebAssembly](https://en.wikipedia.org/wiki/WebAssembly)
    * [https://en.wikipedia.org/wiki/Prompt_engineering](https://en.wikipedia.org/wiki/Prompt_engineering)
    * [https://en.wikipedia.org/wiki/Model_Context_protocol](https://en.wikipedia.org/wiki/Model_Context_protocol)
    * [https://en.wikipedia.org/wiki/Event_driven_architecture](https://en.wikipedia.org/wiki/Event_driven_architecture)
    * [https://en.wikipedia.org/wiki/Concurrency_(computer_science)](https://en.wikipedia.org/wiki/Concurrency_(computer_science))
    * [https://en.wikipedia.org/wiki/Finite_state_machine](https://en.wikipedia.org/wiki/Finite_state_machine)
    * [https://en.wikipedia.org/wiki/Idempotence](https://en.wikipedia.org/wiki/Idempotence)
    * [https://en.wikipedia.org/wiki/Fault_tolerance](https://en.wikipedia.org/wiki/Fault_tolerance)

## Order 2: Priming from Core Technical Foundation to System Behavior

* **Effect:** Forge a **rock-solid technical foundation** from the ground up. By leading with the absolute core enablers – the browser environment (WebAssembly) and direct AI communication protocols (Prompt Engineering, MCP) – this order equips you with the fundamental building blocks first. You'll then explore how to weave these capabilities into dynamic systems, understanding the 'what' and the 'how-to-start' before layering on complex design. **Prepare to tackle problems like:** Engineering a highly responsive Browser AI Agent by mastering WebAssembly and direct prompting techniques.

    * [https://en.wikipedia.org/wiki/WebAssembly](https://en.wikipedia.org/wiki/WebAssembly)
    * [https://en.wikipedia.org/wiki/Prompt_engineering](https://en.wikipedia.org/wiki/Prompt_engineering)
    * [https://en.wikipedia.org/wiki/Model_Context_protocol](https://en.wikipedia.org/wiki/Model_Context_protocol)
    * [https://en.wikipedia.org/wiki/Dynamic_typing](https://en.wikipedia.org/wiki/Dynamic_typing)
    * [https://en.wikipedia.org/wiki/Procedural_programming](https://en.wikipedia.org/wiki/Procedural_programming)
    * [https://en.wikipedia.org/wiki/Polyglot_programming](https://en.wikipedia.org/wiki/Polyglot_programming)
    * [https://en.wikipedia.org/wiki/Event_driven_architecture](https://en.wikipedia.org/wiki/Event_driven_architecture)
    * [https://en.wikipedia.org/wiki/Concurrency_(computer_science)](https://en.wikipedia.org/wiki/Concurrency_(computer_science))
    * [https://en.wikipedia.org/wiki/Finite_state_machine](https://en.wikipedia.org/wiki/Finite_state_machine)
    * [https://en.wikipedia.org/wiki/Idempotence](https://en.wikipedia.org/wiki/Idempotence)
    * [https://en.wikipedia.org/wiki/Fault_tolerance](https://en.wikipedia.org/wiki/Fault_tolerance)
    * [https://en.wikipedia.org/wiki/Monolithic_application](https://en.wikipedia.org/wiki/Monolithic_application)
    * [https://en.wikipedia.org/wiki/Rapid_application_development](https://en.wikipedia.org/wiki/Rapid_application_development)
    * [https://en.wikipedia.org/wiki/Worse_is_better](https://en.wikipedia.org/wiki/Worse_is_better)

## Order 3: Priming from System Characteristics and Problems to Concepts and Solutions

* **Effect:** Confront complexity and champion resilience from the outset. This order immediately immerses you in the defining characteristics of robust systems, starting with concepts like Fault Tolerance and Idempotence. It primes the mind to think about desired outcomes and problems first, positioning the subsequent technical and architectural concepts as the necessary solutions and enablers for achieving inherent robustness and self-healing. Prepare to tackle problems like: Architecting a Browser-based LLM Orchestration System engineered for graceful error handling and autonomous recovery.

    * [https://en.wikipedia.org/wiki/Fault_tolerance](https://en.wikipedia.org/wiki/Fault_tolerance)
    * [https://en.wikipedia.org/wiki/Idempotence](https://en.wikipedia.org/wiki/Idempotence)
    * [https://en.wikipedia.org/wiki/Dynamic_typing](https://en.wikipedia.org/wiki/Dynamic_typing)
    * [https://en.wikipedia.org/wiki/Event_driven_architecture](https://en.wikipedia.org/wiki/Event_driven_architecture)
    * [https://en.wikipedia.org/wiki/Concurrency_(computer_science)](https://en.wikipedia.org/wiki/Concurrency_(computer_science))
    * [https://en.wikipedia.org/wiki/WebAssembly](https://en.wikipedia.org/wiki/WebAssembly)
    * [https://en.wikipedia.org/wiki/Model_Context_protocol](https://en.wikipedia.org/wiki/Model_Context_protocol)
    * [https://en.wikipedia.org/wiki/Prompt_engineering](https://en.wikipedia.org/wiki/Prompt_engineering)
    * [https://en.wikipedia.org/wiki/Finite_state_machine](https://en.wikipedia.org/wiki/Finite_state_machine)
    * [https://en.wikipedia.org/wiki/Polyglot_programming](https://en.wikipedia.org/wiki/Polyglot_programming)
    * [https://en.wikipedia.org/wiki/Procedural_programming](https://en.wikipedia.org/wiki/Procedural_programming)
    * [https://en.wikipedia.org/wiki/Monolithic_application](https://en.wikipedia.org/wiki/Monolithic_application)
    * [https://en.wikipedia.org/wiki/Rapid_application_development](https://en.wikipedia.org/wiki/Rapid_application_development)
    * [https://en.wikipedia.org/wiki/Worse_is_better](https://en.wikipedia.org/wiki/Worse_is_better)

---

## Obtaining Article Content

To facilitate working with the content of these articles locally, this repository provides two methods for obtaining the data and a script to generate a browsable HTML monolith.

### Method 1: Download Raw HTML using a Bash Script (`download_articles.sh`)

This script downloads the raw HTML content of the Wikipedia pages using the `wget` command-line utility into the `raw_articles` directory.

{$triple_backtick}{$bash_lang}
./download_articles.sh
{$triple_backtick}

### Method 2: Download and Parse to Text using a Python Script (`runner.py`)

This script fetches the Wikipedia pages, parses the HTML to extract the main article text, and saves the clean text content to `.txt` files in the `parsed_articles` directory.

{$triple_backtick}{$bash_lang}
python runner.py
{$triple_backtick}

### Method 3: Generate a Smart Monolith HTML File (`smart_monolith.py`)

This script reads the parsed text files from `parsed_articles` and generates a single, self-contained HTML file (`smart_monolith.html`) in the `rendered_results` directory. This HTML file presents the article content interactively, allowing you to toggle the visibility of each article.

{$triple_backtick}{$bash_lang}
python smart_monolith.py
{$triple_backtick}

## Usage of Obtained Content: Fueling Your AI and Development

Unlock the potential of this curated knowledge. The obtained article content (either raw HTML or parsed text) provides a powerful corpus you can leverage for a variety of advanced applications:

* **AI Training & Fine-tuning:** Use the text to provide specialized context or fine-tune LLMs on the principles of resilient, dynamic systems.
* **Contextual Understanding:** Integrate the knowledge into Retrieval Augmented Generation (RAG) systems to give LLMs access to detailed information on these concepts.
* **Code Generation & Analysis:** Fuel code generation tasks or perform local analysis to understand how these technical and philosophical concepts manifest in practice.
* **Building Specialized Tools:** Develop local search, summarization, or concept-mapping tools based on this curated technical knowledge base.

---

Explore these concepts, experiment with the orderings, and unlock new possibilities in building the future of elastic, resilient AI!
EOD;

// --- Content for runner.py ---
// Using Nowdoc (single quotes around EOD) as it doesn't contain markdown triple backticks and might have $ for shell or other literal uses
$runner_py_content = <<<'EOD'
import requests
from bs4 import BeautifulSoup
import os

# List of Wikipedia URLs to parse
# These are the articles we discussed, ordered for the "Philosophy to Practice" priming strategy (Order 1)
wikipedia_urls = [
    "https://en.wikipedia.org/wiki/Worse_is_better",
    "https://en.wikipedia.org/wiki/Rapid_application_development",
    "https://en.wikipedia.org/wiki/Procedural_programming",
    "https://en.wikipedia.org/wiki/Monolithic_application",
    "https://en.wikipedia.org/wiki/Dynamic_typing",
    "https://en.wikipedia.org/wiki/Polyglot_programming",
    "https://en.wikipedia.org/wiki/WebAssembly",
    "https://en.wikipedia.org/wiki/Prompt_engineering",
    "https://en.wikipedia.org/wiki/Model_Context_protocol",
    "https://en.wikipedia.org/wiki/Event_driven_architecture",
    "https://en.wikipedia.org/wiki/Concurrency_(computer_science)",
    "https://en.wikipedia.org/wiki/Finite_state_machine",
    "https://en.wikipedia.org/wiki/Idempotence",
    "https://en.wikipedia.org/wiki/Fault_tolerance",
]

# Directory to save the parsed content
parsed_articles_dir = "parsed_articles"

# --- Setup ---
# Create the output directory if it doesn't exist
if not os.path.exists(parsed_articles_dir):
    os.makedirs(parsed_articles_dir)
    print(f"Created directory: '{parsed_articles_dir}'")

print(f"Starting to parse {len(wikipedia_urls)} articles...")
print("-" * 30)

# --- Parsing Logic ---
for url in wikipedia_urls:
    try:
        print(f"Processing: {url}")

        # Fetch the page content using the requests library
        response = requests.get(url)
        response.raise_for_status() # Raise an exception for bad status codes (like 404 or 500)

        # Parse the HTML content using BeautifulSoup
        soup = BeautifulSoup(response.content, 'html.parser')

        # --- Extracting Main Article Text ---
        # Wikipedia's main content is typically within a div with the id 'mw-content-text'.
        # Within that, the actual article paragraphs are usually <p> tags.
        # This approach tries to get all text from paragraphs in the main content area,
        # which usually avoids most headers, footers, and sidebars.
        main_content_div = soup.find(id='mw-content-text')

        if main_content_div:
            # Find all paragraph tags within the main content
            article_paragraphs = main_content_div.find_all('p')

            # Join the text of these paragraphs
            # We strip whitespace and use a newline as a separator between paragraphs
            article_text = "\n".join([p.get_text(strip=True) for p in article_paragraphs if p.get_text(strip=True)]) # Ensure empty paragraphs are not just newlines

            # --- Saving to File ---
            # Get a clean filename from the URL (e.g., "Worse_is_better.txt")
            # We take the last part of the URL path and add .txt
            title = url.split("/")[-1].replace('(','_').replace(')','') # Sanitize for filenames
            filename = os.path.join(parsed_articles_dir, f"{title}.txt")

            # Save the extracted text to a file with UTF-8 encoding
            with open(filename, 'w', encoding='utf-8') as f:
                f.write(article_text)

            print(f"  -> Saved to: {filename}")

        else:
            print(f"  -> Warning: Could not find main content div ('mw-content-text') for {url}. Skipping.")

    except requests.exceptions.RequestException as e:
        print(f"  -> Error fetching {url}: {e}")
    except Exception as e:
            # Catch any other potential errors during parsing or file writing
            print(f"  -> An unexpected error occurred processing {url}: {e}")


print("-" * 30)
print("Parsing complete.")
print(f"All attempts processed. Check the '{parsed_articles_dir}' directory for results.")
print("\nNote: The parsing logic is simplified to get paragraphs from the main content area. For highly specific or complex extraction (e.g., excluding infoboxes, tables, reference sections), you might need to refine the BeautifulSoup selectors.")
EOD;

// --- Content for smart_monolith.py ---
$smart_monolith_py_content = <<<'EOD'
import os
import glob
import html

# Directory containing the parsed article text files
parsed_articles_dir = "parsed_articles"

# Output directory for the generated HTML file
output_rendered_dir = "rendered_results"

# Output HTML file name
output_html_file = os.path.join(output_rendered_dir, "smart_monolith.html")


# --- HTML Template Structure ---
# We'll build the HTML content dynamically and insert it into this template.
# Includes basic styling and a simple JS function for toggling content visibility.
html_template = """
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resilient Elastic Systems Knowledge Monolith</title>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #0056b3;
            text-align: center;
        }
        h2.article-group-title {
             color: #0056b3;
             margin-top: 30px;
             border-bottom: 1px solid #ccc;
             padding-bottom: 5px;
        }
        .article-title {
            cursor: pointer;
            color: #007bff;
            /* text-decoration: underline; */ /* Can be distracting with many titles */
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 1.2em;
            padding: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            transition: background-color 0.2s ease-in-out;
        }
        .article-title:hover {
            background-color: #d0d9e0;
        }
        .article-content {
            display: none; /* Content is hidden by default */
            border-left: 3px solid #007bff;
            padding-left: 15px;
            margin-bottom: 15px;
            margin-top: 5px;
            background-color: #fdfdfd;
            padding: 10px;
             border-radius: 0 0 4px 4px;
        }
        .article-content p {
            margin-bottom: 0.8em; /* Space between paragraphs */
        }
        /* Style for the "View Monolith" button in README, not part of this file but good to keep in mind */
    </style>
</head>
<body>
    <div class="container">
        <h1>Resilient Elastic Systems Knowledge Monolith</h1>
        <p>This self-contained document compiles key concepts for building dynamic, resilient, and elastic AI systems, particularly relevant for browser-based containers and leveraging less complex LLMs. Click on an article title to reveal its content.</p>

        <div id="articles-container">
            {{articles_content}}
        </div>
    </div>

    <script>
        function toggleArticle(id) {
            const content = document.getElementById('content-' + id);
            const title = document.querySelector('[onclick="toggleArticle(\'' + id + '\')"]'); // Get the title element
            if (content.style.display === 'none' || content.style.display === '') {
                content.style.display = 'block';
                title.style.fontWeight = 'bold'; // Optional: make title bold when open
            } else {
                content.style.display = 'none';
                title.style.fontWeight = 'normal'; // Optional: reset font weight
            }
        }
    </script>
</body>
</html>
"""

# --- Main Script Logic ---

# Check if the parsed articles directory exists
if not os.path.exists(parsed_articles_dir):
    print(f"Error: Directory '{parsed_articles_dir}' not found.")
    print("Please run runner.py first to parse the articles.")
    exit()

# Get a list of all text files in the parsed articles directory
# We want to maintain the order from runner.py if possible, so we try to read them in a specific order if defined
# For now, glob does not guarantee order, but for this specific set, it might be alphabetical by filename
# A more robust way would be to re-use the URL list from runner.py to define order.
# However, runner.py saves files like "Worse_is_better.txt", "Rapid_application_development.txt" etc.
# Let's use the same list of URLs as runner.py to determine the order of files.
# (This assumes runner.py has been run and created files based on this order)

# This list should ideally be shared or imported, but for simplicity, we redefine it here.
# It must match the order and naming convention from runner.py (URL basename as filename)
ordered_article_filenames = [
    "Worse_is_better.txt",
    "Rapid_application_development.txt",
    "Procedural_programming.txt",
    "Monolithic_application.txt",
    "Dynamic_typing.txt",
    "Polyglot_programming.txt",
    "WebAssembly.txt",
    "Prompt_engineering.txt",
    "Model_Context_protocol.txt",
    "Event_driven_architecture.txt",
    "Concurrency_computer_science.txt", # Adjusted for sanitized filename
    "Finite_state_machine.txt",
    "Idempotence.txt",
    "Fault_tolerance.txt",
]

article_files_to_process = []
for fname in ordered_article_filenames:
    fpath = os.path.join(parsed_articles_dir, fname)
    if os.path.exists(fpath):
        article_files_to_process.append(fpath)
    else:
        print(f"Warning: Expected article file '{fname}' not found in '{parsed_articles_dir}'. It will be skipped in the monolith.")

# Fallback if the ordered list is empty or files are missing, process whatever is there (though order is lost)
if not article_files_to_process:
    print("Warning: No files found based on the predefined order. Processing all .txt files found, order may vary.")
    article_files_to_process = glob.glob(os.path.join(parsed_articles_dir, "*.txt"))


if not article_files_to_process:
    print(f"Error: No .txt files found in '{parsed_articles_dir}' to process.")
    print("Please ensure runner.py ran successfully and created files.")
    exit()

# Create the output rendered directory if it doesn't exist
if not os.path.exists(output_rendered_dir):
    os.makedirs(output_rendered_dir)
    print(f"Created directory: '{output_rendered_dir}' for rendered results.")


# Build the HTML content for the articles section
articles_html_content = ""
article_id_counter = 0

for file_path in article_files_to_process:
    try:
        # Extract the article title from the filename
        file_name = os.path.basename(file_path)
        # Remove the file extension and replace underscores/hyphens with spaces for readability
        article_title = os.path.splitext(file_name)[0].replace("_", " ").replace("-", " ")

        # Read the content of the article file
        with open(file_path, 'r', encoding='utf-8') as f:
            article_text = f.read()

        # Escape HTML special characters in the text content to prevent rendering issues
        escaped_article_text = html.escape(article_text)

        # Replace newlines (single \n from runner.py) with HTML paragraph tags
        paragraphs = escaped_article_text.split('\n')
        formatted_text = "".join(f"<p>{p.strip()}</p>" for p in paragraphs if p.strip()) # Ensure empty lines don't create empty <p> tags


        # Create a unique ID for this article's content div
        article_id = f"article-{article_id_counter}"

        # Append the HTML for this article (title and hidden content)
        articles_html_content += f"""
        <div class="article">
            <h3 class="article-title" onclick="toggleArticle('{article_id}')">{article_title}</h3>
            <div id="content-{article_id}" class="article-content">
                {formatted_text}
            </div>
        </div>
        """
        article_id_counter += 1

    except Exception as e:
        print(f"Warning: Could not process file {file_path}: {e}")
        # Continue processing other files even if one fails

# Insert the generated articles content into the main HTML template
final_html_output = html_template.replace("{{articles_content}}", articles_html_content)

# Save the final HTML output to a file
try:
    with open(output_html_file, 'w', encoding='utf-8') as f:
        f.write(final_html_output)
    print(f"\nSuccessfully created '{output_html_file}'.")
    print(f"Open this file in your browser to view the knowledge monolith.")
except Exception as e:
    print(f"Error: Could not write to output file {output_html_file}: {e}")
EOD;

// --- Content for download_articles.sh ---
$download_sh_content = <<<'EOD'
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
EOD;

// --- File Creation Logic ---

if (file_put_contents("README.md", $readme_content)) {
    echo "README.md created successfully.\n";
} else {
    echo "Error creating README.md.\n";
}

if (file_put_contents("runner.py", $runner_py_content)) {
    echo "runner.py created successfully.\n";
    if (!stristr(PHP_OS, 'WIN') && chmod("runner.py", 0755)) { // Check for non-Windows OS before chmod
        echo "runner.py set to executable.\n";
    } elseif (stristr(PHP_OS, 'WIN')) {
        echo "runner.py created. (chmod not applicable on Windows).\n";
    } else {
        echo "Error setting runner.py to executable.\n";
    }
} else {
    echo "Error creating runner.py.\n";
}

if (file_put_contents("smart_monolith.py", $smart_monolith_py_content)) {
    echo "smart_monolith.py created successfully.\n";
    if (!stristr(PHP_OS, 'WIN') && chmod("smart_monolith.py", 0755)) { // Check for non-Windows OS before chmod
        echo "smart_monolith.py set to executable.\n";
    } elseif (stristr(PHP_OS, 'WIN')) {
        echo "smart_monolith.py created. (chmod not applicable on Windows).\n";
    } else {
        echo "Error setting smart_monolith.py to executable.\n";
    }
} else {
    echo "Error creating smart_monolith.py.\n";
}

if (file_put_contents("download_articles.sh", $download_sh_content)) {
    echo "download_articles.sh created successfully.\n";
    // Convert line endings to LF for shell script, just in case PHP defaults to CRLF on Windows
    $file_content = file_get_contents("download_articles.sh");
    $file_content = str_replace("\r\n", "\n", $file_content);
    file_put_contents("download_articles.sh", $file_content);

    if (!stristr(PHP_OS, 'WIN') && chmod("download_articles.sh", 0755)) { // Check for non-Windows OS before chmod
        echo "download_articles.sh set to executable.\n";
    } elseif (stristr(PHP_OS, 'WIN')) {
        echo "download_articles.sh created. (chmod not applicable on Windows. Ensure it has LF line endings if using WSL/Git Bash).\n";
    } else {
        echo "Error setting download_articles.sh to executable.\n";
    }
} else {
    echo "Error creating download_articles.sh.\n";
}

echo "\nAll files generated!\n";
echo "The generated files (README.md, runner.py, smart_monolith.py, download_articles.sh) are now ready to be committed to your repository.\n";
echo "\nTypical next steps for a USER of your repository would be:\n";
echo "1. Ensure Python is installed, then install dependencies: pip install requests beautifulsoup4\n";
echo "2. To get article content (choose one method):\n";
echo "   a) Download raw HTML: ./download_articles.sh (ensure it's executable: chmod +x download_articles.sh on Linux/macOS)\n";
echo "   b) Download and parse to text: python runner.py\n";
echo "3. If you ran 'python runner.py', generate the browsable HTML monolith: python smart_monolith.py\n";
echo "4. Open rendered_results/smart_monolith.html in a browser.\n";

?>