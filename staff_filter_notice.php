<?php
// Connect to database
$con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Cannot connect to the server" . mysqli_error($con));

// Initialize filter variables
$notice_type_filter = isset($_POST["notice_type"]) ? $_POST["notice_type"] : "";
$time_filter = isset($_POST["time"]) ? $_POST["time"] : "";

// Construct SQL query
$sql = "SELECT * FROM notice WHERE (notice_recipient = 'parents' OR notice_recipient = 'teachers' OR notice_recipient = 'all')";
if (!empty($notice_type_filter)) {
    $sql .= " AND notice_type = '" . mysqli_real_escape_string($con, $notice_type_filter) . "'";
}
if (!empty($time_filter)) {
    if ($time_filter == "Latest") {
        $sql .= " ORDER BY date_created DESC";
    } elseif ($time_filter == "Yesterday") {
        $sql .= " AND DATE(date_created) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
    } elseif ($time_filter == "Last Week") {
        $sql .= " AND DATE(date_created) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
    }
}

// Execute SQL query
$result = mysqli_query($con, $sql);

// Close database connection
mysqli_close($con);
?>

<!-- Display filtered results -->
<?php if (mysqli_num_rows($result) > 0): ?>
    <h2>Filtered Results:</h2>
    <ul>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <li><?php echo $row['notice_title']; ?> - <?php echo $row['notice_content']; ?></li>
            <!-- Display other fields as needed -->
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No results found.</p>
<?php endif; ?>
