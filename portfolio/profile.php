<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Include database connection file
include_once 'connect.php';

// Handle logout
if (isset($_GET['logout'])) {
    // Destroy the session
    session_destroy();

    // Unset all session variables
    $_SESSION = [];

    // Redirect to portfolio.html
    header("Location: portfolio.html");
    exit();
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

// Display user details and uploaded image
$image_path = 'uploads/' . $user['image'];

// Handle remove image
if (isset($_POST['remove_image'])) {
    $update_query = "UPDATE signup SET image = NULL WHERE id = '$user_id'";
    if (mysqli_query($conn, $update_query)) {
        // Remove the image file from the server
        unlink($image_path);
        // Reload the page to reflect changes
        header("Location: profile.php");
        exit();
    } else {
        echo "Error removing image: " . mysqli_error($conn);
    }
}

// Handle change image
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['new_image'])) {
    // Ensure a file was uploaded
    if ($_FILES['new_image']['error'] != UPLOAD_ERR_OK) {
        echo "An error occurred during the file upload.";
        exit();
    }

    // File upload constraints
    $allowedTypes = ['image/jpeg', 'image/png'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    $fileType = mime_content_type($_FILES['new_image']['tmp_name']);
    $fileSize = $_FILES['new_image']['size'];

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
    $uploadFile = $uploadDir . basename($_FILES['new_image']['name']);

    if (move_uploaded_file($_FILES['new_image']['tmp_name'], $uploadFile)) {
        // Remove the old image file from the server
        unlink($image_path);
        
        // Update user's profile information in the database
        $image_name = basename($_FILES['new_image']['name']);
        $update_query = "UPDATE signup SET image = '$image_name' WHERE id = '$user_id'";
        if (mysqli_query($conn, $update_query)) {
            // Reload the page to reflect changes
            header("Location: profile.php");
            exit();
        } else {
            echo "Error updating profile: " . mysqli_error($conn);
        }
    } else {
        echo "An error occurred during the file upload.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .profile {
            display: flex;
            align-items: center;
        }
        .avatar {
            flex: 0 0 200px;
            margin-right: 30px;
        }
        .avatar img {
            width: 100%;
            border-radius: 10px;
        }
        .details {
            flex: 1;
        }
        .details p {
            margin: 10px 0;
            color: #555;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>User Profile</h1>
    <div class="profile">
        <div class="avatar">
            <img src="<?php echo $image_path; ?>" alt="Profile Picture">
        </div>
        <div class="details">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Serial Number:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
            <!-- Form to remove image -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <button type="submit" name="remove_image" class="btn btn-danger">Remove Image</button>
            </form>
            <!-- Form to change image -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <input type="file" name="new_image" accept=".jpg, .jpeg, .png">
                <button type="submit" class="btn btn-primary">Change Image</button>
            </form>
            <a href="profile.php?logout=true" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>
</body>
</html>
