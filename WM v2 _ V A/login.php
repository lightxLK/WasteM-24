<?php
session_start(); // Start the session

require 'config.php';
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $result = loginUser($username, $password);

  if ($result === "Login successful!") {
    header("Location: index.html");
    exit();
  } else {
    echo json_encode(array('success' => false, 'message' => $result));
  }
} else {
  header("Location: login.html");
  exit();
}