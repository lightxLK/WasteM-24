<?php
function registerUser($username, $email, $password) {
  // Sanitize input to prevent SQL injection and other attacks
  $username = mysqli_real_escape_string($GLOBALS['conn'], $username);
  $email = mysqli_real_escape_string($GLOBALS['conn'], $email);
  $password = mysqli_real_escape_string($GLOBALS['conn'], $password);

  // Validate input
  if (empty($username) || empty($email) || empty($password)) {
    return "Please fill in all fields.";
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return "Invalid email address.";
  }

  // Check if username already exists
  global $conn;
  $checkUsername = $conn->prepare("SELECT id FROM users WHERE username = ?");
  $checkUsername->bind_param("s", $username);
  $checkUsername->execute();
  $checkUsername->store_result();
  if ($checkUsername->num_rows > 0) {
    return "Username already exists.";
  }
  $checkUsername->close();

  // Hash password for security
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // Insert user data into the database
  $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $username, $email, $hashedPassword);
  if ($stmt->execute()) {
    return "Registration successful!";
  } else {
    return "Error registering user: " . $stmt->error;
  }
  $stmt->close();
}

function loginUser($username, $password) {
  // Sanitize input
  $username = mysqli_real_escape_string($GLOBALS['conn'], $username);
  $password = mysqli_real_escape_string($GLOBALS['conn'], $password);

  // Validate input
  if (empty($username) || empty($password)) {
    return "Please fill in all fields.";
  }

  // Check if user exists
  global $conn;
  $checkUser = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
  $checkUser->bind_param("s", $username);
  $checkUser->execute();
  $checkUser->store_result();
  if ($checkUser->num_rows == 0) {
    return "Invalid username or password.";
  }

  // Retrieve user data
  $checkUser->bind_result($userId, $storedPassword);
  $checkUser->fetch();
  $checkUser->close();

  // Verify password
  if (password_verify($password, $storedPassword)) {
    // Login successful
    $_SESSION['user_id'] = $userId;
    return "Login successful!";
  } else {
    return "Invalid username or password.";
  }
}

function logoutUser() {
  // Destroy session
  session_destroy();
  return "Logged out successfully!";
}

function getDataEntryForm() {
  // Generate HTML code for the data entry form
  return '
    <h2>Data Entry</h2>
    <form id="data-entry-form">
      <label for="date">Date:</label>
      <input type="date" id="date" name="date" required><br>

      <label for="food_waste">Food Waste:</label>
      <input type="number" id="food_waste" name="food_waste" required><br>

      <label for="recycled_water">Recycled Water:</label>
      <input type="number" id="recycled_water" name="recycled_water" required><br>

      <label for="solid_waste">Solid Waste:</label>
      <input type="number" id="solid_waste" name="solid_waste" required><br>

      <label for="wet_waste">Wet Waste:</label>
      <input type="number" id="wet_waste" name="wet_waste" required><br>

      <button type="submit">Submit</button>
    </form>
  ';
}

function submitDataEntry($date, $foodWaste, $recycledWater, $solidWaste, $wetWaste) {
  // Sanitize input
  $date = mysqli_real_escape_string($GLOBALS['conn'], $date);
  $foodWaste = mysqli_real_escape_string($GLOBALS['conn'], $foodWaste);
  $recycledWater = mysqli_real_escape_string($GLOBALS['conn'], $recycledWater);
  $solidWaste = mysqli_real_escape_string($GLOBALS['conn'], $solidWaste);
  $wetWaste = mysqli_real_escape_string($GLOBALS['conn'], $wetWaste);

  // Validate input
  // ...

  // Insert data into the database
  global $conn;
  $stmt = $conn->prepare("INSERT INTO waste_data (user_id, date, food_waste, recycled_water, solid_waste, wet_waste) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("isdddd", $_SESSION['user_id'], $date, $foodWaste, $recycledWater, $solidWaste, $wetWaste);
  if ($stmt->execute()) {
    return "Data submitted successfully!";
  } else {
    return "Error submitting data: " . $stmt->error;
  }
  $stmt->close();
}

function getReports() {
  // Retrieve waste data for the current user
  global $conn;
  $stmt = $conn->prepare("SELECT date, food_waste, recycled_water, solid_waste, wet_waste FROM waste_data WHERE user_id = ?");
  $stmt->bind_param("i", $_SESSION['user_id']);
  $stmt->execute();
  $result = $stmt->get_result();

  // Generate HTML table for reports
  $html = '<h2>Reports</h2><table><tr><th>Date</th><th>Food Waste</th><th>Recycled Water</th><th>Solid Waste</th><th>Wet Waste</th></tr>';
  while ($row = $result->fetch_assoc()) {
    $html .= '<tr><td>' . $row['date'] . '</td><td>' . $row['food_waste'] . '</td><td>' . $row['recycled_water'] . '</td><td>' . $row['solid_waste'] . '</td><td>' . $row['wet_waste'] . '</td></tr>';
  }
  $html .= '</table>';
  return $html;
}