<?php
// generate_project_files.php
// This script generates the core project files.
// It contains the "source of truth" for README.md, runner.py, smart_monolith.py, and download_articles.sh.

// Variable for the triple backticks, ESSENTIAL for README.md to avoid chat markdown issues
$triple_backtick = '```';
$bash_lang = 'bash'; // Default language for bash code blocks in README
$python_lang = 'python'; // For python code blocks in README

// --- Content for README.md ---
// This is the latest README content based on our discussion.
$readme_content = <<<"EOD"
# Resilient Elastic System "Mind" Priming for LLMs
*Unlocking Dynamic Capabilities in Browser-Based AI Agents*

**Suggested Container/Repository Name:** AI_elastic_system_resilience_priming

Dive into the core concepts powering the next generation of AI systems that don't just run, but adapt, heal, and orchestrate complex tasks directly within the browser. This repository presents a unique approach to 'priming' Artificial Intelligence models, especially those with limited context or complexity handling, by curating and ordering foundational technical and philosophical concepts vital for building resilient, dynamic, and incredibly flexible AI agents.

---

## Getting Started

To set up the project and generate the knowledge monolith:

1.  **Clone this repository (or use these generated files):**
    If you are setting this up from an empty directory after running the PHP generator script, you can skip the clone. If you are cloning an existing repo that contains these files:
    {$triple_backtick}{$bash_lang}
    git clone https://github.com/essingen123/AI_elastic_system_resilience_priming.git
    cd AI_elastic_system_resilience_priming
    {$triple_backtick}

2.  **Install Required Python Libraries:**
    Ensure you have Python installed (3.6+ recommended). Open your terminal and run:
    {$triple_backtick}{$bash_lang}
    pip install requests beautifulsoup4
    {$triple_backtick}

3.  **Download and Parse Articles:**
    Run the Python script to fetch and parse the Wikipedia articles:
    {$triple_backtick}{$python_lang}
    python runner.py
    {$triple_backtick}
    This will create a `parsed_articles` directory containing the text content.

4.  **Generate the Knowledge Monolith:**
    Run the Python script to create the interactive HTML file:
    {$triple_backtick}{$python_lang}
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

{$triple_backtick}{$python_lang}
python runner.py
{$triple_backtick}

### Method 3: Generate a Smart Monolith HTML File (`smart_monolith.py`)

This script reads the parsed text files from `parsed_articles` and generates a single, self-contained HTML file (`smart_monolith.html`) in the `rendered_results` directory. This HTML file presents the article content interactively, allowing you to toggle the visibility of each article.

{$triple_backtick}{$python_lang}
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
// This is the latest runner.py content.
$runner_py_content = <<<'EOD'
import requests
from bs4 import BeautifulSoup
import os

# List of Wikipedia URLs to parse
# Order 1: Philosophy to Practice
wikipedia_urls = [
    "https://en.wikipedia.org/wiki/Worse_is_better",
    "https://en.wikipedia.org/wiki/Rapid_application_development",
    "https://en.wikipedia.org/wiki/Procedural_programming",
    "https://en.wikipedia.org/wiki/Monolithic_application",
    "https://en.wikipedia.org/wiki/Dynamic_typing",
    "https://en.wikipedia.org/wiki/Polyglot_programming",
    "https://en.wikipedia.org/wiki/WebAssembly",
    "https://en.wikipedia.org/wiki/Prompt_engineering",
    "https://en.wikipedia.org/wiki/Model_Context_protocol", # This page may not exist
    "https://en.wikipedia.org/wiki/Event_driven_architecture",
    "https://en.wikipedia.org/wiki/Concurrency_(computer_science)",
    "https://en.wikipedia.org/wiki/Finite_state_machine",
    "https://en.wikipedia.org/wiki/Idempotence",
    "https://en.wikipedia.org/wiki/Fault_tolerance",
]

# Directory to save the parsed content
parsed_articles_dir = "parsed_articles"

# --- Setup ---
if not os.path.exists(parsed_articles_dir):
    os.makedirs(parsed_articles_dir)
    print(f"Created directory: '{parsed_articles_dir}'")

print(f"Starting to parse {len(wikipedia_urls)} articles...")
print("-" * 30)

# --- Parsing Logic ---
for url in wikipedia_urls:
    try:
        print(f"Processing: {url}")
        response = requests.get(url, timeout=10) # Added timeout
        response.raise_for_status() 

        soup = BeautifulSoup(response.content, 'html.parser')
        main_content_div = soup.find(id='mw-content-text')

        if main_content_div:
            article_paragraphs = main_content_div.find_all('p', recursive=False) # Only direct children <p>
            
            # Enhanced text extraction logic
            content_texts = []
            for p_tag in article_paragraphs:
                # Attempt to remove common "noise" like edit links, citation needed, etc.
                for sup_tag in p_tag.find_all("sup"): # Remove <sup> tags (often citations, [edit])
                    sup_tag.decompose()
                
                text = p_tag.get_text(separator=' ', strip=True) # Use space as separator for better flow
                if text: # Only add if there's actual text
                    content_texts.append(text)
            
            article_text = "\n\n".join(content_texts) # Join paragraphs with double newline for smart_monolith

            # Sanitize filename (more robustly)
            title_part = url.split("/")[-1]
            sanitized_title = "".join(c if c.isalnum() or c in ('_', '-') else '_' for c in title_part)
            if not sanitized_title: # Handle case where URL ends weirdly
                sanitized_title = "unknown_article_" + str(hash(url)) 

            filename = os.path.join(parsed_articles_dir, f"{sanitized_title}.txt")

            with open(filename, 'w', encoding='utf-8') as f:
                f.write(article_text)
            print(f"  -> Saved to: {filename}")
        else:
            print(f"  -> Warning: Could not find main content div ('mw-content-text') for {url}. Skipping.")

    except requests.exceptions.HTTPError as e:
        if e.response.status_code == 404:
            print(f"  -> Error 404: Page not found at {url}. Skipping.")
        else:
            print(f"  -> HTTP Error fetching {url}: {e}")
    except requests.exceptions.RequestException as e:
        print(f"  -> Error fetching {url}: {e}")
    except Exception as e:
        print(f"  -> An unexpected error occurred processing {url}: {e}")

print("-" * 30)
print("Parsing complete.")
print(f"All attempts processed. Check the '{parsed_articles_dir}' directory for results.")
EOD;

// --- Content for smart_monolith.py ---
// This is the latest smart_monolith.py content.
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

# HTML Template Structure
html_template = """
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resilient Elastic Systems Knowledge Monolith</title>
    <style>
        body {{ font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.7; margin: 0; padding: 0; background-color: #f9f9f9; color: #333; }}
        .header {{ background-color: #2c3e50; color: white; padding: 1em 0; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }}
        .header h1 {{ margin: 0; font-size: 2em; }}
        .container {{ max-width: 960px; margin: 20px auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.07); }}
        .article-title {{ cursor: pointer; color: #3498db; margin-top: 20px; margin-bottom: 8px; font-size: 1.4em; padding: 10px 15px; background-color: #ecf0f1; border-left: 5px solid #3498db; border-radius: 4px; transition: background-color 0.3s ease; }}
        .article-title:hover {{ background-color: #dde4e6; }}
        .article-title.active {{ background-color: #bdc3c7; color: #2c3e50; }}
        .article-content {{ display: none; border: 1px solid #ddd; border-top: none; padding: 15px 20px; margin-bottom: 20px; background-color: #fdfdfd; border-radius: 0 0 4px 4px; }}
        .article-content p {{ margin-bottom: 1em; text-align: justify; }}
        .article-content p:last-child {{ margin-bottom: 0; }}
        .instructions {{ text-align: center; margin-bottom: 25px; color: #555; font-size: 0.95em; }}
        .footer {{ text-align: center; margin-top: 30px; padding: 15px; font-size: 0.9em; color: #7f8c8d; }}
    </style>
</head>
<body>
    <header class="header"><h1>Resilient Elastic Systems Knowledge Monolith</h1></header>
    <div class="container">
        <p class="instructions">This document compiles key concepts for building dynamic, resilient AI systems. Click on an article title to expand/collapse its content.</p>
        <div id="articles-container">
            {{articles_content}}
        </div>
    </div>
    <footer class="footer"><p>© {current_year} Knowledge Aggregator. All rights reserved by original Wikipedia authors.</p></footer>
    <script>
        function toggleArticle(id) {{
            const content = document.getElementById('content-' + id);
            const title = document.querySelector(`[data-id='${{id}}']`);
            if (content.style.display === 'none' || content.style.display === '') {{
                content.style.display = 'block';
                title.classList.add('active');
            }} else {{
                content.style.display = 'none';
                title.classList.remove('active');
            }}
        }}
        document.addEventListener('DOMContentLoaded', () => {{
            const yearSpan = document.querySelector('.footer p');
            if(yearSpan) yearSpan.innerHTML = yearSpan.innerHTML.replace('{{current_year}}', new Date().getFullYear());
        }});
    </script>
</body>
</html>
"""

# --- Main Script Logic ---
if not os.path.exists(parsed_articles_dir):
    print(f"Error: Directory '{parsed_articles_dir}' not found. Run runner.py first.")
    exit()

# Use the same URL list as runner.py to determine file order and titles
ordered_article_info = [
    ("Worse_is_better", "Worse is better"),
    ("Rapid_application_development", "Rapid application development"),
    ("Procedural_programming", "Procedural programming"),
    ("Monolithic_application", "Monolithic application"),
    ("Dynamic_typing", "Dynamic typing"),
    ("Polyglot_programming", "Polyglot programming"),
    ("WebAssembly", "WebAssembly"),
    ("Prompt_engineering", "Prompt engineering"),
    ("Model_Context_protocol", "Model Context protocol (Note: page might not exist)"),
    ("Event_driven_architecture", "Event driven architecture"),
    ("Concurrency_computer_science", "Concurrency (computer science)"), # runner.py sanitizes ( and )
    ("Finite_state_machine", "Finite state machine"),
    ("Idempotence", "Idempotence"),
    ("Fault_tolerance", "Fault tolerance"),
]

article_files_to_process = []
processed_titles = {} # To store title for filename

for base_name, display_title in ordered_article_info:
    # Construct filename based on runner.py's sanitization logic
    sanitized_base = "".join(c if c.isalnum() or c in ('_', '-') else '_' for c in base_name)
    if not sanitized_base: sanitized_base = "unknown_article_" + str(hash(base_name))
    
    file_path = os.path.join(parsed_articles_dir, f"{sanitized_base}.txt")
    if os.path.exists(file_path):
        article_files_to_process.append(file_path)
        processed_titles[file_path] = display_title
    else:
        print(f"Warning: Expected article file '{sanitized_base}.txt' for '{display_title}' not found. Skipping.")

if not article_files_to_process:
    print(f"Error: No .txt files found based on the predefined order in '{parsed_articles_dir}'.")
    exit()

if not os.path.exists(output_rendered_dir):
    os.makedirs(output_rendered_dir)
    print(f"Created directory: '{output_rendered_dir}'")

articles_html_content = ""
article_id_counter = 0

for file_path in article_files_to_process:
    try:
        article_title_display = processed_titles.get(file_path, os.path.splitext(os.path.basename(file_path))[0].replace("_", " "))
        
        with open(file_path, 'r', encoding='utf-8') as f:
            article_text = f.read()

        escaped_article_text = html.escape(article_text)
        # runner.py now uses "\n\n" between paragraphs
        paragraphs = escaped_article_text.split('\n\n') 
        formatted_text = "".join(f"<p>{p.strip()}</p>" for p in paragraphs if p.strip())

        article_id = f"article-{article_id_counter}"
        articles_html_content += f"""
        <div class="article">
            <h2 class="article-title" data-id='{article_id}' onclick="toggleArticle('{article_id}')">{html.escape(article_title_display)}</h2>
            <div id="content-{article_id}" class="article-content">
                {formatted_text if formatted_text else '<p><em>Content could not be loaded or was empty.</em></p>'}
            </div>
        </div>
        """
        article_id_counter += 1
    except Exception as e:
        print(f"Warning: Could not process file {file_path}: {e}")

final_html_output = html_template.replace("{{articles_content}}", articles_html_content)

try:
    with open(output_html_file, 'w', encoding='utf-8') as f:
        f.write(final_html_output)
    print(f"\nSuccessfully created '{output_html_file}'. Open in browser.")
except Exception as e:
    print(f"Error: Could not write to output file {output_html_file}: {e}")
EOD;

// --- Content for download_articles.sh ---
// This is the latest download_articles.sh content.
$download_sh_content = <<<'EOD'
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
EOD;

// --- File Creation Logic ---
// (This part remains the same as your last version of generate_project_files.php)
echo "Attempting to create/update project files...\n";

if (file_put_contents("README.md", $readme_content)) {
    echo "README.md created/updated successfully.\n";
} else {
    echo "Error creating/updating README.md.\n";
}

if (file_put_contents("runner.py", $runner_py_content)) {
    echo "runner.py created/updated successfully.\n";
    if (!stristr(PHP_OS, 'WIN') && chmod("runner.py", 0755)) {
        echo "runner.py set to executable.\n";
    } elseif (stristr(PHP_OS, 'WIN')) {
        echo "runner.py created/updated. (chmod not applicable on Windows).\n";
    } else {
        echo "Error setting runner.py to executable.\n";
    }
} else {
    echo "Error creating/updating runner.py.\n";
}

if (file_put_contents("smart_monolith.py", $smart_monolith_py_content)) {
    echo "smart_monolith.py created/updated successfully.\n";
    if (!stristr(PHP_OS, 'WIN') && chmod("smart_monolith.py", 0755)) {
        echo "smart_monolith.py set to executable.\n";
    } elseif (stristr(PHP_OS, 'WIN')) {
        echo "smart_monolith.py created/updated. (chmod not applicable on Windows).\n";
    } else {
        echo "Error setting smart_monolith.py to executable.\n";
    }
} else {
    echo "Error creating/updating smart_monolith.py.\n";
}

if (file_put_contents("download_articles.sh", $download_sh_content)) {
    echo "download_articles.sh created/updated successfully.\n";
    $file_content = file_get_contents("download_articles.sh");
    $file_content = str_replace("\r\n", "\n", $file_content); // Ensure LF line endings
    file_put_contents("download_articles.sh", $file_content);

    if (!stristr(PHP_OS, 'WIN') && chmod("download_articles.sh", 0755)) {
        echo "download_articles.sh set to executable.\n";
    } elseif (stristr(PHP_OS, 'WIN')) {
        echo "download_articles.sh created/updated. (chmod not applicable on Windows. Ensure LF line endings if using WSL/Git Bash).\n";
    } else {
        echo "Error setting download_articles.sh to executable.\n";
    }
} else {
    echo "Error creating/updating download_articles.sh.\n";
}

echo "\nAll specified files have been processed by the generator.\n";
echo "These are now ready to be committed to your repository if they reflect your desired state.\n";

?>