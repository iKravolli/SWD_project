<?php

// start user session
session_start();

// connect to database
require_once '../includes/db.php';

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// only attendees can cancel bookings
if (($_SESSION['user_role'] ?? '') !== 'attendee') {
    http_response_code(403);
    die("Access denied. Only attendees can cancel bookings.");
}

// allow only POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Method not allowed.");
}

// get booking id from form
$bookingId = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);

// validate booking id
if (!$bookingId) {
    die("Invalid booking ID.");
}

// get current user id
$userId = (int)$_SESSION['user_id'];

// cancel only current user's own booking
$sql = "
    UPDATE bookings
    SET status = 'cancelled'
    WHERE id = ?
      AND user_id = ?
      AND status = 'booked'
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Database error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "ii", $bookingId, $userId);
mysqli_stmt_execute($stmt);

// go back to my bookings page
header("Location: my_bookings.php?success=cancelled");
exit;