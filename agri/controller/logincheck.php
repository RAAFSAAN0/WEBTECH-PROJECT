<?php
session_start();  // Ensure session is started at the beginning
require_once('../model/database.php');  // Including the database functions

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Retrieve email and password from form and trim whitespace
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);  

    // Check if email and password are not empty
    if (empty($email) || empty($password)) {
        echo "Email and Password cannot be empty!";
    } else {
        // Call authenticateUser function to validate credentials and get user role
        $role = authenticateUser($email, $password);

        // If role is returned (meaning authentication passed)
        if ($role) {
            $_SESSION['email'] = $email;  // Store the email in session
            $_SESSION['role'] = $role;    // Store the role in session

            // Redirect to the corresponding dashboard based on the user's role
            if ($role == "Consumer") {
                header('location: ../view/consumerDashboard.html');
                exit();
            } elseif ($role == "Farmer") {
                header('location: ../view/farmerDashboard.html');
                exit();
            }
        } else {
            // If authentication failed, display error message
            echo "Invalid Email or Password!";
        }
    }
} else {
    // If the form wasn't submitted properly, redirect back to login
    header('location: ../view/login.html');
    exit();
}
?>
