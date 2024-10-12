<?php
session_start();

require 'config.php';
require 'functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $result = loginUser($username, $password);

    if ($result['success']) {
        $_SESSION['username'] = $username;
        echo json_encode(['success' => true, 'message' => "Login successful!"]);
    } else {
        echo json_encode(['success' => false, 'message' => $result['message']]);
    }
} else {
    header("Location: login.html");
    exit();
}
?>