<?php
session_start(); // Start the session
header('Content-Type: application/json'); // Set header for JSON response

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    // User is logged in, return their username
    echo json_encode(['loggedIn' => true, 'username' => $_SESSION['username']]);
} else {
    // User is not logged in
    echo json_encode(['loggedIn' => false]);
}
?>
