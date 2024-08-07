<?php
// edit_notice.php

// Connect to database
$con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Cannot connect to the server" . mysqli_error($con));

// Check if notice_id is set in the URL and retrieve the notice details
if (isset($_GET['id'])) {
    $notice_id = intval($_GET['id']); // Convert to integer for safety
    $sql = "SELECT * FROM notice WHERE notice_id = ?";
    
    // Prepare statement
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $notice_id);
    mysqli_stmt_execute($stmt);
    
    // Get result
    $result = mysqli_stmt_get_result($stmt);
    $notice = mysqli_fetch_assoc($result);

    if (!$notice) {
        die("Notice not found.");
    }

    // Close statement
    mysqli_stmt_close($stmt);
} else {
    die("No notice ID provided.");
}

// Check if the form has been submitted to update the notice
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_notice'])) {
    $notice_title = mysqli_real_escape_string($con, $_POST['notice_title']);
    $notice_content = mysqli_real_escape_string($con, $_POST['notice_content']);
    $notice_type = mysqli_real_escape_string($con, $_POST['notice_type']);
    $notice_recipient = mysqli_real_escape_string($con, $_POST['notice_recipient']);
    
    $sql = "UPDATE notice SET notice_title = ?, notice_content = ?, notice_type = ?, notice_recipient = ? WHERE notice_id = ?";
    
    // Prepare statement
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $notice_title, $notice_content, $notice_type, $notice_recipient, $notice_id);
    
    // Execute statement
    if (mysqli_stmt_execute($stmt)) {
        echo "Notice updated successfully.";
        header("Location: staff_view_notice.php");
        exit();
    } else {
        echo "Error updating notice: " . mysqli_stmt_error($stmt);
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Close connection (optional as PHP will close it at script termination)
mysqli_close($con);
?>