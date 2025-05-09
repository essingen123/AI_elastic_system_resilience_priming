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