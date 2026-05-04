<?php
// cancel_booking.php - Attendee cancels a booking
// Based on your delete.php from Prepared Statements lecture

require_once 'auth_check.php';
require_once 'db_connect.php';

// Only Attendees can cancel bookings
if($_SESSION['role'] != 'Attendee')
{
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : 0;

// Get booking details to update event spots
$booking_query = "SELECT * FROM bookings WHERE booking_id = $booking_id AND user_id = $user_id";
$booking_result = mysqli_query($db_connection, $booking_query);

if(mysqli_num_rows($booking_result) == 0)
{
    header("Location: my_bookings.php?error=booking_not_found");
    exit();
}

$booking = mysqli_fetch_assoc($booking_result);
$event_id = $booking['event_id'];

// Process cancellation (based on your delete.php prepared statement pattern)
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['confirm_cancel']))
{
    // Start transaction (from your Chapter 14 - Transactions)
    mysqli_begin_transaction($db_connection);
    
    try
    {
        // Cancel booking - update status (instead of delete, to keep history)
        $cancel_query = "UPDATE bookings SET status = 'Cancelled' WHERE booking_id = $booking_id AND user_id = $user_id";
        $cancel_result = mysqli_query($db_connection, $cancel_query);
        
        if(!$cancel_result)
        {
            throw new Exception("Cancellation failed");
        }
        
        // Update available spots (increase by 1)
        $update_query = "UPDATE events SET available_spots = available_spots + 1 WHERE event_id = $event_id";
        $update_result = mysqli_query($db_connection, $update_query);
        
        if(!$update_result)
        {
            throw new Exception("Failed to update spots");
        }
        
        // If everything works, commit (from your Chapter 14)
        mysqli_commit($db_connection);
        
        header("Location: my_bookings.php?cancelled=true");
        exit();
    }
    catch(Exception $e)
    {
        // If error, rollback (from your Chapter 14)
        mysqli_rollback($db_connection);
        $error_message = "Cancellation failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cancel Booking - Workshop Booking</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .container { width: 500px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; text-align: center; }
        h1 { color: #f44336; }
        .warning { background-color: #f2dede; color: #a94442; padding: 15px; border-radius: 4px; margin: 20px 0; }
        .btn { background-color: #f44336; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px; }
        .btn:hover { background-color: #d32f2f; }
        .btn-cancel { background-color: #4CAF50; }
        .btn-cancel:hover { background-color: #45a049; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Cancel Booking</h1>
        
        <?php if(isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="warning">
            <p><strong>Warning!</strong></p>
            <p>Are you sure you want to cancel this booking?</p>
            <p>This action cannot be undone.</p>
        </div>
        
        <form method="POST" action="">
            <input type="submit" name="confirm_cancel" value="Yes, Cancel Booking" class="btn">
            <a href="my_bookings.php" class="btn btn-cancel">No, Go Back</a>
        </form>
    </div>
</body>
</html>

<?php mysqli_close($db_connection); ?>