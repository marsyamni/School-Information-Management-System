<?php
session_start();

// Check if teacher is logged in
if (!isset($_SESSION['tc_id']) || !isset($_SESSION['email'])) {
    // Redirect to login page if not logged in
    header("Location: login_teacher.php");
    exit;
}

// Connect to the database
$con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Cannot connect to the server: " . mysqli_error($con));

// Get the logged-in user's email from the session
$user_email = $_SESSION['email'];

// Fetch tc_id from teachers table based on email
$sql_tc_id = "SELECT tc_id FROM teachers WHERE email = ?";
$stmt_tc_id = $con->prepare($sql_tc_id);
$stmt_tc_id->bind_param("s", $user_email);
$stmt_tc_id->execute();
$stmt_tc_id->bind_result($tc_id);
$stmt_tc_id->fetch();
$stmt_tc_id->close();

// Fetch first name from the database using email
$sql = "SELECT first_name FROM teachers WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($first_name);
$stmt->fetch();
$stmt->close();
// Check if tc_id was retrieved successfully
if (!$tc_id) {
    // Handle error, possibly redirect or show message
    die("Error: Teacher ID not found.");
}

$subject_filter = "";
$time_filter = "";

// Handle form submission for filtering
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['reset'])) {
        // Reset filters
        $subject_filter = "";
        $time_filter = "";
    } else {
        $subject_filter = isset($_POST["subject"]) ? $_POST["subject"] : "";
        $time_filter = isset($_POST["time"]) ? $_POST["time"] : "";
    }

    // Construct the initial SQL query
    $sql = "SELECT * FROM academic WHERE tc_id = ?";
    $params = [$tc_id];
    $types = "i";

    // Apply subject filter if selected
    if (!empty($subject_filter)) {
        $sql .= " AND subject = ?";
        $params[] = $subject_filter;
        $types .= "s";
    }

    // Apply time filter if selected
    if ($time_filter != "") {
        if ($time_filter == "Latest") {
            $sql .= " ORDER BY date_created DESC LIMIT 1";
        } elseif ($time_filter == "This Week") {
            $sql .= " AND YEARWEEK(DATE(date_created), 1) = YEARWEEK(CURDATE(), 1)";
        } elseif ($time_filter == "Last Week") {
            $sql .= " AND DATE(date_created) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
        }
    
    } else {
        $sql .= " ORDER BY date_created DESC";
    }

    $stmt = $con->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Retrieve all academic updates for the logged-in teacher by default
    $sql = "SELECT * FROM academic WHERE tc_id = ? ORDER BY date_created DESC";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $tc_id);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Close database connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IISSA CommWave</title>
    <!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="tc_style.css">
    
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
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bx-hive'></i>
            <span class="text">CommWave</span>
        </a>
        <ul class="side-menu top">
            <li>
                <a href="tc_index.php">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="tc_view_notice.php">
                    <i class='bx bxs-megaphone'></i>
                    <span class="text">School Notices</span>
                </a>
            </li>
            <li class="active">
                <a href="tc_acad_view.php">
                    <i class='bx bxs-graduation'></i>
                    <span class="text">Academic</span>
                </a>
            </li>
            <li>
                <a href="tc_attd_view.php">
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
                    <a href="tc_profile.php" class="dropdown-item">Profile</a>
                </div>
            </div>
        </nav>
        <!-- NAVBAR -->

        <main>
            <div class="head-title">
                <div class="left">
                    <ul class="breadcrumb">
                        <li>
                            <a lass="active1" href="tc_index.php">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Academic</a>
                        </li>
                    </ul>
                </div>
            </div>
            <br>
            <div class="right">
                <a href="#" class="btn-create-filter" onclick="toggleFilter()">
                    <i class='bx bx-filter'></i>
                    <span class="text">Filter Update</span>
                </a>
            </div>
            <div class="filter-form">
                <div id="filterDropdown" class="filter-content" style="display: none;">
                    <form method="POST" action="">
                        <label for="subject">Subject:</label>
                        <select name="subject" id="subject">
                            <option value="">All</option>
							<option value="Bahasa Melayu" <?php if ($subject_filter == "Bahasa Melayu") echo "selected"; ?>>Bahasa Melayu</option>
							<option value="English" <?php if ($subject_filter == "English") echo "selected"; ?>>English</option>
							<option value="Mathematics" <?php if ($subject_filter == "Mathematics") echo "selected"; ?>>Mathematics</option>
                            <option value="Add Math" <?php if ($subject_filter == "Add Math") echo "selected"; ?>>Add Math</option>
							<option value="Biology" <?php if ($subject_filter == "Biology") echo "selected"; ?>>Biology</option>
							<option value="Chemistry" <?php if ($subject_filter == "Chemistry") echo "selected"; ?>>Chemistry</option>
							<option value="Physics" <?php if ($subject_filter == "Physics") echo "selected"; ?>>Physics</option>
							<option value="Arabic" <?php if ($subject_filter == "Arabic") echo "selected"; ?>>Arabic</option>
							<option value="Pendidikan Islam" <?php if ($subject_filter == "Pendidikan Islam") echo "selected"; ?>>Pendidikan Islam</option>
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
            <div class="right">
                <a href="tc_cAcad.php" class="btn-create-notice">
                    <i class='bx bxs-message-square-add'></i>
                    <span class="text">Create Academic Update</span>
                </a>
            </div>
            <div class="container">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='notice-container'>";
                        echo "<h2>" . $row['acad_title'] . "</h2>";
                        echo "<h3>Subject: " . $row['subject'] . "</h3>";
                        echo "<p>" . $row['acad_content'] . "</p><br>";
                        echo "<h5>Date Created: " . $row['date_created'] . "</h5>";
                        echo "<div class='btn-group'>";
                        echo "<button class='edit-btn' onclick=\"location.href='tc_acad.php?id=" . $row['acad_id'] . "'\">Edit</button>";
                        echo "<button class='delete-btn' data-id='" . $row['acad_id'] . "'>Delete</button>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No academic updates found.</p>";
                }
                ?>
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
    <script src="path/to/jquery.js"></script>
    <script src="path/to/bootstrap.js"></script>
    <script>
        // Filter toggle function
        function toggleFilter() {
            var filterDropdown = document.getElementById("filterDropdown");
            if (filterDropdown.style.display === "block") {
                filterDropdown.style.display = "none";
            } else {
                filterDropdown.style.display = "block";
            }
        }

        // Delete Confirmation Popup
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const noticeId = this.getAttribute('data-id');
                    Confirm.open({
                        title: 'Confirm Academic Update Deletion',
                        message: 'Are you sure you want to delete this academic update?',
                        onok: () => {
                            location.href = 'tc_delete_acad.php?id=' + noticeId;
                        }
                    });
                });
            });
        });

        const Confirm = {
            open(options) {
                options = Object.assign({}, {
                    title: '',
                    message: '',
                    okText: 'Delete',
                    cancelText: 'Cancel',
                    onok: function() {},
                    oncancel: function() {}
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
                                <button class="confirm__button confirm__button--cancel">${options.cancelText}</button>
                            </div>
                        </div>
                    </div>
                `;

                const template = document.createElement('template');
                template.innerHTML = html;

                const confirmEl = template.content.querySelector('.confirm');
                const btnClose = template.content.querySelector('.confirm__close');
                const btnOk = template.content.querySelector('.confirm__button--ok');
                const btnCancel = template.content.querySelector('.confirm__button--cancel');

                confirmEl.addEventListener('click', e => {
                    if (e.target === confirmEl) {
                        options.oncancel();
                        this._close(confirmEl);
                    }
                });

                btnOk.addEventListener('click', () => {
                    options.onok();
                    this._close(confirmEl);
                });

                [btnCancel, btnClose].forEach(el => {
                    el.addEventListener('click', () => {
                        options.oncancel();
                        this._close(confirmEl);
                    });
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
</body>
</html>
