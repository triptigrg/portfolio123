<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
</head>
<body>
  <h2>Reset Your Password</h2>
  <form action="reset_password.php" method="post">
    <label for="email">Enter your email:</label>
    <input type="email" id="email" name="email" required>
    <button type="submit" name="reset">Reset Password</button>
  </form>

<?php
if(isset($_POST['reset'])) {
  // Retrieve the user's email
  $email = $_POST['email'];

  // Generate a random OTP (e.g., 6 digits)
  $otp = mt_rand(100000, 999999);

  // Store the OTP temporarily (e.g., in a session variable)
  session_start();
  $_SESSION['otp'] = $otp;

  // Send the OTP to the user's email
  // Replace 'your_email@example.com' with your email address
  $to = $email;
  $subject = "Password Reset OTP";
  $message = "Your OTP for password reset is: $otp";
  $headers = "From: your_email@example.com";

  // Send email
  mail($to, $subject, $message, $headers);

  // Redirect the user to enter the OTP
  header("Location: enter_otp.php");
  exit();
}
?>
</body>
</html>
