<?php
include 'includes/db.php';
session_start();

// Security: Only logged-in users can see their bookings
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// SQL Join: Added workshops id so we can use it for the cancel button link
$sql = "SELECT workshops.id as workshop_id, workshops.title, workshops.description, workshops.workshop_date 
        FROM workshops 
        JOIN bookings ON workshops.id = bookings.workshop_id 
        WHERE bookings.user_id = $user_id";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Bookings - Workshop Portal</title>
    <!-- Connecting the CSS file -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <div class="logo">Workshop Portal</div>
    <nav class="nav">
        <span class="user-pill">Logged in as: <?php echo $_SESSION['role']; ?></span>
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="logout.php" class="nav-link logout-link">Logout</a>
    </nav>
</header>

<main class="page">
    <section class="hero compact">
        <p class="eyebrow">Attendee Portal</p>
        <h1>My Booked Workshops</h1>
        <p>Review the sessions you have registered for below.</p>
    </section>

    <!-- Display success message if a booking was cancelled -->
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'cancelled'): ?>
        <div class="notice success" style="margin-bottom: 20px;">
            Your booking has been successfully cancelled.
        </div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table class="main-table">
            <thead>
                <tr>
                    <th>Workshop Title</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo date('M d, Y - H:i', strtotime($row['workshop_date'])); ?></td>
                        <td>
                            <!-- The Cancel Button -->
                        <a href="cancel_booking.php?id=<?php echo $row['workshop_id']; ?>" 
                        onclick="return confirm('Are you sure you want to cancel this booking?')" 
                        style="background-color: #d9534f !important; color: white !important; text-decoration: none !important; padding: 8px 15px !important; border-radius: 4px !important; display: inline-block !important; font-size: 14px !important; font-weight: bold !important;">
                        Cancel Booking
                        </a>
                        </td>
                    </tr>
                    <?php } ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px; color: #666;">
                            You haven't booked any workshops yet. <br><br>
                            <a href="index.php" class="btn-small">Browse Workshops</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 20px; text-align: center;">
        <a href="index.php" class="nav-link">⬅ Back to All Workshops</a>
    </div>
</main>

<footer class="site-footer">
    &copy; 2026 Workshop Management System
</footer>

</body>
</html>