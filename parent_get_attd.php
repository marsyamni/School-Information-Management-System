<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login_parent.php");
    exit();
}

// Connect to the database
$con = mysqli_connect("localhost", "root", "", "fyp_db");

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the logged-in user's email from the session
$user_email = $_SESSION['email'];

// Fetch parent ID and first name from the database using email
$sql = "SELECT parent_id, first_name FROM parents WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($parent_id, $first_name);
$stmt->fetch();
$stmt->close();

// Query to fetch all students associated with the parent
$sql_students = "SELECT s.student_id, s.student_name, c.class_name 
                 FROM students s 
                 JOIN class c ON s.class_id = c.class_id 
                 WHERE s.parent_id = ?";
$stmt_students = $con->prepare($sql_students);
$stmt_students->bind_param("i", $parent_id);
$stmt_students->execute();
$result_students = $stmt_students->get_result();

$students_data = [];
while ($row_student = $result_students->fetch_assoc()) {
    $student_id = $row_student['student_id'];
    $students_data[$student_id] = [
        'student_name' => $row_student['student_name'],
        'class_name' => $row_student['class_name'],
        'attendance' => [],
        'present_count' => 0,
        'absent_count' => 0
    ];

    // Query to fetch attendance records for each student for the current month
    $sql_attendance = "SELECT status, date_recorded 
                       FROM attendance 
                       WHERE student_id = ? AND DATE_FORMAT(date_recorded, '%Y-%m') = ?";
    $stmt_attendance = $con->prepare($sql_attendance);
    $stmt_attendance->bind_param("is", $student_id, $current_month);
    $stmt_attendance->execute();
    $result_attendance = $stmt_attendance->get_result();

    while ($row_attendance = $result_attendance->fetch_assoc()) {
        $students_data[$student_id]['attendance'][] = $row_attendance;
        if ($row_attendance['status'] == 'Present') {
            $students_data[$student_id]['present_count']++;
        } else {
            $students_data[$student_id]['absent_count']++;
        }
    }

    $stmt_attendance->close();
}

$stmt_students->close();
mysqli_close($con);
?>
