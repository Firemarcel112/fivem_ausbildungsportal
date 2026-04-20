<?php

exec('git diff --name-only --diff-filter=ACMR', $output_modified);

exec('git diff --name-only --cached --diff-filter=ACMR', $output_staged);

exec('git ls-files --others --exclude-standard', $output_untracked);

// Alles zusammenführen
$output = array_merge($output_modified, $output_staged, $output_untracked);
$output = array_unique($output);

$files_to_scan = preg_grep('/(?<!\.blade)\.php$/', $output);

$only_from_path = [
    'app',
    'database',
];

$files = array_filter($files_to_scan, function ($file) use ($only_from_path) {
    foreach ($only_from_path as $path) {
        if (str_starts_with($file, $path)) {
            return true;
        }
    }

    return false;
});

if (empty($files)) {
    echo "Keine Dateien zu prüfen.\n";
    exit(0);
}

$file_list = implode(' ', array_map('escapeshellarg', $files));

$command = "php vendor/bin/phpstan analyse $file_list --memory-limit=2G";

echo "Running: $command\n\n";

passthru($command, $return_code);

exit($return_code);
