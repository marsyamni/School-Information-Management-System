<?php
// display_students.php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login_staff.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fyp_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's email from the session
$user_email = $_SESSION['email'];

// Fetch first name from the database using email
$sql = "SELECT first_name FROM staffs WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($first_name);
$stmt->fetch();
$stmt->close();

// Fetch students' information
$sql = "SELECT s.student_id, s.student_name, s.ic_num, 
               c.class_name, 
               t.first_name AS teacher_first_name, t.last_name AS teacher_last_name, 
               p.first_name AS parent_first_name, p.last_name AS parent_last_name
        FROM students s
        LEFT JOIN teachers t ON s.tc_id = t.tc_id
        LEFT JOIN parents p ON s.parent_id = p.parent_id
        LEFT JOIN class c ON s.class_id = c.class_id
        ORDER BY s.student_name ASC";
        
$result = $conn->query($sql);

if ($result === false) {
    die("Error fetching students' information: " . $conn->error);
}

// Close connection (optional as PHP will close it at script termination)
$conn->close();
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
    <link rel="stylesheet" href="staff_style.css">

    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            margin-top: 15px;
        }

        .edit-btn, .delete-btn {
            padding: 10px 20px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 5px;
        }

        .edit-btn {
            background-color: #007bff;
            color: white;
        }

        .edit-btn:hover {
            background-color: #0056b3;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .search-bar button {
			padding: 10px;
			background-color: #007bff;
			color: white;
			border: 1px solid #007bff;
			border-radius: 0 5px 5px 0;
			cursor: pointer;
		}
		.search-bar button:hover {
			background-color: #0056b3;
		}

    </style>
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

        //Delete Confirmation Popup
        document.addEventListener('DOMContentLoaded', function() 
        {
            // Add event listener for delete buttons
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const noticeId = this.getAttribute('data-id');
                    Confirm.open({
                        title: 'Confirm User Deletion',
                        message: 'Are you sure you want to delete this user?',
                        onok: () => {
                            location.href = 'staff_delete_student.php?id=' + noticeId;
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

		function togglePassword(element) {
			const input = element.previousElementSibling;
			if (input.type === "password") {
				input.type = "text";
				element.classList.remove('bx-show');
				element.classList.add('bx-hide');
			} else {
				input.type = "password";
				element.classList.remove('bx-hide');
				element.classList.add('bx-show');
			}
		}
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
					<a href="staff_profile.php" class="dropdown-item">Profile</a>
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
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class='active1' href="staff_user_index.php">User Accounts</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Students' Records</a>
                        </li>
                    </ul>
                </div>
            </div>
            <br>
            <div class="container">
                <h3>Students' Records</h3>
                <br>
                <div class="search-bar">
                    <form method="GET" action="">
                        <input type="text" name="search" placeholder="Search by name or email" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit">Search</button>
                        <a href="staff_records_student.php" class="reset-btn">Reset</a>
                    </form>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student Name</th>
                            <th>IC Number</th>
                            <th>Class</th>
                            <th>Teacher</th>
                            <th>Parent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        // Database connection
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "fyp_db";

                        // Create connection
                        $conn = new mysqli($servername, $username, $password, $dbname);

                        // Check connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Handle search query
                        $search = isset($_GET['search']) ? $_GET['search'] : '';

                        // Fetch student records with search filter
                        $sql = "SELECT s.student_id, s.student_name, s.ic_num, c.class_name, t.first_name AS teacher_first_name, t.last_name AS teacher_last_name, p.first_name AS parent_first_name, p.last_name AS parent_last_name
                                FROM students s
                                LEFT JOIN class c ON s.class_id = c.class_id
                                LEFT JOIN teachers t ON s.tc_id = t.tc_id
                                LEFT JOIN parents p ON s.parent_id = p.parent_id
                                WHERE s.student_name LIKE ? OR s.student_id LIKE ? OR s.ic_num LIKE ? OR c.class_name LIKE ?
                                ORDER BY s.student_name ASC";

                        $stmt = $conn->prepare($sql);

                        // Bind parameters
                        $search_param = "%$search%";
                        $stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);

                        // Execute query
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Check if any results found
                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['student_id']}</td>
                                        <td>{$row['student_name']}</td>
                                        <td>{$row['ic_num']}</td>
                                        <td>{$row['class_name']}</td>
                                        <td>{$row['teacher_first_name']} {$row['teacher_last_name']}</td>
                                        <td>{$row['parent_first_name']} {$row['parent_last_name']}</td>
                                        <td>
                                            <div class='btn-group'>
                                                <button class='edit-btn' onclick=\"location.href='staff_student_user.php?id={$row['student_id']}'\">Edit</button>
                                                <button class='delete-btn' data-id='{$row['student_id']}'>Delete</button>
                                            </div>
                                        </td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No records found</td></tr>";
                        }

                        $stmt->close();
                        $conn->close();
                        ?>


                    </tbody>
                </table>
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
