<?php
// edit_notice.php

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

// Check if notice_id is set in the URL and retrieve the notice details
if (isset($_GET['id'])) {
    $notice_id = intval($_GET['id']);
    $sql = "SELECT * FROM notice WHERE notice_id = $notice_id";
    $result = mysqli_query($con, $sql);
    $notice = mysqli_fetch_assoc($result);

    if (!$notice) {
        die("Notice not found.");
    }
} else {
    die("No notice ID provided.");
}

// Check if the form has been submitted to update the notice
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_notice'])) {
    $notice_title = mysqli_real_escape_string($con, $_POST['notice_title']);
    $notice_content = mysqli_real_escape_string($con, $_POST['notice_content']);
    $notice_type = mysqli_real_escape_string($con, $_POST['notice_type']);
    
    $sql = "UPDATE notice SET notice_title = '$notice_title', notice_content = '$notice_content', notice_type = '$notice_type' WHERE notice_id = $notice_id";
    
    if (mysqli_query($con, $sql)) {
        echo "Notice updated successfully.";
        header("Location: staff_view_notice.php");
        exit();
    } else {
        echo "Error updating notice: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="staff_style.css">

	<title>CommWave</title>
	<style>
		/* General container styling */
		.container {
			max-width: 800px;
			margin: 0 auto;
			padding: 20px;
			background-color: #f4f4f4;
			border-radius: 10px;
			box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
		}

		/* Styling for each notice container */
		.notice-container {
			background-color: #ffffff;
			margin-bottom: 20px;
			padding: 15px;
			border-radius: 5px;
			box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
		}

		/* Styling for notice titles */
		.notice-container h2 {
			font-size: 24px;
			margin-bottom: 10px;
			color: #333;
		}

		/* Styling for notice dates */
		.notice-container h4 {
			font-size: 14px;
			margin-bottom: 10px;
			color: #999;
		}

		/* Styling for notice content */
		.notice-container p {
			font-size: 16px;
			line-height: 1.6;
			color: #555;
		}

		/* Styling for button group */
		.btn-group {
			margin-top: 15px;
			text-align: right;
		}

		/* Styling for buttons */
		.edit-btn, .delete-btn {
			padding: 10px 20px;
			font-size: 14px;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			margin-left: 5px;
		}

		/* Edit button styling */
		.edit-btn {
			background-color: #007bff;
			color: white;
		}

		.edit-btn:hover {
			background-color: #0056b3;
		}

		/* Delete button styling */
		.delete-btn {
			background-color: #dc3545;
			color: white;
		}

		.delete-btn:hover {
			background-color: #c82333;
		}

		/* Styling for form inputs */
		input[type="text"], textarea {
			width: 100%;
			padding: 10px;
			margin-bottom: 10px;
			border: 1px solid #ccc;
			border-radius: 4px;
			font-size: 16px;
		}

		/* Styling for form button */
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
				<a href="#">
					<i class='bx bxs-dashboard' ></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li class="active">
				<a href="staff_view_notice.php">
					<i class='bx bxs-megaphone'></i>
					<span class="text">School Notices</span>
				</a>
			</li>
			<li>
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
		<nav>
			<i class='bx bx-menu'></i>
			<a href="#" class="nav-link"></a>  

			<form action="#">
				
			</form>
			
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
                            <a href="staff_view_notice.php">School Notices</a>
                        </li>
						<li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Edit Notice</a>
                        </li>
                    </ul>
                </div>
            </div>
            <br>
            <div class="container">
				<form action="staff_edit_notice.php?id=<?php echo htmlspecialchars($notice_id); ?>" method="POST">
					<div>
						<label for="notice_title">Title:</label>
						<input type="text" id="notice_title" name="notice_title" value="<?php echo htmlspecialchars($notice['notice_title']); ?>" required>
					</div>
					<div>
						<label for="notice_content">Content:</label>
						<textarea id="notice_content" name="notice_content" required><?php echo htmlspecialchars($notice['notice_content']); ?></textarea>
					</div>
					<div>
						<label for="notice_type">Type:</label>
						<select id="notice_type" name="notice_type" required>
							<option value="Event" <?php if ($notice['notice_type'] == 'Event') echo 'selected'; ?>>Event</option>
							<option value="Announcement" <?php if ($notice['notice_type'] == 'Announcement') echo 'selected'; ?>>Announcement</option>
						</select>
					</div>
					<br>
					<div>
						<label for="notice_recipient">Recipient:</label>
						<select id="notice_recipient" name="notice_recipient" required>
							<option value="Parents" <?php if ($notice['notice_recipient'] == 'Parents') echo 'selected'; ?>>Parents</option>
							<option value="Teachers" <?php if ($notice['notice_recipient'] == 'Teachers') echo 'selected'; ?>>Teachers</option>
							<option value="All" <?php if ($notice['notice_recipient'] == 'All') echo 'selected'; ?>>All</option>
						</select>
					</div>
					<br>
					<div>
						<button type="submit" name="update_notice">Update Notice</button>
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
