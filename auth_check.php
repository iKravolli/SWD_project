<?php
// Authentication check - include this at the top of protected pages
// Based on your Sessions lecture (session_start() and checking $_SESSION)

session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id']))
{
    // User not logged in - redirect to login page
    header("Location: login.php");
    exit();
}
?>