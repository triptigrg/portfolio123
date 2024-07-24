<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Include database connection file
include_once 'connect.php';

// Check for file upload errors
if (!isset($_FILES['image']) || $_FILES['image']['error'] != UPLOAD_ERR_OK) {
    echo "An error occurred during the file upload. Error code: " . $_FILES['image']['error'];
    exit();
}

// File upload constraints
$allowedTypes = ['image/jpeg', 'image/png'];
$maxSize = 2 * 1024 * 1024; // 2MB

$fileType = mime_content_type($_FILES['image']['tmp_name']);
$fileSize = $_FILES['image']['size'];

if (!in_array($fileType, $allowedTypes)) {
    echo "Only JPG and PNG files are allowed.";
    exit();
}

if ($fileSize > $maxSize) {
    echo "File size should be less than 2MB.";
    exit();
}

// Move the uploaded file to the desired directory
$uploadDir = 'uploads/';
$uploadFile = $uploadDir . basename($_FILES['image']['name']);

if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
    // Update user's profile information in the database
    $user_id = $_SESSION['user_id'];
    $image_name = basename($_FILES['image']['name']);

    $update_query = "UPDATE signup SET image = '$image_name' WHERE id = '$user_id'";
    if (mysqli_query($conn, $update_query)) {
        
        // Log the activity
        $activity_type = "Profile Image Update";
        $activity_details = "User with ID $user_id updated their profile image.";

        $log_query = "INSERT INTO activity_log (user_id, activity_type, activity_details) VALUES ('$user_id', '$activity_type', '$activity_details')";
        mysqli_query($conn, $log_query);

        // Redirect to profile page
        header("Location: profile.php");
        exit();
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }
} else {
    echo "An error occurred during the file upload. Error code: " . $_FILES['image']['error'];
}
?>
