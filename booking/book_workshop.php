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

// only attendees can book workshops
if (($_SESSION['user_role'] ?? '') !== 'attendee') {
    http_response_code(403);
    die("Access denied. Only attendees can book a class.");
}

// allow only POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Method not allowed.");
}

// get workshop id from form
$workshopId = filter_input(INPUT_POST, 'workshop_id', FILTER_VALIDATE_INT);

// validate workshop id
if (!$workshopId) {
    die("Invalid workshop ID.");
}

// get current user id
$userId = (int)$_SESSION['user_id'];

// start transaction
// this helps prevent overbooking when several users book at the same time
mysqli_begin_transaction($conn);

try {
    // get workshop capacity and current number of bookings
    $workshopSql = "
        SELECT 
            w.id,
            w.capacity,
            COUNT(CASE WHEN b.status = 'booked' THEN 1 END) AS booked_count
        FROM workshops w
        LEFT JOIN bookings b ON w.id = b.workshop_id
        WHERE w.id = ?
        GROUP BY w.id, w.capacity
        FOR UPDATE
    ";

    $workshopStmt = mysqli_prepare($conn, $workshopSql);

    if (!$workshopStmt) {
        throw new Exception(mysqli_error($conn));
    }

    mysqli_stmt_bind_param($workshopStmt, "i", $workshopId);
    mysqli_stmt_execute($workshopStmt);

    $workshopResult = mysqli_stmt_get_result($workshopStmt);
    $workshop = mysqli_fetch_assoc($workshopResult);

    // check if workshop exists
    if (!$workshop) {
        mysqli_rollback($conn);
        die("Workshop not found.");
    }

    // calculate available spots
    $availableSpots = (int)$workshop['capacity'] - (int)$workshop['booked_count'];

    // stop booking if class is full
    if ($availableSpots <= 0) {
        mysqli_rollback($conn);
        header("Location: workshop_details.php?id=$workshopId&error=full");
        exit;
    }

    // check if user already has a booking row for this workshop
    $existingSql = "
        SELECT id, status
        FROM bookings
        WHERE user_id = ?
          AND workshop_id = ?
    ";

    $existingStmt = mysqli_prepare($conn, $existingSql);

    if (!$existingStmt) {
        throw new Exception(mysqli_error($conn));
    }

    mysqli_stmt_bind_param($existingStmt, "ii", $userId, $workshopId);
    mysqli_stmt_execute($existingStmt);

    $existingResult = mysqli_stmt_get_result($existingStmt);
    $existingBooking = mysqli_fetch_assoc($existingResult);

    // if booking is already active, do not book again
    if ($existingBooking && $existingBooking['status'] === 'booked') {
        mysqli_rollback($conn);
        header("Location: workshop_details.php?id=$workshopId&error=already-booked");
        exit;
    }

    // if user cancelled before, reactivate the booking
    if ($existingBooking && $existingBooking['status'] === 'cancelled') {
        $updateSql = "
            UPDATE bookings
            SET status = 'booked',
                booked_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ";

        $updateStmt = mysqli_prepare($conn, $updateSql);

        if (!$updateStmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($updateStmt, "i", $existingBooking['id']);
        mysqli_stmt_execute($updateStmt);

    } else {
        // create new booking
        $insertSql = "
            INSERT INTO bookings (user_id, workshop_id, status)
            VALUES (?, ?, 'booked')
        ";

        $insertStmt = mysqli_prepare($conn, $insertSql);

        if (!$insertStmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($insertStmt, "ii", $userId, $workshopId);
        mysqli_stmt_execute($insertStmt);
    }

    // save transaction
    mysqli_commit($conn);

    // go to my bookings page
    header("Location: my_bookings.php?success=booked");
    exit;

} catch (Exception $e) {
    // undo changes if something went wrong
    mysqli_rollback($conn);

    // fallback for duplicate booking
    if (mysqli_errno($conn) === 1062) {
        header("Location: workshop_details.php?id=$workshopId&error=already-booked");
        exit;
    }

    die("Booking failed: " . htmlspecialchars($e->getMessage()));
}