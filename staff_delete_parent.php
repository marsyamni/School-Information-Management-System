<?php
// Connect to database
$con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Cannot connect to the server" . mysqli_error($con));

// Check if parent_id is set
if (isset($_GET['id'])) {
    $parent_id = intval($_GET['id']); // Convert to integer for safety

    // Start a transaction for atomicity
    mysqli_autocommit($con, false);

    // SQL query to fetch student_ids associated with the parent
    $fetch_students_sql = "SELECT student_id FROM students WHERE parent_id = ?";
    $stmt_fetch_students = mysqli_prepare($con, $fetch_students_sql);
    mysqli_stmt_bind_param($stmt_fetch_students, "i", $parent_id);
    mysqli_stmt_execute($stmt_fetch_students);
    mysqli_stmt_bind_result($stmt_fetch_students, $student_id);

    $student_ids = [];
    while (mysqli_stmt_fetch($stmt_fetch_students)) {
        $student_ids[] = $student_id;
    }
    mysqli_stmt_close($stmt_fetch_students);

    // Prepare statements for deleting associated records
    $delete_attendance_sql = "DELETE FROM attendance WHERE student_id = ?";
    $delete_students_classes_sql = "DELETE FROM students_classes WHERE student_id = ?";
    $delete_students_sql = "DELETE FROM students WHERE student_id = ?";
    $delete_parent_sql = "DELETE FROM parents WHERE parent_id = ?";

    $stmt_attendance = mysqli_prepare($con, $delete_attendance_sql);
    $stmt_students_classes = mysqli_prepare($con, $delete_students_classes_sql);
    $stmt_students = mysqli_prepare($con, $delete_students_sql);
    $stmt_parent = mysqli_prepare($con, $delete_parent_sql);
    mysqli_stmt_bind_param($stmt_attendance, "i", $student_id);
    mysqli_stmt_bind_param($stmt_students_classes, "i", $student_id);
    mysqli_stmt_bind_param($stmt_students, "i", $student_id);
    mysqli_stmt_bind_param($stmt_parent, "i", $parent_id);

    // Delete all associated student records
    foreach ($student_ids as $student_id) {
        mysqli_stmt_execute($stmt_attendance);
        mysqli_stmt_execute($stmt_students_classes);
        mysqli_stmt_execute($stmt_students);
    }

    // Delete the parent record
    mysqli_stmt_execute($stmt_parent);

    // Check for errors and commit transaction
    if (mysqli_stmt_error($stmt_attendance) || mysqli_stmt_error($stmt_students_classes) || mysqli_stmt_error($stmt_students) || mysqli_stmt_error($stmt_parent)) {
        mysqli_rollback($con); // Rollback changes if any error
        echo "Error deleting parent and associated students.";
    } else {
        mysqli_commit($con); // Commit changes if no errors
        echo "Parent and associated students deleted successfully.";
    }

    // Close statements
    mysqli_stmt_close($stmt_attendance);
    mysqli_stmt_close($stmt_students_classes);
    mysqli_stmt_close($stmt_students);
    mysqli_stmt_close($stmt_parent);

    // Redirect to the user records page
    header("Location: staff_records_parent.php");
    exit();
} else {
    echo "Parent ID not set.";
}
?>
