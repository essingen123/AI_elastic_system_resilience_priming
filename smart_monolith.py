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