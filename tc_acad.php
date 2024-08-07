<?php
session_start();

// Check if teacher is logged in
if (!isset($_SESSION['tc_id']) || !isset($_SESSION['email'])) {
    // Redirect to login page
    header("Location: login_teacher.php");
    exit;
}

// Connect to database
$con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Cannot connect to the server: " . mysqli_error($con));

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

// Fetch tc_id from teachers table based on email
$sql_tc_id = "SELECT tc_id FROM teachers WHERE email = ?";
$stmt_tc_id = $con->prepare($sql_tc_id);
$stmt_tc_id->bind_param("s", $user_email);
$stmt_tc_id->execute();
$stmt_tc_id->bind_result($tc_id);
$stmt_tc_id->fetch();
$stmt_tc_id->close();

// Check if tc_id was retrieved successfully
if (!$tc_id) {
    // Handle error, possibly redirect or show message
    die("Error: Teacher ID not found.");
}

// Now, proceed to fetch academic updates using tc_id and academic_id
if (isset($_GET['id'])) {
    $academic_id = $_GET['id'];

    // Query to fetch academic update
    $sql = "SELECT * FROM academic WHERE acad_id = ? AND tc_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ii", $academic_id, $tc_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if academic update exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Fetch academic update details
        $acad_title = $row['acad_title'];
        $subject = $row['subject'];
        $acad_content = $row['acad_content'];
        $date_created = $row['date_created'];

        // Close statement and result set
        $stmt->close();
    } else {
        // Handle case where academic update is not found
        die("Error: Academic update not found.");
    }
} else {
    // Handle case where academic ID parameter is missing
    die("Error: Academic ID parameter missing.");
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
	<link rel="stylesheet" href="tc_style.css">

	<title>IISSA CommWave</title>
    <style>
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 24px;
            max-width: 800px;
            background-color: #f4f4f4;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
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
							location.href = 'tc_delete_acad.php?id=' + noticeId;
						}
					});
				});
			});
		});

		// Logout Confirmation
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
					onok: function() {},
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
			<span class="text">CommWave</span>
		</a>
		<ul class="side-menu top">
			<li>
				<a href="tc_index.php">
				  <i class='bx bxs-dashboard' ></i>
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
							<a class="active1" href="tc_index.php">Dashboard</a>
						</li>
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
							<a class="active" href="#">School Notices</a>
						</li>
					</ul>
				</div>
			</div>
			<br>
			<div class="container">
                <form action="tc_acad_edit.php?id=<?php echo htmlspecialchars($academic_id); ?>" method="POST">
                    <div>
                        <label for="acad_title">Academic Title:</label>
                        <input type="text" class="form-control" id="acad_title" name="acad_title" value="<?php echo htmlspecialchars($acad_title); ?>" required>
                    </div>
                    <div>
						<label for="subject">Subject:</label>
						<select name="subject" id="subject" class="form-control" required>
							<option value="" disabled selected>Select Subject</option>
							<option value="Bahasa Melayu" <?php echo ($subject == 'Bahasa Melayu') ? 'selected' : ''; ?>>Bahasa Melayu</option>
							<option value="English" <?php echo ($subject == 'English') ? 'selected' : ''; ?>>English</option>
							<option value="Mathematics" <?php echo ($subject == 'Mathematics') ? 'selected' : ''; ?>>Mathematics</option>
							<option value="Add Math" <?php echo ($subject == 'Add Math') ? 'selected' : ''; ?>>Add Math</option>
							<option value="Biology" <?php echo ($subject == 'Biology') ? 'selected' : ''; ?>>Biology</option>
							<option value="Chemistry" <?php echo ($subject == 'Chemistry') ? 'selected' : ''; ?>>Chemistry</option>
							<option value="Physics" <?php echo ($subject == 'Physics') ? 'selected' : ''; ?>>Physics</option>
							<option value="Arabic" <?php echo ($subject == 'Arabic') ? 'selected' : ''; ?>>Arabic</option>
							<option value="Pendidikan Islam" <?php echo ($subject == 'Pendidikan Islam') ? 'selected' : ''; ?>>Pendidikan Islam</option>
						</select>
					</div>
                    <div>
                        <label for="acad_content">Academic Content:</label>
                        <textarea class="form-control" id="acad_content" name="acad_content" rows="5" required><?php echo htmlspecialchars($acad_content); ?></textarea>
                    </div>
                    <button type="submit" name="update_acad">Update Academic Information</button>
                    <a href="tc_acad_view.php" class="cancel">Cancel</a>
                </form>
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
