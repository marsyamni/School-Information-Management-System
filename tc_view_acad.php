<?php
// Include the filter process
include 'staff_filter_notice.php';
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
        //Logout popup modal
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
        
          //Filter
        function toggleFilter() {
            var filterDropdown = document.getElementById("filterDropdown");
            if (filterDropdown.style.display === "block") {
                filterDropdown.style.display = "none";
            } else {
                filterDropdown.style.display = "block";
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
            <span class="text">CommWave</span>
        </a>
        <ul class="side-menu top">
            <li>
                <a href="parent_index.php">
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
                <a href="staff_create_index.php">
                    <i class='bx bxs-group'></i>
                    <span class="text">User Accounts</span>
                </a>
            </li>

        </ul>
        <ul class="side-menu">
            <li>
                <a href="#">
                    <i class='bx bxs-cog' ></i>
                    <span class="text">Settings</span>
                </a>
            </li>
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
            <i class='bx bx-menu' ></i>
            <!--<a href="#" class="nav-link">Categories</a>  -->
            <form action="#">
            </form>
            <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode"></label>
            
            <a href="#" class="profile">
                <img src="img/people.png">
            </a>
        </nav>
        <!-- NAVBAR -->

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
                            <a href="staff_index.php">Dashboard</a>
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
                <div id="filterDropdown" class="filter-content">
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
                            <option value="Yesterday" <?php if ($time_filter == "Yesterday") echo "selected"; ?>>Yesterday</option>
                            <option value="Last Week" <?php if ($time_filter == "Last Week") echo "selected"; ?>>Last Week</option>
                        </select>

                        <button type="submit">Apply Filter</button>
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
                // Display retrieved notices for parents
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<div class='notice-container'>"; // Add opening div for each notice
                        echo "<h2>" . $row['notice_type'] . ": " . $row['notice_title'] . "</h2>";
                        echo "<h5>Date Created: " . $row['date_created'] . "</h5><br>";
                        echo "<h5>Recipient: " . $row['notice_recipient'] . "</h5><br>";
                        echo "<p>" . $row['notice_content'] . "</p>";
                        // Add buttons for edit and delete
                        echo "<div class='btn-group'>";
                        echo "<button class='edit-btn' onclick=\"location.href='staff_notice.php?id=" . $row['notice_id'] . "'\">Edit</button>";
                        echo "<button class='delete-btn' data-id='" . $row['notice_id'] . "'>Delete</button>";
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
