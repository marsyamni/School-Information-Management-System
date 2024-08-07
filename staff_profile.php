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
	<style>
		
		.head-title {
			margin-bottom: 20px;
		}

		.head-title .left {
			display: flex;
			flex-direction: column;
		}

		.breadcrumb {
			list-style: none;
			padding: 0;
			display: flex;
		}

		.breadcrumb li {
			margin-right: 5px;
		}

		.breadcrumb li i {
			margin-right: 5px;
		}

		.profile-section, .student-profile-section {
			background-color: #f1f1f1;
			padding: 20px;
			border-radius: 5px;
			margin-bottom: 20px;
		}

		h2 {
			margin-top: 0;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 20px;
		}

		table td {
			padding: 10px;
			border: 1px solid #ddd;
			font-family: var(--poppins);
		}

		button:hover {
			background-color: #4a148c;
		}

		input[type="text"], input[type="email"] {
			width: 100%;
			padding: 8px;
			box-sizing: border-box;
			border: 1px solid #ddd;
			border-radius: 5px;
			font-family: 'Poppins', sans-serif;
		}

		.show-hide-button {
            background-color: #6200ea;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
        }

        .show-hide-button:hover {
            background-color: #4a148c;
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
		function toggleIcNum() {
            var icNumElement = document.getElementById('teacherIcNum');
            icNumElement.style.display = icNumElement.style.display === 'none' ? 'inline' : 'none';
        }

		function togglePassword() {
			var passwordField = document.getElementById('password');
			var passwordFieldType = passwordField.type;

			if (passwordFieldType === 'password') {
				passwordField.type = 'text';
			} else {
				passwordField.type = 'password';
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
			<i class='bx bx-menu' ></i>
			<!--<a href="#" class="nav-link">Categories</a>  -->
			
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
					<a href="staff_profile.php" class="dropdown-item">Profile</a>
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
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
							<a class="active" href="#">Profile</a>
						</li>
					</ul>
				</div>
			</div>

			<div class="profile-section">
                <h2>Staff Profile</h2>
                <br>
                <form method="POST" action="staff_up_profile.php">
                    <table>
                        <tr>
                            <td><strong>First Name:</strong></td>
                            <td><?php echo htmlspecialchars($first_name); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Last Name:</strong></td>
                            <td><?php echo htmlspecialchars($last_name); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td><input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>" required></td>
                        </tr>
                        <tr>
                            <td><strong>Password</strong></td>
                            <td>
                                <input id="password" type="password" name="password" value="<?php echo htmlspecialchars($password); ?>">
                                <button type="button" class="show-hide-button" onclick="togglePassword()">Show/Hide</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td><input type="text" name="phone_num" value="<?php echo htmlspecialchars($phone_num); ?>" required></td>
                        </tr>
                        <tr>
                            <td><strong>Address:</strong></td>
                            <td><input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>" required></td>
                        </tr>
                    </table>
                    <button type="submit" id="updateButton">Update</button>
                </form>
            </div>
        </main>
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