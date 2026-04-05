<?php
session_start();

// Destroy all sessions
session_destroy();

// Set headers to prevent back button access after logout
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Redirect to instructor login page
header("Location: login_admin/login.php");
exit();
?>
