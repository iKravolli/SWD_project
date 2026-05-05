<?php
include 'includes/db.php';
session_start();

// Security: Match your Organiser role exactly
if ($_SESSION['role'] !== 'Organiser') {
    die("Access Denied. Your current role is: " . $_SESSION['role']);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Perform the deletion
    $sql = "DELETE FROM workshops WHERE id = $id"; 
    
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=deleted"); 
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "No ID found.";
}
?>