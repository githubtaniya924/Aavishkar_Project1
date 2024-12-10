<?php
// Database credentials
$host = "localhost:3307";
$dbname = "EmployeeData";
$username = "root";
$password = "";

// Create connection using mysqli
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // Connection is successful
    // echo "Connected successfully to the database"; // For testing
}
?>
