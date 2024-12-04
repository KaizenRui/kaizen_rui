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

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attributes_ref";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle search query
$matches = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchKeyword = trim($_GET['search']);
    $filter = isset($_GET['filter']) ? $_GET['filter'] : null;

    if ($filter) {
        // Database-based filtering
        $searchKeyword = $conn->real_escape_string($searchKeyword);
        $query = "SELECT * FROM uploaded_files WHERE $filter LIKE '%$searchKeyword%'";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $matches[] = [
                    'file_name' => $row['file_name'],
                    'file_type' => $row['file_type'],
                    'uploader_name' => $row['uploader_name'],
                    'upload_date' => $row['upload_date']
                ];
            }
        }
    } else {
        // File system search (fallback)
        searchFiles($rootDir, $matches, $searchKeyword);
    }
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
    <title>Search and Preview Files with Filters</title>
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
        select {
            padding: 10px;
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
    <h2>Search and Preview Files with Filters</h2>
    <a href="index.html" class="back-button">Back to Main Menu</a>
    <!-- Search Form with Filters -->
    <form action="" method="GET">
        <label for="filter">Filter by:</label>
        <select name="filter" id="filter" required>
            <option value="file_name">File Name</option>
            <option value="file_type">File Type</option>
            <option value="uploader_name">Uploader Name</option>
            <option value="upload_date">Upload Date</option>
        </select>
        <label for="search">Search for a file:</label>
        <input type="text" id="search" name="search" placeholder="Enter keywords..." required>
        <button type="submit">Search</button>
    </form>

    <!-- Display Results -->
    <?php 
    if (isset($_GET['search']) && !empty($matches)): ?>
        <h3>Search Results:</h3>
        <table>
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>File Type</th>
                    <th>Uploader Name</th>
                    <th>Upload Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matches as $file): ?>
                <tr>
                    <td><?php echo htmlspecialchars($file['file_name']); ?></td>
                    <td><?php echo htmlspecialchars($file['file_type']); ?></td>
                    <td><?php echo htmlspecialchars($file['uploader_name']); ?></td>
                    <td><?php echo htmlspecialchars($file['upload_date']); ?></td>
                    <td>
                        <a href="?preview=<?php echo urlencode($file['file_name']); ?>" target="_blank">Preview</a>
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
