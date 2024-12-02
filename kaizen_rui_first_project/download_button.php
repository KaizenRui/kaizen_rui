<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Management System</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins', sans-serif;
            text-align: center;
            margin: 0;
            background: linear-gradient(to bottom, #4facfe, #00f2fe);
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        h1 {
            color: #fff;
            font-size: 2.5rem;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
        }

        .nav-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .nav-buttons a {
            text-decoration: none;
            color: #fff;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            padding: 12px 25px;
            border-radius: 50px;
            font-size: 1rem;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .nav-buttons a:hover {
            background: linear-gradient(to right, #2575fc, #6a11cb);
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        /* Parallax Effect Section (if needed) */
        .parallax {
            background: url('your-image.jpg') no-repeat center center/cover;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
            margin-top: 50px;
        }

        .parallax h2 {
            font-size: 2rem;
        }
    </style>
</head>
<body>
    <h1>Welcome to the File Management System</h1>
    <div class="nav-buttons">
        <a href="upload_button.html">Go to Upload Files</a>
        <a href="download_button.php">Go to Download Files</a>
        <a href="preview.php">Go to Preview Files</a>
    </div>
    <div class="parallax">
        <h2>Manage Your Files Seamlessly</h2>
    </div>
</body>
</html>
