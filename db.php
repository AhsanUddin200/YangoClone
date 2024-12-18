<?php
$host = "localhost";
$user = "root";
$pass = "";
$db_name = "yango";

// Database connection
$conn = mysqli_connect($host, $user, $pass, $db_name);

// Check connection
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>
