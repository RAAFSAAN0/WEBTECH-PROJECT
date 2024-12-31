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
// Function to add a new farmer to the database
// Function to add a new farmer to the database
function addFarmer($first_name, $last_name, $email, $mobile, $password, $country, $address, $dob) {
    $conn = getConnection();
    $sql = "INSERT INTO Farmer (first_name, last_name, email, mobile, password, country, address, dob) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $first_name, $last_name, $email, $mobile, $password, $country, $address, $dob);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}


function authenticateUser($email, $password) {
    $conn = getConnection();  // Ensure database connection is set up

    // Check if the connection is successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check the 'farmer' table for matching email and password
    $sql = "SELECT * FROM farmer WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);

    // Check if the prepare statement failed
    if (!$stmt) {
        die("SQL Error in farmer query: " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // If farmer is found
    if ($result->num_rows > 0) {
        return 'Farmer';  // Return Farmer role if credentials are valid
    }

    // Check the 'consumer' table for matching email and password
    $sql = "SELECT role FROM consumer WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Error in consumer query: " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a row is returned for consumer
    if ($result->num_rows > 0) {
        return 'Consumer';  // Return Consumer role if credentials are valid
    }

    // Check the 'student' table for matching email and password
    $sql = "SELECT role FROM student WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Error in student query: " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a row is returned for student
    if ($result->num_rows > 0) {
        return 'Student';  // Return Student role if credentials are valid
    }

    // If no matching user found in any table
    return false;
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
// Function to get user ID by email and role (Consumer or Farmer)
function getUserIdByEmail($email, $role) {
    $conn = getConnection();  // Ensure database connection is set up

    // Query based on the user's role
    if ($role == 'Consumer') {
        $sql = "SELECT id FROM consumer WHERE email = ?";
    } elseif ($role == 'Student') {
        $sql = "SELECT id FROM student WHERE email = ?";
    } elseif ($role == 'Farmer') {
        $sql = "SELECT id FROM farmer WHERE email = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        return $user['id'];
    }

    return false;  // If no user found
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
