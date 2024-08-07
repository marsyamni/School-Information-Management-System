<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email and password are set
    if (isset($_POST["email"]) && isset($_POST["password"])) {
        // Retrieve and sanitize data
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);

        // Connect to database
        $con = mysqli_connect("localhost", "root", "", "fyp_db");

        // Check connection
        if (!$con) {
            echo json_encode([
                'success' => false,
                'message' => 'Database connection failed.'
            ]);
            exit();
        }

        // Prepare SQL
        $sql = "SELECT * FROM staffs WHERE email = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows == 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Username does not exist!'
            ]);
        } else {
            // Fetch user data
            $row = $result->fetch_array(MYSQLI_BOTH);

            // Verify password (assuming passwords are stored in plain text, consider using password_hash)
            if ($row["password"] == $password) {
                $_SESSION["staff_id"] = $row["staff_id"]; // Store tc_id in session
                $_SESSION["email"] = $row["email"]; // Store email in session
                echo json_encode([
                    'success' => true
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Login failed! Incorrect password.'
                ]);
            }
        }

        // Close statement and connection
        $stmt->close();
        mysqli_close($con);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Email and password are required.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>
