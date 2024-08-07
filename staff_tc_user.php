<?php
// edit_teacher.php

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

// Check if teacher_id is set in the URL and retrieve the teacher details
if (isset($_GET['id'])) {
    $teacher_id = intval($_GET['id']);
    $sql = "SELECT * FROM teachers WHERE tc_id = $teacher_id";
    $result = mysqli_query($con, $sql);
    $teacher = mysqli_fetch_assoc($result);

    if (!$teacher) {
        die("Teacher not found.");
    }
} else {
    die("No teacher ID provided.");
}

// Check if the form has been submitted to update the teacher
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_teacher'])) {
    $first_name = mysqli_real_escape_string($con, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($con, $_POST['last_name']);
    $phone_num = mysqli_real_escape_string($con, $_POST['phone_num']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $ic_num = mysqli_real_escape_string($con, $_POST['ic_num']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $class_id = !empty($_POST['class_id']) ? mysqli_real_escape_string($con, $_POST['class_id']) : NULL;
    
    $sql = "UPDATE teachers SET first_name = '$first_name', last_name = '$last_name', phone_num = '$phone_num', email = '$email', password = '$password', subject = '$subject', ic_num = '$ic_num', address = '$address', class_id = ? WHERE teacher_id = $teacher_id";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $class_id);
    if (mysqli_stmt_execute($stmt)) {
        echo "Teacher updated successfully.";
        header("Location: staff_records_tc.php");
        exit();
    } else {
        echo "Error updating teacher: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}

$teacher_id = htmlspecialchars($_GET['id']);
$sql = "SELECT * FROM teachers WHERE tc_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();

// Fetch class names
$sql_classes = "SELECT class_id, class_name FROM class";
$result_classes = mysqli_query($con, $sql_classes);

$classes = [];
if ($result_classes->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($result_classes)) {
        $classes[] = $row;
    }
} else {
    echo "No classes found";
}

if (empty($classes)) {
    echo "Classes array is empty";
}


// Close connection (optional as PHP will close it at script termination)
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Teacher</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="staff_style.css">

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

        input[type="text"], input[type="email"], input[type="password"], textarea, select {
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

        .hidden {
            display: none;
        }
    </style>

    <script>
        function toggleClassSelection(value) {
            const classGroup = document.getElementById('class_group');
            if (value === 'Yes') {
                classGroup.classList.remove('hidden');
            } else {
                classGroup.classList.add('hidden');
                document.getElementById('class_id').value = ''; // Clear the class selection
            }
        }
    </script>
</head>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bx-hive'></i>
            <span class="text">CommWave</span>
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
					<a href="staff_profile.php" class="dropdown-item">Profile</a>
				</div>
			</div>
        </nav>
        <!-- NAVBAR -->

        <main>
            <div class="head-title">
                <div class="left">
                    <ul class="breadcrumb">
                        <li>
                            <a href="staff_index.php">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a href="staff_user_index.php">Users' Accounts</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Edit Teacher Information</a>
                        </li>
                    </ul>
                </div>
            </div>
            <br>
            <div class="container">
            <form action="staff_tc_edit.php?id=<?php echo htmlspecialchars($teacher_id); ?>" method="POST">
                <input type="hidden" name="tc_id" value="<?php echo htmlspecialchars($teacher['tc_id']); ?>">

                <div>
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($teacher['first_name']); ?>" required>
                </div>
                <div>
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($teacher['last_name']); ?>" required>
                </div>
                <div>
                    <label for="phone_num">Phone Number:</label>
                    <input type="text" id="phone_num" name="phone_num" value="<?php echo htmlspecialchars($teacher['phone_num']); ?>" required>
                </div>
                <div>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
                </div>
                <div>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($teacher['password']); ?>" required>
                </div>
                <div>
                    <label for="subject">Subject:</label>
                    <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($teacher['subject']); ?>" required>
                </div>
                <div>
                    <label for="ic_num">IC Number:</label>
                    <input type="text" id="ic_num" name="ic_num" value="<?php echo htmlspecialchars($teacher['ic_num']); ?>" required>
                </div>
                <div>
                    <label for="address">Address:</label>
                    <textarea id="address" name="address" rows="4" cols="50" required><?php echo htmlspecialchars($teacher['address']); ?></textarea>
                </div>
                <div>
                    <label for="class_teacher">Class Teacher:</label>
                    <select name="class_teacher" id="class_teacher" required onchange="toggleClassSelection(this.value)">
                        <option value="No" <?php if ($teacher['class_id'] == NULL) echo 'selected'; ?>>No</option>
                        <option value="Yes" <?php if ($teacher['class_id'] != NULL) echo 'selected'; ?>>Yes</option>
                    </select>
                </div>
                <div class="form-group <?php if ($teacher['class_id'] == NULL) echo 'hidden'; ?>" id="class_group">
                    <label for="class_id">Class:</label>
                    <select name="class_id" id="class_id">
                        <option value="" disabled>Select Class</option>
                        <?php
                        foreach ($classes as $class) {
                            $selected = ($teacher['class_id'] == $class['class_id']) ? 'selected' : '';
                            echo "<option value='{$class['class_id']}' $selected>{$class['class_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <br>
                <div>
                    <button type="submit" name="update_teacher">Update Teacher</button>
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
