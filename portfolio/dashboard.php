<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Include database connection file
include_once 'connect.php';

// Function to insert activity log
function insertActivityLog($conn, $userId, $activityType, $activityDetails) {
    $timestamp = date("Y-m-d H:i:s");
    $insert_query = "INSERT INTO activity_log (user_id, timestamp, activity_type, activity_details) VALUES ('$userId', '$timestamp', '$activityType', '$activityDetails')";
    mysqli_query($conn, $insert_query);
}

// Check if the form was submitted and handle image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image'])) {
    // Ensure a file was uploaded
    if ($_FILES['image']['error'] != UPLOAD_ERR_OK) {
        echo "An error occurred during the file upload.";
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
            // Insert activity log
            insertActivityLog($conn, $user_id, 'Image Upload', 'User uploaded a profile image.');
            
            // Redirect to profile page after successful upload
            header("Location: profile.php");
            exit();
        } else {
            echo "Error updating profile: " . mysqli_error($conn);
        }
    } else {
        echo "An error occurred during the file upload.";
    }
}

// Retrieve user information from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM signup WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Check if user data is retrieved correctly
if (!$user) {
    echo "Error fetching user data.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="dashboard.css"> <!-- You can create this CSS file for styling -->
</head>
<body>
<div class="container">
    <h1>Welcome to your dashboard, <?php echo htmlspecialchars($user['name']); ?>!</h1>
    <div class="profile">
        <div class="avatar">
            <form id="uploadForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <input type="file" name="image" accept=".jpg, .jpeg, .png">
                <button type="submit" class="btn btn-primary">Upload Image</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Redirect to portfolio.html after 15 seconds
    setTimeout(function(){
        alert("Your session will be timed out. Please save your work.");
        setTimeout(function(){
            window.location.href = "portfolio.html";
        }, 2000); // Redirect after 2 seconds of showing the alert
    }, 10000); // Show alert after 10 seconds
</script>
</body>
</html>
