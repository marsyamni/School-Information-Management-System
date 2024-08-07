<?php
// edit_teacher.php

// Connect to database
$con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Cannot connect to the server" . mysqli_error($con));

// Check if the form has been submitted to update the teacher
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_teacher'])) {
    // Retrieve form data
    $teacher_id = intval($_POST['tc_id']); // Convert to integer for safety
    $first_name = mysqli_real_escape_string($con, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($con, $_POST['last_name']);
    $phone_num = mysqli_real_escape_string($con, $_POST['phone_num']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $ic_num = mysqli_real_escape_string($con, $_POST['ic_num']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $class_teacher = mysqli_real_escape_string($con, $_POST['class_teacher']);
    $class_id = ($class_teacher === 'Yes') ? mysqli_real_escape_string($con, $_POST['class_id']) : NULL;

    // Prepare SQL update statement
    $sql = "UPDATE teachers SET first_name = ?, last_name = ?, phone_num = ?, email = ?, password = ?, subject = ?, ic_num = ?, address = ?, class_id = ? WHERE tc_id = ?";
    
    // Prepare the statement
    if ($stmt = mysqli_prepare($con, $sql)) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "ssssssssii", $first_name, $last_name, $phone_num, $email, $password, $subject, $ic_num, $address, $class_id, $teacher_id);
        
        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to the staff records page after successful update
            header("Location: staff_records_tc.php");
            exit();
        } else {
            echo "Error updating teacher: " . mysqli_stmt_error($stmt);
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
