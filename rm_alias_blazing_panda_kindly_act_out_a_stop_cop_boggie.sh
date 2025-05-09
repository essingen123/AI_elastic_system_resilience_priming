#!/bin/bash
# rm_alias_blazing_panda_kindly_act_out_a_stop_cop_boggie.sh
#
# Suggests aliasing 'rm' to something safer and provides instructions.
# This script DOES NOT automatically create the alias.

# Emojis
E_PANDA="🐼"
E_POLICE="👮"
E_DANCE="💃"
E_WARN="⚠️"
E_INFO="ℹ️"
E_OK="✅"
E_TOOLBOX="🧰"
E_LIGHTBULB="💡"

echo "$E_PANDA $E_POLICE $E_DANCE Welcome to the Blazing Panda's Kindly Stop-Cop Boogie for 'rm'!"
echo "---------------------------------------------------------------------------------"
echo "$E_INFO This script helps you consider making 'rm' (the remove command) a bit safer."
echo "$E_WARN 'rm -rf' can be very dangerous! One small typo can lead to disaster."
echo ""

# Check current 'rm' alias (this is a bit shell-dependent in its output)
CURRENT_RM_ALIAS=$(alias rm 2>/dev/null)

if [ -n "$CURRENT_RM_ALIAS" ]; then
    echo "$E_INFO Current 'rm' alias detected:"
    echo "  $CURRENT_RM_ALIAS"
    echo "$E_INFO If this already includes safety flags like '-i' (interactive), you might be good!"
else
    echo "$E_INFO No specific alias found for 'rm'. Using the system default."
fi

echo ""
echo "$E_LIGHTBULB Here are some ideas for safer 'rm' usage:"
echo ""
echo "1. $E_TOOLBOX Always use the interactive flag '-i':"
echo "   This makes 'rm' ask for confirmation before deleting each file."
echo "   Example alias for your shell configuration file (e.g., ~/.bashrc, ~/.zshrc):"
echo "     alias rm='rm -i'"
echo ""
echo "2. $E_TOOLBOX Consider a 'safe-rm' or 'trash' utility:"
echo "   These tools move files to a trash directory instead of deleting them permanently."
echo "   You might need to install them (e.g., 'trash-cli' on Linux/macOS via pip/brew)."
echo "   Example alias (if you install 'trash-cli' and want 'rm' to use it):"
echo "     alias rm='trash-put'"
echo ""
echo "3. $E_TOOLBOX For batch deletions (like 'rm -rf dir/*'), use a validation script:"
echo "   (Like the 'human_validate_delete.sh' we discussed previously)"
echo "   This involves listing files first, then confirming before actual deletion."
echo ""

# Determine user's shell (basic check)
USER_SHELL=$(basename "$SHELL")
CONFIG_FILE=""
if [ "$USER_SHELL" = "bash" ]; then
    CONFIG_FILE="~/.bashrc (or ~/.bash_profile for login shells)"
elif [ "$USER_SHELL" = "zsh" ]; then
    CONFIG_FILE="~/.zshrc"
elif [ "$USER_SHELL" = "fish" ]; then
    CONFIG_FILE="~/.config/fish/config.fish (use 'alias rm \"rm -i\"')"
else
    CONFIG_FILE="your shell's configuration file"
fi

echo "$E_INFO To apply an alias like 'alias rm=\"rm -i\"' permanently:"
echo "  1. Open your shell configuration file: $CONFIG_FILE"
echo "  2. Add the alias line (e.g., alias rm='rm -i') to the file and save it."
echo "  3. For the change to take effect, either:"
echo "     a) Source the file (e.g., 'source ~/.bashrc' or 'source ~/.zshrc')"
echo "     b) Or simply open a new terminal window."
echo ""
echo "$E_OK Remember, the Blazing Panda wants your files to be safe! $E_PANDA"
echo "---------------------------------------------------------------------------------"