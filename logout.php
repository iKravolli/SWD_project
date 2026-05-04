<?php
// logout.php - Destroy session and redirect to Homepage

session_start(); // Start session to destroy it

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to Homepage (index.php) - IMPORTANT: Change this line
header("Location: index.php");
exit();
?>