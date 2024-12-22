<?php
// Function to create a database connection
function getConnection() {
    $conn = mysqli_connect('127.0.0.1', 'root', '', 'agriculture');
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

// Function to check if the email is unique (not already present in the Consumer or Farmer table)
function isEmailUnique($email) {
    $conn = getConnection();
    
    // Check if the email exists in either the Consumer or Farmer tables
    $sql = "SELECT email FROM Farmer WHERE email = ? UNION SELECT email FROM Consumer WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    $stmt->close();
    $conn->close();

    return $result->num_rows === 0; // Email is unique if no rows are found
}

// Function to add a new Consumer user
function addConsumer($first_name, $last_name, $email, $mobile, $password) {
    $conn = getConnection();

    // Insert the password directly without hashing
    $sql = "INSERT INTO Consumer (first_name, last_name, email, mobile, password) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $mobile, $password);
    
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();

    return $result; // Return whether the insert was successful
}

// Function to add a new Farmer user
function addFarmer($first_name, $last_name, $email, $mobile, $password) {
    $conn = getConnection();

    // Insert the password directly without hashing
    $sql = "INSERT INTO Farmer (first_name, last_name, email, mobile, password) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $mobile, $password);
    
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();

    return $result;
}

// Function to authenticate user during login by checking in both the Consumer and Farmer tables
function authenticateUser($email, $password) {
    $conn = getConnection(); // Establish database connection

    // Query to get the password from the Consumer table
    $sql = "SELECT password FROM Consumer WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email); // Bind the email parameter
    $stmt->execute();
    $stmt->bind_result($storedPassword); // Bind the stored password result to a variable

    // If a result is found, compare the passwords
    if ($stmt->fetch()) {
        // Directly compare the entered password with the stored plain-text password
        if ($password === $storedPassword) {
            // Return the role (or a default value for this case)
            return 'Consumer';  // Defaulting to 'Consumer' as per your table structure
        }
    }

    // Query for Farmer table as well
    $sql2 = "SELECT password FROM Farmer WHERE email = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $email);
    $stmt2->execute();
    $stmt2->bind_result($storedPassword2);

    if ($stmt2->fetch()) {
        if ($password === $storedPassword2) {
            return 'Farmer';  // Return 'Farmer' if password matches in Farmer table
        }
    }

    // Close the statements and connection
    $stmt->close();
    $stmt2->close();
    $conn->close();

    return false; // Return false if authentication fails
}
?>
