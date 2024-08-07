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

// Fetch parent ID and first name from the database using email
$sql = "SELECT parent_id, first_name FROM parents WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($parent_id, $first_name);
$stmt->fetch();
$stmt->close();

// Fetch all students associated with the parent
$sql_students = "SELECT student_id, student_name FROM students WHERE parent_id = ?";
$stmt_students = $con->prepare($sql_students);
$stmt_students->bind_param("i", $parent_id);
$stmt_students->execute();
$result_students = $stmt_students->get_result();

$students = [];
while ($row_student = $result_students->fetch_assoc()) {
    $students[] = $row_student;
}

$stmt_students->close();

// Get the selected student ID
$selected_student_id = isset($_GET['student_id']) ? $_GET['student_id'] : $students[0]['student_id'];

// Fetch class_id from the database using student_id
$sql_class = "SELECT class_id FROM students WHERE student_id = ?";
$stmt_class = $con->prepare($sql_class);
$stmt_class->bind_param("i", $selected_student_id);
$stmt_class->execute();
$stmt_class->bind_result($class_id);
$stmt_class->fetch();
$stmt_class->close();

// Check if class_id is valid
if (!$class_id) {
    echo "Error: No class found for the selected student.";
    exit();
}

// Retrieve academic information for the selected student's class
$sql_acad = "SELECT * FROM academic WHERE class_id = ?";
$stmt_acad = $con->prepare($sql_acad);
$stmt_acad->bind_param("i", $class_id);
$stmt_acad->execute();
$result_acad = $stmt_acad->get_result();

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
    <style>
        /* Modal styles */
		.modal {
			display: none;
			position: fixed;
			z-index: 1;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			
			overflow: auto;
			background-color: rgb(0, 0, 0);
			background-color: rgba(0, 0, 0, 0.4);
			padding-top: 60px;
			box-sizing: border-box;
			border-color: black;
		}

		.modal-content {
			font-family: 'Poppins', sans-serif;
			background-color: #fefefe;
			margin: 5% auto;
			padding: 20px;
			border: 1px solid #888;
			width: 80%;
			max-width: 300px;
			text-align: center;
			border-radius: 10px;
			border: 3px solid #888; /* Increased border width */
		}

		.close {
			color: #aaa;
			float: right;
			font-size: 28px;
			font-weight: bold;
		}

		.close:hover,
		.close:focus {
			color: black;
			text-decoration: none;
			cursor: pointer;
		}

		button {
			margin: 10px;
			padding: 10px 20px;
			font-size: 16px;
			cursor: pointer;
			border: none;
			border-radius: 5px;
		}

		button:hover {
			background-color: #ddd;
		}
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
        function toggleFilter() {
            var filterDropdown = document.getElementById("filterDropdown");
            if (filterDropdown.style.display === "block") {
                filterDropdown.style.display = "none";
            } else {
                filterDropdown.style.display = "block";
            }
        }
		document.addEventListener('DOMContentLoaded', function() {
			// Add event listener to logout link
			document.querySelector('.logout').addEventListener('click', function(event) {
				event.preventDefault();
				Confirm.open({
					title: 'Logout Confirmation',
					message: 'Are you sure you want to logout?',
					okText: 'Logout',
					onok: function() {
						// Perform logout action here, e.g., redirect to logout page
						window.location.href = 'home.html';
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
                  <i class='bx bxs-dashboard' ></i>
                  <span class="text">Dashboard</span>
                </a>
              </li>
              
            <li>
                <a href="parent_view_notice.php">
                    <i class='bx bxs-megaphone'></i>
                    <span class="text">School Notices</span>
                </a>
            </li>
            <li class="active">
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
            <a href="logout_users.php" class="logout">  
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
                        <li><i class='bx bx-chevron-right' ></i></li>
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
					<span class="text">Filter Academic Update</span>
				</a>
			</div>
    
			<div class="filter-form">
				<div id="filterDropdown" class="filter-content">
					<form method="post" action="">
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
						<button type="button" class="close-btn" onclick="toggleFilter()">Close</button>
					</form>
				</div>
			</div>

            <!-- Student Selection Dropdown -->
            <div class="filter">
                <form action="parent_view_acad.php" method="GET">
                    <label for="student_id">Select Student:</label>
                    <select name="student_id" id="student_id" onchange="this.form.submit()">
                        <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['student_id']; ?>" <?php echo ($student['student_id'] == $selected_student_id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($student['student_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <div class="container">
                <?php
                // Process and display retrieved academic information
                if ($result_acad->num_rows > 0) {
                    while ($row = $result_acad->fetch_assoc()) {
                        echo "<div class='notice-container'>";
                        echo "<h2>" . htmlspecialchars($row['acad_title']) . "</h2>";
                        echo "<h3>Subject: " . htmlspecialchars($row['subject']) . "</h3><br>";
                        echo "<p>" . htmlspecialchars($row['acad_content']) . "</p><br>";
                        echo "<p>Date Created: " . htmlspecialchars($row['date_created']) . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "No academic information found for the selected student.";
                }

                // Close connection
                mysqli_close($con);
                ?>
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
    <!-- CONTENT -->
    <script src="script.js" defer></script>
</body>
</html>
