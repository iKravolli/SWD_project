<?php
// delete_event.php - Organiser deletes events
// Based on your delete.php from Prepared Statements lecture

require_once 'auth_check.php';
require_once 'role_check.php';
require_once 'db_connect.php';

// Only Organiser or Admin can delete events
check_role(array('Organiser', 'Admin'));

include 'navbar.php';

$organiser_id = $_SESSION['user_id'];
$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : 0;

// Check if event exists and user has permission (based on your search.php pattern)
$check_query = "SELECT * FROM events WHERE event_id = $event_id";
$check_result = mysqli_query($db_connection, $check_query);

if(mysqli_num_rows($check_result) == 0)
{
    $error_message = "Event not found.";
}
else
{
    $event = mysqli_fetch_assoc($check_result);
    
    // Check if user is the organiser or admin
    if($_SESSION['role'] != 'Admin' && $event['organiser_id'] != $organiser_id)
    {
        $error_message = "You don't have permission to delete this event.";
    }
    else
    {
        $event_title = $event['event_title'];
        
        // Process deletion when confirmed (based on your delete.php pattern)
        if(isset($_GET['confirm']) && $_GET['confirm'] == 'yes')
        {
            // Start transaction (from your Chapter 14)
            mysqli_begin_transaction($db_connection);
            
            try
            {
                // First delete related bookings (foreign key constraint)
                $delete_bookings = "DELETE FROM bookings WHERE event_id = $event_id";
                $bookings_result = mysqli_query($db_connection, $delete_bookings);
                
                if(!$bookings_result)
                {
                    throw new Exception("Failed to delete related bookings");
                }
                
                // Then delete the event
                $delete_event = "DELETE FROM events WHERE event_id = $event_id";
                $event_result = mysqli_query($db_connection, $delete_event);
                
                if(!$event_result)
                {
                    throw new Exception("Failed to delete event");
                }
                
                // If everything works, commit (from your Chapter 14)
                mysqli_commit($db_connection);
                
                $success_message = "Event deleted successfully!";
                echo '<meta http-equiv="refresh" content="2;url=organiser_dashboard.php">';
            }
            catch(Exception $e)
            {
                // If error, rollback (from your Chapter 14)
                mysqli_rollback($db_connection);
                $error_message = "Deletion failed: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Event - Workshop Booking</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 500px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; text-align: center; background-color: #f9f9f9; }
        h1 { color: #f44336; }
        .warning { background-color: #f2dede; color: #a94442; padding: 15px; border-radius: 4px; margin: 20px 0; }
        .event-details { background-color: #f0f0f0; padding: 10px; border-radius: 4px; margin: 15px 0; }
        .btn { background-color: #f44336; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px; text-decoration: none; display: inline-block; }
        .btn:hover { background-color: #d32f2f; }
        .btn-cancel { background-color: #4CAF50; }
        .btn-cancel:hover { background-color: #45a049; }
        .success { color: green; text-align: center; margin-bottom: 15px; padding: 10px; background-color: #dff0d8; border-radius: 4px; }
        .error { color: red; text-align: center; margin-bottom: 15px; padding: 10px; background-color: #f2dede; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Delete Event</h1>
        
        <?php if(isset($success_message)): ?>
            <div class="success"><?php echo $success_message; ?></div>
            <br>
            <a href="organiser_dashboard.php" class="btn btn-cancel">Back to Dashboard</a>
        <?php elseif(isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
            <br>
            <a href="organiser_dashboard.php" class="btn btn-cancel">Back to Dashboard</a>
        <?php else: ?>
            <div class="warning">
                <p><strong>Warning!</strong></p>
                <p>Are you sure you want to delete this event?</p>
                <div class="event-details">
                    <p><strong>Event:</strong> <?php echo htmlspecialchars($event_title); ?></p>
                </div>
                <p><strong>This action cannot be undone and will also delete all bookings for this event!</strong></p>
            </div>
            
            <a href="delete_event.php?event_id=<?php echo $event_id; ?>&confirm=yes" class="btn">Yes, Delete Event</a>
            <a href="organiser_dashboard.php" class="btn btn-cancel">No, Go Back</a>
        <?php endif; ?>
    </div>
</body>
</html>

<?php mysqli_close($db_connection); ?>