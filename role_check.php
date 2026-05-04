<?php
// Role check - include after auth_check.php
// Based on your project brief - Role-Based Access Control

function check_role($allowed_roles)
{
    // Check if user is logged in first (session should already be started by auth_check.php)
    if(!isset($_SESSION['user_id']))
    {
        header("Location: login.php");
        exit();
    }
    
    $user_role = $_SESSION['role'];
    
    // Convert single role to array for easier checking
    if(!is_array($allowed_roles))
    {
        $allowed_roles = array($allowed_roles);
    }
    
    // Check if user's role is allowed
    if(!in_array($user_role, $allowed_roles))
    {
        // Not authorized - show error message and redirect
        echo "<h2>Access Denied</h2>";
        echo "<p>You do not have permission to access this page.</p>";
        echo "<a href='index.php'>Go back to Home</a>";
        exit();
    }
}

// Example usage at top of pages:
// require_once 'auth_check.php';
// check_role('Admin'); // Only Admin
// check_role(array('Admin', 'Organiser')); // Admin OR Organiser
?>