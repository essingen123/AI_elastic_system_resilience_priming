#!/bin_sh_or_bash_or_whatever_runs_this_g_file
# This is the file 'g'
# It tries its best to find and run g.sh using a common shell.

# Determine the directory of this 'g' script itself.
# This helps if 'g' is in PATH and called from anywhere,
# and g.sh is expected to be in the same directory as 'g'.
SCRIPT_DIR_G="$(cd "$(dirname -- "$0")" && pwd -P)"

# The target script we want to run
TARGET_SCRIPT_NAME="g.sh"
TARGET_SCRIPT_PATH="${SCRIPT_DIR_G}/${TARGET_SCRIPT_NAME}"

# Function to attempt execution
try_execute() {
    local interpreter="$1"
    local script_to_run="$2"
    shift 2 # Remove interpreter and script from arguments, pass rest
    if command -v "$interpreter" >/dev/null 2>&1; then
        echo "Attempting to run with: $interpreter $script_to_run ..." >&2
        "$interpreter" "$script_to_run" "$@" # Pass remaining arguments
        return $?
    fi
    return 127 # Command not found
}

if [ -f "${TARGET_SCRIPT_PATH}" ]; then
    # Try direct execution first (if g.sh is +x and has a shebang)
    if [ -x "${TARGET_SCRIPT_PATH}" ]; then
        echo "Attempting direct execution of: ${TARGET_SCRIPT_PATH} ..." >&2
        # Use exec to replace this process, more efficient
        exec "${TARGET_SCRIPT_PATH}" "$@"
        # If exec fails (e.g., permission denied despite -x, or bad shebang), it will continue.
        # This is unlikely if -x check passes, but as a fallback:
        echo "Direct execution failed, trying specific interpreters..." >&2
    fi

    # Try with bash explicitly
    try_execute "bash" "${TARGET_SCRIPT_PATH}" "$@"
    exit_status=$?
    if [ $exit_status -eq 0 ]; then exit 0; fi # Success
    if [ $exit_status -ne 127 ]; then # 127 means bash not found, any other error means bash found but script failed
        echo "Running with bash failed (exit code $exit_status)." >&2
        exit $exit_status
    fi


    # Fallback to sh (more POSIX, might work if bash isn't default or g.sh is simple)
    try_execute "sh" "${TARGET_SCRIPT_PATH}" "$@"
    exit_status=$?
    if [ $exit_status -eq 0 ]; then exit 0; fi
    if [ $exit_status -ne 127 ]; then
        echo "Running with sh failed (exit code $exit_status)." >&2
        exit $exit_status
    fi

    echo "Error: Could not execute '${TARGET_SCRIPT_NAME}' with bash or sh." >&2
    echo "Please ensure bash or sh is installed and '${TARGET_SCRIPT_NAME}' is runnable." >&2
    exit 1

else
    echo "Error: The target script '${TARGET_SCRIPT_NAME}' was not found at '${TARGET_SCRIPT_PATH}'." >&2
    echo "Please ensure both 'g' (this script) and '${TARGET_SCRIPT_NAME}' are correctly placed." >&2
    exit 1
fi