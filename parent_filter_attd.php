<?php
/*
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo "Not logged in";
    exit();
}

// Connect to the database
$con = mysqli_connect("localhost", "root", "", "fyp_db");

// Check connection
if (!$con) {
    echo "Connection failed: " . mysqli_connect_error();
    exit();
}

// Get the logged-in user's email from the session
$user_email = $_SESSION['email'];

// Fetch parent ID from the database using email
$sql = "SELECT parent_id FROM parents WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($parent_id);
$stmt->fetch();
$stmt->close();

// Get the selected month
$current_month = date('Y-m');
if (isset($_GET['month'])) {
    $current_month = $_GET['month'];
}

// Query to fetch student's attendance records for the parent for the specific month
$sql = "SELECT a.student_id, s.student_name, c.class_name, a.status, a.date_recorded
        FROM attendance a
        JOIN students s ON a.student_id = s.student_id
        JOIN class c ON s.class_id = c.class_id
        WHERE s.parent_id = ? AND DATE_FORMAT(a.date_recorded, '%Y-%m') = ?
        ORDER BY a.date_recorded DESC";
$stmt = $con->prepare($sql);
$stmt->bind_param("is", $parent_id, $current_month);
$stmt->execute();
$result = $stmt->get_result();

// Prepare data for visualization
$attendance_data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $student_id = $row['student_id'];
        if (!isset($attendance_data[$student_id])) {
            $attendance_data[$student_id] = [
                'student_name' => $row['student_name'],
                'class_name' => $row['class_name'],
                'present' => 0,
                'absent' => 0
            ];
        }
        if ($row['status'] == 'present') {
            $attendance_data[$student_id]['present']++;
        } else {
            $attendance_data[$student_id]['absent']++;
        }
    }
}
$stmt->close();
mysqli_close($con);

// Output the HTML content for the attendance table
ob_start();
if ($result && $result->num_rows > 0) {
    $current_student_id = null;
    $result->data_seek(0); // Reset result pointer
    while ($row = $result->fetch_assoc()) {
        if ($row['student_id'] !== $current_student_id) {
            if ($current_student_id !== null) {
                echo "</tbody></table></div></div>"; // Close previous student's table
            }
            // Start a new student's table
            $current_student_id = $row['student_id'];
            echo "<div class='order'>";
            echo "<div class='head'>";
            echo "<h3>Student: " . htmlspecialchars($row['student_name']) . " (Class: " . htmlspecialchars($row['class_name']) . ")</h3>";
            //echo "<p>Present: " . $attendance_data[$current_student_id]['present'] . " days, Absent: " . $attendance_data[$current_student_id]['absent'] . " days</p>";
            echo "</div>";
            echo "<table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>";
        }
        // Display attendance record for the current student
        $status_class = $row['status'] == 'present' ? 'status-present' : 'status-absent';
        echo "<tr>
                <td>" . htmlspecialchars($row['date_recorded']) . "</td>
                <td class='$status_class'>" . htmlspecialchars($row['status']) . "</td>
              </tr>";
    }
    echo "</tbody></table></div></div>"; // Close the last student's table
} else {
    echo "No attendance records found.";
}
$html_content = ob_get_clean();
echo $html_content;
exit();
*/

session_start();

// Connect to the database
$con = mysqli_connect("localhost", "root", "", "fyp_db");

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get parent ID from the session (this assumes the parent ID is stored in the session)
if (!isset($_SESSION['email'])) {
    echo "User not logged in.";
    exit();
}

// Get the logged-in user's email from the session
$user_email = $_SESSION['email'];

// Fetch parent ID from the database using email
$sql = "SELECT parent_id FROM parents WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($parent_id);
$stmt->fetch();
$stmt->close();

// Get the requested month from the query string
$current_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

// Query to fetch student's attendance records for the parent for the specific month
$sql = "SELECT a.student_id, s.student_name, c.class_name, a.status, a.date_recorded
        FROM attendance a
        JOIN students s ON a.student_id = s.student_id
        JOIN class c ON s.class_id = c.class_id
        WHERE s.parent_id = ? AND DATE_FORMAT(a.date_recorded, '%Y-%m') = ?
        ORDER BY a.date_recorded DESC";
$stmt = $con->prepare($sql);
$stmt->bind_param("is", $parent_id, $current_month);
$stmt->execute();
$result = $stmt->get_result();

// Prepare data for visualization
$attendance_data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $student_id = $row['student_id'];
        if (!isset($attendance_data[$student_id])) {
            $attendance_data[$student_id] = [
                'student_name' => $row['student_name'],
                'class_name' => $row['class_name'],
                'present' => 0,
                'absent' => 0
            ];
        }
        if ($row['status'] == 'present') {
            $attendance_data[$student_id]['present']++;
        } else {
            $attendance_data[$student_id]['absent']++;
        }
    }
}


// Generate the HTML for the attendance table and summary
ob_start();
if ($result && $result->num_rows > 0) {
    $current_student_id = null;
    $result->data_seek(0); // Reset result pointer
    while ($row = $result->fetch_assoc()) {
        if ($row['student_id'] !== $current_student_id) {
            if ($current_student_id !== null) {
                echo "</tbody></table></div></div>"; // Close previous student's table
            }
            // Start a new student's table
            $current_student_id = $row['student_id'];
            echo "<div class='order'>";
            echo "<div class='head'>";
            echo "<h3>Student: " . htmlspecialchars($row['student_name']) . " (Class: " . htmlspecialchars($row['class_name']) . ")</h3>";
            //echo "<p>Present: " . $attendance_data[$current_student_id]['present'] . " days, Absent: " . $attendance_data[$current_student_id]['absent'] . " days</p>";
            echo "</div>";
            echo "<table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>";
        }
        // Display attendance record for the current student
        $status_class = $row['status'] == 'present' ? 'status-present' : 'status-absent';
        echo "<tr>
                <td>" . htmlspecialchars($row['date_recorded']) . "</td>
                <td class='$status_class'>" . htmlspecialchars($row['status']) . "</td>
              </tr>";
    }
    echo "</tbody></table></div></div>"; // Close the last student's table
} else {
    echo "No attendance records found.";
}
$attendance_table_html = ob_get_clean();

$total_present = array_sum(array_column($attendance_data, 'present'));
$total_absent = array_sum(array_column($attendance_data, 'absent'));

$response = [
    'table' => $attendance_table_html,
    'present' => $total_present,
    'absent' => $total_absent
];

echo json_encode($response);

$stmt->close();
mysqli_close($con);
?>
