<?php
require 'config.php';
require 'functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  $result = registerUser($username, $email, $password);

  if ($result === "Registration successful!") {
    echo json_encode(array('success' => true, 'message' => $result));
  } else {
    echo json_encode(array('success' => false, 'message' => $result));
  }
} else {
  header("Location: register.html");
  exit();
}