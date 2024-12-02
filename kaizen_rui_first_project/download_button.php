<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search and Download Files</title>
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
        #results {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Search and Download Files</h2>

    <form action="download_button_backend.php" method="GET">
        <label for="search">Search for a file:</label>
        <input type="text" id="search" name="search" placeholder="Enter keywords..." required>
        <button type="submit">Search</button>
    </form>

    <div id="results">
        <?php
        // Check if search results are available
        if (isset($_GET['search'])) {
            // Fetch the search results from the backend
            $matches = json_decode(file_get_contents('php://input'), true);
            
            if (!empty($matches)) {
                echo "<h3>Search Results:</h3>";
                echo "<table>";
                echo "<thead><tr><th>File Name</th><th>File Type</th><th>Size (KB)</th><th>Actions</th></tr></thead>";
                echo "<tbody>";
                foreach ($matches as $match) {
                    $fileName = htmlspecialchars($match['name']);
                    $fileType = htmlspecialchars($match['type']);
                    $fileSize = htmlspecialchars($match['size']);
                    $filePath = htmlspecialchars($match['path']);
                    echo "<tr>";
                    echo "<td>{$fileName}</td>";
                    echo "<td>{$fileType}</td>";
                    echo "<td>{$fileSize}</td>";
                    echo "<td><a href=\"{$filePath}\" download>Download</a></td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<p>No files found for \"" . htmlspecialchars($_GET['search']) . "\".</p>";
            }
        }
        ?>
    </div>
 
</body>
</html>
