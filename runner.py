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