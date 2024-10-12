<?php
session_start(); // Start the session
session_destroy(); // Destroy the session
header('Content-Type: application/json'); // Set header for JSON response

// Return a success response
echo json_encode(['success' => true]);
?>
