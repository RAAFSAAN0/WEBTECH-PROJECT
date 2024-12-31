<!DOCTYPE html>
<html>
<head>
    <title>Tutorial Upload</title>
</head>
<body>
    <h1>Upload Tutorial</h1>
    <form action="upload_video.php" method="post" enctype="multipart/form-data">
        <label for="video_title">Video Title:</label><br>
        <input type="text" id="video_title" name="video_title" required><br><br>

        <label for="video_description">Video Description:</label><br>
        <textarea id="video_description" name="video_description" rows="4" cols="50" required></textarea><br><br>

        <label for="video_file">Upload Video:</label><br>
        <input type="file" id="video_file" name="video_file" accept="video/*" required><br><br>

        <button type="submit">Upload</button>
    </form>
</body>
</html>
