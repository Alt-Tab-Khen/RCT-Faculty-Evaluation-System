<?php
$admin_password = "rct admin 1923";
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
echo $hashed_password;
?>

<?php
session_start();

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

// Prepare and execute SQL query
$sql = "INSERT INTO admins (email, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$email = 'admin123@gmail.com';
$password = '$2y$10$SREG0k0kz8SJZ/5U9Vh...vMhN5FW/xnfAlTMskFmr7PvVNFTlQ1C';

$stmt->bind_param("ss", $email, $password);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

echo "New record created successfully";

$stmt->close();
$conn->close();
?>