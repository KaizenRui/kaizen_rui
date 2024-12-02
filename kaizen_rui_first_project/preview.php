<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Base directory to search
$rootDir = __DIR__; // Current directory
$allowedPreviewExtensions = ['txt', 'jpg', 'png', 'pdf']; // Supported file types for preview

// Recursive function to search for files
function searchFiles($directory, &$fileList, $keyword) {
    foreach (scandir($directory) as $file) {
        if ($file !== '.' && $file !== '..') {
            $filePath = $directory . DIRECTORY_SEPARATOR . $file;
            if (is_dir($filePath)) {
                // Recursively search subdirectories
                searchFiles($filePath, $fileList, $keyword);
            } else {
                // Match file name with keyword
                if (stripos($file, $keyword) !== false) {
                    $fileList[] = $filePath;
                }
            }
        }
    }
}

// Handle search query
$matches = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchKeyword = trim($_GET['search']);
    searchFiles($rootDir, $matches, $searchKeyword);
}

// File preview handler
if (isset($_GET['preview'])) {
    $filePath = realpath($_GET['preview']);
    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

    // Validate file for preview
    if ($filePath && file_exists($filePath) && in_array($fileExtension, $allowedPreviewExtensions)) {
        if (in_array($fileExtension, ['jpg', 'png'])) {
            // Image preview
            header('Content-Type: image/' . $fileExtension);
            readfile($filePath);
        } elseif ($fileExtension === 'txt') {
            // Text file preview
            header('Content-Type: text/plain');
            readfile($filePath);
        } elseif ($fileExtension === 'pdf') {
            // PDF preview
            header('Content-Type: application/pdf');
            readfile($filePath);
        } else {
            echo "Preview not available for this file type.";
        }
        exit;
    } else {
        echo "Invalid file or preview not supported.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search and Preview Files</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Search and Preview Files</h2>
    <a href="index.html" class="back-button">Back to Main Menu</a>
    <!-- Search Form -->
    <form action="" method="GET">
        <label for="search">Search for a file:</label>
        <input type="text" id="search" name="search" placeholder="Enter keywords..." required>
        <button type="submit">Search</button>
    </form>

    <!-- Display Results -->
    <?php if (isset($_GET['search']) && !empty($matches)): ?>
        <h3>Search Results:</h3>
        <table>
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>File Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matches as $filePath): 
                    $fileName = basename($filePath);
                    $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
                    $encodedPath = urlencode($filePath); // Encode path for safe use in URLs
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($fileName); ?></td>
                    <td><?php echo htmlspecialchars($fileType); ?></td>
                    <td>
                        <?php if (in_array($fileType, $allowedPreviewExtensions)): ?>
                            <a href="?preview=<?php echo $encodedPath; ?>" target="_blank">Preview</a>
                        <?php else: ?>
                            Preview not available
                        <?php endif; ?>
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
