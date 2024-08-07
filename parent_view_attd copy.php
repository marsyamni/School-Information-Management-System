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

// Get the current month
$current_month = date('Y-m');

// Check if a specific month is requested
if (isset($_GET['month'])) {
    $current_month = $_GET['month'];
}

// Query to fetch all students associated with the parent
$sql_students = "SELECT s.student_id, s.student_name, c.class_name 
                 FROM students s 
                 JOIN class c ON s.class_id = c.class_id 
                 WHERE s.parent_id = ?";
$stmt_students = $con->prepare($sql_students);
$stmt_students->bind_param("i", $parent_id);
$stmt_students->execute();
$result_students = $stmt_students->get_result();

$students_data = [];
while ($row_student = $result_students->fetch_assoc()) {
    $student_id = $row_student['student_id'];
    $students_data[$student_id] = [
        'student_name' => $row_student['student_name'],
        'class_name' => $row_student['class_name'],
        'attendance' => []
    ];

    // Query to fetch attendance records for each student for the specific month
    $sql_attendance = "SELECT status, date_recorded 
                       FROM attendance 
                       WHERE student_id = ? AND DATE_FORMAT(date_recorded, '%Y-%m') = ?";
    $stmt_attendance = $con->prepare($sql_attendance);
    $stmt_attendance->bind_param("is", $student_id, $current_month);
    $stmt_attendance->execute();
    $result_attendance = $stmt_attendance->get_result();

    $present_count = 0;
    $absent_count = 0;
    while ($row_attendance = $result_attendance->fetch_assoc()) {
        $students_data[$student_id]['attendance'][] = $row_attendance;
        if ($row_attendance['status'] == 'Present') {
            $present_count++;
        } else {
            $absent_count++;
        }
    }
    $students_data[$student_id]['present_count'] = $present_count;
    $students_data[$student_id]['absent_count'] = $absent_count;

    $stmt_attendance->close();
}

$stmt_students->close();
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CommWave</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="dev_style.css">
    <style>
        .container {
            margin-top: 50px;
        }
        .student-container {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .summary-container {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .summary-box {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 150px;
            text-align: center;
        }
        .present {
            background-color: #c3e6cb;
        }
        .absent {
            background-color: #f5c6cb;
        }
        .chart-container {
            margin-top: 20px;
        }
        .order {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .head {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
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
            <li>
                <a href="parent_view_acad.php">
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
                    <a href="#" class="dropdown-item">Change Password</a>
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
                            <a href="parent_index.php">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li>
                            <a class="active" href="#">Attendance</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter">
                <form action="" method="GET" id="monthFilterForm">
                    <label for="month">Select Month:</label>
                    <input type="month" id="month" name="month" value="<?php echo htmlspecialchars($current_month); ?>" onchange="updateAttendanceData()">
                </form>
            </div>
            <div class="container"> 
                <?php foreach ($students_data as $student_id => $student_info): ?>
                    <div class="student-container">
                        <h3>Student: <?php echo htmlspecialchars($student_info['student_name']); ?> (Class: <?php echo htmlspecialchars($student_info['class_name']); ?>)</h3>
                        <!-- Summary Section -->
                        <div class="summary-container">
                            <div class="summary-box present">
                                Present: <?php echo $student_info['present_count']; ?> days
                            </div>
                            <div class="summary-box absent">
                                Absent: <?php echo $student_info['absent_count']; ?> days
                            </div>
                        </div>
                        <br>
                        <!-- Visualization Section -->
                        <div class="chart-container">
                            <div class="order">
                                <div class="head">
                                    <h3>Attendance Summary</h3>
                                </div>
                                <canvas id="attendanceChart_<?php echo $student_id; ?>"></canvas>
                            </div>
                        </div>
                        <!-- Attendance Table Section -->
                        <div class="table-data">
                            <?php if (count($student_info['attendance']) > 0): ?>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($student_info['attendance'] as $attendance): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($attendance['date_recorded']); ?></td>
                                                <td class="<?php echo $attendance['status'] == 'Present' ? 'text-success' : 'text-danger'; ?>">
                                                    <?php echo htmlspecialchars($attendance['status']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>No attendance records found.</p>
                            <?php endif; ?>
                        </div>

                        
                    </div>
                <?php endforeach; ?>
            </div>
        </main>>
    </section>
       
    <!-- JavaScript for fetching and updating attendance data -->
    <!-- JavaScript for fetching and updating attendance data -->
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            <?php foreach ($students_data as $student_id => $student_info): ?>
                renderChart('<?php echo $student_id; ?>', <?php echo $student_info['present_count']; ?>, <?php echo $student_info['absent_count']; ?>);
            <?php endforeach; ?>
        });

        function updateAttendanceData() {
            const month = document.getElementById('month').value;
            window.location.href = `parent_view_attd.php?month=${month}`;
        }

        function renderChart(student_id, presentCount, absentCount) {
            const ctx = document.getElementById(`attendanceChart_${student_id}`).getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Present', 'Absent'],
                    datasets: [
                        {
                            label: 'Attendance Summary',
                            data: [presentCount, absentCount],
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.2)', // Present color
                                'rgba(255, 99, 132, 0.2)'   // Absent color
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>
