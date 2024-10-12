<?php
session_start(); // Start the session

require 'config.php'; // Make sure this file contains your DB connection settings
require 'functions.php'; // Make sure this file contains your loginUser function

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Call the loginUser function
    $result = loginUser($username, $password);

    if ($result === "Login successful!") {
        $_SESSION['username'] = $username; // Set session variable
        echo json_encode(['success' => true, 'message' => $result]);
    } else {
        echo json_encode(['success' => false, 'message' => $result]);
    }
} else {
    // Redirect to login.html if the request method is not POST
    header("Location: login.html");
    exit();
}
?>