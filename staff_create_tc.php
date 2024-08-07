<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login_staff.php");
    exit();
}

// Connect to the database
$con = mysqli_connect("localhost", "root", "", "fyp_db");

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $first_name = mysqli_real_escape_string($con, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($con, $_POST['last_name']);
    $phone_num = mysqli_real_escape_string($con, $_POST['phone_num']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $ic_num = mysqli_real_escape_string($con, $_POST['ic_num']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $class_teacher = mysqli_real_escape_string($con, $_POST['class_teacher']);
    $class_id = ($class_teacher === 'Yes') ? mysqli_real_escape_string($con, $_POST['class']) : NULL;

    // Insert into the database
    $sql = "INSERT INTO teachers (first_name, last_name, phone_num, email, password, subject, ic_num, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssssssss", $first_name, $last_name, $phone_num, $email, $password, $subject, $ic_num, $address);

    if ($stmt->execute()) {
        $teacher_id = $stmt->insert_id;

        // Update class table if teacher is class teacher
        if ($class_teacher === 'Yes') {
            $sql = "UPDATE classes SET tc_id = ? WHERE class_name = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("is", $teacher_id, $class_id);
            $stmt->execute();
        }

        // Redirect to staff_records_tc.php
        header("Location: staff_records_tc.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

mysqli_close($con);
?>
