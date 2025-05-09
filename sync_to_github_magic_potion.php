<?php
// sync_to_github.php

$configFile = ".github_sync_repo_url.txt"; // Changed to .txt
$placeholderUrl = "PUT_REPO_URL_HERE_SYNCER";
$defaultBranch = "main"; // Or use 'master' if that's your default

// --- Helper Function to Execute Shell Commands ---
function executeCommand($command, &$output_lines = null, &$return_var = null) {
    echo "[CMD] $command\n";
    // Use passthru to see live output and get return status
    // For commands where we need to capture output specifically, we might use exec.
    // For now, passthru is good for transparency with git commands.
    passthru($command, $return_val_passthru);
    $return_var = $return_val_passthru; // passthru returns last line of output, $return_var gets actual exit code.

    // A more robust way to capture output AND return code if needed later:
    // exec($command . ' 2>&1', $output_array, $return_code_exec);
    // $output_lines = $output_array;
    // $return_var = $return_code_exec;

    if ($return_var !== 0) {
        echo "[ERROR] Command failed with exit code $return_var: $command\n";
        return false;
    }
    return true;
}

// --- Main Logic ---

// 1. Check/Create/Read Config File
if (!file_exists($configFile)) {
    echo "[INFO] Configuration file '$configFile' not found.\n";
    file_put_contents($configFile, $placeholderUrl);
    echo "[INFO] Created '$configFile'. Please edit it and replace '$placeholderUrl' with your GitHub repository HTTPS URL (e.g., https://github.com/username/repo.git).\n";
    exit(1);
}

$repoUrlHttps = trim(file_get_contents($configFile));

if ($repoUrlHttps === $placeholderUrl || empty($repoUrlHttps)) {
    echo "[ERROR] Repository URL in '$configFile' is a placeholder or empty.\n";
    echo "[ERROR] Please edit '$configFile' and put the HTTPS URL of your GitHub repository.\n";
    exit(1);
}

// 2. Validate and Convert HTTPS URL to SSH URL
$repoUrlSsh = '';
if (preg_match('|^https://github.com/([^/]+)/([^/.]+?)(\.git)?$|', $repoUrlHttps, $matches)) {
    $user = $matches[1];
    $repo = $matches[2];
    $repoUrlSsh = "git@github.com:{$user}/{$repo}.git";
} else {
    echo "[ERROR] Invalid GitHub HTTPS URL format in '$configFile': $repoUrlHttps\n";
    echo "[ERROR] Expected format: https://github.com/username/repository or https://github.com/username/repository.git\n";
    exit(1);
}

echo "[INFO] Target HTTPS URL: $repoUrlHttps\n";
echo "[INFO] Converted to SSH URL for git: $repoUrlSsh\n";

// 3. Initialize Git Repo if it doesn't exist
if (!is_dir(".git")) {
    echo "[INFO] No .git directory found. Initializing a new Git repository...\n";
    if (!executeCommand("git init -b $defaultBranch")) exit(1);
    echo "[INFO] Git repository initialized with default branch '$defaultBranch'.\n";
} else {
    echo "[INFO] Existing Git repository found.\n";
}

// 4. Check and Configure Remote 'origin'
$currentRemoteUrl = trim(shell_exec("git remote get-url origin 2>/dev/null")); // Capture output

if (empty($currentRemoteUrl)) {
    echo "[INFO] No remote 'origin' found. Adding remote 'origin' -> $repoUrlSsh\n";
    if (!executeCommand("git remote add origin " . escapeshellarg($repoUrlSsh))) exit(1);
} elseif ($currentRemoteUrl !== $repoUrlSsh) {
    echo "[WARN] Remote 'origin' currently points to '$currentRemoteUrl'.\n";
    echo "[WARN] Updating remote 'origin' to point to '$repoUrlSsh'.\n";
    if (!executeCommand("git remote set-url origin " . escapeshellarg($repoUrlSsh))) exit(1);
} else {
    echo "[INFO] Remote 'origin' is correctly configured to $repoUrlSsh.\n";
}

// 5. Add, Commit, and Push
echo "[INFO] Adding all changes to staging...\n";
if (!executeCommand("git add .")) exit(1);

// Check if there are changes to commit
// `git diff-index --quiet HEAD --` returns 0 if no changes, 1 if changes.
system("git diff-index --quiet HEAD --", $return_var_diff);

if ($return_var_diff === 0) {
    echo "[INFO] No changes to commit. Workspace is clean.\n";
    // Optional: git pull
    // echo "[INFO] Checking for remote changes...\n";
    // executeCommand("git pull origin HEAD --ff-only"); // Pull current branch
} else {
    $commitMessage = "Sync update via script on " . date("Y-m-d H:i:s");
    echo "[INFO] Committing changes with message: '$commitMessage'...\n";
    if (!executeCommand("git commit -m " . escapeshellarg($commitMessage))) exit(1);

    echo "[INFO] Pushing changes to origin (current branch)...\n";
    if (!executeCommand("git push origin HEAD")) exit(1); // Pushes current branch to remote branch of same name
    echo "[INFO] Sync complete!\n";
}

exit(0);

?>