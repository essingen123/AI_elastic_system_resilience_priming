#!/bin/sh
# This is the file 'g'
# Its purpose is to find and execute g.sh in the current directory.

# Get the directory where this 'g' script itself is located.
# This helps if 'g' is in PATH and called from elsewhere, but 'g.sh' is next to it.
# However, for your described use case (chmod +x g in the project root),
# current directory is more relevant for finding g.sh.
# SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)" # More robust for finding script's own dir
CURRENT_DIR="." # Assume g.sh is in the current working directory

G_SHELL_SCRIPT="g.sh"

# Check if g.sh exists in the current directory
if [ -f "${CURRENT_DIR}/${G_SHELL_SCRIPT}" ]; then
    # If g.sh is not executable, this 'g' script could try to make it so,
    # but you specifically want to `chmod +x g` yourself.
    # So, 'g' will assume 'g.sh' might also need to be executable
    # or that it will be run by explicitly calling a shell.

    # For maximum compatibility if g.sh might not have +x itself yet:
    # Execute g.sh using sh (or bash if g.sh specifically needs bash features)
    # This bypasses the need for g.sh to be executable if it has a shebang.
    if [ -x "${CURRENT_DIR}/${G_SHELL_SCRIPT}" ]; then
        exec "${CURRENT_DIR}/${G_SHELL_SCRIPT}" "$@" # Use exec to replace 'g' process with g.sh
    else
        # Attempt to run with sh if not executable. g.sh should have a #!/bin/bash or #!/bin/sh
        echo "INFO: '${G_SHELL_SCRIPT}' not executable, attempting to run with 'sh'..." >&2
        sh "${CURRENT_DIR}/${G_SHELL_SCRIPT}" "$@"
        exit $? # Exit with the status of g.sh
    fi
else
    echo "Error: The companion script '${G_SHELL_SCRIPT}' was not found in the current directory." >&2
    echo "Please ensure both 'g' (this script) and '${G_SHELL_SCRIPT}' are in your project root." >&2
    exit 1
fi