<?php
// db.php - Database connection file
// Creates a MySQLi connection used across the application.

// Database credentials
$host = "localhost"; // hostname (usually localhost)
$user = "root";      // database username
$pass = "";          // database password
$db   = "OETMS";     // database name

// Establish connection to MySQL
$conn = new mysqli($host, $user, $pass, $db);

// Stop execution if connection fails
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>