<?php
// sync_master.php - More versatile GitHub interaction script

$configFile = ".github_sync_repo_url.txt";
$defaultBranch = "main"; // Or use 'master' if that's your default
$repoUrlHttps = ''; // Will hold the validated HTTPS URL
$repoUrlSsh = '';   // Will hold the SSH version for git operations

// --- Helper Function to Execute Shell Commands ---
function executeCommand($command, &$output_lines = [], &$return_var = null, $show_output = true) {
    echo "[CMD] $command\n";
    $full_command = $command . ' 2>&1'; // Capture stderr to stdout for full output
    $output_lines = []; // Reset output lines
    
    if ($show_output) {
        // Use passthru for live output if $show_output is true
        passthru($full_command, $passthru_return_val);
        $return_var = $passthru_return_val;
        // Note: passthru doesn't reliably capture all output into an array.
        // If we need captured output AND live output, it's trickier without temporary files or more complex IPC.
        // For now, if show_output is true, output_lines might not be fully populated from passthru.
    } else {
        exec($full_command, $output_lines, $exec_return_val);
        $return_var = $exec_return_val;
    }

    if ($return_var !== 0) {
        echo "[ERROR] Command failed with exit code $return_var: $command\n";
        if (!$show_output && !empty($output_lines)) { // Print captured output if command failed and wasn't shown live
            foreach ($output_lines as $line) {
                echo "[OUTPUT] $line\n";
            }
        }
        return false;
    }
    return true;
}

function captureCommandOutput($command, &$return_var = null) {
    $output_lines = [];
    if (executeCommand($command, $output_lines, $return_var, false)) { // Don't show output live
        return implode("\n", $output_lines);
    }
    return null; // or an empty string, depending on how you want to handle failure
}


// --- User Input Helper ---
function askUser($prompt, $default = '') {
    echo "[ACTION] $prompt" . ($default ? " [$default]" : "") . ": ";
    $input = trim(fgets(STDIN));
    return $input === '' ? $default : $input;
}

// --- URL Helper Functions ---
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

// --- gh CLI Check ---
function isGhCliInstalled() {
    $output = [];
    $return_var = 1;
    // Use exec to check gh version without showing output unless an error occurs
    @exec('gh --version 2>&1', $output, $return_var); 
    return $return_var === 0;
}

// --- Cool Name Generator (Simple) ---
function generateCoolRepoName() {
    $adjectives = ['swift', 'silent', 'brave', 'clever', 'mighty', 'magic', 'cosmic', 'epic', 'robo'];
    $nouns = ['river', 'mountain', 'code', 'journey', 'nova', 'engine', 'badger', 'photon', 'script'];
    $adj = $adjectives[array_rand($adjectives)];
    $noun = $nouns[array_rand($nouns)];
    $timestamp = date('YmdHis');
    // Get current directory name as a base, sanitize it
    $dirName = basename(getcwd());
    $dirName = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $dirName);
    $dirName = strtolower(trim($dirName, '_'));
    if (empty($dirName) || $dirName === 'scripts' || $dirName === 'project') { // generic dir names
        return "auto_repo_{$adj}_{$noun}_{$timestamp}";
    }
    return "{$dirName}_{$adj}_{$noun}";

}

// --- Main Logic ---
echo "GitHub Sync & Tools Script\n";
echo "---------------------------\n";

// --- Step 1: Determine Action ---
$action = '';
if (file_exists($configFile)) {
    $loadedUrl = trim(file_get_contents($configFile));
    if (isValidGithubHttpsUrl($loadedUrl)) {
        $repoUrlHttps = $loadedUrl;
        echo "[INFO] Found existing config for: $repoUrlHttps\n";
        // If config exists, default action is to sync, but offer other options.
        $choice = askUser("Config found. Sync this repo (s), create a NEW repo (n), or create a Gist (g)?", "s");
        if ($choice === 's') $action = 'sync';
        elseif ($choice === 'n') $action = 'new_repo';
        elseif ($choice === 'g') $action = 'gist';
        else { echo "[ERROR] Invalid choice.\n"; exit(1); }
    } else {
        echo "[WARN] Config file '$configFile' contains an invalid URL. Please choose an action.\n";
    }
}

if (empty($action)) { // No valid config, or user chose not to sync from config
    if (is_dir(".git")) {
        $currentRemoteUrl = captureCommandOutput("git remote get-url origin");
        if ($currentRemoteUrl) {
            $derivedHttpsUrl = convertToHttpsUrl(trim($currentRemoteUrl));
            if ($derivedHttpsUrl && isValidGithubHttpsUrl($derivedHttpsUrl)) {
                $repoUrlHttps = $derivedHttpsUrl;
                echo "[INFO] Current directory is a Git repo connected to: $repoUrlHttps\n";
                $choice = askUser("Sync this repo (s), create a NEW repo (n - DANGER if current is already a repo), or create a Gist (g)?", "s");
                 if ($choice === 's') $action = 'sync';
                 elseif ($choice === 'n') $action = 'new_repo'; // User might want to detach and make new
                 elseif ($choice === 'g') $action = 'gist';
                 else { echo "[ERROR] Invalid choice.\n"; exit(1); }
            }
        }
    }
}

if (empty($action)) { // Still no action decided (no config, not a git repo, or user wants new/gist)
    echo "What would you like to do?\n";
    echo "1. Sync with an EXISTING GitHub repository.\n";
    echo "2. Create a NEW GitHub repository for this project.\n";
    echo "3. Create a Gist from a single file.\n";
    $choice = askUser("Enter choice (1, 2, or 3)", "1");
    if ($choice === '1') $action = 'sync_existing_prompt_url';
    elseif ($choice === '2') $action = 'new_repo';
    elseif ($choice === '3') $action = 'gist';
    else { echo "[ERROR] Invalid choice.\n"; exit(1); }
}

// --- Step 2: Execute Action ---

// ** GIST CREATION **
if ($action === 'gist') {
    if (!isGhCliInstalled()) {
        echo "[ERROR] GitHub CLI ('gh') is not installed or not in PATH. Gist creation requires it.\n";
        echo "Please install it from https://cli.github.com/\n";
        exit(1);
    }
    $fileName = askUser("Enter the filename to create a Gist from");
    if (empty($fileName) || !file_exists($fileName)) {
        echo "[ERROR] File '$fileName' not found or no filename provided.\n";
        exit(1);
    }
    $gistDescription = askUser("Optional Gist description");
    $isPublic = askUser("Make Gist public? (yes/NO)", "NO");
    $publicFlag = (strtolower($isPublic) === 'yes' || strtolower($isPublic) === 'y') ? "--public" : "";

    echo "[INFO] Creating Gist for '$fileName'...\n";
    if (executeCommand("gh gist create $publicFlag -d " . escapeshellarg($gistDescription) . " " . escapeshellarg($fileName))) {
        echo "[INFO] Gist created successfully!\n";
    } else {
        echo "[ERROR] Failed to create Gist.\n";
    }
    exit(0); // Gist creation is a terminal action for this script run
}

// ** NEW REPOSITORY CREATION **
if ($action === 'new_repo') {
    if (!isGhCliInstalled()) {
        echo "[ERROR] GitHub CLI ('gh') is not installed or not in PATH. Repository creation requires it.\n";
        echo "Please install it from https://cli.github.com/\n";
        exit(1);
    }
    if (is_dir(".git")) {
        $confirm = askUser("This directory is ALREADY a Git repository. Are you SURE you want to create a new GitHub repo and potentially reconfigure this local repo? This could be problematic if origin is already set. (yes/NO)", "NO");
        if (strtolower($confirm) !== 'yes' && strtolower($confirm) !== 'y') {
            echo "[INFO] Aborting new repository creation.\n";
            exit(0);
        }
        // Optionally, here you could offer to remove existing .git or reconfigure remote
    }

    $suggestedName = generateCoolRepoName();
    $repoName = askUser("Enter desired repository name on GitHub", $suggestedName);
    $repoVisibility = askUser("Repository visibility: public, private, internal (default: private)?", "private");
    if (!in_array($repoVisibility, ['public', 'private', 'internal'])) $repoVisibility = 'private';
    
    $description = askUser("Optional repository description");

    echo "[INFO] Creating new GitHub repository '$repoName'...\n";
    $createCommand = "gh repo create {$repoName} --{$repoVisibility} --source=. --remote=origin ";
    if (!empty($description)) {
        $createCommand .= "-d " . escapeshellarg($description) . " ";
    }
    // The --push option is problematic if there are no commits yet.
    // Let's init, add, commit first, then push.
    
    if (!is_dir(".git")) {
        if (!executeCommand("git init -b $defaultBranch")) exit(1);
    }
    // Ensure remote origin is not already set or set it correctly
    $currentRemoteUrl = captureCommandOutput("git remote get-url origin");
    if ($currentRemoteUrl) {
        executeCommand("git remote remove origin"); // Remove if exists, to be replaced by gh
    }

    echo "[INFO] Staging all files for initial commit...\n";
    if (!executeCommand("git add .")) exit(1);

    system("git diff-index --quiet HEAD --staged", $return_var_diff_staged);
    if ($return_var_diff_staged !== 0) { // 0 means no staged changes (or no HEAD yet)
        $initialCommitMessage = "Initial commit for " . $repoName;
        echo "[INFO] Making initial commit: '$initialCommitMessage'...\n";
        if (!executeCommand("git commit -m " . escapeshellarg($initialCommitMessage))) {
             echo "[WARN] Initial commit failed. Maybe no files were added or an error occurred.\n";
        }
    } else {
        echo "[INFO] No files to stage for initial commit, or repo is empty.\n";
    }

    if (executeCommand($createCommand)) {
        echo "[INFO] Successfully created and configured remote for '$repoName'.\n";
        $repoUrlHttps = "https://github.com/" . captureCommandOutput("gh repo view $repoName --json owner --jq .owner.login", $ret_owner) . "/" . $repoName; // Attempt to get full URL
        if (!isValidGithubHttpsUrl($repoUrlHttps)) { // Fallback if jq fails
             // Try to get from git remote
            $newRemoteUrl = captureCommandOutput("git remote get-url origin");
            if ($newRemoteUrl) $repoUrlHttps = convertToHttpsUrl(trim($newRemoteUrl));
        }

        if (isValidGithubHttpsUrl($repoUrlHttps)) {
            file_put_contents($configFile, $repoUrlHttps);
            echo "[INFO] Saved repository URL to '$configFile': $repoUrlHttps\n";
            echo "[INFO] Pushing initial commit to the new repository...\n";
            if (!executeCommand("git push -u origin $defaultBranch")) { // Use default branch
                echo "[WARN] Initial push failed. The repo was created, but you might need to push manually.\n";
            } else {
                 echo "[INFO] Initial push successful!\n";
            }
        } else {
            echo "[WARN] Could not automatically determine the full HTTPS URL for the new repo. Please check GitHub.\n";
        }
    } else {
        echo "[ERROR] Failed to create new GitHub repository.\n";
        exit(1);
    }
    $action = 'sync'; // After creating, we might want to sync any further changes made during this script run.
                     // Or just exit. For now, let's consider it done.
    exit(0);
}


// ** SYNCING (EXISTING or after NEW_REPO_PROMPT_URL) **
if ($action === 'sync_existing_prompt_url' && empty($repoUrlHttps)) {
    $userInputUrl = askUser("Paste your GitHub repository HTTPS URL (e.g., https://github.com/username/repo.git) and press Enter");
    if (isValidGithubHttpsUrl($userInputUrl)) {
        $repoUrlHttps = convertToHttpsUrl($userInputUrl);
        file_put_contents($configFile, $repoUrlHttps);
        echo "[INFO] Saved URL to '$configFile': $repoUrlHttps\n";
    } else {
        echo "[ERROR] The URL you entered is not a valid GitHub HTTPS URL: '$userInputUrl'\n";
        exit(1);
    }
}

if (empty($repoUrlHttps)) {
    echo "[ERROR] No valid repository URL configured or provided. Cannot sync.\n";
    exit(1);
}

$repoUrlSsh = convertToSshUrl($repoUrlHttps);
if (empty($repoUrlSsh)) {
    echo "[ERROR] Could not convert HTTPS URL to SSH URL. HTTPS: '$repoUrlHttps'\n"; exit(1);
}
echo "[INFO] Syncing with SSH URL: $repoUrlSsh\n";

if (!is_dir(".git")) {
    echo "[INFO] Initializing Git repository...\n";
    if (!executeCommand("git init -b $defaultBranch")) exit(1);
}

$currentOriginUrlSsh = trim(captureCommandOutput("git remote get-url origin"));
if (empty($currentOriginUrlSsh)) {
    echo "[INFO] Adding remote 'origin' -> $repoUrlSsh\n";
    if (!executeCommand("git remote add origin " . escapeshellarg($repoUrlSsh))) exit(1);
} elseif ($currentOriginUrlSsh !== $repoUrlSsh) {
    echo "[WARN] Updating remote 'origin' from '$currentOriginUrlSsh' to '$repoUrlSsh'.\n";
    if (!executeCommand("git remote set-url origin " . escapeshellarg($repoUrlSsh))) exit(1);
} else {
    echo "[INFO] Remote 'origin' is correctly configured.\n";
}

// Determine current branch
$currentBranch = trim(captureCommandOutput("git symbolic-ref --short HEAD"));
if (empty($currentBranch)) {
    // If no branch (e.g. new repo, no commits), use default.
    // This check is important before pull/push.
    // If there are no commits, pull will likely fail or do nothing.
    // Push HEAD won't work without a branch.
    // For an uncommitted new repo, let add/commit handle it first.
    $commitCount = (int)trim(captureCommandOutput("git rev-list --count HEAD 2>/dev/null", $rev_list_ret));
    if ($rev_list_ret !== 0 || $commitCount === 0) { // No commits yet or error
        $currentBranch = $defaultBranch; // Assume we'll be on default after first commit
        echo "[INFO] No commits yet or unable to determine branch, will use '$defaultBranch' for operations if needed.\n";
    } else {
         echo "[ERROR] Could not determine current branch, and commits exist. Please resolve manually.\n"; exit(1);
    }
} else {
    echo "[INFO] Current local branch is '$currentBranch'.\n";
}


echo "[INFO] Adding all local changes to staging...\n";
if (!executeCommand("git add .")) exit(1);

system("git diff-index --quiet HEAD --staged", $return_var_diff_staged);
$hasStagedChanges = ($return_var_diff_staged !== 0);

if ($hasStagedChanges) {
    $commitMessage = "Sync update via script on " . date("Y-m-d H:i:s");
    echo "[INFO] Committing staged changes: '$commitMessage'...\n";
    if (!executeCommand("git commit -m " . escapeshellarg($commitMessage))) {
        // Commit can fail if e.g. pre-commit hook fails, or nothing to commit (though we checked)
        echo "[WARN] Commit failed. Proceeding with pull/push if there are no uncommitted changes that conflict.\n";
    }
} else {
    echo "[INFO] No new changes to commit locally.\n";
}

// PULLING CHANGES
echo "[INFO] Attempting to pull changes from remote 'origin/$currentBranch'...\n";
$pull_output = [];
$pull_return_var = 0;
// We need to execute the pull and check its output for "Already up to date." or merge conflicts
// Using `executeCommand` with live output for pull
if (executeCommand("git pull origin " . escapeshellarg($currentBranch), $pull_output, $pull_return_var)) {
    echo "[INFO] Pull successful or already up-to-date.\n";
    // Check if output contains "Already up to date." to avoid unnecessary force push questions.
    // However, `git pull` output is diverse. A successful merge is also not "Already up to date."
    // A better check is if local is now ahead or diverged.
} else {
    echo "[WARN] 'git pull' indicated issues (e.g., merge conflicts). You may need to resolve these manually.\n";
    echo "[WARN] Further push operations might fail or have unintended consequences if conflicts are not resolved.\n";
    // At this point, we could exit or offer to proceed with caution.
    // For now, let's allow proceeding but warn the user.
}

// PUSHING CHANGES
$forcePush = false;
$pushChoice = askUser("Do you want your LOCAL version to be the SINGLE SOURCE OF TRUTH and overwrite remote if necessary (yes/NO)? This is a --force-with-lease push.", "NO");
if (strtolower($pushChoice) === 'yes' || strtolower($pushChoice) === 'y') {
    $forcePush = true;
    echo "[WARN] YOU ARE ABOUT TO FORCE PUSH. This will overwrite the remote history for branch '$currentBranch'.\n";
    $confirmForce = askUser("Confirm force push (YES/no)?", "no"); // Default to no for safety
    if (strtoupper($confirmForce) !== 'YES') {
        echo "[INFO] Force push cancelled by user.\n";
        $forcePush = false;
    }
}

if ($forcePush) {
    echo "[INFO] Force-pushing local '$currentBranch' to 'origin/$currentBranch' (single source of truth)...\n";
    if (executeCommand("git push --force-with-lease origin " . escapeshellarg($currentBranch))) {
        echo "[INFO] Force push successful!\n";
    } else {
        echo "[ERROR] Force push failed.\n";
    }
} else {
    echo "[INFO] Attempting standard push of local '$currentBranch' to 'origin/$currentBranch'...\n";
    // Check if local is ahead of remote before pushing normally, or if there's anything to push.
    // `git status -sbuno` can show if ahead, behind or diverged.
    // Example output: ## main...origin/main [ahead 1]
    $statusOutput = captureCommandOutput("git status -sbuno"); // -s short, -b branch, -u no untracked, -o one line
    $canPushNormally = true;
    if (strpos($statusOutput, "behind") !== false) {
        echo "[WARN] Local branch '$currentBranch' is behind remote. Standard push will likely fail. Pull again or resolve.\n";
        $canPushNormally = false;
    }
    if (strpos($statusOutput, "diverged") !== false) {
        echo "[WARN] Local branch '$currentBranch' has diverged from remote. Standard push will fail. Merge/rebase needed.\n";
        $canPushNormally = false;
    }
    // Also check if there's anything to push (i.e. local is actually ahead)
    // If not ahead and no staged changes, there's nothing a normal push would do.
    if (strpos($statusOutput, "ahead") === false && !$hasStagedChanges && $return_var_diff_staged === 0) {
        // if not ahead and no new commits were made by this script run
        $commitCountAfterPotentialCommit = (int)trim(captureCommandOutput("git rev-list --count HEAD 2>/dev/null"));
        if ($commitCountAfterPotentialCommit == (int)trim(captureCommandOutput("git rev-list --count origin/{$currentBranch} 2>/dev/null", $ret_remote_count)) && $ret_remote_count === 0) {
             echo "[INFO] Local and remote are in sync, or nothing to push.\n";
             $canPushNormally = false; // effectively
        }
    }


    if ($canPushNormally) {
        if (executeCommand("git push origin " . escapeshellarg($currentBranch))) {
            echo "[INFO] Standard push successful!\n";
        } else {
            echo "[ERROR] Standard push failed. This might be due to new remote changes (pull again), or other issues.\n";
        }
    } else {
        echo "[INFO] Standard push skipped due to branch state (behind, diverged, or already in sync).\n";
    }
}

echo "[INFO] Script finished.\n";
exit(0);
?>