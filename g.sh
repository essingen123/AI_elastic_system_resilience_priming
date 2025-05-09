#!/bin/bash
# g.sh - Shortcut to run the G-Git PHP sync script

# This script expects 'g_git_auto_git_grit_sync.php' to be in the same directory.
PHP_SCRIPT_TO_RUN="./g_git_auto_git_grit_sync.php"

if [ -f "$PHP_SCRIPT_TO_RUN" ]; then
    php "$PHP_SCRIPT_TO_RUN" "$@" # Pass all arguments
else
    echo "Error: PHP sync script '$PHP_SCRIPT_TO_RUN' not found." >&2
    exit 1
fi