<?php
// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Database connection
  $conn = mysqli_connect("localhost", "root", "", "fyp_db");

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Validate and sanitize inputs
  $date = mysqli_real_escape_string($conn, date('Y-m-d'));
  $attendance = $_POST['attendance'];

  // Iterate over each student's attendance and insert into the database
  foreach ($attendance as $student_id => $status) {
    $status = ucfirst(strtolower($status)); // Normalize the status

    // Validate student ID and status
    if (!is_numeric($student_id) || !in_array($status, ['Present', 'Absent'])) {
      echo "Invalid data received.";
      continue; // Skip to next iteration
    }

    // Check for existing record
    $sql_check = "SELECT 1 FROM attendance 
                  WHERE student_id = ? AND class_id = ? AND date_recorded = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("iis", $student_id, $_SESSION['class_id'], $date);
    $stmt_check->execute();
    $stmt_check->store_result();

    $exists = $stmt_check->num_rows > 0;
    $stmt_check->close();

    if ($exists) {
      // Update existing record
      $sql_update = "UPDATE attendance SET status = VALUES(status) 
                      WHERE student_id = ? AND class_id = ? AND date_created = ?";
      $stmt_update = $conn->prepare($sql_update);
      $stmt_update->bind_param("iis", $student_id, $_SESSION['class_id'], $date);
      $stmt_update->execute();
      $stmt_update->close();
    } else {
      // Insert new record
      $sql = "INSERT INTO attendance (student_id, class_id, date_recorded, status) 
              VALUES (?, (SELECT class_id FROM students WHERE student_id = ?), ?, ?)
              ON DUPLICATE KEY UPDATE status = VALUES(status)";

      $stmt = $conn->prepare($sql);
      $stmt->bind_param("iiss", $student_id, $student_id, $date, $status);
      $stmt->execute();
      $stmt->close();
    }
  }

  // Close connection
  $conn->close();

  // Redirect to attendance view page
  header("Location: tc_attd_view.php");
  exit();
}
