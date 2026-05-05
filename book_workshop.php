<?php
include 'includes/db.php';
session_start();

// Security: Only Attendees can book
if ($_SESSION['role'] !== 'Attendee') {
    die("Access Denied. Only students/attendees can book workshops.");
}

if (isset($_GET['id'])) {
    $workshop_id = $_GET['id'];
    $user_id = $_SESSION['user_id']; // This comes from login script

    // Insert the booking into the new table
    $sql = "INSERT INTO bookings (user_id, workshop_id) VALUES ('$user_id', '$workshop_id')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=booked");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>