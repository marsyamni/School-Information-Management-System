<?php
// staff_student_edit.php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login_staff.php");
    exit();
}

// Connect to database
$con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Cannot connect to the server" . mysqli_error($con));

// Get the logged-in user's email from the session
$user_email = $_SESSION['email'];

// Fetch first name from the database using email
$sql = "SELECT first_name FROM staffs WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($first_name);
$stmt->fetch();
$stmt->close();

// Initialize variables
$student = [];
$classes = [];
$teachers = [];
$parents = [];
$errors = [];

// Check if student_id is set in the URL and retrieve the student details
if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']);
    $sql_student = "SELECT * FROM students WHERE student_id = ?";
    $stmt_student = $con->prepare($sql_student);
    $stmt_student->bind_param("i", $student_id);
    $stmt_student->execute();
    $result_student = $stmt_student->get_result();

    if ($result_student->num_rows === 1) {
        $student = $result_student->fetch_assoc();
    } else {
        die("Student not found.");
    }

    $stmt_student->close();
} else {
    die("No student ID provided.");
}

// Fetch classes
$sql_classes = "SELECT class_id, class_name FROM class";
$result_classes = mysqli_query($con, $sql_classes);
while ($row = mysqli_fetch_assoc($result_classes)) {
    $classes[] = $row;
}

// Fetch teachers
$sql_teachers = "SELECT tc_id, first_name, last_name FROM teachers";
$result_teachers = mysqli_query($con, $sql_teachers);
while ($row = mysqli_fetch_assoc($result_teachers)) {
    $teachers[] = $row;
}

// Fetch parents
$sql_parents = "SELECT parent_id, first_name, last_name FROM parents";
$result_parents = mysqli_query($con, $sql_parents);
while ($row = mysqli_fetch_assoc($result_parents)) {
    $parents[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    // Sanitize input data
    $student_name = mysqli_real_escape_string($con, $_POST['student_name']);
    $ic_num = mysqli_real_escape_string($con, $_POST['ic_num']);
    $class_id = !empty($_POST['class_id']) ? mysqli_real_escape_string($con, $_POST['class_id']) : NULL;
    $tc_id = !empty($_POST['tc_id']) ? mysqli_real_escape_string($con, $_POST['tc_id']) : NULL;
    $parent_id = !empty($_POST['parent_id']) ? mysqli_real_escape_string($con, $_POST['parent_id']) : NULL;
    $parent_first_name = mysqli_real_escape_string($con, $_POST['parent_first_name']);
    $parent_last_name = mysqli_real_escape_string($con, $_POST['parent_last_name']);

    // Validate input data (optional, depending on your requirements)

    // Update parent's name only if it's changed
    if (!empty($parent_first_name) && !empty($parent_last_name)) {
        $sql_update_parent = "UPDATE parents SET first_name = ?, last_name = ? WHERE parent_id = ?";
        $stmt_update_parent = $con->prepare($sql_update_parent);
        $stmt_update_parent->bind_param("ssi", $parent_first_name, $parent_last_name, $parent_id);

        if ($stmt_update_parent->execute()) {
            echo "Parent's name updated successfully.<br>";
        } else {
            $errors[] = "Error updating parent's name: " . $stmt_update_parent->error;
        }

        $stmt_update_parent->close();
    }

    // Update student's information
    $sql_update_student = "UPDATE students SET student_name = ?, ic_num = ?, class_id = ?";

    // Only include tc_id in the update query if it's not empty
    if (!empty($tc_id)) {
        $sql_update_student .= ", tc_id = ?";
    }

    $sql_update_student .= " WHERE student_id = ?";
    $stmt_update_student = $con->prepare($sql_update_student);

    // Bind parameters based on whether tc_id is included in the query
    if (!empty($tc_id)) {
        $stmt_update_student->bind_param("ssiii", $student_name, $ic_num, $class_id, $tc_id, $student_id);
    } else {
        $stmt_update_student->bind_param("ssii", $student_name, $ic_num, $class_id, $student_id);
    }

    if ($stmt_update_student->execute()) {
        echo "Student updated successfully.";
        header("Location: staff_records_student.php");
        exit();
    } else {
        $errors[] = "Error updating student: " . $stmt_update_student->error;
    }

    $stmt_update_parent->close();
    $stmt_update_student->close();
}

// Close connection (optional as PHP will close it at script termination)
mysqli_close($con);
?>
