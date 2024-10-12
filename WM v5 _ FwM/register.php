<?php
require 'config.php';
require 'functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $username = trim(htmlspecialchars($_POST['username']));
    $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $password = $_POST['password'];

    // Basic input validation
    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit();
    }
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
        exit();
    }

    // Register user
    $result = registerUser($username, $email, $password);

    // Return result
    if ($result === "Registration successful!") {
        echo json_encode(['success' => true, 'message' => $result]);
    } else {
        echo json_encode(['success' => false, 'message' => $result]);
    }
} else {
    header("Location: register.html");
    exit();
}