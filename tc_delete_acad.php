<?php
// Connect to database
$con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Cannot connect to the server" . mysqli_error($con));

// Check if notice_id is set
if (isset($_GET['id'])) {
    $acad_id = $_GET['id'];

    // SQL query to delete the notice
    $sql = "DELETE FROM academic WHERE acad_id = '$acad_id'";

    // Execute the query
    mysqli_query($con, $sql);
}

// Redirect to the main page
header("Location: tc_acad_view.php");
exit();
?>
