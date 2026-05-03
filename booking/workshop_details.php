<?php
// booking/workshop_details.php

// start user session
session_start();

// connect to database
require_once '../includes/db.php';

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// get current user data
$userId = (int)$_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'User';
$userRole = $_SESSION['user_role'] ?? '';

// get workshop id from URL
$workshopId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// validate workshop id
if (!$workshopId) {
    die("Invalid workshop ID.");
}

// get selected workshop with available spots
$sql = "
    SELECT 
        w.id,
        w.title,
        w.description,
        w.workshop_date,
        w.capacity,
        COUNT(CASE WHEN b.status = 'booked' THEN 1 END) AS booked_count,
        w.capacity - COUNT(CASE WHEN b.status = 'booked' THEN 1 END) AS available_spots
    FROM workshops w
    LEFT JOIN bookings b ON w.id = b.workshop_id
    WHERE w.id = ?
    GROUP BY w.id, w.title, w.description, w.workshop_date, w.capacity
";

// prepare SQL query
$stmt = mysqli_prepare($conn, $sql);

// check query error
if (!$stmt) {
    die("Database error: " . mysqli_error($conn));
}

// bind workshop id
mysqli_stmt_bind_param($stmt, "i", $workshopId);

// run query
mysqli_stmt_execute($stmt);

// get workshop
$result = mysqli_stmt_get_result($stmt);
$workshop = mysqli_fetch_assoc($result);

// stop if workshop does not exist
if (!$workshop) {
    die("Workshop not found.");
}

// check if attendee already booked this workshop
$bookingSql = "
    SELECT id
    FROM bookings
    WHERE user_id = ?
      AND workshop_id = ?
      AND status = 'booked'
";

$bookingStmt = mysqli_prepare($conn, $bookingSql);

if (!$bookingStmt) {
    die("Database error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($bookingStmt, "ii", $userId, $workshopId);
mysqli_stmt_execute($bookingStmt);

$bookingResult = mysqli_stmt_get_result($bookingStmt);
$alreadyBooked = mysqli_fetch_assoc($bookingResult) !== null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($workshop['title']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- main css -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="site-header">
    <a href="browse_workshops.php" class="logo">Booking</a>

    <nav class="nav">
        <a href="browse_workshops.php">Workshops</a>

        <?php if ($userRole === 'attendee'): ?>
            <a href="my_bookings.php">My Bookings</a>
        <?php endif; ?>

        <?php if ($userRole === 'organiser'): ?>
            <a href="../add_workshop.php">Add Workshop</a>
        <?php endif; ?>

        <span class="user-pill">
            <?= htmlspecialchars($userName) ?> · <?= htmlspecialchars($userRole) ?>
        </span>

        <a href="../logout.php" class="logout-link">Logout</a>
    </nav>
</header>

<main class="page">

    <section class="details-layout">
        <article class="details-card">
            <div class="card-tag">Class Details</div>

            <h1><?= htmlspecialchars($workshop['title']) ?></h1>

            <p class="details-description">
                <?= nl2br(htmlspecialchars($workshop['description'] ?? '')) ?>
            </p>

            <div class="details-list">
                <div>
                    <span>Date and Time</span>
                    <strong><?= date('d M Y, H:i', strtotime($workshop['workshop_date'])) ?></strong>
                </div>

                <div>
                    <span>Available Spots</span>
                    <strong>
                        <?= (int)$workshop['available_spots'] ?> / <?= (int)$workshop['capacity'] ?>
                    </strong>
                </div>
            </div>

            <!-- error messages -->
            <?php if (isset($_GET['error']) && $_GET['error'] === 'full'): ?>
                <div class="notice error">This class is fully booked.</div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'already-booked'): ?>
                <div class="notice error">You have already booked this class.</div>
            <?php endif; ?>

            <!-- booking area -->
            <?php if ($userRole === 'attendee'): ?>

                <?php if ($alreadyBooked): ?>
                    <div class="notice success">
                        You have already booked a spot in this class.
                    </div>

                <?php elseif ((int)$workshop['available_spots'] <= 0): ?>
                    <div class="notice error">
                        This class is fully booked.
                    </div>

                <?php else: ?>
                    <form method="POST" action="book_workshop.php">
                        <input type="hidden" name="workshop_id" value="<?= (int)$workshop['id'] ?>">

                        <button type="submit" class="btn-primary">
                            Book a Spot
                        </button>
                    </form>
                <?php endif; ?>

            <?php else: ?>
                <div class="notice">
                    Only attendees can book a spot in a class.
                </div>
            <?php endif; ?>

            <a href="browse_workshops.php" class="btn-secondary">Back to Workshops</a>
        </article>
    </section>

</main>

<footer class="site-footer">
    <p>Griffith College | 2026</p>
</footer>

</body>
</html>