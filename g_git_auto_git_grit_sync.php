<?php
// g_git_auto_git_grit_sync.php
// Version: 2023-10-28_GitGrit_v1.2_Bootstrap
date_default_timezone_set('UTC');
$scriptVersion = "GitGrit_v1.2_Bootstrap (" . date('Y-m-d') . ")";

$thisScriptName = basename(__FILE__); // Get the name of this PHP script itself
$configFileName = "g_git_auto_git_grit_sync_2_config_4_humains_etc_config.vonBaronAfNullConfig";
$gShellScriptName = "g.sh";
$gLauncherName = "g";
$defaultBranch = "main";

// Emojis
$e_sparkle = "✨"; $e_config_file = "⚙️"; $e_link = "🔗"; $e_rocket = "🚀"; $e_wrench = "🔧";
$e_floppy = "💾"; $e_question = "❓"; $e_warn = "⚠️"; $e_info = "ℹ️";
$e_ok = "✅"; $e_party = "🎉"; $e_git = "🐙"; $e_sync = "🔄"; $e_toad = "🐸"; $e_bash = "🐧"; // Penguin for shell

echo "$e_sparkle G-Git Auto Git Grit Sync - v{$scriptVersion} $e_sparkle\n";
echo "Run timestamp: " . date('Y-m-d H:i:s T') . "\n";
echo "---------------------------------------------------\n";

// --- Helper Functions ---
function executeCommand($command, &$output_lines = [], &$return_var = null, $show_output = true) {
    global $e_git, $e_warn; // Access global emojis
    echo "$e_git [CMD] $command\n";
    $full_command = $command . ' 2>&1';
    $output_lines = [];
    if ($show_output) {
        passthru($full_command, $passthru_return_val); $return_var = $passthru_return_val;
    } else {
        exec($full_command, $output_lines, $exec_return_val); $return_var = $exec_return_val;
    }
    if ($return_var !== 0) {
        $is_git_commit = (strpos($command, "git commit") === 0);
        $is_nothing_to_commit = false;
        if ($is_git_commit && !$show_output && !empty($output_lines)) {
            foreach($output_lines as $line) {
                if (strpos($line, "nothing to commit") !== false || strpos($line, "no changes added to commit") !== false) {
                    $is_nothing_to_commit = true; break;
                }
            }
        }
        if (!$is_nothing_to_commit) {
            echo "$e_warn [ERROR] Command failed (code $return_var): $command\n";
            if (!$show_output && !empty($output_lines)) { foreach ($output_lines as $line) { echo "[OUTPUT] $line\n"; } }
        }
        return false;
    }
    return true;
}

function captureCommandOutput($command, &$return_var = null) {
    $output_lines = [];
    if (executeCommand($command, $output_lines, $return_var, false)) return implode("\n", $output_lines);
    return null;
}

function askUser($prompt, $default = '') {
    global $e_question; // Access global emoji
    echo "$e_question [ACTION] $prompt" . ($default ? " [$default]" : "") . ": ";
    $input = trim(fgets(STDIN));
    return $input === '' ? $default : $input;
}

function isValidGithubHttpsUrl($url) { return preg_match('|^https://github.com/([^/]+)/([^/.]+?)(\.git)?$|', $url); }
function convertToSshUrl($httpsUrl) {
    if (preg_match('|^https://github.com/([^/]+)/([^/.]+?)(\.git)?$|', $httpsUrl, $matches)) return "git@github.com:{$matches[1]}/{$matches[2]}.git";
    return null;
}

function generateThematicCommitMessage() {
    global $e_sync, $e_toad, $e_sparkle;
    $actions = ["Conjure", "Weave", "Forge", "Sculpt", "Channel", "Align", "Harmonize", "Unify", "Evolve", "Synthesize"];
    $adjectives = ["Cosmic", "Starlight", "Runic", "Zephyr", "Quantum", "Arcane", "Fierce", "Silent", "Whimsical", "Nebula", "Astral", "Lunar", "Solar", "Verdant", "Crystal"];
    $nouns = ["Codex", "Matrix", "Algorithm", "Cipher", "Scriptum", "Byteflux", "Vector", "GitGrit", "Continuum", "Echo", "Relay", "Oracle", "Beacon"];
    $particles = ["von", "de", "le", "el", "zog", "klor", "ish", "flux", "zen", "qua"];
    $date_str = date("Y-m-d H:i");
    return sprintf("%s %s %s %s %s %s %s | %s", $e_sync, $actions[array_rand($actions)], $adjectives[array_rand($adjectives)], $particles[array_rand($particles)], $nouns[array_rand($nouns)], $e_toad, $e_sparkle, $date_str);
}

function readConfigFile($filePath, $placeholderUrl) {
    global $e_warn, $e_info;
    $settings = ['REPO_URL_HTTPS' => $placeholderUrl, 'AUTO_SYNC_ENABLED' => false]; // Defaults
    if (!file_exists($filePath)) return $settings;

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        echo "$e_warn Warning: Could not read config file '$filePath'. Using defaults.\n";
        return $settings;
    }
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key); $value = trim($value);
            if (array_key_exists($key, $settings)) {
                if ($key === 'AUTO_SYNC_ENABLED') {
                    $settings[$key] = (strtolower($value) === 'true');
                } else {
                    $settings[$key] = $value;
                }
            }
        }
    }
    return $settings;
}

function writeConfigFile($filePath, array $settings, $placeholderUrl) {
    global $e_ok, $e_floppy, $e_warn;
    $urlToWrite = empty($settings['REPO_URL_HTTPS']) || $settings['REPO_URL_HTTPS'] === $placeholderUrl ? $placeholderUrl : $settings['REPO_URL_HTTPS'];
    $content = "# $filePath\n";
    $content .= "# G-Git Auto Git Grit Sync Configuration\n";
    $content .= "# Human-readable, vonBaronAfNullConfig approved!\n";
    $content .= "# Please edit REPO_URL_HTTPS with your target GitHub repository's HTTPS URL.\n";
    $content .= "REPO_URL_HTTPS=" . $urlToWrite . "\n";
    $content .= "AUTO_SYNC_ENABLED=" . ($settings['AUTO_SYNC_ENABLED'] ? 'true' : 'false') . "\n";

    if (file_put_contents($filePath, $content) !== false) {
        echo "$e_ok $e_floppy Configuration written to '$filePath'.\n";
        return true;
    } else {
        echo "$e_warn Error: Could not write to config file '$filePath'.\n";
        return false;
    }
}

function ensureLauncherScriptsExist($phpScriptName, $gShellScriptName, $gLauncherName) {
    global $e_ok, $e_warn, $e_bash, $e_wrench;
    $gShellContent = <<<BASH
#!/bin/bash
# $gShellScriptName - Shortcut to run the G-Git PHP sync script

# This script expects '$phpScriptName' to be in the same directory.
PHP_SCRIPT_TO_RUN="./$phpScriptName"

if [ -f "\$PHP_SCRIPT_TO_RUN" ]; then
    php "\$PHP_SCRIPT_TO_RUN" "\$@" # Pass all arguments
else
    echo "Error: PHP sync script '\$PHP_SCRIPT_TO_RUN' not found." >&2
    exit 1
fi
BASH;

    $gLauncherContent = <<<SH
#!/bin/sh
# $gLauncherName - Finds and executes $gShellScriptName

# Assumes $gShellScriptName is in the current directory or next to this launcher
CURRENT_DIR="."
G_SHELL_SCRIPT_TO_RUN="\$1" # Expect g.sh to be passed as first argument for flexibility

if [ -z "\$G_SHELL_SCRIPT_TO_RUN" ]; then
    G_SHELL_SCRIPT_TO_RUN="$gShellScriptName" # Default if no arg
fi

TARGET_SCRIPT_PATH="\${CURRENT_DIR}/\${G_SHELL_SCRIPT_TO_RUN}"

if [ -f "\${TARGET_SCRIPT_PATH}" ]; then
    if [ -x "\${TARGET_SCRIPT_PATH}" ]; then
        exec "\${TARGET_SCRIPT_PATH}" "\${@:2}" # Pass remaining args
    else
        # echo "INFO: '\${TARGET_SCRIPT_PATH}' not executable, trying with sh..." >&2
        sh "\${TARGET_SCRIPT_PATH}" "\${@:2}"
        exit \$?
    fi
else
    echo "Error: Target shell script '\${TARGET_SCRIPT_PATH}' not found." >&2
    exit 1
fi
SH;

    $scriptsCreated = 0;
    if (!file_exists($gShellScriptName)) {
        if (file_put_contents($gShellScriptName, $gShellContent) !== false) {
            chmod($gShellScriptName, 0755);
            echo "$e_ok $e_bash Launcher '$gShellScriptName' created and made executable.\n";
            $scriptsCreated++;
        } else {
            echo "$e_warn Failed to create '$gShellScriptName'.\n";
        }
    }

    if (!file_exists($gLauncherName)) {
        // Pass g.sh as an argument to the 'g' launcher's content generation
        // This is a bit meta, the 'g' script itself can be generic but here we customize its default.
        $gLauncherPersonalizedContent = str_replace('"$gShellScriptName"', '"'.$gShellScriptName.'"', $gLauncherContent);

        if (file_put_contents($gLauncherName, $gLauncherPersonalizedContent) !== false) {
            chmod($gLauncherName, 0755);
            echo "$e_ok $e_wrench Universal launcher '$gLauncherName' created and made executable.\n";
            $scriptsCreated++;
        } else {
            echo "$e_warn Failed to create '$gLauncherName'.\n";
        }
    }
    if ($scriptsCreated > 0) {
        echo "$e_info You can now run the sync using './$gLauncherName' (which calls './$gShellScriptName').\n";
    }
}


// --- Initial Setup: Config and Launcher Scripts ---
$placeholderUrl = "PASTE_YOUR_GITHUB_HTTPS_REPO_URL_HERE";
ensureLauncherScriptsExist($thisScriptName, $gShellScriptName, $gLauncherName); // Create g and g.sh if they don't exist

$configSettings = readConfigFile($configFileName, $placeholderUrl);
$repoUrlHttps = $configSettings['REPO_URL_HTTPS'];
$autoSyncEnabled = $configSettings['AUTO_SYNC_ENABLED']; // boolean
$configChangedDuringSession = false;

if (!file_exists($configFileName) || empty($repoUrlHttps) || $repoUrlHttps === $placeholderUrl) {
    if(!file_exists($configFileName)) echo "$e_info $e_config_file Config file '$configFileName' not found. Creating with placeholder URL.\n";
    else if (empty($repoUrlHttps) || $repoUrlHttps === $placeholderUrl) echo "$e_warn $e_config_file Config file found, but REPO_URL_HTTPS is missing or is a placeholder.\n";
    
    $configSettings['REPO_URL_HTTPS'] = $placeholderUrl; // Ensure placeholder is set
    $configSettings['AUTO_SYNC_ENABLED'] = false;      // Default to false
    writeConfigFile($configFileName, $configSettings, $placeholderUrl);
    echo "$e_info Please edit '$configFileName', replace '$placeholderUrl' with your target GitHub repo's HTTPS URL, and re-run (e.g., using './$gLauncherName').\n";
    exit(0);
}


if (!isValidGithubHttpsUrl($repoUrlHttps)) {
    echo "$e_warn Invalid GitHub HTTPS URL format in '$configFileName': $repoUrlHttps\n";
    echo "$e_info Expected format: https://github.com/username/repository.git (or without .git).\n";
    echo "$e_info Please correct it in '$configFileName' and re-run.\n";
    exit(1);
}

// --- Auto-sync mode interaction ---
if ($autoSyncEnabled) {
    echo "$e_sync Auto-sync mode is ON. Proceeding with Git operations...\n";
} else {
    $enableAuto = askUser("Auto-sync mode is OFF. Enable for future runs? (yes/NO)", "NO");
    if (strtolower($enableAuto) === 'yes' || strtolower($enableAuto) === 'y') {
        $configSettings['AUTO_SYNC_ENABLED'] = true;
        $configChangedDuringSession = true;
        echo "$e_info Auto-sync will be enabled for the next run.\n";
    }

    $syncNow = askUser("Proceed with sync for THIS session? (YES/no)", "YES");
    if (!(strtolower($syncNow) === 'yes' || strtolower($syncNow) === 'y')) {
        if ($configChangedDuringSession) {
            writeConfigFile($configFileName, $configSettings, $placeholderUrl);
        }
        echo "$e_info Sync for this session cancelled by user.\n";
        exit(0);
    }
}
if ($configChangedDuringSession) { // Save any changes to auto-sync preference
    writeConfigFile($configFileName, $configSettings, $placeholderUrl);
}


// --- Proceed with Git Operations ---
$repoUrlSsh = convertToSshUrl($repoUrlHttps);
if (!$repoUrlSsh) {
    echo "$e_warn Could not convert HTTPS URL to SSH format: $repoUrlHttps\n"; exit(1);
}
echo "$e_info $e_link Target HTTPS: $repoUrlHttps\n";
echo "$e_info $e_link Using SSH for git: $repoUrlSsh\n";

if (!is_dir(".git")) {
    echo "$e_info No .git directory. Initializing new Git repository...\n";
    if (!executeCommand("git init -b $defaultBranch")) exit(1);
    echo "$e_ok Git repository initialized.\n";
} else {
    echo "$e_info Existing Git repository found.\n";
}

$currentRemoteUrl = trim(captureCommandOutput("git remote get-url origin"));
if (empty($currentRemoteUrl)) {
    echo "$e_info No remote 'origin'. Adding: $repoUrlSsh\n";
    if (!executeCommand("git remote add origin " . escapeshellarg($repoUrlSsh))) exit(1);
} elseif ($currentRemoteUrl !== $repoUrlSsh) {
    echo "$e_warn Remote 'origin' points to '$currentRemoteUrl'.\n";
    echo "$e_info Updating 'origin' to: $repoUrlSsh\n";
    if (!executeCommand("git remote set-url origin " . escapeshellarg($repoUrlSsh))) exit(1);
} else {
    echo "$e_info Remote 'origin' correctly configured.\n";
}

echo "$e_info Staging all changes...\n";
if (!executeCommand("git add .")) exit(1);

$commitMsg = generateThematicCommitMessage();
echo "$e_info Attempting commit: '$commitMsg'\n";
$commit_output_lines = []; $commit_ret_var = null;
executeCommand("git commit -m " . escapeshellarg($commitMsg), $commit_output_lines, $commit_ret_var, false);

$nothingToCommit = false;
if ($commit_ret_var !== 0) {
    if(!empty($commit_output_lines)) {
        foreach($commit_output_lines as $line) {
            if (strpos($line, "nothing to commit") !== false || strpos($line, "no changes added to commit") !== false) {
                $nothingToCommit = true; break;
            }
        }
    }
    if ($nothingToCommit) echo "$e_info No new changes to commit.\n";
    else echo "$e_warn Commit command exited with code $commit_ret_var. Local changes might not be committed.\n";
} else {
    echo "$e_ok Local changes committed.\n";
}

// --- Pull logic ---
echo "$e_info Pulling remote changes for current branch (if any)...\n";
$pull_output_lines = []; $pull_ret_var = null;
// Assumes user is on the branch they want to sync (HEAD)
if (!executeCommand("git pull origin HEAD --rebase=merges --autostash", $pull_output_lines, $pull_ret_var, true)) { // Try rebase to keep history cleaner, autostash local changes
    $pull_output_str = implode("\n", $pull_output_lines);
    if (strpos($pull_output_str, 'fix conflicts and then commit the result') !== false ||
        strpos($pull_output_str, 'Automatic merge failed') !== false ||
        strpos($pull_output_str, 'you have unmerged paths') !== false ||
        strpos($pull_output_str, 'conflict') !== false) { // Broader conflict check
        echo "$e_warn $e_warn CRITICAL: MERGE/REBASE CONFLICTS DETECTED after pull attempt.\n";
        echo "$e_info Please resolve the conflicts manually in your editor, then:\n";
        echo "1. `git add <resolved-files>`\n";
        echo "2. `git rebase --continue` (if rebase was in progress) or `git commit` (if a merge was attempted and failed mid-way)\n";
        echo "$e_info After resolving and committing, you can try syncing again or push manually.\n";
        exit(1);
    } else {
        echo "$e_warn Pull failed for other reasons. Check output above. Manual intervention may be needed.\n";
        // Don't exit here, let it try to push. If push fails, then user definitely needs to intervene.
    }
} else {
    echo "$e_ok Pull successful (or no new changes from remote).\n";
}


// --- Push logic ---
echo "$e_info Attempting standard push to origin (current branch)...\n";
$push_output_lines = []; $push_ret_var = null;
if (executeCommand("git push origin HEAD", $push_output_lines, $push_ret_var, true)) {
    echo "$e_ok $e_rocket Push successful! All synced.\n";
} else {
    $push_output_str = implode("\n", $push_output_lines);
    $isNonFastForward = (strpos($push_output_str, 'rejected') !== false && 
                        (strpos($push_output_str, 'non-fast-forward') !== false || 
                         strpos($push_output_str, 'remote contains work that you do') !== false ||
                         strpos($push_output_str, 'failed to push some refs') !== false ));

    if ($isNonFastForward) {
        echo "$e_warn Standard push failed because remote has diverged (non-fast-forward).\n";
        echo "$e_info This usually means someone else pushed changes since your last pull/sync.\n";
        $forceConfirm = askUser("Attempt to force push (WITH LEASE) to make local the 'Single Source of Truth'? $e_warn DANGEROUS - can overwrite remote changes! (yes/NO)", "NO");

        if (strtolower($forceConfirm) === 'yes' || strtolower($forceConfirm) === 'y') {
            $reallySure = askUser("ARE YOU ABSOLUTELY SURE? This can't be undone easily. Type 'YES_OVERWRITE_REMOTE' to confirm:", "NO");
            if ($reallySure === 'YES_OVERWRITE_REMOTE') {
                echo "$e_warn $e_warn Force pushing (with lease) local branch to origin...\n";
                if (executeCommand("git push --force-with-lease origin HEAD")) {
                    echo "$e_ok $e_rocket Force push (with lease) successful!\n";
                } else {
                    echo "$e_warn Force push (with lease) failed. Check errors.\n";
                }
            } else {
                echo "$e_info Force push cancelled. Manual resolution needed (e.g., pull again, merge/rebase, then try standard push).\n";
            }
        } else {
            echo "$e_info Force push not chosen. Manual resolution needed. Try `git pull` again, then `git push`.\n";
        }
    } else {
        echo "$e_warn Standard push failed for other reasons. Check output above. You might need to `git pull` first if there were remote changes.\n";
    }
}

echo "---------------------------------------------------\n";
echo "$e_party G-Git Grit Sync process complete! $e_party\n";
?>