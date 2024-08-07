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

// Get class_id from session
$class_id = isset($_SESSION['class_id']) ? $_SESSION['class_id'] : null;

// Default to today's date if date is not set in GET parameters
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Fetch first name and class name from the database using email
$sql = "SELECT t.first_name, c.class_name, t.class_id 
        FROM teachers t
        JOIN class c ON t.class_id = c.class_id
        WHERE t.email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($first_name, $class_name, $class_id);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">



    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="tc_style.css">

    <title>IISSA CommWave</title>
    <style>
        .breadcrumb{
            background-color: #c6e2b0;
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
       /*  document.addEventListener('DOMContentLoaded', function() {
            Delete Confirmation Popup
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const noticeId = this.getAttribute('data-id');
                    Confirm.open({
                        title: 'Confirm Notice Deletion',
                        message: 'Are you sure you want to delete this notice?',
                        okText: 'Delete',
                        onok: () => {
                            location.href = 'tc_delete_acad.php?id=' + noticeId;
                        }
                    });
                });
            });

            // Logout Confirmation Popup
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
        };*/

    </script>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bx-hive'></i>
            <span class="text">IISSA CommWave</span>
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
            <li>
                <a href="tc_acad_view.php">
                    <i class='bx bxs-graduation'></i>
                    <span class="text">Academic</span>
                </a>
            </li>
            <li class="active">
                <a href="#">
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

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <ul class="breadcrumb">
                        <li>
                            <a class="active1" href="tc_index.php">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li>
                            <a class="active" href="#">Attendance</a>
                        </li>
                    </ul>
                </div>
            </div>
            <form action="tc_attd_view.php" method="get">
                <label for="date">Select Date:</label>
                <input type="date" id="date" name="date" value="<?php echo $date; ?>">
                <input type="submit" value="View">
            </form>
            <div class="right">
                <a href="tc_attd_record.php" class="btn-create-notice">
                    <i class='bx bx-calendar-check'></i>
                    <span class="text">Record Attendance</span>
                </a>
            </div>
            <br>
            <table border="1">
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Status</th>
                </tr>
                <?php
                if (isset($date)) {
                    // Query to fetch attendance records for the teacher's class
                    $sql = "SELECT a.student_id, s.student_name, c.class_name, a.status
                            FROM attendance a
                            JOIN students s ON a.student_id = s.student_id
                            JOIN class c ON a.class_id = c.class_id
                            WHERE a.date_recorded = ? AND a.class_id = ?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("si", $date, $class_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Normalize the status value
                            $status = ucfirst(strtolower(trim($row['status'])));
                            $status_color = ($status == 'Present') ? 'text-success' : 'text-danger';

                            echo "<tr>
                                    <td>{$row['student_id']}</td>
                                    <td>{$row['student_name']}</td>
                                    <td>{$row['class_name']}</td>
                                    <td class='{$status_color}'>{$row['status']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No records found for the selected date.</td></tr>";
                    }

                    $stmt->close();
                }
                ?>
            </table>
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
    <script src="script.js"></script>
</body>
</html>
