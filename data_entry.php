<?php
session_start(); // Start the session

require 'config.php'; // Include the database configuration
require 'functions.php'; // Include functions for data handling

// Check if the request method is POST and the user is logged in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    // Retrieve data from POST request
    $date = $_POST['date'];
    $foodWaste = $_POST['food_waste'];
    $recycledWater = $_POST['recycled_water'];
    $solidWaste = $_POST['solid_waste'];
    $wetWaste = $_POST['wet_waste'];

    // Call the function to submit data entry
    $result = submitDataEntry($date, $foodWaste, $recycledWater, $solidWaste, $wetWaste);

    // Return JSON response
    echo json_encode(['success' => $result === "Data submitted successfully!", 'message' => $result]);
} else {
    // If the user is not logged in, redirect to login page
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.html");
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => "You must be logged in to access this page."]);
    }
}
?>
