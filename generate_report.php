<?php
session_start();
header('Content-Type: application/json');

// Database connection
require 'config.php'; // Include the database configuration file
require 'functions.php'; // Include the functions file if needed

// Create connection
$conn = new mysqli($servername, $username, '', $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Get and decode the input data
$data = json_decode(file_get_contents('php://input'), true);
$reports = [];

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

// Prepare query based on request type
if ($data['type'] === 'date_range') {
    $fromDate = $conn->real_escape_string($data['from_date']);
    $toDate = $conn->real_escape_string($data['to_date']);
    
    $query = "SELECT * FROM waste_data WHERE user_id = ? AND date BETWEEN ? AND ? ORDER BY date ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $_SESSION['user_id'], $fromDate, $toDate);

} elseif ($data['type'] === 'month') {
    $month = $conn->real_escape_string($data['month']);
    
    $query = "SELECT * FROM waste_data WHERE user_id = ? AND MONTH(date) = ? ORDER BY date ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $_SESSION['user_id'], $month);
    
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit();
}

// Execute query and fetch results
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
    // Calculate totals
    $totalFoodWaste = array_sum(array_column($reports, 'food_waste'));
    $totalRecycledWater = array_sum(array_column($reports, 'recycled_water'));
    $totalSolidWaste = array_sum(array_column($reports, 'solid_waste'));
    $totalWetWaste = array_sum(array_column($reports, 'wet_waste'));

    echo json_encode([
        "success" => true,
        "reports" => $reports,
        "total_food_waste" => $totalFoodWaste,
        "total_recycled_water" => $totalRecycledWater,
        "total_solid_waste" => $totalSolidWaste,
        "total_wet_waste" => $totalWetWaste,
    ]);
} else {
    echo json_encode(["success" => false, "message" => "No data found."]);
}

// Close the connection
$stmt->close();
$conn->close();
?>
