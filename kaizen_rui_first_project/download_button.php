<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Directory to search files
$rootDir = __DIR__; // Base directory
$allFiles = [];

// Recursive function to list all .txt files in the directory
function listAllFiles($directory, &$fileList) {
    foreach (scandir($directory) as $file) {
        if ($file !== '.' && $file !== '..') {
            $filePath = $directory . DIRECTORY_SEPARATOR . $file;
            if (is_dir($filePath)) {
                listAllFiles($filePath, $fileList); // Recursive call for subdirectories
            } else {
                // Only add .txt files to the list
                if (pathinfo($file, PATHINFO_EXTENSION) === 'txt') {
                    $fileList[] = $filePath;
                }
            }
        }
    }
}

// Fetch all .txt files
listAllFiles($rootDir, $allFiles);

// Handle search query
$matches = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchKeyword = strtolower(trim($_GET['search']));

    // Search for .txt files containing the search keyword
    foreach ($allFiles as $filePath) {
        if (strpos(strtolower(basename($filePath)), $searchKeyword) !== false) {
            $matches[] = $filePath;
        }
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
        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(to bottom, #4facfe, #00f2fe);
            color: #333;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        h1, h3 {
            color: #fff;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 2px solid #ccc;
            border-radius: 30px;
        }

        button {
            padding: 10px 20px;
            border: none;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #fff;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background: linear-gradient(to right, #2575fc, #6a11cb);
            transform: scale(1.05);
        }

        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background: #fff;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .back-button {
            margin-top: 20px;
            padding: 10px 20px;
            text-decoration: none;
            color: #fff;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: linear-gradient(to right, #2575fc, #6a11cb);
            transform: scale(1.05);
        }

        p {
            color: #fff;
            font-size: 1.2rem;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <h1>File Search and Downloader</h1>

    <!-- Back Button -->
    <a href="index.html" class="back-button">Back to Main Menu</a>

    <!-- Search Form -->
    <form action="" method="get">
        <input type="text" name="search" placeholder="Search for .txt files..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
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
                    $fileSize = round(filesize($match) / 1024, 2);
                    $fileContent = htmlspecialchars(file_get_contents($match)); // Get file content
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($fileName); ?></td>
                    <td>txt</td>
                    <td><?php echo htmlspecialchars($fileSize); ?></td>
                    <td><button onclick="downloadFile('<?php echo addslashes($fileContent); ?>', '<?php echo addslashes($fileName); ?>')">Download</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif (isset($_GET['search'])): ?>
        <p>No files found for "<?php echo htmlspecialchars($_GET['search']); ?>".</p>
    <?php endif; ?>

    <script>
        // JavaScript logic to download file content
        function downloadFile(content, filename) {
            let a = document.createElement('a');
            let blob = new Blob([content], { type: 'text/plain' });
            let url = URL.createObjectURL(blob);
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>
