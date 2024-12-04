<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getFolderSize($folder) {
    $size = 0;
    foreach (glob($folder . '/*') as $file) {
        $size += is_file($file) ? filesize($file) : getFolderSize($file);
    }
    return $size;
}

// Initialize uploader variable
$uploader = "Unknown"; // Default uploader name if not provided
// Define allowed file types and size limit
$allowedExtensions = ["pdf", "docx", "txt", "jpg", "jpeg", "png", "gif", "xlsx", "xls", "mp4"];
$maxFileSize = 25 * 1024 * 1024; // 25 MB in bytes

// Output HTML with styling
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Management</title>
    <link rel="stylesheet" href="upload_button_backend.css">
</head>
<body>
';

if (isset($_POST['submit'])) {
    $target_dir = __DIR__ . "/uploads/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (!empty($_POST['uploader'])) {
        $uploader = htmlspecialchars($_POST['uploader']);
    }

    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $file_size = $_FILES["file"]["size"];

    if (!in_array($file_type, $allowedExtensions)) {
        echo "<p>Error: Only the following file types are allowed: " . implode(', ', $allowedExtensions) . ".</p>";
        exit;
    }

    if ($file_size > $maxFileSize) {
        echo "<p>Error: The file exceeds the maximum allowed size of 25 MB.</p>";
        exit;
    }

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        file_put_contents($target_file . '.txt', "Uploader: " . $uploader);
        echo "<p>The file <strong>" . htmlspecialchars(basename($_FILES["file"]["name"])) . "</strong> has been uploaded successfully by <strong>" . $uploader . "</strong>.</p>";
    } else {
        echo "<p>Error: There was an error uploading your file. Please try again.</p>";
    }
} else {
    echo "<p>No file was uploaded.</p>";
}

      // Database connection details
      $host = 'localhost';
      $db = 'attributes_ref';
      $user = 'root';
      $pass = '';

      // Establish database connection
      $conn = new mysqli($host, $user, $pass, $db);

      // Check connection
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }

      // Insert file details into the database
      $stmt = $conn->prepare("INSERT INTO uploaded_files (file_name, file_type, uploader_name) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $file_name, $file_type, $uploader);

      // Set variables for insertion
      $file_name = basename($_FILES["file"]["name"]);
      $stmt->execute();

      if ($stmt->affected_rows > 0) {
          echo "<p>File details saved to the database successfully.</p>";
      } else {
          echo "<p>Error saving file details to the database.</p>";
      }

      // Close the statement and connection
      $stmt->close();
      $conn->close();



echo '<a href="index.html">Back to Main Menu</a>';
echo '<a href="?action=view_files">View Uploaded Files</a>';

if (isset($_GET['action']) && $_GET['action'] === 'view_files') {
    $folder = __DIR__ . "/uploads/";
    $files = scandir($folder);

    echo "<h3>Uploaded Files</h3>";
    echo '<table>';
    echo "<thead><tr><th>File Name</th><th>File Type</th><th>Date Uploaded</th><th>Uploader</th><th>Size (KB)</th></tr></thead>";
    echo "<tbody>";

    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $file_path = $folder . $file;
            $file_date = date("Y-m-d H:i:s", filemtime($file_path));
            $file_size = round(filesize($file_path) / 1024, 2);
            $file_type = pathinfo($file_path, PATHINFO_EXTENSION);

            $uploader = "Unknown";
            $metadata_file = $file_path . '.txt';
            if (file_exists($metadata_file)) {
                $metadata = file_get_contents($metadata_file);
                if (preg_match('/Uploader:\s*(.*)/', $metadata, $matches)) {
                    $uploader = $matches[1];
                }
            }

            echo "<tr>";
            echo "<td>" . htmlspecialchars($file) . "</td>";
            echo "<td>" . htmlspecialchars($file_type) . "</td>";
            echo "<td>" . htmlspecialchars($file_date) . "</td>";
            echo "<td>" . htmlspecialchars($uploader) . "</td>";
            echo "<td>" . htmlspecialchars($file_size) . "</td>";
            echo "</tr>";
        }
    }

    echo "</tbody></table>";
    $total_size = round(getFolderSize($folder) / 1024, 2);
    echo "<p>Total Folder Size: <strong>" . $total_size . " KB</strong></p>";
}

echo '</body></html>';
?>
