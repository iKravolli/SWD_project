<?php
session_start();    // Find the current session
session_destroy();  // Wipe all session data aka loging you out of the session
header("Location: login.php"); // Send you back to the login page
exit();
?>