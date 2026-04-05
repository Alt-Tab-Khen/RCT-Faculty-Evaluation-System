<?php


$servername = "localhost";
$db_username = "root"; // Replace with your database username
$db_password = ""; // Replace with your database password
$dbname = "rct_evel";

// Create a connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>