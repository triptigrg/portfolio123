<?php
// Database connection settings
$servername = "localhost:3307"; // Change if your database server is different
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "portfolio"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully";
}
?>