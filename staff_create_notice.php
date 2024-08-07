<?php

if (isset($_POST['Create_Notice'])) {
    // Retrieve data from HTML form
    $notice_title = trim($_POST["notice_title"]);
    $notice_type = trim($_POST["notice_type"]);
    $notice_recipient = trim($_POST["notice_recipient"]);
    $notice_content = trim($_POST["notice_content"]);

    // Get current date and format it
    $date_created = date('Y-m-d');

    // Connect to database
    $con = mysqli_connect("localhost", "root", "", "fyp_db") or die("Cannot connect to the server: " . mysqli_error($con));

    /*Handle file uploads
    $attachments = [];
    if (!empty($_FILES['notice_attachment']['name'][0])) {
        $target_dir = "C:/xampp/htdocs/uploads/"; // Adjust the path accordingly

        foreach ($_FILES['notice_attachment']['name'] as $key => $filename) {
            $target_file = $target_dir . basename($_FILES["notice_attachment"]["name"][$key]);
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if file is a valid type
            $allowed_types = ["jpg", "jpeg", "png", "pdf", "doc", "docx"];
            if (in_array($file_type, $allowed_types)) {
                if (move_uploaded_file($_FILES['notice_attachment']['tmp_name'][$key], $target_file)) {
                    $attachments[] = $target_file;
                } else {
                    echo "Sorry, there was an error uploading your file: " . htmlspecialchars($filename);
                }
            } else {
                echo "File type not allowed: " . htmlspecialchars($filename);
            }
        }
    }
    
    // Convert attachments array to comma-separated string
    $attachments_str = implode(',', $attachments);
*/
    // Prepare statement for notice details
    $sql = "INSERT INTO notice (notice_title, notice_type, notice_recipient, notice_content, date_created) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);

    // Bind values to the prepared statement
    mysqli_stmt_bind_param($stmt, "sssss", $notice_title, $notice_type, $notice_recipient, $notice_content, $date_created);

    // Execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        // Redirect to staff_view_notice.php after successful insert
        header("Location: staff_view_notice.php");
        exit(); // Ensure no further code is executed after redirection
    } else {
        echo "Record NOT Inserted! Error: " . mysqli_stmt_error($stmt);
    }

    // Close the prepared statement and connection
    mysqli_stmt_close($stmt);
    mysqli_close($con);
}
?>

<!-- HTML to display uploaded attachments -->
<h2>Uploaded Attachments</h2>
<?php
foreach ($attachments as $attachment) {
    echo '<a href="' . $attachment . '" target="_blank">' . basename($attachment) . '</a><br>';
}
?>
