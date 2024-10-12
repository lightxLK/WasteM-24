<?php
require_once 'config.php';

function registerUser($username, $email, $password) {
    global $conn;

    // Sanitize input
    $username = mysqli_real_escape_string($conn, trim($username));
    $email = mysqli_real_escape_string($conn, trim($email));
    $password = mysqli_real_escape_string($conn, $password);

    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        return "Please fill in all fields.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email address.";
    }

    // Check if username or email already exists
    $checkUser = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $checkUser->bind_param("ss", $username, $email);
    $checkUser->execute();
    $checkUser->store_result();
    if ($checkUser->num_rows > 0) {
        return "Username or email already exists.";
    }
    $checkUser->close();

    // Hash password and insert user data into database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        $stmt->close();
        return "Registration successful!";
    } else {
        return "Error registering user: " . $stmt->error;
    }
}

function loginUser($username, $password) {
    global $conn;

    $username = mysqli_real_escape_string($conn, trim($username));

    if (empty($username) || empty($password)) {
        return ["success" => false, "message" => "Please fill in all fields."];
    }

    $checkUser = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $checkUser->bind_param("s", $username);
    $checkUser->execute();
    $checkUser->store_result();

    if ($checkUser->num_rows === 0) {
        return ["success" => false, "message" => "Invalid username or password."];
    }

    $checkUser->bind_result($userId, $storedPassword);
    $checkUser->fetch();
    $checkUser->close();

    if (password_verify($password, $storedPassword)) {
        $_SESSION['user_id'] = $userId;
        return ["success" => true, "message" => "Login successful!"];
    } else {
        return ["success" => false, "message" => "Invalid username or password."];
    }
}

function logoutUser() {
    session_destroy();
    return "Logged out successfully!";
}

function submitDataEntry($date, $foodWaste, $recycledWater, $solidWaste, $wetWaste) {
    global $conn;

    $date = mysqli_real_escape_string($conn, $date);
    $foodWaste = floatval($foodWaste);
    $recycledWater = floatval($recycledWater);
    $solidWaste = floatval($solidWaste);
    $wetWaste = floatval($wetWaste);

    if (empty($date) || $foodWaste < 0 || $recycledWater < 0 || $solidWaste < 0 || $wetWaste < 0) {
        return "Invalid input. All fields must be filled and numeric values should be non-negative.";
    }

    $stmt = $conn->prepare("INSERT INTO waste_data (user_id, date, food_waste, recycled_water, solid_waste, wet_waste) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdddd", $_SESSION['user_id'], $date, $foodWaste, $recycledWater, $solidWaste, $wetWaste);

    if ($stmt->execute()) {
        $stmt->close();
        return "Data submitted successfully!";
    } else {
        return "Error submitting data: " . $stmt->error;
    }
}

function getReports() {
    global $conn;
    
    $stmt = $conn->prepare("SELECT date, food_waste, recycled_water, solid_waste, wet_waste FROM waste_data WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $reports = [];
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
    $stmt->close();

    return $reports;
}