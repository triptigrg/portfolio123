<?php
session_start();

if(isset($_POST['submit'])) {
  // Retrieve the entered OTP
  $enteredOTP = $_POST['otp'];

  // Retrieve the stored OTP
  $storedOTP = $_SESSION['otp'];

  if($enteredOTP == $storedOTP) {
    // OTP matched, allow the user to reset password
    header("Location: reset_form.php");
    exit();
  } else {
    // Incorrect OTP, display error message
    echo "Invalid OTP. Please try again.";
  }
}



// SMTP Server Address
$smtpServer = 'smtp.example.com'; // Replace with your SMTP server address

// SMTP Port Number
$smtpPort = 587; // Replace with your SMTP port number

// Encryption Method (STARTTLS or SSL/TLS)
$encryption = 'tls'; // Replace with your encryption method

// SMTP Authentication Credentials
$username = 'your_username'; // Replace with your SMTP username
$password = 'your_password'; // Replace with your SMTP password

// Debugging Output (Optional)
// Enable debugging to view SMTP server communication (not recommended in production)
$debug = false;

// Create a PHPMailer instance
$mail = new PHPMailer\PHPMailer\PHPMailer();

// Enable verbose debugging output if debugging is enabled
$mail->SMTPDebug = $debug ? SMTP::DEBUG_CONNECTION : 0;

// Set SMTP server settings
$mail->isSMTP();
$mail->Host = $smtpServer;
$mail->Port = $smtpPort;
$mail->SMTPSecure = $encryption;
$mail->SMTPAuth = true;
$mail->Username = $username;
$mail->Password = $password;

// Additional settings (if needed)
$mail->CharSet = 'UTF-8';

// Check if SMTP settings are configured correctly
if (!$mail->Host || !$mail->Port || !$mail->Username || !$mail->Password) {
    echo "SMTP settings are not configured correctly.";
    // Handle error condition appropriately
} else {
    echo "SMTP settings are configured correctly.";
    // Proceed with sending emails
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enter OTP</title>
</head>
<body>
  <h2>Enter OTP</h2>
  <form action="enter_otp.php" method="post">
    <label for="otp">Enter the OTP sent to your email:</label>
    <input type="text" id="otp" name="otp" required>
    <button type="submit" name="submit">Submit OTP</button>
  </form>
</body>
</html>
