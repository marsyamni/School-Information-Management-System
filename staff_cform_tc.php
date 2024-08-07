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
	<style>
		.hidden {
			display: none;
		}
	</style>
</head>
<body>
	<script>
		function toggleClassSelection(value) {
			const classGroup = document.getElementById('class_group');
			if (value === 'Yes') {
				classGroup.classList.remove('hidden');
			} else {
				classGroup.classList.add('hidden');
				document.getElementById('class').value = ''; // Clear the class selection
			}
		}
	</script>

	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="#" class="brand">
			<i class='bx bx-hive'></i>
			<span class="text">CommWave</span>
		</a>
		<ul class="side-menu top">
			<li>
				<a href="staff_index.php">
					<i class='bx bxs-dashboard'></i>
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
				<a href="#" class="logout">
					<i class='bx bxs-log-out-circle'></i>
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
					
					<ul class="breadcrumb">
						<li>
							<a class='active1' href="staff_index.php">Dashboard</a>
						</li>
						<li><i class='bx bx-chevron-right'></i></li>
						<li>
							<a class="active1" href="staff_user_index.php">User Accounts</a>
						</li>
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
							<a class="active" href="#">Teacher Registration</a>
						</li>
					</ul>
				</div>
			</div>
			<br>
			<div class="form-container">
				<form action="staff_create_tc.php" method="post">
					<fieldset>
						<legend>Teacher Registration</legend>
						<div class="form-group">
							<label for="first_name">First Name:</label>
							<input type="text" placeholder="e.g Adam" id="first_name" name="first_name" required>
						</div>
						
						<div class="form-group">
							<label for="last_name">Last Name:</label>
							<input type="text" placeholder="e.g Bin Abu" id="last_name" name="last_name" required>
						</div>
						<div class="form-group">
							<label for="phone_num">Phone Number:</label>
							<input type="text" placeholder="01xxxxxxxxx" id="phone_num" name="phone_num" required>
						</div>
						<div class="form-group">
							<label for="ic_num">IC No.:</label>
							<input type="text" placeholder="01xxxx-xx-xxxx" id="ic_num" name="ic_num" required>
						</div>
						<div class="form-group">
							<label for="address">Address:</label>
							<textarea id="address" name="address" required rows="4" cols="80"></textarea>
						</div>
						<div class="form-group">
							<label for="email">Email:</label>
							<input type="email" id="email" name="email" required>
						</div>
						<div class="form-group">
							<label for="password">Password:</label>
							<input id="password" name="password" required>
						</div>
						
						<div class="form-group">
							<label for="subject">Subject:</label>
							<select name="subject" id="subject" required>
								<option value="" disabled selected>Select Subject</option>
								<option value="Bahasa Melayu">Bahasa Melayu</option>
								<option value="English">English</option>
								<option value="Mathematics">Mathematics</option>
								<option value="Add Math">Add Math</option>
								<option value="Biology">Biology</option>
								<option value="Chemistry">Chemistry</option>
								<option value="Physics">Physics</option>
								<option value="Arabic">Arabic</option>
								<option value="Pendidikan Islam">Pendidikan Islam</option>
								<option value="Sejarah">Sejarah</option>
							</select>
						</div>
						
						<div class="form-group">
							<label for="class_teacher">Class Teacher:</label>
							<select name="class_teacher" id="class_teacher" required onchange="toggleClassSelection(this.value)">
								<option value="" disabled selected>Class Teacher?</option>
								<option value="Yes">Yes</option>
								<option value="No">No</option>
							</select>
						</div>
						<div class="form-group hidden" id="class_group">
							<label for="class">Class:</label>
							<select name="class" id="class">
								<option value="" disabled selected>Select Student's Class</option>
								<option value="1 Razi">1 Razi</option>
								<option value="2 Baihaqi">2 Baihaqi</option>
								<option value="3 Battuta">3 Battuta</option>
								<option value="4 Biruni">4 Biruni</option>
								<option value="5 Chemy">5 Chemy</option>
							</select>
						</div>
						
						<button type="submit" name="register_teacher">Register</button>
					</fieldset>
				</form>
			</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->

	<script src="script.js"></script>
</body>
</html>
