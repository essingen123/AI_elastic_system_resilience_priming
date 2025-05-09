<?php
// sync_to_github.php

$configFile = ".github_sync_repo_url.txt";
$defaultBranch = "main"; // Or use 'master' if that's your default
$repoUrlHttps = ''; // Will hold the validated HTTPS URL
$repoUrlSsh = '';   // Will hold the SSH version for git operations

// --- Helper Function to Execute Shell Commands ---
function executeCommand($command, &$output_lines = null, &$return_var = null) {
    echo "[CMD] $command\n";
    passthru($command, $passthru_return_val);
    $return_var = $passthru_return_val;

    if ($return_var !== 0) {
        echo "[ERROR] Command failed with exit code $return_var: $command\n";
        return false;
    }
    return true;
}

// --- URL Helper Functions ---
function isValidGithubHttpsUrl($url) {
    return preg_match('|^https://github.com/([^/]+)/([^/.]+?)(\.git)?$|', $url);
}

function isValidGithubSshUrl($url) {
    return preg_match('|^git@github\.com:([^/]+)/([^/.]+?)\.git$|', $url);
}

function convertToSshUrl($httpsUrl) {
    if (preg_match('|^https://github.com/([^/]+)/([^/.]+?)(\.git)?$|', $httpsUrl, $matches)) {
        return "git@github.com:{$matches[1]}/{$matches[2]}.git";
    }
    return null;
}

function convertToHttpsUrl($sshOrHttpsUrl) {
    if (isValidGithubHttpsUrl($sshOrHttpsUrl)) {
        // Remove .git suffix if present for consistency in storage
        return preg_replace('/\.git$/', '', $sshOrHttpsUrl);
    }
    if (preg_match('|^git@github\.com:([^/]+)/([^/.]+?)\.git$|', $sshOrHttpsUrl, $matches)) {
        return "https://github.com/{$matches[1]}/{$matches[2]}";
    }
    return null;
}

// --- Main Logic ---

// 1. Try to load URL from config file
if (file_exists($configFile)) {
    $loadedUrl = trim(file_get_contents($configFile));
    if (isValidGithubHttpsUrl($loadedUrl)) {
        $repoUrlHttps = $loadedUrl;
        echo "[INFO] Using repository URL from config file: $repoUrlHttps\n";
    } else {
        echo "[WARN] Invalid URL found in '$configFile'. Will try to determine or prompt.\n";
    }
}

// 2. If no valid URL from config, try to derive from existing Git remote 'origin'
if (empty($repoUrlHttps) && is_dir(".git")) {
    $currentRemoteUrl = trim(shell_exec("git remote get-url origin 2>/dev/null"));
    if (!empty($currentRemoteUrl)) {
        $derivedHttpsUrl = convertToHttpsUrl($currentRemoteUrl);
        if ($derivedHttpsUrl && isValidGithubHttpsUrl($derivedHttpsUrl)) {
            $repoUrlHttps = $derivedHttpsUrl;
            echo "[INFO] Derived repository HTTPS URL from current git remote 'origin': $repoUrlHttps\n";
            // Save this derived URL to config for next time
            file_put_contents($configFile, $repoUrlHttps);
            echo "[INFO] Saved derived URL to '$configFile' for future use.\n";
        } else {
             echo "[WARN] Current 'origin' remote ('$currentRemoteUrl') is not a recognized GitHub URL. Will prompt.\n";
        }
    }
}

// 3. If still no URL, prompt the user
if (empty($repoUrlHttps)) {
    echo "[ACTION] Please paste your GitHub repository HTTPS URL (e.g., https://github.com/username/repo.git) and press Enter:\n";
    $userInputUrl = trim(fgets(STDIN));

    if (isValidGithubHttpsUrl($userInputUrl)) {
        $repoUrlHttps = convertToHttpsUrl($userInputUrl); // Normalize (e.g. remove .git)
        echo "[INFO] Using repository URL provided by user: $repoUrlHttps\n";
        // Save this user-provided URL to config for next time
        file_put_contents($configFile, $repoUrlHttps);
        echo "[INFO] Saved URL to '$configFile' for future use.\n";
    } else {
        echo "[ERROR] The URL you entered is not a valid GitHub HTTPS URL: '$userInputUrl'\n";
        exit(1);
    }
}

// 4. Convert validated HTTPS URL to SSH for git operations
$repoUrlSsh = convertToSshUrl($repoUrlHttps);
if (empty($repoUrlSsh)) {
    // This should not happen if $repoUrlHttps was validated by isValidGithubHttpsUrl
    echo "[ERROR] Could not convert HTTPS URL to SSH URL. HTTPS: '$repoUrlHttps'\n";
    exit(1);
}
echo "[INFO] Using SSH URL for git operations: $repoUrlSsh\n";


// 5. Initialize Git Repo if it doesn't exist
if (!is_dir(".git")) {
    echo "[INFO] No .git directory found. Initializing a new Git repository...\n";
    if (!executeCommand("git init -b $defaultBranch")) exit(1);
    echo "[INFO] Git repository initialized with default branch '$defaultBranch'.\n";
} else {
    echo "[INFO] Existing Git repository found.\n";
}

// 6. Check and Configure Remote 'origin'
$currentOriginUrlSsh = '';
$gitRemoteOutput = shell_exec("git remote get-url origin 2>/dev/null");
if ($gitRemoteOutput !== null) {
    $currentOriginUrlSsh = trim($gitRemoteOutput);
}


if (empty($currentOriginUrlSsh)) {
    echo "[INFO] No remote 'origin' found. Adding remote 'origin' -> $repoUrlSsh\n";
    if (!executeCommand("git remote add origin " . escapeshellarg($repoUrlSsh))) exit(1);
} elseif ($currentOriginUrlSsh !== $repoUrlSsh) {
    echo "[WARN] Remote 'origin' currently points to '$currentOriginUrlSsh'.\n";
    echo "[WARN] Updating remote 'origin' to point to '$repoUrlSsh'.\n";
    if (!executeCommand("git remote set-url origin " . escapeshellarg($repoUrlSsh))) exit(1);
} else {
    echo "[INFO] Remote 'origin' is correctly configured to $repoUrlSsh.\n";
}

// 7. Add, Commit, and Push
echo "[INFO] Adding all changes to staging...\n";
if (!executeCommand("git add .")) exit(1);

// Check if there are changes to commit
system("git diff-index --quiet HEAD --", $return_var_diff);

if ($return_var_diff === 0) {
    echo "[INFO] No changes to commit. Workspace is clean.\n";
} else {
    $commitMessage = "Sync update via script on " . date("Y-m-d H:i:s");
    echo "[INFO] Committing changes with message: '$commitMessage'...\n";
    if (!executeCommand("git commit -m " . escapeshellarg($commitMessage))) exit(1);

    echo "[INFO] Pushing changes to origin (current branch)...\n";
    if (!executeCommand("git push origin HEAD")) exit(1);
    echo "[INFO] Sync complete!\n";
}

exit(0);

?>