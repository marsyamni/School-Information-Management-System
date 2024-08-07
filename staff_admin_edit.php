<?php
// edit_user.php

// Connect to database
$con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Cannot connect to the server" . mysqli_error($con));

// Check if user_id is set in the URL and retrieve the user details
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']); // Convert to integer for safety
    $sql = "SELECT * FROM staffs WHERE staff_id = ?";
    
    // Prepare statement
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    
    // Get result
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        die("User not found.");
    }

    // Close statement
    mysqli_stmt_close($stmt);
} else {
    die("No user ID provided.");
}

// Check if the form has been submitted to update the user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $first_name = mysqli_real_escape_string($con, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($con, $_POST['last_name']);
    $phone_num = mysqli_real_escape_string($con, $_POST['phone_num']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    
    $sql = "UPDATE staffs SET first_name = ?, last_name = ?, phone_num = ?, email = ?, password = ? WHERE staff_id = ?";
    
    // Prepare statement
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $first_name, $last_name, $phone_num, $email, $password, $user_id);
    
    // Execute statement
    if (mysqli_stmt_execute($stmt)) {
        echo "User updated successfully.";
        header("Location: staff_records_admin.php");
        exit();
    } else {
        echo "Error updating user: " . mysqli_stmt_error($stmt);
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Close connection (optional as PHP will close it at script termination)
mysqli_close($con);
?>