<?php
session_start();

// Check if teacher is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page
    header("Location: login_teacher.php");
    exit;
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
$sql = "SELECT first_name FROM teachers WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($first_name);
$stmt->fetch();
$stmt->close();

// Close connection
mysqli_close($con);
?>

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