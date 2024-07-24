<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection file
include_once 'connect.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $name = mysqli_real_escape_string($conn, $_POST['Name']);
    $email = mysqli_real_escape_string($conn, $_POST['Email']);
    $password = mysqli_real_escape_string($conn, $_POST['Password']);

    // Password complexity requirements
    $min_length = 6;
    $has_uppercase = preg_match('/[A-Z]/', $password);
    $has_lowercase = preg_match('/[a-z]/', $password);
    $has_number = preg_match('/\d/', $password);
    $has_special_char = preg_match('/[^a-zA-Z\d]/', $password);

    // Check if password meets complexity requirements
    if (strlen($password) < $min_length || !$has_uppercase || !$has_lowercase || !$has_number || !$has_special_char) {
        echo "Error: Password must be at least 6 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the email already exists in the database
        $check_query = "SELECT * FROM signup WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_query);
        if (mysqli_num_rows($check_result) > 0) {
            echo "Error: This email is already registered.";
        } else {
            // Insert form data into database with hashed password
            $sql = "INSERT INTO signup (name, email, password) VALUES ('$name', '$email', '$hashed_password')";

            if (mysqli_query($conn, $sql)) {
                // Start a new session and store user ID
                session_start();
                $_SESSION['user_id'] = mysqli_insert_id($conn);
                header("Location: form.html");
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    }
}
?>
