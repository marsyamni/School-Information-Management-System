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
    die("Connection failed: " . mysqli_connect_error());
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

$notice_type_filter = "";
$time_filter = "";
$notice_recipient_filter = "";

// Handle form submission for filtering
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['reset'])) {
        // Reset filters
        $notice_type_filter = "";
        $time_filter = "";
        $notice_recipient_filter = "";
    } else {
        $notice_type_filter = isset($_POST["notice_type"]) ? $_POST["notice_type"] : "";
        $time_filter = isset($_POST["time"]) ? $_POST["time"] : "";
        $notice_recipient_filter = isset($_POST["notice_recipient"]) ? $_POST["notice_recipient"] : "";
    }
}

// Construct the initial SQL query
$sql = "SELECT * FROM notice WHERE (notice_recipient = 'parents' OR notice_recipient = 'teachers' OR notice_recipient = 'all')";

// Append filters to the SQL query
$filters = [];

if (!empty($notice_type_filter)) {
    $filters[] = "notice_type = '" . mysqli_real_escape_string($con, $notice_type_filter) . "'";
}
if (!empty($time_filter)) {
    if ($time_filter == "This Week") {
        $filters[] = "YEARWEEK(DATE(date_created), 1) = YEARWEEK(CURDATE(), 1)";
    } elseif ($time_filter == "Last Week") {
        $filters[] = "DATE(date_created) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
    }
}
if (!empty($notice_recipient_filter)) {
    $filters[] = "notice_recipient = '" . mysqli_real_escape_string($con, $notice_recipient_filter) . "'";
}

// Apply filters if any
if (!empty($filters)) {
    $sql .= " AND " . implode(" AND ", $filters);
}

// Add the ORDER BY clause to ensure notices are ordered by date_created in descending order
$sql .= " ORDER BY date_created DESC";

// If the 'Latest' filter is applied, limit to one result
if ($time_filter == "Latest") {
    $sql .= " LIMIT 1";
}

// Execute the query
$result = mysqli_query($con, $sql);

// Check for errors in the query execution
if (!$result) {
    die("Error executing query: " . mysqli_error($con));
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
        // Filter
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
            // Add event listener for delete buttons
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const noticeId = this.getAttribute('data-id');
                    Confirm.open({
                        title: 'Confirm Notice Deletion',
                        message: 'Are you sure you want to delete this notice?',
                        onok: () => {
                            location.href = 'staff_delete_notice.php?id=' + noticeId;
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

                // Elements
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
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bx-hive'></i>
            <span class="text">IISSA CommWave</span>
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
                <a href="staff_user_index.php">
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
                        <li><i class='bx bx-chevron-right' ></i></li>
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

                        <label for="notice_recipient">Notice Recipient:</label>
                        <select name="notice_recipient" id="notice_recipient">
                            <option value="">All</option>
                            <option value="parents" <?php if ($notice_recipient_filter == "parents") echo "selected"; ?>>Parents</option>
                            <option value="teachers" <?php if ($notice_recipient_filter == "teachers") echo "selected"; ?>>Teachers</option>
                            <option value="all" <?php if ($notice_recipient_filter == "all") echo "selected"; ?>>All</option>
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

            <br>
            <div class="right">
                <a href="staff_cNotice.php" class="btn-create-notice">
                    <i class='bx bxs-message-square-add'></i>
                    <span class="text">Create Notice</span>
                </a>
            </div>
            
            <div class="container">
            <?php
                // Display retrieved notices
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<div class='notice-container' style='margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; border-radius: 10px;'>"; // Add opening div for each notice

                        // Notice details
                        echo "<h2>" . htmlspecialchars($row['notice_type']) . ": " . htmlspecialchars($row['notice_title']) . "</h2>";
                        echo "<h5>Date Created: " . htmlspecialchars($row['date_created']) . "</h5>";
                        echo "<h5>Recipient: " . htmlspecialchars($row['notice_recipient']) . "</h5>";
                        echo "<p>" . nl2br(htmlspecialchars($row['notice_content'])) . "</p><br>";

                        // Edit and Delete buttons
                        echo "<div class='btn-group'>";
                        echo "<button class='edit-btn' onclick=\"location.href='staff_notice.php?id=" . htmlspecialchars($row['notice_id']) . "'\">Edit</button>";
                        echo "<button class='delete-btn' data-id='" . htmlspecialchars($row['notice_id']) . "'>Delete</button>";
                        echo "</div>";

                        echo "</div>"; // Add closing div for each notice
                    }
                } else {
                    echo "No notices found.";
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
    <script src="script.js" defer></script>
</body>
</html>
