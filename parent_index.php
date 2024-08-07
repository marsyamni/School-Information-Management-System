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
    die("Connection failed: " . mysqli_connect_error());
}

// Get the logged-in user's email from the session
$user_email = $_SESSION['email'];

// Fetch first name from the database using email
$sql = "SELECT first_name FROM parents WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($first_name);
$stmt->fetch();
$stmt->close();

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
	<link rel="stylesheet" href="dev_style.css">

	<title>IISSA CommWave</title>
	<style>
		
	</style>
</head>
<body>

	<script>
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
			<span class="text">IISSA CommWave</span>
		</a>
		<ul class="side-menu top">
			<li class="active">
				<a href="#">
					<i class='bx bxs-dashboard'></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="parent_view_notice.php">
					<i class='bx bxs-megaphone'></i>
					<span class="text">School Notices</span>
				</a>
			</li>
			<li>
				<a href="parent_view_acad.php">
					<i class='bx bxs-graduation'></i>
					<span class="text">Academic</span>
				</a>
			</li>
			<li>
				<a href="parent_view_attd.php">
					<i class='bx bx-spreadsheet'></i>
					<span class="text">Attendance</span>
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
				</div>
			</div>
		</nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<div class="head-title">
				<div class="left">
					<?php if (!empty($first_name)): ?>
                        <h1>Welcome, <?php echo htmlspecialchars($first_name); ?>!</h1>
                    <?php else: ?>
                        <h1>Welcome!</h1>
                    <?php endif; ?>
					<ul class="breadcrumb">
						<li>
							<a href="#">Dashboard</a>
						</li>
						<li><i class='bx bx-chevron-right'></i></li>
						<li>
							<a class="active" href="#">Home</a>
						</li>
					</ul>
				</div>
			</div>
				
			<div class="container">
				<div class="announcement-section">
					<ul class="section-info">
						<!-- Announcements' Section -->
						<li>
							<!-- Go to Announcements page -->
							<a href="parent_view_notice.php" class="selection-link">
								<div class="icon">
									<i class='bx bxs-megaphone'></i>	
								</div>
								<div class="announcement-content">
									<h3>School Notices</h3>
									<p>Get the latest notices from the school here.</p>
								</div>
							</a>
						</li>
						<!-- Academic Section -->
						<li>
							<!-- Go to Academic page -->
							<a href="parent_view_acad.php" class="selection-link">
								<div class="icon">
									<i class='bx bxs-graduation'></i>	
								</div>
								<div class="announcement-content">
									<h3>Academic</h3>
									<p>Get the latest class information from the class teacher here.</p>
								</div>	
							</a>
						</li>
						<!-- Attendance Section -->
						<li>
						<a href="parent_view_attd.php" class="selection-link">
								<div class="icon">
									<i class='bx bx-spreadsheet'></i>	
								</div>
								<div class="announcement-content">
									<h3>Attendance</h3>
									<p>Check your child's attendance history here.</p>
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


</body>
</html>
