<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Qwen API Integration
$hugging_face_token = "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"; // Replace with your Hugging Face token
$qwen_model_url = "https://api-inference.huggingface.co/models/Qwen/Qwen2.5-72B-Instruct";

// Database credentials
$db_host = 'localhost';
$db_user = 'root';
$db_password = ''; // Replace with your database password
$db_name = 'attributes_ref'; // Updated to lowercase

// Function to interact with Qwen API
function interactWithQwen($query) {
    global $hugging_face_token, $qwen_model_url;

    $json_data = json_encode(["inputs" => $query]);

    $ch = curl_init($qwen_model_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $hugging_face_token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return "Error communicating with Qwen API: " . curl_error($ch);
    }

    curl_close($ch);

    $response_data = json_decode($response, true);

    if (!isset($response_data[0]['generated_text'])) {
        return "Invalid response structure. Full response: " . htmlspecialchars($response);
    }

    return $response_data[0]['generated_text'];
}

// Connect to the database
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from uploaded_files table
$sql = "SELECT id, file_name, file_type, uploader_name, upload_date FROM uploaded_files"; 
$result = $conn->query($sql);

// Initialize a string to store the formatted data
$convertedText = "";
if ($result->num_rows > 0) {
    $convertedText .= "id | file name | file type | uploader name | upload date\n";
    $convertedText .= str_repeat("-", 60) . "\n";

    while ($row = $result->fetch_assoc()) {
        $convertedText .= "{$row['id']} | {$row['file_name']} | {$row['file_type']} | {$row['uploader_name']} | {$row['upload_date']}\n";
    }
} else {
    $convertedText = "No data found in the uploaded_files table.";
}

$conn->close();

// Check if the user has submitted a query
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
    $userQuery = $_POST['query'];
    $formattedQuery = "Answer the following question based on the data:\n" . $convertedText . "\n" . $userQuery;
    $qwenResponse = interactWithQwen($formattedQuery);
} else {
    $qwenResponse = "Ask a question about the uploaded files.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qwen Chatbox</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .chatbox {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .input-field {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .response {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #e9f9e9;
        }
        h2 {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="chatbox">
        <h2>Ask Qwen about Uploaded Files</h2>

        <form method="POST">
            <input type="text" name="query" class="input-field" placeholder="Type your question..." required>
            <button type="submit" class="input-field">Ask</button>
        </form>

        <div class="response">
            <h3>Qwen's Response:</h3>
            <p><?php echo nl2br(htmlspecialchars($qwenResponse)); ?></p>
        </div>
    </div>
</body>
</html>
