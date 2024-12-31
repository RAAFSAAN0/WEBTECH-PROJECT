<?php
session_start();
require_once('../model/database.php');

// Check if the user is logged in
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Consumer') {
    header('Location: ../view/login.html');
    exit();
}

// Fetch the logged-in user's data
$consumerData = fetchConsumerByEmail($_SESSION['email']);
$first_name = $consumerData['first_name'];
$last_name = $consumerData['last_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consumer Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../asset/consumerDashboard.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1 class="header-title">Consumer Dashboard</h1>
            <!-- <p class="header-user">Logged in as: <?php echo $first_name . ' ' . $last_name; ?></p> -->
        </header>
        <nav class="nav">
            <ul class="nav-list">
                <li class="nav-item"><a class="nav-link" href="consumerDashboard.php"><i class='bx bx-home'></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="buy-products.html"><i class='bx bx-cart'></i> Buy Products</a></li>
                <li class="nav-item"><a class="nav-link" href="shopping-cart.html"><i class='bx bx-shopping-bag'></i> Shopping Cart</a></li>
                <li class="nav-item"><a class="nav-link" href="video_list.php"><i class='bx bx-play-circle'></i> Video Tutorials</a></li>
                <li class="nav-item"><a class="nav-link" href="purchase-history.html"><i class='bx bx-history'></i> Purchase History</a></li>
                <li class="nav-item"><a class="nav-link" href="../controller/consumer_view.php"><i class='bx bx-user'></i> View Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="forgot_password.html"><i class='bx bx-cog'></i> Settings</a></li>
                <li class="nav-item"><a class="nav-link" href="../controller/logout.php"><i class='bx bx-log-out'></i> Logout</a><li> 
            </ul>
        </nav>
        <main class="main">
            <section class="welcome-section">
            <img src="/agri/Consumer/uploads/images/agri.gif" alt="Agriculture GIF">

                <h2 class="welcome-title">Welcome, <?php echo $first_name; ?>!</h2>
                <p class="welcome-text">Select any option from the menu to get started.</p>
            </section>
        </main>
    </div>
</body>
</html>
