<?php
// Connect to the database and start the session to remember the user
include 'includes/db.php';
session_start();

// If the user isn't logged in, kick them back to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get all workshops from the database, newest ones first
$sql = "SELECT * FROM workshops ORDER BY workshop_date DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Workshop Manager</title>
    <!-- Connect to the CSS file for styling -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <div class="logo">Workshop Portal</div>
    <nav class="nav">
        <!-- Show what role the logged-in user has -->
        <span class="user-pill">Logged in as: <?php echo $_SESSION['role']; ?></span>
        
        <!-- Only show the Admin link if the user is an Admin -->
        <?php if ($_SESSION['role'] === 'Admin'): ?>
            <a href="admin_control.php" class="nav-link">⚙️ Admin</a>
        <?php endif; ?>
        
        <!-- Only show My Bookings if the user is an Attendee -->
        <?php if ($_SESSION['role'] === 'Attendee'): ?>
            <a href="attendee_booking.php" class="nav-link">📅 My Bookings</a>
        <?php endif; ?>
        
        <a href="logout.php" class="nav-link logout-link">Logout</a>
    </nav>
</header>

<main class="page">
    <section class="hero compact">
        <p class="eyebrow">Dashboard</p>
        <h1>Available Workshops</h1>
        <p>Browse and manage the latest educational sessions.</p>
        
        <!-- Only Organisers get the button to create a new workshop -->
        <?php if ($_SESSION['role'] === 'Organiser'): ?>
            <br>
            <a href="add_workshop.php" class="btn-primary">+ Add New Workshop</a>
        <?php endif; ?>
    </section>

    <!-- Check for "success" messages in the URL and display them -->
    <?php if(isset($_GET['msg'])): ?>
        <div class="notice success">
            <?php 
                if($_GET['msg'] == 'added') echo "Workshop created successfully!";
                if($_GET['msg'] == 'deleted') echo "Workshop removed from system.";
                if($_GET['msg'] == 'booked') echo "Booking confirmed!";
            ?>
        </div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table class="main-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop through every workshop found in the database -->
                <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <!-- Format the database date into something easy to read -->
                    <td><?php echo date('M d, Y - H:i', strtotime($row['workshop_date'])); ?></td>
<td>
                        <?php if ($_SESSION['role'] === 'Organiser'): ?>
                            <!-- Organisers see a Delete button -->
                            <a href="delete_workshop.php?id=<?php echo $row['id']; ?>" 
                               onclick="return confirm('Are you sure?')" 
                               class="btn-danger">Delete</a>

                        <?php elseif ($_SESSION['role'] === 'Admin'): ?>
                            <!-- Admins see a remove button-->
                            <a href="delete_workshop.php?id=<?php echo $row['id']; ?>" 
                               onclick="return confirm('Admin: Permanently delete this workshop?')" 
                               class="btn-danger" 
                               style="background-color: #bb2d3b; color: white !important; text-decoration: none; display: inline-block;">Remove</a>

                        <?php elseif ($_SESSION['role'] === 'Attendee'): ?>
                            <!-- Attendees see a Book Now button -->
                            <a href="book_workshop.php?id=<?php echo $row['id']; ?>" 
                               class="btn-small">Book Now</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</main>

<footer class="site-footer">
    &copy; 2026 Workshop Management System
</footer>

</body>
</html>