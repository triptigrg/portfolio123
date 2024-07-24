<?php
session_start();

// Database configuration
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "portfolio";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to handle failed login attempts
function handleFailedLogin($conn) {
    if (!isset($_SESSION['failed_attempts'])) {
        $_SESSION['failed_attempts'] = 0;
    }
    if (!isset($_SESSION['lockout_end_time'])) {
        $_SESSION['lockout_end_time'] = 0;
    }

    // Check if the account is locked
    if ($_SESSION['lockout_end_time'] > time()) {
        // Account is locked
        $remainingTime = $_SESSION['lockout_end_time'] - time();
        echo "<script>alert('Account is locked. Please try again after " . $remainingTime . " seconds.');</script>";
        return false;
    }

    // Check if user is logging in
    if (isset($_POST['login'])) {
        $email = $_POST['Email'];
        $password = $_POST['Password'];

        // Query the database for the user
        $stmt = $conn->prepare("SELECT id, email, password FROM signup WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $db_email, $db_password);
            $stmt->fetch();

            // Verify hashed password
            if (password_verify($password, $db_password)) {
                $_SESSION['user_id'] = $id;
                header("Location: dashboard.php");
                exit();
            } else {
                // Increment failed attempts after the third attempt
                $_SESSION['failed_attempts']++;

                // Lock the account if failed attempts exceed 3
                if ($_SESSION['failed_attempts'] >= 3) {
                    $_SESSION['lockout_end_time'] = time() + 20; // Lock the account for 20 seconds
                    echo "<script>alert('Too many failed attempts. Account locked for 20 seconds.');</script>";
                } else {
                    echo "<script>alert('Invalid email or password');</script>";
                }
            }
        } else {
            echo "<script>alert('No user found with this email');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Required fields are missing.');</script>";
    }

    return true;
}

// Call the function to handle failed login attempts
handleFailedLogin($conn);

// Close connection
$conn->close();
?>
