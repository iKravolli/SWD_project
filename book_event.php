<?php
// book_event.php - Attendee books an event
// Based on your signup.php (INSERT) and search.php (GET parameter)

require_once 'auth_check.php';
require_once 'db_connect.php';

// Only Attendees can book events
if($_SESSION['role'] != 'Attendee')
{
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : 0;

// Get event details to check availability
$event_query = "SELECT * FROM events WHERE event_id = $event_id";
$event_result = mysqli_query($db_connection, $event_query);

if(mysqli_num_rows($event_result) == 0)
{
    header("Location: events.php?error=event_not_found");
    exit();
}

$event = mysqli_fetch_assoc($event_result);

// Check if event has available spots
if($event['available_spots'] <= 0)
{
    header("Location: events.php?error=no_spots");
    exit();
}

// Check if user already booked this event
$check_query = "SELECT * FROM bookings WHERE user_id = $user_id AND event_id = $event_id AND status = 'Confirmed'";
$check_result = mysqli_query($db_connection, $check_query);

if(mysqli_num_rows($check_result) > 0)
{
    header("Location: events.php?error=already_booked");
    exit();
}

// Process the booking
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['confirm_booking']))
{
    // Start transaction (from your Chapter 14 - Transactions)
    mysqli_begin_transaction($db_connection);
    
    try
    {
        // Insert booking (based on your signup.php INSERT pattern)
        $booking_query = "INSERT INTO bookings (user_id, event_id, booking_date, status) 
                          VALUES ($user_id, $event_id, NOW(), 'Confirmed')";
        $booking_result = mysqli_query($db_connection, $booking_query);
        
        if(!$booking_result)
        {
            throw new Exception("Booking failed");
        }
        
        // Update available spots (decrease by 1)
        $update_query = "UPDATE events SET available_spots = available_spots - 1 WHERE event_id = $event_id";
        $update_result = mysqli_query($db_connection, $update_query);
        
        if(!$update_result)
        {
            throw new Exception("Failed to update spots");
        }
        
        // If everything works, commit (from your Chapter 14)
        mysqli_commit($db_connection);
        
        header("Location: my_bookings.php?booked=true");
        exit();
    }
    catch(Exception $e)
    {
        // If error, rollback (from your Chapter 14)
        mysqli_rollback($db_connection);
        $error_message = "Booking failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Event - Workshop Booking</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .container { width: 500px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        h1 { color: #333; text-align: center; }
        h2 { color: #4CAF50; }
        .event-details { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .event-details p { margin: 10px 0; }
        .btn { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px; }
        .btn:hover { background-color: #45a049; }
        .btn-cancel { background-color: #f44336; }
        .btn-cancel:hover { background-color: #d32f2f; }
        .error { color: red; text-align: center; margin-bottom: 15px; }
        .info { background-color: #dff0d8; color: #3c763d; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Confirm Booking</h1>
        
        <?php if(isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="info">
            Please review the event details before confirming your booking.
        </div>
        
        <h2><?php echo htmlspecialchars($event['event_title']); ?></h2>
        
        <div class="event-details">
            <p><strong>Description:</strong> <?php echo htmlspecialchars($event['event_description']); ?></p>
            <p><strong>Date:</strong> <?php echo $event['event_date']; ?></p>
            <p><strong>Time:</strong> <?php echo $event['event_time']; ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
            <p><strong>Price:</strong> €<?php echo $event['price']; ?></p>
            <p><strong>Spots Available:</strong> <?php echo $event['available_spots']; ?></p>
        </div>
        
        <form method="POST" action="">
            <input type="submit" name="confirm_booking" value="Confirm Booking" class="btn">
            <a href="events.php" class="btn btn-cancel">Cancel</a>
        </form>
    </div>
</body>
</html>

<?php mysqli_close($db_connection); ?>