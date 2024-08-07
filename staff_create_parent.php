<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login_staff.php");
    exit();
}

// Connect to MySQL database using mysqli
$con = mysqli_connect("localhost", "root", "", "fyp_db");

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_parent'])) {
    // Parent details from form
    $parent_first_name = $_POST['first_name'];
    $parent_last_name = $_POST['last_name'];
    $phone_num = $_POST['phone_num'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $parent_address = $_POST['address'];

    // Insert parent data into Parents table
    $insert_parent_query = "INSERT INTO parents (first_name, last_name, phone_num, email, password, address) 
                            VALUES ('$parent_first_name', '$parent_last_name', '$phone_num', '$email', '$password', '$parent_address')";
    
    if (mysqli_query($con, $insert_parent_query)) {
        $parent_id = mysqli_insert_id($con); // Get the ID of the inserted parent

        // Student details from form
        $student_names = $_POST['student_name'];
        $ic_nums = $_POST['ic_num'];
        $class_ids = $_POST['class']; // Assuming this is the selected class ID from the form

        // Loop through each student entry
        foreach ($student_names as $index => $student_name) {
            $ic_num = $ic_nums[$index];
            $selected_class_id = $class_ids[$index];

            // Fetch class information based on selected class_id
            $select_class_query = "SELECT tc_id FROM class WHERE class_id = '$selected_class_id'";
            $result = mysqli_query($con, $select_class_query);
            if ($result && mysqli_num_rows($result) > 0) {
                $class_info = mysqli_fetch_assoc($result);
                $tc_id = $class_info['tc_id'];

                // Insert student data into Students table
                $insert_student_query = "INSERT INTO students (student_name, ic_num, class_id, parent_id, tc_id) 
                                         VALUES ('$student_name', '$ic_num', '$selected_class_id', '$parent_id', '$tc_id')";
                if (mysqli_query($con, $insert_student_query)) {
                    $student_id = mysqli_insert_id($con); // Get the ID of the inserted student

                    // Insert into school_classes table to link student and class
                    $insert_student_class_query = "INSERT INTO students_classes (student_id, class_id) 
                                                  VALUES ('$student_id', '$selected_class_id')";
                    if (!mysqli_query($con, $insert_student_class_query)) {
                        die("Error inserting into school_classes: " . mysqli_error($con));
                    } else {
                        echo "Student $student_name registered successfully in school_classes.<br>";
                    }
                } else {
                    die("Error inserting student data: " . mysqli_error($con));
                }
            } else {
                die("Error: Invalid class selected or class not found.");
            }
        }
        // echo "Parent and students registration successful!";
        // Redirect to success page or continue with other actions
        header("Location: staff_records_student.php");
        exit();
    } else {
        die("Error inserting parent data: " . mysqli_error($con));
    }

    // Close connection
    mysqli_close($con);
}
?>
