<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Directory to search files
$rootDir = __DIR__; // Base directory
$allFiles = [];

// Recursive function to list all files in the directory
function listAllFiles($directory, &$fileList) {
    foreach (scandir($directory) as $file) {
        if ($file !== '.' && $file !== '..') {
            $filePath = $directory . DIRECTORY_SEPARATOR . $file;
            if (is_dir($filePath)) {
                listAllFiles($filePath, $fileList); // Recursive call for subdirectories
            } else {
                // Allow specific file types
                if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['txt', 'jpg', 'png', 'docx', 'xlsx'])) {
                    $fileList[] = $filePath;
                }
            }
        }
    }
}

// Fetch all allowed files
listAllFiles($rootDir, $allFiles);

// Handle search query
$matches = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchKeyword = strtolower(trim($_GET['search']));

    // Search for files containing the search keyword
    foreach ($allFiles as $filePath) {
        if (strpos(strtolower(basename($filePath)), $searchKeyword) !== false) {
            $matches[] = $filePath;
        }
    }
}

// Handle file download request
if (isset($_GET['file'])) {
    $filePath = realpath($_GET['file']);
    if ($filePath && file_exists($filePath) && strpos($filePath, $rootDir) === 0) {
        // Validate that the file exists within the allowed directory
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        echo "Error: File not found or access denied.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Search and Downloader</title>
    <style>
        /* Styling similar to previous version */
    </style>
</head>
<body>
    <h1>File Search and Downloader</h1>

    <!-- Back Button -->
    <a href="index.html" class="back-button">Back to Main Menu</a>

    <!-- Search Form -->
    <form action="" method="get">
        <input type="text" name="search" placeholder="Search for files..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($matches)): ?>
        <h3>Search Results:</h3>
        <table>
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>File Type</th>
                    <th>Size (KB)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matches as $match): 
                    $fileName = basename($match);
                    $fileType = pathinfo($match, PATHINFO_EXTENSION);
                    $fileSize = round(filesize($match) / 1024, 2);
                    $encodedPath = urlencode($match); // Encode the file path
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($fileName); ?></td>
                    <td><?php echo htmlspecialchars($fileType); ?></td>
                    <td><?php echo htmlspecialchars($fileSize); ?></td>
                    <td>
                        <a href="?file=<?php echo $encodedPath; ?>">Download</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif (isset($_GET['search'])): ?>
        <p>No files found for "<?php echo htmlspecialchars($_GET['search']); ?>".</p>
    <?php endif; ?>
    
</body>
</html>
        
