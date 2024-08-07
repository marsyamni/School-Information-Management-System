<?php
// staff_parent_edit.php

// Connect to database
$con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Cannot connect to the server" . mysqli_error($con));

// Check if the form has been submitted to update the parent
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_parent'])) {
    // Retrieve form data
    $parent_id = intval($_POST['parent_id']); // Convert to integer for safety
    $first_name = mysqli_real_escape_string($con, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($con, $_POST['last_name']);
    $phone_num = mysqli_real_escape_string($con, $_POST['phone_num']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $address = mysqli_real_escape_string($con, $_POST['address']);

    // Prepare SQL update statement
    $sql = "UPDATE parents SET first_name = ?, last_name = ?, phone_num = ?, email = ?, password = ?, address = ? WHERE parent_id = ?";
    
    // Prepare the statement
    if ($stmt = mysqli_prepare($con, $sql)) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "ssssssi", $first_name, $last_name, $phone_num, $email, $password, $address, $parent_id);
        
        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to the parent records page after successful update
            header("Location: staff_records_parent.php");
            exit();
        } else {
            echo "Error updating parent: " . mysqli_stmt_error($stmt);
        }
        
        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing the SQL statement: " . mysqli_error($con);
    }
} else {
    die("Invalid request method.");
}

// Close connection
mysqli_close($con);
?>
