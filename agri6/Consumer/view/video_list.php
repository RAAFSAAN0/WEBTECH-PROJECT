<?php
// Include database connection
require '../model/database.php';

// Initialize database connection
$conn = new mysqli('localhost', 'root', '', 'agriculture'); // Replace with your database name

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch videos based on search input
$sql = "SELECT v.id, v.title, f.first_name, f.last_name, v.video_path
        FROM video v
        JOIN farmer f ON v.email = f.email
        WHERE v.title LIKE ? OR f.first_name LIKE ? OR f.last_name LIKE ?";
$stmt = $conn->prepare($sql);
$searchParam = '%' . $search . '%';
$stmt->bind_param('sss', $searchParam, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video List</title>
</head>
<body>
    <h2>Video List</h2>
    <form method="GET" action="video_list.php">
        <input type="text" name="search" placeholder="Search by title or uploader" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div style='border: 1px solid #ddd; margin-bottom: 20px; padding: 10px;'>";
            echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
            echo "<p><strong>Uploaded by:</strong> " . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</p>";

            // Video preview
            $videoURL = "http://localhost/agri2/Consumer/video/" . basename($row['video_path']);
            echo "<video width='320' height='240' controls>
                    <source src='" . htmlspecialchars($videoURL) . "' type='video/mp4'>
                    Your browser does not support the video tag.
                  </video>";

            // Link to view details
            echo "<p><a href='video_details.php?id=" . urlencode($row['id']) . "'>View Details</a></p>";
            echo "</div>";
        }
    } else {
        echo "<p>No videos found.</p>";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
