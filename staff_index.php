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

// Get the number of students
$sql = "SELECT COUNT(*) FROM students";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result, MYSQLI_NUM);
$student_count = $row[0];

// Get the number of teachers
$sql = "SELECT COUNT(*) FROM teachers";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result, MYSQLI_NUM);
$teacher_count = $row[0];

// Get the number of staffs
$sql = "SELECT COUNT(*) FROM parents";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result, MYSQLI_NUM);
$parent_count = $row[0];

// Close connection
mysqli_close($con);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CommWave</title>
    <!-- External Stylesheets -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="staff_style_old.css">
    <!-- Inline Styles -->
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

    <!-- Sidebar Section -->
    <section id="sidebar">
        <!-- Brand Logo -->
        <a href="#" class="brand">
            <i class='bx bx-hive'></i>
            <span class="text">CommWave</span>
        </a>
        <!-- Sidebar Menu -->
        <ul class="side-menu top">
            <li class="active">
                <a href="#">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
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
        <!-- Logout Section -->
        <ul class="side-menu">
            <li>
                <a href="#" class="logout" onclick="showLogoutModal()">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
    </section>
    <!-- End Sidebar Section -->

    <!-- Content Section -->
    <section id="content">
        <!-- Navbar -->
        <nav>
            <i class='bx bx-menu'></i>
            <a href="#" class="nav-link"></a>
            <form action="#"></form>
            <!-- Profile Dropdown -->
            <div class="profile" id="profileDropdown">
                <i class='bx bxs-user-circle' style="margin-right: 10px;"></i>
                <span id="userName"><?php echo htmlspecialchars($first_name); ?></span>
                <i class='bx bx-chevron-down'></i>
                <!-- Dropdown Menu -->
                <div class="dropdown-menu" id="dropdownMenu">
                    <div class="profile-info">
                        <i class='bx bxs-user-circle'></i>
                        <span><?php echo htmlspecialchars($first_name); ?></span>
                    </div>
                    <a href="staff_profile.php" class="dropdown-item">Profile</a>

                </div>
            </div>
        </nav>
        <!-- End Navbar -->

        <!-- Main Content -->
        <main>
            <!-- Head Title Section -->
            <div class="head-title">
                <div class="left">
                    <?php if (!empty($first_name)): ?>
                        <h1>Welcome, <?php echo htmlspecialchars($first_name); ?>!</h1>
                    <?php else: ?>
                        <h1>Welcome!</h1>
                    <?php endif; ?>
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Home</a></li>
                    </ul>
                </div>
            </div>
            <!-- End Head Title Section -->

            <!-- Box Info Section -->
            <ul class="box-info">
                <li>
                    <i class='bx bxs-graduation'></i>
                    <span class="text">
                        <h3><?php echo $student_count; ?></h3>
                        <p>Students</p>
                    </span>
                </li>
                <li>  
                    <i class='bx bxs-face'></i>	
                    <span class="text">
                        <h3><?php echo $parent_count; ?></h3>
                        <p>Parents</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-book-reader'></i>
                    <span class="text">
                        <h3><?php echo $teacher_count; ?></h3>
                        <p>Teachers</p>
                    </span>
                </li>
                
            </ul>
            <!-- End Box Info Section -->

            <!-- Container Section -->
            <div class="container">
                <div class="announcement-section">
                    <ul class="section-info">
                        <!-- Announcements' Section -->
                        <li>
                            <a href="staff_view_notice.php" class="selection-link">
                                <div class="icon">
                                    <i class='bx bxs-megaphone'></i>
                                </div>
                                <div class="announcement-content">
                                    <h3>School Notices</h3>
                                    <p>Create and manage school notices here.</p>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="staff_user_index.php" class="selection-link">
                                <div class="icon">
                                    <i class='bx bxs-group'></i>
                                </div>
                                <div class="announcement-content">
                                    <h3>User Accounts</h3>
                                    <p>Create and manage user accounts here.</p>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- End Container Section -->
        </main>
        <!-- End Main Content -->
    </section>
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
