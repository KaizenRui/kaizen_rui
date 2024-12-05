<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Qwen API Integration
$hugging_face_token = "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"; // Replace with your Hugging Face token
$qwen_model_url = "https://api-inference.huggingface.co/models/Qwen/Qwen2.5-72B-Instruct";

// Function to interact with Qwen API
function interactWithQwen($query) {
    global $hugging_face_token, $qwen_model_url;

    // Prepare API input
    $json_data = json_encode(["inputs" => $query]);

    // Initialize CURL request
    $ch = curl_init($qwen_model_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $hugging_face_token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

    // Execute request and handle response
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return "Error communicating with Qwen API: " . curl_error($ch);
    }

    curl_close($ch);

    // Parse response
    $response_data = json_decode($response, true);

    // Check for a valid generated text
    if (!isset($response_data[0]['generated_text'])) {
        return "Invalid response structure. Full response: " . htmlspecialchars($response);
    }

    return $response_data[0]['generated_text'];
}

// Directory path where files are located
$folderPath = "C:\\xampp\\htdocs\\kaizen_rui_first_project\\test_qwen";

// Validate the folder and retrieve files
if (!is_dir($folderPath)) {
    die("Error: The specified folder does not exist.");
}

$files = array_diff(scandir($folderPath), ['.', '..']); // Exclude . and .. from results

// Prepare file list for Qwen
$fileList = empty($files) 
    ? "No files found in the folder." 
    : implode(", ", $files);

// Simplified query for Qwen
$queryToQwen = empty($files) 
    ? "There are no files in the folder. Please confirm."
    : "Here are the file names: $fileList. Can you confirm this list?";

// Send query to Qwen API
$qwenResponse = interactWithQwen($queryToQwen);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qwen File Listing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .response, .files {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        h2 {
            color: #333;
        }
    </style>
</head>
<body>
    <h2>Qwen File Processing</h2>

    <div class="files">
        <h3>File List:</h3>
        <pre><?php echo htmlspecialchars($fileList); ?></pre>
    </div>

    <div class="response">
        <h3>Qwen's Response:</h3>
        <p><?php echo nl2br(htmlspecialchars($qwenResponse)); ?></p>
    </div>
</body>
</html>
