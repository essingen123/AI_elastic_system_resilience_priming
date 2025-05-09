<?php
// sync_master_interactive.php - Refined GitHub Sync & Tools Script

$configFile = ".github_sync_repo_url.txt";
$defaultBranch = "main";
$repoUrlHttps = '';
$repoUrlSsh = '';

// --- (Helper functions: executeCommand, captureCommandOutput, askUser, URL helpers, isGhCliInstalled, generateCoolRepoName, generateCoolCommitMessage - remain the same as your latest version) ---
// --- Helper Function to Execute Shell Commands ---
function executeCommand($command, &$output_lines = [], &$return_var = null, $show_output = true) {
    echo "[CMD] $command\n";
    $full_command = $command . ' 2>&1'; // Capture stderr to stdout
    $output_lines = [];
    
    if ($show_output) {
        passthru($full_command, $passthru_return_val);
        $return_var = $passthru_return_val;
    } else {
        exec($full_command, $output_lines, $exec_return_val);
        $return_var = $exec_return_val;
    }

    if ($return_var !== 0) {
        $is_git_commit = (strpos($command, "git commit") === 0);
        $is_nothing_to_commit = false;
        if ($is_git_commit && !$show_output && !empty($output_lines)) {
            foreach($output_lines as $line) {
                if (strpos($line, "nothing to commit") !== false || strpos($line, "no changes added to commit") !== false) {
                    $is_nothing_to_commit = true;
                    break;
                }
            }
        }
        if (!$is_nothing_to_commit) {
            echo "[ERROR] Command failed with exit code $return_var: $command\n";
            if (!$show_output && !empty($output_lines)) {
                foreach ($output_lines as $line) { echo "[OUTPUT] $line\n"; }
            }
        }
        return false; 
    }
    return true;
}

function captureCommandOutput($command, &$return_var = null) {
    $output_lines = [];
    if (executeCommand($command, $output_lines, $return_var, false)) {
        return implode("\n", $output_lines);
    }
    return null;
}

function askUser($prompt, $default = '') {
    echo "[ACTION] $prompt" . ($default ? " [$default]" : "") . ": ";
    $input = trim(fgets(STDIN));
    return $input === '' ? $default : $input;
}

function isValidGithubHttpsUrl($url) {
    return preg_match('|^https://github.com/([^/]+)/([^/.]+?)(\.git)?$|', $url);
}

function convertToSshUrl($httpsUrl) {
    if (preg_match('|^https://github.com/([^/]+)/([^/.]+?)(\.git)?$|', $httpsUrl, $matches)) {
        return "git@github.com:{$matches[1]}/{$matches[2]}.git";
    }
    return null;
}

function convertToHttpsUrl($sshOrHttpsUrl) {
    if (isValidGithubHttpsUrl($sshOrHttpsUrl)) {
        return preg_replace('/\.git$/', '', $sshOrHttpsUrl);
    }
    if (preg_match('|^git@github\.com:([^/]+)/([^/.]+?)\.git$|', $sshOrHttpsUrl, $matches)) {
        return "https://github.com/{$matches[1]}/{$matches[2]}";
    }
    return null;
}

function isGhCliInstalled() {
    @exec('gh --version 2>&1', $output, $return_var);
    return $return_var === 0;
}

function generateCoolRepoName() { // Using your latest version of this
    $adjectives = [
        'Enchanted', 'Mystic', 'Whispering', 'Golden', 'Starry', 'Dreamy', 'Fabled',
        'Wondrous', 'Sunlit', 'Moonlit', 'Silent', 'Soaring', 'Hidden', 'Ancient',
        'Verdant', 'Azure', 'Crimson', 'Sparkling', 'Forgotten', 'Timeless', 'Zephyr',
        'Crystal', 'Ethereal', 'Radiant', 'Celestial', 'Arcane', 'Vivid'
    ];
    $nouns = [
        'Dragon', 'Sprite', 'Phoenix', 'Unicorn', 'Griffin', 'Scroll', 'Elixir', 'Gem',
        'Talisman', 'Amulet', 'Oracle', 'Portal', 'Labyrinth', 'Citadel', 'Grove',
        'Willow', 'River', 'Comet', 'Nebula', 'Sanctuary', 'Meadow', 'Quest', 'Echo',
        'Harbor', 'Spire', 'Chalice', 'Glyph', 'Relic', 'Beacon'
    ];
    $adj = $adjectives[array_rand($adjectives)];
    $noun = $nouns[array_rand($nouns)];
    $dirName = basename(getcwd());
    $dirNameSanitized = strtolower(preg_replace('/[^a-zA-Z0-9_]+/', '_', str_replace(' ', '_', $dirName)));
    if (empty($dirNameSanitized) || strlen($dirNameSanitized) < 3 || $dirNameSanitized === "scripts" || $dirNameSanitized === "php_scripts") {
        $dirNameSanitized = "codex";
    }
    return "{$adj}{$noun}_{$dirNameSanitized}_" . date('Ymd');
}

function generateCoolCommitMessage($type = 'sync', $repoName = null) { // Using your latest version
    $actions_sync = ["Sync", "Update", "Evolve", "Harmonize", "Weave", "Forge", "Align", "Merge", "Conjure", "Channel"];
    $actions_init = ["Genesis", "Birth", "Awaken", "Spark", "Launch", "Manifest", "Inscribe", "Originate"];

    $adjectives = ["Cosmic", "Verdant", "Starlight", "Runic", "Zephyr", "Quantum", "Arcane", "Fierce", "Silent", "Whimsical", "Nebula", "Astral", "Crimson", "Lunar", "Solar"];
    $particles = ["pow", "le", "du", "von", "ze", "ish", "ka", "omni", "zzt", "flux", "kria", "sol"];
    $tech_nouns = ["Codex", "Matrix", "Algo", "Cipher", "Scriptum", "Byteflux", "Vector", "GitGrit", "Relay", "NodeCore", "QuantumLeap"];
    $myth_nouns = ["NovaToad", "StarSprite", "RuneGolem", "EchoScroll", "RelicCharm", "OracleStone", "DreamWeaver", "ChronoFox", "AetherPrawn", "VoidKraken"];
    $emojis_general = ["✨", "📜", "💡", "🔮", "🌀", "🌠"];
    $date_str = date("Y-m-d H:i");

    $adj = $adjectives[array_rand($adjectives)]; $p1 = $particles[array_rand($particles)];
    $p2 = $particles[array_rand($particles)]; while ($p2 === $p1) { $p2 = $particles[array_rand($particles)]; }
    $tech = $tech_nouns[array_rand($tech_nouns)]; $myth = $myth_nouns[array_rand($myth_nouns)];
    $num = rand(101, 777); $random_emoji = $emojis_general[array_rand($emojis_general)];
    $core_phrase = "{$adj} {$p1} {$tech} {$p2} {$myth}{$num}";

    if ($type === 'init' && $repoName) {
        $action_word = $actions_init[array_rand($actions_init)]; $rocket_emoji = "🚀";
        return "{$rocket_emoji} {$action_word}: {$core_phrase} for {$repoName} {$random_emoji} | {$date_str}";
    } else {
        $action_word = $actions_sync[array_rand($actions_sync)]; $sync_emoji = "🔄"; $toad_emoji = "🐸";
        $phrase_elements = [$action_word.":", $core_phrase]; shuffle($phrase_elements);
        return "{$sync_emoji} " . implode(" ", $phrase_elements) . " {$toad_emoji} {$random_emoji} | {$date_str}";
    }
}

// --- MAIN SCRIPT ---
echo "========================================\n";
echo " GitHub Sync & Tools Master Script \n";
echo "========================================\n";

$action = '';

// (Step 1: Determine primary action - REMAINS THE SAME)
if (file_exists($configFile)) {
    $loadedUrl = trim(file_get_contents($configFile));
    if (isValidGithubHttpsUrl($loadedUrl)) {
        $repoUrlHttps = $loadedUrl;
        echo "[INFO] Found existing config for: $repoUrlHttps\n";
        $choice = askUser("Config found. Sync (s), create NEW repo (n), Gist (g), quit (q)?", "s");
    } else {
        echo "[WARN] Config file '$configFile' contains an invalid URL.\n";
        $choice = askUser("Invalid config. Create NEW repo (n), setup EXISTING remote (e), Gist (g), quit (q)?", "e");
    }
} else { 
    if (is_dir(".git")) {
        $currentRemoteUrl = trim(captureCommandOutput("git remote get-url origin"));
        if ($currentRemoteUrl) {
            $derivedHttpsUrl = convertToHttpsUrl($currentRemoteUrl);
            if ($derivedHttpsUrl && isValidGithubHttpsUrl($derivedHttpsUrl)) {
                $repoUrlHttps = $derivedHttpsUrl; 
                echo "[INFO] Current Git repo connected to: $repoUrlHttps\n";
                $choice = askUser("Sync this repo (s), create different NEW repo (n), Gist (g), quit (q)?", "s");
            } else {
                echo "[WARN] Current 'origin' remote ('$currentRemoteUrl') is not a recognized GitHub URL.\n";
                $choice = askUser("Create NEW GitHub repo (n), setup EXISTING remote (e), Gist (g), quit (q)?", "n");
            }
        } else { 
            $choice = askUser("Git repo has no 'origin'. Create NEW GitHub repo (n), setup EXISTING remote (e), Gist (g), quit (q)?", "n");
        }
    } else { 
        $choice = askUser("Not a Git repo. Create NEW GitHub repo (n), connect to EXISTING (e), Gist (g), quit (q)?", "n");
    }
}

if ($choice === 's') $action = 'sync';
elseif ($choice === 'n') { $repoUrlHttps = ''; $action = 'new_repo'; } 
elseif ($choice === 'e') $action = 'sync_existing_prompt_url';
elseif ($choice === 'g') $action = 'gist';
elseif ($choice === 'q') { echo "[INFO] Quitting.\n"; exit(0); }
else { echo "[ERROR] Invalid choice.\n"; exit(1); }


// (Step 2: Execute Gist and New Repo actions - REMAINS THE SAME)
if ($action === 'gist') {
    if (!isGhCliInstalled()) {
        echo "[ERROR] GitHub CLI ('gh') is required for Gist creation. Please install it.\n"; exit(1);
    }
    $fileName = askUser("Enter the filename for the Gist");
    if (empty($fileName) || !file_exists($fileName)) {
        echo "[ERROR] File '$fileName' not found or not provided.\n"; exit(1);
    }
    $gistDesc = askUser("Gist description (optional)");
    $publicGist = askUser("Make Gist public? (yes/NO)", "NO");
    $publicFlag = (strtolower($publicGist) === 'yes' || strtolower($publicGist) === 'y') ? "--public" : "";
    
    if (executeCommand("gh gist create $publicFlag -d " . escapeshellarg($gistDesc) . " " . escapeshellarg($fileName))) {
        echo "[INFO] Gist created successfully!\n";
    }
    exit(0);
}

if ($action === 'new_repo') {
    if (!isGhCliInstalled()) {
        echo "[ERROR] GitHub CLI ('gh') is required. Please install it.\n"; exit(1);
    }
    $suggestedName = generateCoolRepoName();
    $repoName = askUser("Enter repository name for GitHub", $suggestedName);
    $repoVisibility = strtolower(askUser("Visibility (public/private/internal)", "private"));
    if(!in_array($repoVisibility, ['public', 'private', 'internal'])) $repoVisibility = 'private';
    $repoDesc = askUser("Description (optional)");

    echo "[INFO] Creating new GitHub repository: $repoName\n";

    if (!is_dir(".git")) {
        if (!executeCommand("git init -b $defaultBranch")) exit(1);
    }
    captureCommandOutput("git remote remove origin", $ret_remove_dummy); 

    if (!executeCommand("git add .")) exit(1);
    
    $initialCommitMsg = generateCoolCommitMessage('init', $repoName);
    echo "[INFO] Attempting initial commit: '$initialCommitMsg'\n";
    $commit_output_lines = []; $commit_ret_var = null;
    executeCommand("git commit -m " . escapeshellarg($initialCommitMsg), $commit_output_lines, $commit_ret_var, false);
    
    if ($commit_ret_var !== 0) {
        $is_nothing_to_commit = false;
        if(!empty($commit_output_lines)) {
            foreach($commit_output_lines as $line) {
                if (strpos($line, "nothing to commit") !== false || strpos($line, "no changes added to commit") !== false) {
                    $is_nothing_to_commit = true; break;
                }
            }
        }
        if ($is_nothing_to_commit) echo "[INFO] No new changes for initial commit.\n";
        else echo "[WARN] Initial commit command exited with code $commit_ret_var. Proceeding.\n";
    } else echo "[INFO] Initial commit successful.\n";
    
    $createCmd = "gh repo create " . escapeshellarg($repoName) . " --{$repoVisibility} --source=. --remote=origin --push";
    if (!empty($repoDesc)) $createCmd .= " -d " . escapeshellarg($repoDesc);
    
    if (executeCommand($createCmd)) {
        echo "[INFO] Repository '$repoName' created on GitHub and remote 'origin' configured.\n";
        $newRemoteUrl = trim(captureCommandOutput("git remote get-url origin"));
        if ($newRemoteUrl) {
            $repoUrlHttps = convertToHttpsUrl($newRemoteUrl);
            if (isValidGithubHttpsUrl($repoUrlHttps)) {
                file_put_contents($configFile, $repoUrlHttps);
                echo "[INFO] Saved new repository URL to '$configFile': $repoUrlHttps\n";
            } else echo "[WARN] Could not determine valid HTTPS URL. Remote: $newRemoteUrl\n";
        }
    } else echo "[ERROR] Failed to create repository '$repoName' on GitHub.\n";
    exit(0);
}


// --- Sync Action Logic (Primary Change Area) ---
if ($action === 'sync_existing_prompt_url' && empty($repoUrlHttps)) {
    $userInputUrl = askUser("Paste your EXISTING GitHub repository HTTPS URL");
    if (isValidGithubHttpsUrl($userInputUrl)) {
        $repoUrlHttps = convertToHttpsUrl($userInputUrl);
        file_put_contents($configFile, $repoUrlHttps);
        echo "[INFO] Saved URL to '$configFile': $repoUrlHttps\n";
    } else {
        echo "[ERROR] Invalid GitHub HTTPS URL provided: '$userInputUrl'\n"; exit(1);
    }
}

if (empty($repoUrlHttps)) {
    echo "[ERROR] No repository URL. Cannot sync.\n"; exit(1);
}
$repoUrlSsh = convertToSshUrl($repoUrlHttps);
if (!$repoUrlSsh) { echo "[ERROR] Could not convert to SSH URL: $repoUrlHttps\n"; exit(1); }

echo "[INFO] Preparing to sync with: $repoUrlHttps (SSH: $repoUrlSsh)\n";

// Initialize Git and set remote if needed
if (!is_dir(".git")) {
    if (!executeCommand("git init -b $defaultBranch")) exit(1);
}
$currentRemote = trim(captureCommandOutput("git remote get-url origin"));
if (empty($currentRemote)) {
    if (!executeCommand("git remote add origin " . escapeshellarg($repoUrlSsh))) exit(1);
} elseif ($currentRemote !== $repoUrlSsh) {
    if (!executeCommand("git remote set-url origin " . escapeshellarg($repoUrlSsh))) exit(1);
}

// Determine current branch, or initialize if new repo
$currentBranch = trim(captureCommandOutput("git symbolic-ref --short HEAD"));
if (empty($currentBranch)) {
    $currentBranch = $defaultBranch;
    echo "[INFO] No current branch. Will use '$defaultBranch'.\n";
    captureCommandOutput("git rev-parse --verify HEAD", $head_exists_ret_dummy);
    if ($head_exists_ret_dummy !== 0) { // No commits yet
        echo "[INFO] No commits yet. Staging files for initial commit on '$defaultBranch'.\n";
        if (!executeCommand("git add .")) exit(1);
        system("git diff-index --quiet --cached HEAD --", $has_staged_init_ret_dummy); // Check if anything was added
        if ($has_staged_init_ret_dummy !== 0) {
            $initialBranchCommitMsg = generateCoolCommitMessage('init', basename(getcwd()) . " on " . $currentBranch);
            if (!executeCommand("git commit -m " . escapeshellarg($initialBranchCommitMsg))) {
                 echo "[WARN] Initial branch commit failed.\n";
            }
        } else {
            echo "[INFO] No files for initial branch commit.\n";
        }
    }
}
echo "[INFO] Current local branch for sync: $currentBranch\n";

// Stage and Commit Local Changes
echo "[INFO] Staging all local changes...\n";
if (!executeCommand("git add .")) exit(1);

$commit_output_lines_sync = []; $commit_ret_var_sync = null;
$syncCommitMsg = generateCoolCommitMessage('sync');
echo "[INFO] Attempting sync commit: '$syncCommitMsg'\n";
executeCommand("git commit -m " . escapeshellarg($syncCommitMsg), $commit_output_lines_sync, $commit_ret_var_sync, false); // Capture output

if ($commit_ret_var_sync !== 0) {
    $is_nothing_to_commit_sync = false;
    if(!empty($commit_output_lines_sync)) {
        foreach($commit_output_lines_sync as $line) {
            if (strpos($line, "nothing to commit") !== false || strpos($line, "no changes added to commit") !== false) {
                $is_nothing_to_commit_sync = true; break;
            }
        }
    }
    if ($is_nothing_to_commit_sync) echo "[INFO] No new local changes to commit.\n";
    else echo "[WARN] Sync commit command exited with code $commit_ret_var_sync. Local changes might not be committed.\n";
} else echo "[INFO] Local changes committed successfully.\n";

// Pull Remote Changes
echo "[INFO] Pulling changes from remote 'origin/$currentBranch'...\n";
$pull_output_lines = []; $pull_ret_var = null;
if (!executeCommand("git pull origin " . escapeshellarg($currentBranch), $pull_output_lines, $pull_ret_var, true)) {
    $pull_output_str = implode("\n", $pull_output_lines);
    if (strpos($pull_output_str, 'fix conflicts and then commit the result') !== false ||
        strpos($pull_output_str, 'Automatic merge failed') !== false ||
        strpos($pull_output_str, 'you have unmerged paths') !== false ) {
        echo "[CRITICAL] MERGE CONFLICTS DETECTED. Please resolve manually:\n";
        echo "1. Edit conflicted files.\n2. `git add <resolved-files>`\n3. `git commit`\n";
        echo "Then re-run this script or push manually.\n";
        exit(1);
    } else {
        echo "[ERROR] Pull failed. Check output. Manual intervention may be needed.\n";
        exit(1);
    }
} else {
    echo "[INFO] Pull successful.\n";
}

// Attempt Standard Push
echo "[INFO] Attempting standard push to 'origin/$currentBranch'...\n";
$push_output_lines = []; $push_ret_var = null;
if (executeCommand("git push origin " . escapeshellarg($currentBranch), $push_output_lines, $push_ret_var, true)) {
    echo "[INFO] Standard push successful! All synced.\n";
} else {
    // Standard push failed. NOW offer the force push option if it looks like a non-fast-forward.
    $push_output_str = implode("\n", $push_output_lines);
    if (strpos($push_output_str, 'rejected') !== false && (strpos($push_output_str, 'non-fast-forward') !== false || strpos($push_output_str, 'remote contains work that you do') !== false )) {
        echo "[WARN] Standard push failed because remote has diverged (non-fast-forward).\n";
        $truthConfirm = askUser("MAKE LOCAL THE 'SINGLE SOURCE OF TRUTH'? (This uses `git push --force-with-lease` to REWRITE remote history for '$currentBranch'. Use if you are CERTAIN your local history is what the remote should reflect, potentially discarding some remote commits. DANGEROUS!) (yes/NO)", "NO");

        if (strtolower($truthConfirm) === 'yes' || strtolower($truthConfirm) === 'y') {
            $reallySure = askUser("ARE YOU ABSOLUTELY SURE? Type 'YES_I_UNDERSTAND_THE_RISKS' to confirm force push:", "NO");
            if ($reallySure === 'YES_I_UNDERSTAND_THE_RISKS') {
                echo "[WARN] Force pushing (with lease) local '$currentBranch' to 'origin/$currentBranch'...\n";
                if (executeCommand("git push --force-with-lease origin " . escapeshellarg($currentBranch))) {
                    echo "[INFO] Force push (with lease) successful!\n";
                } else {
                    echo "[ERROR] Force push (with lease) failed.\n";
                }
            } else {
                echo "[INFO] Force push cancelled. Manual resolution needed.\n";
            }
        } else {
            echo "[INFO] Force push not chosen. Manual resolution needed (e.g., rebase, or merge remote changes differently).\n";
        }
    } else {
        echo "[ERROR] Standard push failed for other reasons. Check output above.\n";
    }
}

echo "[INFO] Script finished.\n";
exit(0);
?>