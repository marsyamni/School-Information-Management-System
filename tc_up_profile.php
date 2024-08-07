<?php
session_start();

// Connect to the database
$con = mysqli_connect("localhost", "root", "", "fyp_db");

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Update parent's profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone_num = $_POST['phone_num'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $user_email = $_SESSION['email']; // Use session email, as email is not editable

    // Prepare update query
    $sql_update = "UPDATE teachers SET phone_num = ?, password = ?, address = ? WHERE email = ?";
    $stmt_update = $con->prepare($sql_update);
    $stmt_update->bind_param("ssss", $phone_num, $password, $address, $user_email);

    // Execute update query
    if ($stmt_update->execute()) {
        // Close statement
        $stmt_update->close();

        // Close connection
        mysqli_close($con);

        // Redirect to parent_profile.php with success message
        header("Location: tc_profile.php?update=success");
        exit();
    } else {
        // Error handling
        echo "Error updating profile: " . $stmt_update->error;
    }
} else {
    // Invalid request handling
    echo "Invalid request!";
}
?>
