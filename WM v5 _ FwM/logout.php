<?php
session_start(); // Start the session

// Destroy the session to log the user out
session_destroy(); 

// Set the header for JSON response
header('Content-Type: application/json');

// Return a success response indicating the user has been logged out
echo json_encode(['success' => true]);
?>