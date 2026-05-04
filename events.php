<?php
// events.php - Browse events (updated with session checks)
require_once 'auth_check.php'; // Make sure user is logged in
require_once 'db_connect.php';
include 'navbar.php';

// Get user role for customization
$user_role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// Get all events (based on your search.php pattern)
$query = "SELECT * FROM events ORDER BY event_date ASC";
$result = mysqli_query($db_connection, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Browse Events - Workshop Booking</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; }
        h1 { color: #333; }
        .events-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
        .event-card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; background-color: #f9f9f9; }
        .event-card h3 { margin-top: 0; color: #4CAF50; }
        .event-card p { margin: 10px 0; }
        .event-card .details { color: #666; font-size: 14px; }
        .book-btn { background-color: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block; margin-top: 10px; }
        .book-btn:hover { background-color: #45a049; }
        .edit-btn { background-color: #008CBA; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block; margin-top: 10px; margin-right: 10px; }
        .delete-btn { background-color: #f44336; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block; margin-top: 10px; }
        .no-events { text-align: center; color: #666; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Available Workshops & Events</h1>
        
        <?php if(mysqli_num_rows($result) > 0): ?>
            <div class="events-grid">
                <?php while($event = mysqli_fetch_assoc($result)): ?>
                    <div class="event-card">
                        <h3><?php echo htmlspecialchars($event['event_title']); ?></h3>
                        <p><?php echo htmlspecialchars($event['event_description']); ?></p>
                        <div class="details">
                            <p><strong>Date:</strong> <?php echo $event['event_date']; ?></p>
                            <p><strong>Time:</strong> <?php echo $event['event_time']; ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                            <p><strong>Available Spots:</strong> <?php echo $event['available_spots']; ?></p>
                            <p><strong>Price:</strong> €<?php echo $event['price']; ?></p>
                        </div>
                        
                        <?php if($user_role == 'Attendee'): ?>
                            <a href="book_event.php?event_id=<?php echo $event['event_id']; ?>" class="book-btn">Book Now</a>
                        <?php elseif($user_role == 'Organiser' && $event['organiser_id'] == $user_id): ?>
                            <a href="edit_event.php?event_id=<?php echo $event['event_id']; ?>" class="edit-btn">Edit</a>
                            <a href="delete_event.php?event_id=<?php echo $event['event_id']; ?>" class="delete-btn" onclick="return confirm('Delete this event?');">Delete</a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-events">
                <p>No events available at the moment. Please check back later!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php mysqli_close($db_connection); ?>