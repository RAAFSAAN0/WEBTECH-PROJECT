<?php
session_start();  
require_once('../model/database.php');  

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);  

    if (empty($email) || empty($password)) {
        echo "Email and Password cannot be empty!";
    } else {
        $role = authenticateUser($email, $password);

       
        if ($role) {
            $_SESSION['email'] = $email;  
            $_SESSION['role'] = $role;    

            if ($role == "Consumer") {
                header('location: ../view/consumerDashboard.html');
                exit();
            } elseif ($role == "Farmer") {
                header('location: ../view/farmers_dashboard.php');
                exit();
            }
        } else {
            echo "Invalid Email or Password!";
        }
    }
} else {
    header('location: ../view/login.html');
    exit();
}
?>
