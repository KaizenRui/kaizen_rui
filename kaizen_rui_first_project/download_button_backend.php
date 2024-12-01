<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Directory to start the search
$rootDir = __DIR__; // Adjust if the root is elsewhere

// Check if the user clicked on a file to download
if (isset($_GET['file'])) {
    $fileToDownload = $_GET['file'];
    $filePath = realpath($rootDir . '/' . $fileToDownload); // Use realpath to resolve the absolute path

    // Check if file exists and is within the allowed directory
    if ($filePath && strpos($filePath, $rootDir) === 0 && file_exists($filePath)) {
        // Set headers to initiate the download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        echo "<p>Error: File not found or access denied.</p>";
    }
}

// Get search query
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchKeyword = strtolower(trim($_GET['search']));
    $matches = [];

    // Function to recursively search files
    function searchFiles($directory, $keyword, &$results) {
        foreach (scandir($directory) as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $directory . '/' . $file;
                if (is_dir($filePath)) {
                    // Recursive call for subdirectories
                    searchFiles($filePath, $keyword, $results);
                } else {
                    // Add file to results if name contains keyword
                    if (strpos(strtolower($file), $keyword) !== false) {
                        $results[] = $filePath;
                    }
                }
            }
        }
    }

    // Start searching
    searchFiles($rootDir, $searchKeyword, $matches);

    // Display results
    if (!empty($matches)) {
        echo "<h3>Search Results for '<strong>" . htmlspecialchars($searchKeyword) . "</strong>':</h3>";
        echo '<table>';
        echo '<tr><th>File Name</th><th>File Type</th><th>Upload Date</th><th>Uploader</th><th>Size (KB)</th><th>Action</th></tr>';
        
        foreach ($matches as $match) {
            $fileName = basename($match);
            $fileType = pathinfo($match, PATHINFO_EXTENSION);
            $fileDate = date("Y-m-d H:i:s", filemtime($match));
            $fileSize = round(filesize($match) / 1024, 2); // Size in KB

            // Get uploader info from metadata (if exists)
            $uploader = "Unknown";
            $metadataFile = $match . '.txt';
            if (file_exists($metadataFile)) {
                $metadata = file_get_contents($metadataFile);
                if (preg_match('/Uploader:\s*(.*)/', $metadata, $matches)) {
                    $uploader = $matches[1];
                }
            }

            echo "<tr>";
            echo "<td>" . htmlspecialchars($fileName) . "</td>";
            echo "<td>" . htmlspecialchars($fileType) . "</td>";
            echo "<td>" . htmlspecialchars($fileDate) . "</td>";
            echo "<td>" . htmlspecialchars($uploader) . "</td>";
            echo "<td>" . htmlspecialchars($fileSize) . "</td>";
            // Pass the full file path for download
            echo "<td><a href='?file=" . urlencode($match) . "'>Download</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No files found matching '<strong>" . htmlspecialchars($searchKeyword) . "</strong>'.</p>";
    }
    // Back button
    echo "<a href='download_button.php' style='padding: 10px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Back to Search</a>";
} else {
    echo "<p>Please enter a search keyword.</p>";
}
?>
