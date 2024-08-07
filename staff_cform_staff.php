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
							<a class='active1' href="staff_index.php">Dashboard</a>
						</li>
						<li><i class='bx bx-chevron-right'></i></li>
						<li>
							<a class='active1' href="staff_user_index.php">User Accounts</a>
						</li>
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
							<a class="active" href="#">Staff Registration</a>
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
				<form action="staff_create_admin.php" method="post">
					<fieldset>
						<legend>Staff Registration</legend>
						<div class="form-group">
							<label for="title">First Name:</label>
							<input type="text" placeholder="e.g Adam" id="first_name" name="first_name" required>
						</div>
						<br>
						<div class="form-group">
							<label for="title">Last Name:</label>
							<input type="text" placeholder="e.g Bin Abu" id="last_name" name="last_name" required>
						</div>
						<div class="form-group">
							<label for="title">Phone Number:</label>
							<input type="text" placeholder="01xxxxxxxxx" id="phone_num" name="phone_num" required>
						</div>
						<div class="form-group">
							<label for="title">Email:</label>
							<input type="text" id="email" name="email" required>
						</div>
						<div class="form-group">
							<label for="title">Password</label>
							<input type="text" id="password" name="password" required>
						</div>
						
						<button type="submit" name="register_parent">Register</button>
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