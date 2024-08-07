<?php
// Start session (if not already started)
session_start();

// Check if teacher is logged in
if (!isset($_SESSION['tc_id']) || !isset($_SESSION['email'])) {
    // Redirect to login page
    header("Location: login_teacher.php");
    exit;
}
// Retrieve tc_id from session
$tc_id = $_SESSION['tc_id'];

// Connect to database
$con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Connection failed: " . mysqli_connect_error());

// Function to fetch class_id based on tc_id
function getClassId($con, $tc_id) {
    $sql = "SELECT class_id FROM class WHERE tc_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt === false) {
        die("Error preparing statement: " . mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, "i", $tc_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $class_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $class_id;
}

// Example of inserting data into the academic table with dynamic tc_id and class_id
if (isset($_POST['Create_Acad'])) {
    // Retrieve data from HTML form
    $acad_title = trim($_POST["acad_title"]); // Sanitize user input (optional)
    $subject = trim($_POST["subject"]); // Sanitize user input (optional)
    $acad_content = trim($_POST["acad_content"]); // Sanitize user input (optional)
    $date_created = date('Y-m-d'); // Format as YYYY-MM-DD

    // Get class_id dynamically based on tc_id
    $class_id = getClassId($con, $tc_id);

    // Check if class_id is valid
    if (!$class_id) {
        echo "Error: No class found for the logged-in teacher.";
        exit;
    }

    // Prepared statement to insert data into academic table
    $sql = "INSERT INTO academic (acad_title, subject, acad_content, date_created, tc_id, class_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt === false) {
        die("Error preparing statement: " . mysqli_error($con));
    }

    // Bind parameters and execute statement
    mysqli_stmt_bind_param($stmt, "ssssii", $acad_title, $subject, $acad_content, $date_created, $tc_id, $class_id);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: tc_acad_view.php");
    } else {
        // Handle duplicate entry or other errors
        if (mysqli_errno($con) == 1062) {
            echo "Error: Duplicate entry or unique constraint violation.";
        } else {
            echo "Error creating academic update: " . mysqli_stmt_error($stmt);
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($con);
?>
