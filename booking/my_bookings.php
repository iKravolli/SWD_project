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

// only attendees can view their bookings
if (($_SESSION['user_role'] ?? '') !== 'attendee') {
    http_response_code(403);
    die("Access denied. Only attendees can view bookings.");
}

// get current user data
$userId = (int)$_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'User';
$userRole = $_SESSION['user_role'] ?? '';

// get all active bookings for current attendee
$sql = "
    SELECT 
        b.id AS booking_id,
        b.booked_at,
        w.id AS workshop_id,
        w.title,
        w.description,
        w.workshop_date
    FROM bookings b
    INNER JOIN workshops w ON b.workshop_id = w.id
    WHERE b.user_id = ?
      AND b.status = 'booked'
    ORDER BY w.workshop_date ASC
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Database error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);

// small helper for short description
function shortText($text, $limit = 120) {
    if (strlen($text) <= $limit) {
        return $text;
    }

    return substr($text, 0, $limit) . '...';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- main css -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="site-header">
    <a href="browse_workshops.php" class="logo">Booking</a>

    <nav class="nav">
        <a href="browse_workshops.php">Workshops</a>
        <a href="my_bookings.php">My Bookings</a>

        <span class="user-pill">
            <?= htmlspecialchars($userName) ?> · <?= htmlspecialchars($userRole) ?>
        </span>

        <a href="../logout.php" class="logout-link">Logout</a>
    </nav>
</header>

<main class="page">

    <section class="hero compact">
        <p class="eyebrow">Attendee Area</p>
        <h1>My Bookings</h1>
        <p>View and manage the classes you have booked.</p>
    </section>

    <!-- success messages -->
    <?php if (isset($_GET['success']) && $_GET['success'] === 'booked'): ?>
        <div class="notice success">Your spot was booked successfully.</div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'cancelled'): ?>
        <div class="notice success">Your booking was cancelled successfully.</div>
    <?php endif; ?>

    <!-- bookings list -->
    <section class="cards-grid">

        <?php if (count($bookings) === 0): ?>
            <div class="empty-state">
                <h2>No bookings yet</h2>
                <p>You have not booked any classes yet.</p>

                <a href="browse_workshops.php" class="btn-primary">
                    Browse Workshops
                </a>
            </div>
        <?php endif; ?>

        <?php foreach ($bookings as $booking): ?>
            <article class="event-card">
                <div class="card-tag">Booked</div>

                <h2><?= htmlspecialchars($booking['title']) ?></h2>

                <p class="event-description">
                    <?= htmlspecialchars(shortText($booking['description'] ?? '')) ?>
                </p>

                <div class="event-meta">
                    <span>
                        <strong>Class Date:</strong>
                        <?= date('d M Y, H:i', strtotime($booking['workshop_date'])) ?>
                    </span>

                    <span>
                        <strong>Booked At:</strong>
                        <?= date('d M Y, H:i', strtotime($booking['booked_at'])) ?>
                    </span>
                </div>

                <div class="card-actions">
                    <a href="workshop_details.php?id=<?= (int)$booking['workshop_id'] ?>" class="btn-secondary">
                        View Class
                    </a>

                    <form method="POST" action="cancel_booking.php">
                        <input type="hidden" name="booking_id" value="<?= (int)$booking['booking_id'] ?>">

                        <button type="submit" class="btn-danger">
                            Cancel Booking
                        </button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>

    </section>

</main>

<footer class="site-footer">
    <p>Griffith College | 2026</p>
</footer>

</body>
</html>