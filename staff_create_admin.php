<?php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fyp_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO staffs (first_name, last_name, phone_num, email, password) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $first_name, $last_name, $phone_num, $email, $password);

// Set parameters and execute
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$phone_num = $_POST['phone_num'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

if ($stmt->execute()) {
    // Redirect to records page after successful registration
    header("Location: staff_records_admin.php");
    exit(); // Ensure no further code is executed
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();

?>