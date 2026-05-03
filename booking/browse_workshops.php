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

// get current user data
$userName = $_SESSION['user_name'] ?? 'User';
$userRole = $_SESSION['user_role'] ?? '';

// get search text from URL
$search = trim($_GET['search'] ?? '');

// get all workshops and count available spots
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
    WHERE w.title LIKE ?
       OR w.description LIKE ?
    GROUP BY w.id, w.title, w.description, w.workshop_date, w.capacity
    ORDER BY w.workshop_date ASC
";

// prepare SQL query
$stmt = mysqli_prepare($conn, $sql);

// check query error
if (!$stmt) {
    die("Database error: " . mysqli_error($conn));
}

// bind search value
$searchParam = '%' . $search . '%';
mysqli_stmt_bind_param($stmt, "ss", $searchParam, $searchParam);

// run query
mysqli_stmt_execute($stmt);

// get result
$result = mysqli_stmt_get_result($stmt);
$workshops = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
    <title>Browse Workshops</title>
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

    <section class="hero">
        <p class="eyebrow">Workshop Booking Site</p>
        <h1>Browse Available Classes</h1>
        <p>Find cooking classes, gym sessions, photography workshops and other learning events.</p>
    </section>

    <!-- search form -->
    <section class="toolbar">
        <form method="GET" action="browse_workshops.php" class="search-form">
            <input 
                type="text" 
                name="search" 
                placeholder="Search workshops by title or description..."
                value="<?= htmlspecialchars($search) ?>"
            >

            <button type="submit">Search</button>
        </form>
    </section>

    <!-- workshops grid -->
    <section class="cards-grid">

        <?php if (count($workshops) === 0): ?>
            <div class="empty-state">
                <h2>No workshops found</h2>
                <p>Try searching for another class.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($workshops as $workshop): ?>
            <article class="event-card">
                <div class="card-tag">Workshop</div>

                <h2><?= htmlspecialchars($workshop['title']) ?></h2>

                <p class="event-description">
                    <?= htmlspecialchars(shortText($workshop['description'] ?? '')) ?>
                </p>

                <div class="event-meta">
                    <span>
                        <strong>Date:</strong>
                        <?= date('d M Y, H:i', strtotime($workshop['workshop_date'])) ?>
                    </span>

                    <span>
                        <strong>Available spots:</strong>
                        <?= (int)$workshop['available_spots'] ?> / <?= (int)$workshop['capacity'] ?>
                    </span>
                </div>

                <a href="workshop_details.php?id=<?= (int)$workshop['id'] ?>" class="btn-primary">
                    View Details
                </a>
            </article>
        <?php endforeach; ?>

    </section>

</main>

<footer class="site-footer">
    <p>Griffith College | 2026</p>
</footer>

</body>
</html>