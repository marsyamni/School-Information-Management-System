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

$notice_type_filter = "";
$time_filter = "";

// Handle form submission for filtering
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['reset'])) {
        // Reset filters
        $notice_type_filter = "";
        $time_filter = "";
    } else {
        $notice_type_filter = isset($_POST["notice_type"]) ? $_POST["notice_type"] : "";
        $time_filter = isset($_POST["time"]) ? $_POST["time"] : "";
    }

    // Retrieve notices for parents with optional filters
    $sql = "SELECT * FROM notice WHERE (notice_recipient = 'parents' OR notice_recipient = 'all')";
    if ($notice_type_filter != "") {
        $sql .= " AND notice_type = '" . mysqli_real_escape_string($con, $notice_type_filter) . "'";
    }
    if ($time_filter != "") {
        if ($time_filter == "Latest") {
            $sql .= " ORDER BY date_created DESC LIMIT 1";
        } elseif ($time_filter == "This Week") {
            $sql .= " AND YEARWEEK(DATE(date_created), 1) = YEARWEEK(CURDATE(), 1)";
        } elseif ($time_filter == "Last Week") {
            $sql .= " AND DATE(date_created) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
        }
    }
    
    $result = mysqli_query($con, $sql);
} else {
    // Retrieve all notices for parents by default
    $sql = "SELECT * FROM notice WHERE (notice_recipient = 'parents' OR notice_recipient = 'all') ORDER BY date_created DESC";
    $result = mysqli_query($con, $sql);
}



// Close database connection
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

	<title>CommWave</title>
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

    function toggleFilter() {
        var filterDropdown = document.getElementById("filterDropdown");
        if (filterDropdown.style.display === "block") {
            filterDropdown.style.display = "none";
        } else {
            filterDropdown.style.display = "block";
        }
    }

    document.addEventListener('DOMContentLoaded', function() 
    {
        // Add event listener to logout link
        document.querySelector('.logout').addEventListener('click', function(event) {
            event.preventDefault();
            Confirm.open({
                title: 'Logout Confirmation',
                message: 'Are you sure you want to logout?',
                okText: 'Logout',
                onok: function() {
                    // Perform logout action here, e.g., redirect to logout page
                    location.href = 'home.html';
                }
            });
        });
    });

    const Confirm = {
        open(options) {
            options = Object.assign({}, {
                title: '',
                message: '',
                okText: 'OK',
                onok: function () {},
            }, options);
            
            const html = `
                <div class="confirm">
                    <div class="confirm__window">
                        <div class="confirm__titlebar">
                            <span class="confirm__title">${options.title}</span>
                            <button class="confirm__close">&times;</button>
                        </div>
                        <div class="confirm__content">${options.message}</div>
                        <div class="confirm__buttons">
                            <button class="confirm__button confirm__button--ok confirm__button--fill">${options.okText}</button>
                        </div>
                    </div>
                </div>
            `;

            const template = document.createElement('template');
            template.innerHTML = html;

            // Elements
            const confirmEl = template.content.querySelector('.confirm');
            const btnClose = template.content.querySelector('.confirm__close');
            const btnOk = template.content.querySelector('.confirm__button--ok');

            confirmEl.addEventListener('click', e => {
                if (e.target === confirmEl) {
                    this._close(confirmEl);
                }
            });

            btnOk.addEventListener('click', () => {
                options.onok();
                this._close(confirmEl);
            });

            btnClose.addEventListener('click', () => {
                this._close(confirmEl);
            });

            document.body.appendChild(template.content);
        },

        _close(confirmEl) {
            confirmEl.classList.add('confirm--close');

            confirmEl.addEventListener('animationend', () => {
                document.body.removeChild(confirmEl);
            });
        }
    };
</script>
<!-- SIDEBAR -->
<section id="sidebar">
    <a href="#" class="brand">
        <i class='bx bx-hive'></i>
        <span class="text">IISSA CommWave</span>
    </a>
    <ul class="side-menu top">
        <li>
            <a href="parent_index.php">
                <i class='bx bxs-dashboard'></i>
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
    <nav class="navbar">
        <i class='bx bx-menu'></i>
        <a href="#" class="nav-link"></a>
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

    <main>
        <div class="head-title">
            <div class="left">
                <ul class="breadcrumb">
                    <li>
                        <a class="active1" href="parent_index.php">Dashboard</a>
                    </li>
                    <li><i class='bx bx-chevron-right'></i></li>
                    <li>
                        <a class="active" href="#">School Notices</a>
                    </li>
                </ul>
            </div>
        </div>
        <br>
        <div class="right">
            <a href="#" class="btn-create-filter" onclick="toggleFilter()">
                <i class='bx bx-filter'></i>
                <span class="text">Filter Notice</span>
            </a>
        </div>
        <br>

        <div class="filter-form">
            <div id="filterDropdown" class="filter-content" style="display: none;">
                <form method="post" action="">
                    <label for="notice_type">Notice Type:</label>
                    <select name="notice_type" id="notice_type">
                        <option value="">All</option>
                        <option value="Event" <?php if ($notice_type_filter == "Event") echo "selected"; ?>>Event</option>
                        <option value="Announcement" <?php if ($notice_type_filter == "Announcement") echo "selected"; ?>>Announcement</option>
                        <!-- Add more notice types as needed -->
                    </select>

                    <label for="time">Time:</label>
                    <select name="time" id="time">
                        <option value="">All</option>
                        <option value="Latest" <?php if ($time_filter == "Latest") echo "selected"; ?>>Latest</option>
                        <option value="This Week" <?php if ($time_filter == "This Week") echo "selected"; ?>>This Week</option>
                        <option value="Last Week" <?php if ($time_filter == "Last Week") echo "selected"; ?>>Last Week</option>
                    </select>
					
                    <button type="submit">Apply Filter</button>
                    <button type="submit" name="reset" style="margin-top: 10px;">Reset</button>
                    <button type="button" class="close-btn" onclick="toggleFilter()">Close</button>
                </form>
            </div>
        </div>

        <!-- Display notices -->
        <div class="notice-container">
            <?php if (isset($result) && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="notice-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; border-radius: 10px;">
                        <h2><?php echo $row['notice_type']; ?>: <?php echo $row['notice_title']; ?></h2>
                        <h4>Date Created: <?php echo $row['date_created']; ?></h4>
                        <p><?php echo $row['notice_content']; ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No notices found for parents.</p>
            <?php endif; ?>
        </div>
    </main>
    <!-- MAIN -->
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
<script src="script.js" defer></script>
</body>
</html>
