<?php
// Connect to database
$con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Cannot connect to the server" . mysqli_error($con));

// Check if user_id is set
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']); // Convert to integer for safety

    // SQL query to delete the user
    $sql = "DELETE FROM teachers WHERE tc_id = ?";

    // Prepare statement
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);

    // Execute the query
    mysqli_stmt_execute($stmt);

    // Close statement
    mysqli_stmt_close($stmt);
}

// Redirect to the user records page
header("Location: staff_records_tc.php");
exit();
?>
