<?php
session_start();
require_once('../model/database.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $email = $_POST['email'];
    $recent_password = $_POST['recent_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Check if the email exists in the database
    $consumerData = fetchConsumerByEmail($email);
    if ($consumerData) {
        // Check if the recent password is correct (since you're using plain text)
        if ($recent_password === $consumerData['password']) {
            // Check if the new password and confirm new password match
            if ($new_password === $confirm_new_password) {
                // Update the password in the database with the new one (plain text)
                $conn = getConnection();
                $sql = "UPDATE Consumer SET password = ? WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $new_password, $email);

                if ($stmt->execute()) {
                    echo "Password has been updated successfully.";
                } else {
                    echo "Error updating password.";
                }

                $stmt->close();
                $conn->close();
            } else {
                echo "New passwords do not match.";
            }
        } else {
            echo "Recent password is incorrect.";
        }
    } else {
        echo "No account found with that email.";
    }
}
?>
