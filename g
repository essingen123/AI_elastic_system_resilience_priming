#!/bin/sh
# g - Finds and executes g.sh

# Assumes g.sh is in the current directory or next to this launcher
CURRENT_DIR="."
G_SHELL_SCRIPT_TO_RUN="$1" # Expect g.sh to be passed as first argument for flexibility

if [ -z "$G_SHELL_SCRIPT_TO_RUN" ]; then
    G_SHELL_SCRIPT_TO_RUN="g.sh" # Default if no arg
fi

TARGET_SCRIPT_PATH="${CURRENT_DIR}/${G_SHELL_SCRIPT_TO_RUN}"

if [ -f "${TARGET_SCRIPT_PATH}" ]; then
    if [ -x "${TARGET_SCRIPT_PATH}" ]; then
        exec "${TARGET_SCRIPT_PATH}" "${@:2}" # Pass remaining args
    else
        # echo "INFO: '${TARGET_SCRIPT_PATH}' not executable, trying with sh..." >&2
        sh "${TARGET_SCRIPT_PATH}" "${@:2}"
        exit $?
    fi
else
    echo "Error: Target shell script '${TARGET_SCRIPT_PATH}' not found." >&2
    exit 1
fi