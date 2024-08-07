<?php
// edit_student.php

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

// Check if student_id is set in the URL and retrieve the student details
if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']);
    $sql = "
        SELECT students.*, parents.first_name AS parent_first_name, parents.last_name AS parent_last_name 
        FROM students 
        LEFT JOIN parents ON students.parent_id = parents.parent_id 
        WHERE students.student_id = ?
    ";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$student) {
        die("Student not found.");
    }
} else {
    die("No student ID provided.");
}

// Check if the form has been submitted to update the student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_student'])) {
    $student_name = mysqli_real_escape_string($con, $_POST['student_name']);
    $ic_num = mysqli_real_escape_string($con, $_POST['ic_num']);
    $class_id = !empty($_POST['class_id']) ? mysqli_real_escape_string($con, $_POST['class_id']) : NULL;
    $parent_id = !empty($_POST['parent_id']) ? mysqli_real_escape_string($con, $_POST['parent_id']) : NULL;

    // Get the tc_id for the selected class
    $sql = "SELECT tc_id FROM class WHERE class_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $stmt->bind_result($tc_id);
    $stmt->fetch();
    $stmt->close();

    // Update students table
    $sql = "UPDATE students SET student_name = ?, ic_num = ?, class_id = ?, tc_id = ?, parent_id = ? WHERE student_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssiiii", $student_name, $ic_num, $class_id, $tc_id, $parent_id, $student_id);
    
    if ($stmt->execute()) {
        // Update students_classes table
        $sql = "UPDATE students_classes SET class_id = ? WHERE student_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ii", $class_id, $student_id);
        $stmt->execute();
        
        echo "Student updated successfully.";
        header("Location: display_students.php");
        exit();
    } else {
        echo "Error updating student: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch classes
$sql_classes = "SELECT class_id, class_name FROM class";
$result_classes = mysqli_query($con, $sql_classes);
$classes = [];
while ($row = mysqli_fetch_assoc($result_classes)) {
    $classes[] = $row;
}

// Fetch parents for autocomplete
$sql_parents = "SELECT parent_id, first_name, last_name FROM parents";
$result_parents = mysqli_query($con, $sql_parents);
$parents = [];
while ($row = mysqli_fetch_assoc($result_parents)) {
    $parents[] = $row;
}

// Close connection (optional as PHP will close it at script termination)
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CommWave</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="staff_style.css">

    <!-- jQuery UI for autocomplete -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .form-container {
            background-color: #ffffff;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }

        label {
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"], input[type="email"], textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
    <script>
        function updateTcId(classId) {
            if (!classId) return;

            fetch(`staff_get_tcid.php?class_id=${classId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('tc_id').value = data.tc_id;
                });
        }

        $(function() {
            var parents = <?php echo json_encode(array_map(function($parent) {
                return [
                    'label' => $parent['first_name'] . ' ' . $parent['last_name'],
                    'value' => $parent['parent_id']
                ];
            }, $parents)); ?>;

            $("#parent_name").autocomplete({
                source: parents,
                select: function(event, ui) {
                    $("#parent_name").val(ui.item.label);  // Display the parent's name
                    $("#parent_id").val(ui.item.value);    // Store the parent's ID in the hidden field
                    return false;  // Prevent the default behavior of autocomplete which sets the input value to the selected item's value
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li>")
                    .append("<div>" + item.label + "</div>")
                    .appendTo(ul);
            };

            $("#parent_name").blur(function() {
                if (!$(this).val()) {
                    $("#parent_id").val('');
                }
            });
        });
    </script>
</head>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bx-hive'></i>
            <span class="text">IISSA CommWave</span>
        </a>
        <ul class="side-menu top">
            <li>
                <a href="staff_index.php">
                    <i class='bx bxs-dashboard' ></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="staff_view_notice.php">
                    <i class='bx bxs-megaphone'></i>
                    <span class="text">School Notices</span>
                </a>
            </li>
            <li class="active">
                <a href="staff_user_index.php">
                    <i class='bx bxs-group'></i>
                    <span class="text">User Accounts</span>
                </a>
            </li>
        </ul>
        <ul class="side-menu">
            <li>
                <a href="home.html" class="logout">  
                    <i class='bx bxs-log-out-circle' ></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
    </section>
    <!-- SIDEBAR -->

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav class="navbar">
            <i class='bx bx-menu' ></i>
            <form action="#"></form>
            <div class="profile" id="profileDropdown">
                <i class='bx bxs-user-circle' style="margin-right: 10px;"></i>
                <span id="userName"><?php echo htmlspecialchars($first_name); ?></span>
                <i class='bx bx-chevron-down'></i>

                <div class="dropdown-menu" id="dropdownMenu">
                    <div class="profile-info">
                        <i class='bx bxs-user-circle'></i>
                        <span><?php echo htmlspecialchars($first_name); ?></span>
                    </div>
                    <a href="parent_profile.php" class="dropdown-item">Profile</a>
                    <a href="#" class="dropdown-item">Change Password</a>
                </div>
            </div>
        </nav>
        <!-- NAVBAR -->

        <main>
            <div class="head-title">
                <div class="left">
                    <ul class="breadcrumb">
                        <li>
                            <a class='active1' href="staff_index.php">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class='active1' href="staff_user_index.php">User Accounts</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Edit Student Information</a>
                        </li>
                    </ul>
                </div>
            </div>
            <br>
            <div class="container">
            <form action="staff_student_edit.php?id=<?php echo htmlspecialchars($student_id); ?>" method="POST">
                <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>">

                <div>
                    <label for="student_name">Student Name:</label>
                    <input type="text" id="student_name" name="student_name" value="<?php echo htmlspecialchars($student['student_name']); ?>" required>
                </div>
                <div>
                    <label for="ic_num">IC Number:</label>
                    <input type="text" id="ic_num" name="ic_num" value="<?php echo htmlspecialchars($student['ic_num']); ?>" required>
                </div>
                <div>
                    <label for="class_id">Class:</label>
                    <select name="class_id" id="class_id" onchange="updateTcId(this.value)">
                        <option value="" disabled>Select Class</option>
                        <?php
                        foreach ($classes as $class) {
                            $selected = ($student['class_id'] == $class['class_id']) ? 'selected' : '';
                            echo "<option value='{$class['class_id']}' $selected>{$class['class_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <input type="hidden" id="tc_id" name="tc_id" value="">
                <div>
                    <br>
                    <label for="parent_name">Current Parent Name: <?php echo htmlspecialchars($student['parent_first_name'] . ' ' . $student['parent_last_name']); ?></label>
                    <input type="text" id="parent_name" name="parent_name" value="" placeholder="Select and edit parent's name if needed" >
                    <input type="hidden" id="parent_id" name="parent_id" value="<?php echo htmlspecialchars($student['parent_id']); ?>">
                </div>
                <br>
                <div>
                    <button type="submit" name="update_student">Update Student</button>
                </div>
            </form>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script src="script.js" defer></script>
</body>
</html>
