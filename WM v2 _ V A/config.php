<?php
$servername = "127.0.0.1:3307";
$username = "root";
$dbname = "waste_management";

// Create connection
$conn = new mysqli($servername, $username, '', $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully";
}