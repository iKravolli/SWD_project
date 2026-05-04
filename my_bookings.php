<?php
// my_bookings.php - Attendee views their booked events
require_once 'auth_check.php';
require_once 'db_connect.php';
include 'navbar.php';

// Only Attendees should access this
if($_SESSION['role'] != 'Attendee')
{
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's bookings with event details (JOIN from your SQL lecture)
$query = "SELECT b.*, e.event_title, e.event_date, e.event_time, e.location, e.price 
          FROM bookings b 
          JOIN events e ON b.event_id = e.event_id 
          WHERE b.user_id = $user_id 
          ORDER BY b.booking_date DESC";

$result = mysqli_query($db_connection, $query);
$booking_count = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings - Workshop Booking</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .cancel-btn { background-color: #f44336; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; }
        .no-bookings { text-align: center; color: #666; margin-top: 50px; }
        .status-confirmed { color: green; font-weight: bold; }
        .status-cancelled { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Bookings</h1>
        
        <?php if($booking_count > 0): ?>
            <table>
                <tr>
                    <th>Event</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Price</th>
                    <th>Booking Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                
                <?php while($booking = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['event_title']); ?></td>
                        <td><?php echo $booking['event_date']; ?></td>
                        <td><?php echo $booking['event_time']; ?></td>
                        <td><?php echo htmlspecialchars($booking['location']); ?></td>
                        <td>€<?php echo $booking['price']; ?></td>
                        <td><?php echo $booking['booking_date']; ?></td>
                        <td class="status-<?php echo strtolower($booking['status']); ?>"><?php echo $booking['status']; ?></td>
                        <td>
                            <?php if($booking['status'] == 'Confirmed'): ?>
                                <a href="cancel_booking.php?booking_id=<?php echo $booking['booking_id']; ?>" 
                                   class="cancel-btn" 
                                   onclick="return confirm('Cancel this booking?');">Cancel</a>
                            <?php else: ?>
                                <span style="color:gray;">Cancelled</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <div class="no-bookings">
                <p>You haven't booked any events yet.</p>
                <p><a href="events.php">Browse Events</a> to book your first workshop!</p>
            </div>
        <?php endif; ?>
        
        <br>
        <a href="events.php">Back to Events</a>
    </div>
</body>
</html>

<?php mysqli_close($db_connection); ?>