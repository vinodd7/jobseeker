<?php
// ENABLE ERROR REPORTING (helps debug)
error_reporting(E_ALL);
ini_set("display_errors", 1);

// DATABASE CONNECTION
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "jobseeer"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// CHECK DB CONNECTION
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// RECEIVE FORM DATA
$fullName = $_POST['fullName'] ?? '';
$email = $_POST['email'] ?? '';
$userName = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

// CHECK PASSWORD MATCH
if ($password !== $confirmPassword) {
    echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
    exit();
}

// CHECK IF EMAIL OR USERNAME ALREADY EXISTS
$check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$check->bind_param("ss", $email, $userName);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('Email or Username already exists!'); window.history.back();</script>";
    exit();
}

// HASH PASSWORD
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// INSERT USER INTO DATABASE
$stmt = $conn->prepare("INSERT INTO users (full_name, email, username, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $fullName, $email, $userName, $hashedPassword);

if ($stmt->execute()) {
    echo "<script>alert('Registration Successful!'); window.location='dashboard.html';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$conn->close();
?>
