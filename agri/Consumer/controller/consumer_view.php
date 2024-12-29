<?php
session_start();
require_once('../model/database.php');

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Consumer') {
    header('Location: ../view/login.html');
    exit();
}

// Fetch the logged-in consumer's data
$consumerData = fetchConsumerByEmail($_SESSION['email']);
$id = $consumerData['id'];
$first_name = $consumerData['first_name'];
$last_name = $consumerData['last_name'];
$email = $consumerData['email'];
$mobile = $consumerData['mobile'];
$country = $consumerData['country'];
$address = $consumerData['address'];
$dob = $consumerData['dob'];
$profile_image = $consumerData['profile_image'];

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    // Handle file upload
    $targetDir = "../uploads/images/";
    $fileName = "profile_" . uniqid() . "." . pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
    $targetFile = $targetDir . $fileName;
    
    // Check if the file is an image
    if (getimagesize($_FILES['profile_image']['tmp_name']) !== false) {
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
            // Update the profile image in the database
            $conn = getConnection();
            $sql = "UPDATE Consumer SET profile_image = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $fileName, $id);
            $stmt->execute();
            $stmt->close();
            $conn->close();

            // Refresh the consumer's data with the new profile image
            $consumerData = fetchConsumerByEmail($_SESSION['email']);
            $profile_image = $consumerData['profile_image'];
        } else {
            echo "Failed to upload image.";
        }
    } else {
        echo "Please upload a valid image file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consumer Profile</title>
    <link rel="stylesheet" href="../asset/consumer_view.css">
</head>
<body>
    <h1>Consumer Profile</h1>
    <p>Welcome, <?php echo $first_name . " " . $last_name; ?>!</p>

    <!-- Display profile image if available -->
    <img src="../uploads/images/<?php echo $profile_image ? $profile_image : 'default.jpg'; ?>" alt="Profile Picture" width="150" height="150">

    <!-- Form to upload a new profile image -->
    <form action="consumer_view.php" method="POST" enctype="multipart/form-data">
        <label for="profile_image">Upload a new profile image:</label><br>
        <input type="file" name="profile_image" accept="image/*" required><br><br>
        <button type="submit">Upload Image</button>
    </form>

    <!-- Display other profile information -->
    <h2>Profile Details</h2>
    <p><strong>Name:</strong> <?php echo $first_name . " " . $last_name; ?></p>
    <p><strong>Email:</strong> <?php echo $email; ?></p>
    <p><strong>Mobile:</strong> <?php echo $mobile; ?></p>
    <p><strong>Country:</strong> <?php echo $country; ?></p>
    <p><strong>Address:</strong> <?php echo $address; ?></p>
    <p><strong>Date of Birth:</strong> <?php echo $dob; ?></p>


    <a href="consumer_edit.php"><button>Edit Profile</button></a>


    <a href="logout.php">Logout</a>
</body>
</html>
