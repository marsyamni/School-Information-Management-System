<?php
session_start();

// Check if teacher is logged in
if (!isset($_SESSION['tc_id']) || !isset($_SESSION['email'])) {
    // Redirect to login page if not logged in
    header("Location: login_teacher.php");
    exit;
}

// Check if academic update ID is provided in URL
if (!isset($_GET['id'])) {
    // Redirect to the academic view page if ID is not provided
    header("Location: tc_acad_view.php");
    exit;
}

// Validate academic ID from URL
$academic_id = $_GET['id'];
if (!is_numeric($academic_id)) {
    // Redirect to academic view page if ID is not numeric
    header("Location: tc_acad_view.php");
    exit;
}

// Connect to the database
$con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Cannot connect to the server: " . mysqli_error($con));

// Retrieve form data
$acad_title = $_POST['acad_title'];
$subject = $_POST['subject'];
$acad_content = $_POST['acad_content'];

// Update academic information in the database
$sql = "UPDATE academic SET acad_title = ?, subject = ?, acad_content = ? WHERE acad_id = ? AND tc_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("sssii", $acad_title, $subject, $acad_content, $academic_id, $_SESSION['tc_id']);

if ($stmt->execute()) {
    // If update is successful, redirect to the academic view page
    header("Location: tc_acad_view.php");
    exit;
} else {
    // If update fails, handle the error (e.g., display an error message)
    echo "Error updating academic update: " . $stmt->error;
}

$stmt->close();
mysqli_close($con);
?>
