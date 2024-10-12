<?php

session_start(); // Start the session

require 'config.php';
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
  $date = $_POST['date'];
  $foodWaste = $_POST['food_waste'];
  $recycledWater = $_POST['recycled_water'];
  $solidWaste = $_POST['solid_waste'];
  $wetWaste = $_POST['wet_waste'];

  $result = submitDataEntry($date, $foodWaste, $recycledWater, $solidWaste, $wetWaste);

  if ($result === "Data submitted successfully!") {
    echo json_encode(array('success' => true, 'message' => $result));
  } else {
    echo json_encode(array('success' => false, 'message' => $result));
  }
} else {
  if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
  } else {
    echo json_encode(array('success' => false, 'message' => "You must be logged in to access this page."));
  }
}