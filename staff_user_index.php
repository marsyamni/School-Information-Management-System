<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login_parent.php");
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

// Get the number of students
$sql = "SELECT COUNT(*) FROM students";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result, MYSQLI_NUM);
$parent_count = $row[0];

// Get the number of teachers
$sql = "SELECT COUNT(*) FROM teachers";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result, MYSQLI_NUM);
$teacher_count = $row[0];

// Get the number of staffs
$sql = "SELECT COUNT(*) FROM staffs";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result, MYSQLI_NUM);
$staff_count = $row[0];

// Close connection
mysqli_close($con);

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

	<title>IISSA CommWave</title>
</head>
<body>
	<script>
		//Logout Popup
		function showLogoutModal() {
			document.getElementById('logoutModal').style.display = 'block';
		}

		function hideLogoutModal() {
			document.getElementById('logoutModal').style.display = 'none';
		}

		document.addEventListener('DOMContentLoaded', function() {
			document.getElementById('confirmLogoutBtn').addEventListener('click', function(event) {
				event.preventDefault(); // Prevent default form submission
				document.getElementById('logoutForm').submit(); // Submit the form
			});
		});
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
				<a href="staff_create_index.php">
					<i class='bx bxs-group'></i>
					<span class="text">User Accounts</span>
				</a>
			</li>
		</ul>
		<ul class="side-menu">
			<li>
				<a href="#" class="logout" onclick="showLogoutModal()">
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

		<!-- MAIN -->
		<main>
			<div class="head-title">
				<div class="left">
					<!-- MAIN -->
					<ul class="breadcrumb">
						<li>
							<a class='active1' href="staff_index.php">Dashboard</a>
						</li>
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
							<a class="active" href="#">User Accounts</a>
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
			
			<div class="container">
				<div class="announcement-section">

					<header>
						<h1>Users' Registration</h1>
					</header>
					<ul class="section-info">

						<!-- Parents & Students' Section -->
						<li>
							<!-- Go to Parents & Students' page to register-->
							<a href="staff_cform_parent.php" class="selection-link">

							<div class="icon">
								<i class='bx bxs-graduation'></i>	
							</div>
							<div class="announcement-content">
								<h3>Parents & Students Registration</h3></a>
							</div>
						</li>

						<!-- Teachers' Section -->
						<li>
							<!-- Go to Teachers' page to register-->
							<a href="staff_cform_tc.php" class="selection-link">

							<div class="icon">
								<i class='bx bxs-book-reader'></i>	
							</div>
							<div class="announcement-content">
								<h3>Teachers Registration</h3></a>
							</div>
						</li>	
						
						<!-- Staffs' Section -->
						<li>
							<!-- Go to Staffs' page to register-->
							<a href="staff_cform_staff.php" class="selection-link">

							<div class="icon">
								<i class='bx bx-task-x'></i>	
							</div>
							<div class="announcement-content">
								<h3>Staff Registration</h3></a>
							</div>
						</li>
					</ul>	
					<br>
					<br>
					<header>
						<h1>Users' Information</h1>
					</header>
					<ul class="section-info">
						<!-- Parents & Students' Section -->
						<li>
							<!-- Go to Parents & Students' page to view records-->
							<a href="staff_records_parent.php" class="selection-link">

							<div class="icon">
								<i class='bx bxs-face'></i>	
							</div>
							<div class="announcement-content">
								<h3>Parents' Records</h3></a>
							</div>
						</li>

						<!-- Teachers' Section -->
						<li>
							<!-- Go to Teachers' page to view records-->
							<a href="staff_records_student.php" class="selection-link">

							<div class="icon">
								<i class='bx bxs-graduation'></i>	
							</div>
							<div class="announcement-content">
								<h3>Students' Records</h3></a>
							</div>
						</li>	
						
						<!-- Staffs' Section -->
						<li>
							<!-- Go to Staffs' page to view records-->
							<a href="staff_records_tc.php" class="selection-link">

							<div class="icon">
								<i class='bx bxs-book-reader'></i>	
							</div>
							<div class="announcement-content">
								<h3>Teachers' Records</h3></a>
							</div>
						</li>

						<li>
							<!-- Go to Staffs' page to view records-->
							<a href="staff_records_admin.php" class="selection-link">

							<div class="icon">
								<i class='bx bx-task-x'></i>	
							</div>
							<div class="announcement-content">
								<h3>Staffs' Records</h3></a>
							</div>
						</li>
					</ul>	
				</div>
			</div>
		
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->
	<form id="logoutForm" action="logout_users.php" method="post">
		<div id="logoutModal" class="modal">
		<div class="modal-content">
			<span class="close" onclick="hideLogoutModal()">&times;</span>
			<p>Are you sure you want to logout?</p>
			<button type="submit" id="confirmLogoutBtn">Logout</button>
		</div>
	</div>
	</form>

	<script src="script.js"></script>
</body>
</html>