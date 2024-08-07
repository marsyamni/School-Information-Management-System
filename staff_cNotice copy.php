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
		die("Connection failed: ". mysqli_connect_error());
	}
	
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
			<li class="active">
				<a href="#">
					<i class='bx bxs-megaphone'></i>
					<span class="text">School Notices</span>
				</a>
			</li>
			<li>
				<a href="staff_create_index.php">
					<i class='bx bxs-group'></i>
					<span class="text">User Accounts</span>
				</a>
			</li>
		</ul>
		<ul class="side-menu">
			<li>
				<a href="#">
					<i class='bx bxs-cog' ></i>
					<span class="text">Settings</span>
				</a>
			</li>
			<li>
				<a href="#" class="logout">
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
				<span id="userName"><?php echo htmlspecialchars($first_name); ?></span>
				<i class='bx bxs-user-circle'></i>
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

		<!-- MAIN -->
		<main>
			<div class="head-title">
				<div class="left">
					<!-- MAIN 
					<h1>Welcome, </h1>-->
					<ul class="breadcrumb">
						<li>
							<a href="#">Dashboard</a>
						</li>
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
							<a class="active" href="#">School Notices</a>
						</li>
					</ul>
				</div>
				<!--
				<a href="#" class="btn-download">
					<i class='bx bxs-cloud-download' ></i>
					<span class="text">Download PDF</span>
				</a>
				-->
			</div>
			<br>
			<div class="form-container">
				<form action="staff_create_notice.php" method="post">
					<fieldset>
						<legend>Create Notice</legend>
						<div class="form-group">
							<label for="title">Title:</label>
							<input type="text" id="title" name="notice_title" required>
						</div>
						<br>
						<div class="form-group">
							<label for="type">Notice Type:</label>
							<select name="notice_type" id="notice_type">
								<option value="" disabled selected>Select Notice Type</option>
								<option value="Event">Event</option>
								<option value="Announcement">Announcement</option>
							</select>
						</div>
						<div class="form-group">
							<label for="recipient">Recipient:</label>
							<select name="notice_recipient" id="notice_recipient">
								<option value="" disabled selected>Select Recipient</option>
								<option value="Parents">Parents</option>
								<option value="Teachers">Teachers</option>
								<option value="All">All</option>
							</select>
						</div>
						<br>
						<div class="form-group">
							<label for="notice-content">Content:</label>
						</div>
						<div class="form-group">
							<textarea id="notice-content" name="notice_content" rows="4" cols="80"></textarea>
						</div>
						<br>
						<button type="submit" name="Create_Notice">Create Notice</button>
					</fieldset>
				</form>
			</div>
			  
			<!-- 
			
			 -->
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->
	

	<script src="script.js"></script>
</body>
</html>