<?php
// get_tc_id.php

if (isset($_GET['class_id'])) {
    $class_id = intval($_GET['class_id']);

    // Connect to database
    $con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Cannot connect to the server" . mysqli_error($con));

    $sql = "SELECT tc_id FROM class WHERE class_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $stmt->bind_result($tc_id);
    $stmt->fetch();
    $stmt->close();

    mysqli_close($con);

    echo json_encode(['tc_id' => $tc_id]);
}
?>
