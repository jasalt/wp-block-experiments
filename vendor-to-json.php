#!/usr/bin/env php
<?php

// PHP script `vendor-to-json` that is run on composer project root and traverses through folders in `./vendor/` storing each file handle into nested associative array in format required by PHP-WASM files constructor parameter (and data-files attribute). Ignores folders.

// Usage:
//   ./vendor-to-json.php [prefix]
//
// Parameters:
//   [prefix] - Optional. A path prefix to prepend to all parent paths in the output JSON.
//              This is useful when your files will be served from a subdirectory.
//              Example: ./vendor-to-json.php "/my-app" will generate parent paths like "/my-app/vendor/..."
//              If not provided, no prefix will be added.

// ```
// [
// {
// "name": "autoload.php",
// "parent": "/prefix/vendor",  // With prefix if provided
// "url": "/vendor/autoload.php"
// },
// {
// "name": "autoload_classmap.php",
// "parent": "/prefix/vendor/composer/",  // With prefix if provided
// "url": "/vendor/composer/autoload_classmap.php"
// }
// ]
// ```

// Example of `tree ./vendor`:
// ```
// vendor/
// ├── autoload.php
// ├── composer
// │   ├── autoload_classmap.php
// │   ├── autoload_files.php
// │   ├── autoload_namespaces.php
// │   ├── autoload_psr4.php
// │   ├── autoload_real.php
// │   ├── autoload_static.php
// │   ├── ClassLoader.php
// │   ├── installed.json
// │   ├── installed.php
// │   ├── InstalledVersions.php
// │   ├── LICENSE
// │   └── platform_check.php
// ├── symfony
// │   ├── deprecation-contracts
// │   │   ├── CHANGELOG.md
// ...


// Get the target path prefix from command line argument or use empty string
$targetPrefix = isset($argv[1]) ? rtrim($argv[1], '/') : '';

if (!file_exists('./vendor')) {
    echo "Error: vendor directory not found. Run this script from your composer project root.\n";
    exit(1);
}

// Initialize empty files array
$files = [];

/**
 * Recursively scans the directory and builds the file structure
 * @param string $dir The directory to scan
 * @param string $parentPath The parent path with prefix for the JSON output
 * @param string $urlPath The URL path without prefix
 */
function scanDirectory($dir, $parentPath, $urlPath) {
    global $files, $targetPrefix;

    $items = scandir($dir);

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $fullPath = $dir . '/' . $item;
        $newUrlPath = $urlPath . '/' . $item;
        $newParentPath = $targetPrefix . $newUrlPath;

        if (is_dir($fullPath)) {
            // Recursively scan subdirectory without adding the directory itself
            scanDirectory($fullPath, $newParentPath, $newUrlPath);
        } else {
            // Add file
            $files[] = [
                'name' => $item,
                'parent' => $parentPath,
                'url' => $newUrlPath
            ];
        }
    }
}

// Start the traversal
scanDirectory('./vendor', $targetPrefix . '/vendor', '/vendor');

// Convert to JSON and output
echo json_encode($files, JSON_PRETTY_PRINT);

// Optionally save to a file
file_put_contents('vendor-files.json', json_encode($files, JSON_PRETTY_PRINT));
echo "\nJSON saved to vendor-files.json\n";
