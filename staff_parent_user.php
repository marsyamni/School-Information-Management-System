<?php

session_start();


// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login_staff.php");
    exit();
}

// Connect to database
$con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Cannot connect to the server" . mysqli_error($con));

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

// Check if parent_id is set in the URL and retrieve the parent details
if (isset($_GET['id'])) {
    $parent_id = intval($_GET['id']);
    $sql = "SELECT * FROM parents WHERE parent_id = $parent_id";
    $result = mysqli_query($con, $sql);
    $parent = mysqli_fetch_assoc($result);

    if (!$parent) {
        die("Parent not found.");
    }
} else {
    die("No parent ID provided.");
}

// Check if the form has been submitted to update the parent
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_parent'])) {
    $first_name = mysqli_real_escape_string($con, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($con, $_POST['last_name']);
    $phone_num = mysqli_real_escape_string($con, $_POST['phone_num']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $address = mysqli_real_escape_string($con, $_POST['address']);

    $sql = "UPDATE parents SET first_name = '$first_name', last_name = '$last_name', phone_num = '$phone_num', email = '$email', password = '$password', address = '$address' WHERE parent_id = $parent_id";

    if (mysqli_query($con, $sql)) {
        echo "Parent updated successfully.";
        header("Location: staff_records_parent.php");
        exit();
    } else {
        echo "Error updating parent: " . mysqli_error($con);
    }
}

// Close connection (optional as PHP will close it at script termination)
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Teacher</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="staff_style.css">

    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .form-container {
            background-color: #ffffff;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }

        label {
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"], input[type="email"], input[type="password"], textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        .hidden {
            display: none;
        }
    </style>

    <script>
        function toggleClassSelection(value) {
            const classGroup = document.getElementById('class_group');
            if (value === 'Yes') {
                classGroup.classList.remove('hidden');
            } else {
                classGroup.classList.add('hidden');
                document.getElementById('class_id').value = ''; // Clear the class selection
            }
        }

        //Delete Confirmation Popup
        document.addEventListener('DOMContentLoaded', function() 
        {
            // Add event listener for delete buttons
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const noticeId = this.getAttribute('data-id');
                    Confirm.open({
                        title: 'Confirm Notice Deletion',
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
    </script>
</head>
<body>
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
            <li class="active">
                <a href="staff_user_index.php">
                    <i class='bx bxs-group'></i>
                    <span class="text">User Accounts</span>
                </a>
            </li>
        </ul>
        <ul class="side-menu">
           
            <li>
                <a href="home.html" class="logout">  
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
                            <a href="staff_index.php">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class='active1' href="staff_user_index.php">Users' Accounts</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Edit Parent Information</a>
                        </li>
                    </ul>
                </div>
            </div>
            <br>
            <div class="container">
                <form action="staff_parent_edit.php?id=<?php echo htmlspecialchars($parent_id); ?>" method="POST">
                    <input type="hidden" name="parent_id" value="<?php echo htmlspecialchars($parent['parent_id']); ?>">

                    <div>
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($parent['first_name']); ?>" required>
                    </div>
                    <div>
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($parent['last_name']); ?>" required>
                    </div>
                    <div>
                        <label for="phone_num">Phone Number:</label>
                        <input type="text" id="phone_num" name="phone_num" value="<?php echo htmlspecialchars($parent['phone_num']); ?>" required>
                    </div>
                    <div>
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($parent['email']); ?>" required>
                    </div>
                    <div>
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($parent['password']); ?>" required>
                    </div>
                    <div>
                        <label for="address">Address:</label>
                        <textarea id="address" name="address" rows="4" cols="50" required><?php echo htmlspecialchars($parent['address']); ?></textarea>
                    </div>
                    <br>
                    <div>
                        <button type="submit" name="update_parent">Update Parent</button>
                    </div>
                </form>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script src="script.js" defer></script>
</body>
</html>
