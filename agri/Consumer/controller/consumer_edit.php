<?php
session_start();
require_once('../model/database.php');


if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Consumer') {
    header('Location: ../view/login.html');
    exit();
}


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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email']; 
    $mobile = $_POST['mobile'];
    $country = $_POST['country'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../uploads/images/";
        $fileName = "profile_" . uniqid() . "." . pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $targetFile = $targetDir . $fileName;
        
        if (getimagesize($_FILES['profile_image']['tmp_name']) !== false) {
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
                $profile_image = $fileName;
            } else {
                echo "Failed to upload image.";
            }
        } else {
            echo "Please upload a valid image file.";
        }
    }

    // Update the profile data in the database
    $conn = getConnection();
    $sql = "UPDATE Consumer SET first_name = ?, last_name = ?, mobile = ?, country = ?, address = ?, dob = ?, profile_image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $first_name, $last_name, $mobile, $country, $address, $dob, $profile_image, $id);
    if ($stmt->execute()) {
        echo "Profile updated successfully!";
        // Optionally redirect to the profile view page
        header('Location: consumer_view.php');
        exit();
    } else {
        echo "Error updating profile.";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Consumer Profile</title>
</head>
<body>
    <h1>Edit Consumer Profile</h1>

    <form action="consumer_edit.php" method="POST" enctype="multipart/form-data">
        <label for="first_name">First Name:</label><br>
        <input type="text" name="first_name" value="<?php echo $first_name; ?>" required><br><br>

        <label for="last_name">Last Name:</label><br>
        <input type="text" name="last_name" value="<?php echo $last_name; ?>" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" value="<?php echo $email; ?>" readonly><br><br>

        <label for="mobile">Mobile:</label><br>
        <input type="text" name="mobile" value="<?php echo $mobile; ?>" required><br><br>

        <label for="country">Country:</label><br>
        <input type="text" name="country" value="<?php echo $country; ?>" required><br><br>

        <label for="address">Address:</label><br>
        <textarea name="address" required><?php echo $address; ?></textarea><br><br>

        <label for="dob">Date of Birth:</label><br>
        <input type="date" name="dob" value="<?php echo $dob; ?>" required><br><br>

        <button type="submit">Update Profile</button>
    </form>

    <a href="consumer_view.php">Back to Profile</a>
</body>
</html>
