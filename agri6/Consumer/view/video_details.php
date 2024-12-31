<?php
session_start();
require_once('../model/database.php');  // Assuming this file contains necessary database functions

// Check if the video ID is passed via the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid video ID!";
    exit();
}

$videoId = $_GET['id'];

// Fetch video details from the database
$conn = getConnection();
$sql = "SELECT v.id, v.title, v.description, v.video_path, v.upload_date, v.likes, v.dislikes, f.first_name, f.last_name 
        FROM video v
        JOIN farmer f ON v.email = f.email
        WHERE v.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $videoId);
$stmt->execute();
$result = $stmt->get_result();

// Check if the video exists
if ($result->num_rows === 0) {
    echo "Video not found!";
    exit();
}

$video = $result->fetch_assoc();

// Fetch comments for the video (if any)
$sql_comments = "SELECT c.comment_text, c.comment_date, u.first_name, u.last_name 
                 FROM video_comments c
                 JOIN consumer u ON c.user_id = u.id
                 WHERE c.video_id = ?
                 ORDER BY c.comment_date DESC";
$stmt_comments = $conn->prepare($sql_comments);
$stmt_comments->bind_param("i", $videoId);
$stmt_comments->execute();
$comments = $stmt_comments->get_result();

// Handle like and dislike actions
if (isset($_POST['like'])) {
    // Increment likes
    $sql_like = "UPDATE video SET likes = likes + 1 WHERE id = ?";
    $stmt_like = $conn->prepare($sql_like);
    $stmt_like->bind_param("i", $videoId);
    $stmt_like->execute();
    header("Location: video_details.php?id=" . $videoId);
    exit();
}

if (isset($_POST['dislike'])) {
    // Increment dislikes
    $sql_dislike = "UPDATE video SET dislikes = dislikes + 1 WHERE id = ?";
    $stmt_dislike = $conn->prepare($sql_dislike);
    $stmt_dislike->bind_param("i", $videoId);
    $stmt_dislike->execute();
    header("Location: video_details.php?id=" . $videoId);
    exit();
}

// Check if comment is being submitted
if (isset($_POST['submit_comment']) && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $commentText = $_POST['comment_text'];

    // Insert comment into the database
    $sql_insert_comment = "INSERT INTO video_comments (video_id, user_id, user_type, comment_text) 
                           VALUES (?, ?, ?, ?)";
    $stmt_insert_comment = $conn->prepare($sql_insert_comment);
    $userType = $_SESSION['role'];  // Consumer, Farmer, etc.
    $stmt_insert_comment->bind_param("iiss", $videoId, $userId, $userType, $commentText);
    $stmt_insert_comment->execute();
    $stmt_insert_comment->close();

    // Redirect to refresh the page
    header("Location: video_details.php?id=" . $videoId);
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Details</title>
</head>
<body>
    <h2>Video Details</h2>

    <!-- Video Information -->
    <h3><?php echo htmlspecialchars($video['title']); ?></h3>
    <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($video['description'])); ?></p>
    <p><strong>Uploaded by:</strong> <?php echo htmlspecialchars($video['first_name']) . ' ' . htmlspecialchars($video['last_name']); ?></p>
    <p><strong>Upload Date:</strong> <?php echo date("F j, Y, g:i a", strtotime($video['upload_date'])); ?></p>
    <p><strong>Likes:</strong> <?php echo $video['likes']; ?> | <strong>Dislikes:</strong> <?php echo $video['dislikes']; ?></p>

    <!-- Video Preview -->
    <video width="640" height="360" controls>
        <source src="<?php echo htmlspecialchars('http://localhost/agri6/Consumer/video/' . basename($video['video_path'])); ?>" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <!-- Like / Dislike Buttons -->
    <form method="POST">
        <button type="submit" name="like">Like</button>
        <button type="submit" name="dislike">Dislike</button>
    </form>

    <hr>

    <!-- Comments Section -->
    <h4>Comments</h4>
    <?php if ($comments->num_rows > 0): ?>
        <?php while ($comment = $comments->fetch_assoc()): ?>
            <div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">
                <p><strong><?php echo htmlspecialchars($comment['first_name']) . ' ' . htmlspecialchars($comment['last_name']); ?>:</strong> <?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                <p><small>Posted on: <?php echo date("F j, Y, g:i a", strtotime($comment['comment_date'])); ?></small></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No comments yet.</p>
    <?php endif; ?>

    <!-- Comment Form -->
    <h4>Add a Comment</h4>
    <?php if (isset($_SESSION['email'])): ?>
        <form method="POST">
            <textarea name="comment_text" rows="4" cols="50" required placeholder="Write your comment here..."></textarea><br>
            <button type="submit" name="submit_comment">Submit Comment</button>
        </form>
    <?php else: ?>
        <p><a href="login.html">Login</a> to leave a comment.</p>
    <?php endif; ?>
</body>
</html>
