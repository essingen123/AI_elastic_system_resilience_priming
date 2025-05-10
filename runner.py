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