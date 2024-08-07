<?php
// Connect to database
$con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Cannot connect to the server" . mysqli_error($con));

// Check if notice_id is set
if (isset($_GET['id'])) {
    $notice_id = $_GET['id'];

    // SQL query to delete the notice
    $sql = "DELETE FROM notice WHERE notice_id = '$notice_id'";

    // Execute the query
    mysqli_query($con, $sql);
}

// Redirect to the main page
header("Location: staff_view_notice.php");
exit();
?>
