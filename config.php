<?php
$servername = "127.0.0.1:3307"; // Server name or IP address
$username = "root"; // Database username
$password = ""; // Database password (keep it empty if there's none)
$dbname = "waste_management"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection and handle errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Output error message and terminate script
} 
// Optional: Uncomment the line below if you want to see a confirmation message
// else {
//     echo "Connected successfully"; // Confirm successful connection
// }
?>