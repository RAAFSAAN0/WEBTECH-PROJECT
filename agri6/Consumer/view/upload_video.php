<?php
// Include database connection
require '../model/database.php'; // Ensure this file connects to your database

// Initialize database connection
$conn = new mysqli('localhost', 'root', '', 'agriculture'); // Replace 'your_database_name' with your database

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Directory where the video will be uploaded
    $targetDir = "C:/xampp/htdocs/agri6/Consumer/video/"; // Ensure this is the correct path for your environment

    // Fetch form data
    $farmerEmail = isset($_POST['email']) ? $_POST['email'] : '';
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $uploadDate = date('Y-m-d H:i:s'); // Current timestamp

    // Handle the video file
    if (isset($_FILES['video']) && !empty($_FILES['video']['name'])) {
        $videoFile = $_FILES['video']['name'];
        $targetFilePath = $targetDir . basename($videoFile);
        $videoFileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allowed file types
        $allowedTypes = ['mp4', 'avi', 'mov', 'wmv'];

        // Validate file type
        if (in_array(strtolower($videoFileType), $allowedTypes)) {
            // Optional: Validate file size (e.g., limit to 50MB)
            if ($_FILES['video']['size'] <= 5000 * 1024 * 1024) { // 50MB limit
                // Sanitize the file name to avoid issues with special characters
                $targetFilePath = $targetDir . uniqid('video_') . '.' . $videoFileType;

                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES['video']['tmp_name'], $targetFilePath)) {
                    // Insert video details into the database
                    $sql = "INSERT INTO video (email, title, description, video_path, upload_date) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);

                    if ($stmt) {
                        $stmt->bind_param('sssss', $farmerEmail, $title, $description, $targetFilePath, $uploadDate);
                        if ($stmt->execute()) {
                            echo "Video uploaded successfully.";
                        } else {
                            echo "Database error: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        echo "Failed to prepare the SQL statement.";
                    }
                } else {
                    echo "Failed to upload the video. Please try again.";
                }
            } else {
                echo "The file is too large. Maximum allowed size is 50MB.";
            }
        } else {
            echo "Invalid file type. Only MP4, AVI, MOV, and WMV files are allowed.";
        }
    } else {
        echo "No video file was selected. Please choose a file to upload.";
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Video</title>
    <script>
        // Function to preview video before uploading
        function previewVideo() {
            const fileInput = document.getElementById('video');
            const videoPreview = document.getElementById('videoPreview');
            const file = fileInput.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    videoPreview.src = e.target.result;
                    videoPreview.style.display = 'block'; // Show video preview
                }
                reader.readAsDataURL(file);
            } else {
                videoPreview.style.display = 'none'; // Hide video preview if no file selected
            }
        }
    </script>
</head>
<body>
    <h2>Upload Video</h2>
    <form action="upload_video.php" method="POST" enctype="multipart/form-data">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br><br>

        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required><br><br>

        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea><br><br>

        <label for="video">Select Video:</label>
        <input type="file" name="video" id="video" accept="video/*" required onchange="previewVideo()"><br><br>

        <video id="videoPreview" width="400" style="display:none;" controls>
            Your browser does not support the video tag.
        </video><br><br>

        <button type="submit">Upload</button>
    </form>
</body>
</html>
