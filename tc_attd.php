<?php
session_start();

// Check if teacher is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page or handle unauthorized access
    header("Location: login_teacher.php");
    exit;
}

// Connect to the database
$conn = new mysqli("localhost", "root", "", "fyp_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get class_id from session
$class_id = isset($_SESSION['class_id']) ? $_SESSION['class_id'] : null;

// Check if a date is set, if not use current date
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Query to fetch attendance records for the selected date
$sql = "SELECT a.student_id, s.student_name, c.class_name, a.status
        FROM attendance a
        JOIN students s ON a.student_id = s.student_id
        JOIN class c ON a.class_id = c.class_id
        WHERE a.date_created = '$date' AND c.class_id = '$class_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Display attendance information
    echo "<table border='1'>
            <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Class</th>
                <th>Status</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        $status = $row['status'];
        echo "<tr>
                <td>{$row['student_id']}</td>
                <td>{$row['student_name']}</td>
                <td>{$row['class_name']}</td>
                <td>{$status}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No attendance records found for the selected date.";
}

$conn->close();
?>
