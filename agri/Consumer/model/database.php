<?php
// Function to establish a connection to the database
function getConnection() {
    $conn = mysqli_connect('127.0.0.1', 'root', '', 'agriculture');
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

// Function to check if email is unique across Consumer and Farmer tables
function isEmailUnique($email) {
    $conn = getConnection();
    $sql = "SELECT email FROM Farmer WHERE email = ? UNION SELECT email FROM Consumer WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $conn->close();
    return $result->num_rows === 0;  // Return true if no result (email is unique)
}

// Function to add a new consumer to the database
function addConsumer($first_name, $last_name, $email, $mobile, $password, $country, $address, $dob, $role) {
    $conn = getConnection();
    $sql = "INSERT INTO consumer (first_name, last_name, email, mobile, password, country, address, dob, role, profile_image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $first_name, $last_name, $email, $mobile, $password, $country, $address, $dob, $role);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}


// Function to add a new farmer to the database
function addFarmer($first_name, $last_name, $email, $mobile, $password) {
    $conn = getConnection();
    $sql = "INSERT INTO Farmer (first_name, last_name, email, mobile, password) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $mobile, $password);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}

// Function to authenticate the user (consumer or farmer)
function authenticateUser($email, $password) {
    $conn = getConnection();

    // Check Consumer table for matching email and password
    $sql = "SELECT password FROM Consumer WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email); 
    $stmt->execute();
    $stmt->bind_result($storedPassword);
    if ($stmt->fetch()) {
        if ($password === $storedPassword) {
            return 'Consumer';  
        }
    }

    // Check Farmer table for matching email and password
    $sql2 = "SELECT password FROM Farmer WHERE email = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $email);
    $stmt2->execute();
    $stmt2->bind_result($storedPassword2);
    if ($stmt2->fetch()) {
        if ($password === $storedPassword2) {
            return 'Farmer';  
        }
    }

    $stmt->close();
    $stmt2->close();
    $conn->close();

    return false; // Return false if email/password not found
}

// Function to fetch all consumers' data
function fetchAllConsumers() {
    $conn = getConnection();
    $sql = "SELECT id, first_name, last_name, email, mobile FROM Consumer";
    $result = $conn->query($sql);

    $consumers = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $consumers[] = $row;
        }
    }
    $conn->close();
    return $consumers;
}

// Function to fetch a specific consumer's data by email (current logged-in user)
function fetchConsumerByEmail($email) {
    $conn = getConnection(); // Assuming getConnection() is your function to connect to the DB
    $sql = "SELECT * FROM Consumer WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $consumer = $result->fetch_assoc();

    // Check if the consumer data exists and return it
    if ($consumer) {
        $stmt->close();
        $conn->close();
        return $consumer;
    } else {
        // Handle case where no consumer is found with this email
        $stmt->close();
        $conn->close();
        return null;
    }
}


// Function to delete a consumer from the database
function deleteConsumer($id) {
    $conn = getConnection();
    $sql = "DELETE FROM Consumer WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}

// Function to update consumer details
function updateConsumer($id, $first_name, $last_name, $email, $mobile) {
    $conn = getConnection();
    $sql = "UPDATE Consumer SET first_name = ?, last_name = ?, email = ?, mobile = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $mobile, $id);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}
?>
