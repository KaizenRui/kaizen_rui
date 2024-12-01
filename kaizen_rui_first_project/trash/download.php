<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Directory where the files are stored
$uploadDir = __DIR__ . '/uploads/';

// Check if the 'file' parameter is set in the URL
if (isset($_GET['file'])) {
    // Sanitize the file name
    $fileName = basename($_GET['file']); 
    $filePath = $uploadDir . $fileName;

    // Check if the file exists
    if (file_exists($filePath)) {
        // Set headers to force download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        // Output the file
        readfile($filePath);
        exit;
    } else {
        echo "Error: The requested file does not exist.";
    }
} else {
    echo "Error: No file specified.";
}
?>
