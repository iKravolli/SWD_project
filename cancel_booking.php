<?php
include 'includes/db.php';
session_start();

// Security: Only logged-in Attendees can cancel
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Attendee') {
    die("Access Denied.");
}

// Check if the ID is provided in the URL
if (isset($_GET['id'])) {
    $workshop_id = mysqli_real_escape_string($conn, $_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Delete the specific booking for this user and workshop
    $sql = "DELETE FROM bookings WHERE user_id = '$user_id' AND workshop_id = '$workshop_id'";
    
    if (mysqli_query($conn, $sql)) {
        // Redirect back to the bookings page with a success message
        header("Location: attendee_booking.php?msg=cancelled");
        exit();
    } else {
        echo "Error removing booking: " . mysqli_error($conn);
    }
} else {
    // If no ID is found, just go back to the bookings page
    header("Location: attendee_booking.php");
    exit();
}
?>