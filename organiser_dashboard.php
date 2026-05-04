<?php
// organiser_dashboard.php - Organiser manages their events
require_once 'auth_check.php';
require_once 'role_check.php';
require_once 'db_connect.php';

// Check if user is Organiser or Admin (Admin can also access)
check_role(array('Organiser', 'Admin'));

include 'navbar.php';

$organiser_id = $_SESSION['user_id'];

// Get all events created by this organiser (based on your search.php pattern)
$query = "SELECT * FROM events WHERE organiser_id = $organiser_id ORDER BY event_date ASC";
$result = mysqli_query($db_connection, $query);

// Count events
$event_count = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Organiser Dashboard - Workshop Booking</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; }
        h1 { color: #333; }
        .stats { background-color: #f0f0f0; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .edit-btn { background-color: #008CBA; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; }
        .delete-btn { background-color: #f44336; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; }
        .create-btn { background-color: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 20px; }
        .no-events { text-align: center; color: #666; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Organiser Dashboard</h1>
        
        <div class="stats">
            <h3>Welcome, <?php echo htmlspecialchars($_SESSION['firstname']); ?>!</h3>
            <p><strong>Total Events Created:</strong> <?php echo $event_count; ?></p>
        </div>
        
        <a href="create_event.php" class="create-btn">+ Create New Event</a>
        
        <h2>Your Events</h2>
        
        <?php if($event_count > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Event Title</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Available Spots</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
                
                <?php while($event = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $event['event_id']; ?></td>
                        <td><?php echo htmlspecialchars($event['event_title']); ?></td>
                        <td><?php echo $event['event_date']; ?></td>
                        <td><?php echo $event['event_time']; ?></td>
                        <td><?php echo htmlspecialchars($event['location']); ?></td>
                        <td><?php echo $event['available_spots']; ?></td>
                        <td>€<?php echo $event['price']; ?></td>
                        <td>
                            <a href="edit_event.php?event_id=<?php echo $event['event_id']; ?>" class="edit-btn">Edit</a>
                            <a href="delete_event.php?event_id=<?php echo $event['event_id']; ?>" class="delete-btn" onclick="return confirm('Delete this event?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <div class="no-events">
                <p>You haven't created any events yet.</p>
                <p>Click the "Create New Event" button to get started!</p>
            </div>
        <?php endif; ?>
        
        <br>
        <a href="index.php">Back to Home</a>
    </div>
</body>
</html>

<?php mysqli_close($db_connection); ?>