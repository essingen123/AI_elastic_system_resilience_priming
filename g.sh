#!/bin/bash

# g.sh - Shortcut to run the G-Git Auto Git Grit Sync PHP script

# Option 1: If g_git_auto_git_grit_sync.php is in the same directory as g.sh
# SCRIPT_PATH="$(dirname "$0")/g_git_auto_git_grit_sync.php"

# Option 2: If g_git_auto_git_grit_sync.php is expected to be in a specific location
# or you want to make g.sh more portable (e.g., place g.sh in /usr/local/bin)
# you might need to adjust the path or assume it's in the PATH.
# For simplicity, let's assume it's in the current directory when g.sh is run,
# or you can make SCRIPT_PATH absolute if g_git_auto_git_grit_sync.php has a fixed location.

# Let's assume g_git_auto_git_grit_sync.php is in the current directory
# where g.sh is being executed from (i.e., you run ./g.sh from your project root)
SCRIPT_NAME="g_git_auto_git_grit_sync.php"

# Check if the PHP script exists in the current directory
if [ -f "./${SCRIPT_NAME}" ]; then
    php "./${SCRIPT_NAME}" "$@" # Pass all arguments from g.sh to the PHP script
elif [ -f "$(dirname "$0")/${SCRIPT_NAME}" ]; then # Check if it's next to g.sh itself
    php "$(dirname "$0")/${SCRIPT_NAME}" "$@"
else
    # Try to find it if g.sh is in PATH and script is in current dir of execution
    if command -v php >/dev/null 2>&1 && [ -f "${SCRIPT_NAME}" ]; then
        php "${SCRIPT_NAME}" "$@"
    else
        echo "Error: ${SCRIPT_NAME} not found."
        echo "Please ensure it's in the current directory, or next to g.sh, or adjust SCRIPT_PATH in g.sh."
        exit 1
    fi
fi