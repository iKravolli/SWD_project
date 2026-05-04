<?php
// edit_event.php - Organiser edits existing events
// Based on your update.php (prepared statements) and search.php (GET parameter)

require_once 'auth_check.php';
require_once 'role_check.php';
require_once 'db_connect.php';

// Only Organiser or Admin can edit events
check_role(array('Organiser', 'Admin'));

include 'navbar.php';

$organiser_id = $_SESSION['user_id'];
$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : 0;

// Initialize variables
$event_title = "";
$event_description = "";
$event_date = "";
$event_time = "";
$location = "";
$available_spots = "";
$price = "";

// Initialize error arrays
$errors = array();
$title_err = "";
$description_err = "";
$date_err = "";
$time_err = "";
$location_err = "";
$spots_err = "";
$price_err = "";
$success_message = "";

// Function to sanitize input (from your Lecture04)
function pass_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = strip_tags($data);
    return $data;
}

// Get event data for display (based on your search.php pattern)
$fetch_query = "SELECT * FROM events WHERE event_id = $event_id";
$fetch_result = mysqli_query($db_connection, $fetch_query);

if(mysqli_num_rows($fetch_result) == 0)
{
    echo "<div class='container'><p style='color:red'>Event not found.</p><a href='organiser_dashboard.php'>Back to Dashboard</a></div>";
    exit();
}

$event = mysqli_fetch_assoc($fetch_result);

// Check if user is the organiser or admin
if($_SESSION['role'] != 'Admin' && $event['organiser_id'] != $organiser_id)
{
    echo "<div class='container'><p style='color:red'>You don't have permission to edit this event.</p><a href='organiser_dashboard.php'>Back to Dashboard</a></div>";
    exit();
}

// Populate form fields with existing data
$event_title = $event['event_title'];
$event_description = $event['event_description'];
$event_date = $event['event_date'];
$event_time = $event['event_time'];
$location = $event['location'];
$available_spots = $event['available_spots'];
$price = $event['price'];

if($_SERVER['REQUEST_METHOD'] == "POST")
{
    // Validate Event Title
    if(empty($_POST['event_title']))
    {
        $title_err = "<span style='color:red'>Event title is required</span>";
        $errors[] = "Event title is required";
    }
    else
    {
        $event_title = pass_input($_POST['event_title']);
        if(strlen($event_title) < 3)
        {
            $title_err = "<span style='color:red'>Event title must be at least 3 characters</span>";
            $errors[] = "Event title too short";
        }
    }
    
    // Validate Event Description
    if(empty($_POST['event_description']))
    {
        $description_err = "<span style='color:red'>Event description is required</span>";
        $errors[] = "Event description is required";
    }
    else
    {
        $event_description = pass_input($_POST['event_description']);
    }
    
    // Validate Event Date
    if(empty($_POST['event_date']))
    {
        $date_err = "<span style='color:red'>Event date is required</span>";
        $errors[] = "Event date is required";
    }
    else
    {
        $event_date = pass_input($_POST['event_date']);
    }
    
    // Validate Event Time
    if(empty($_POST['event_time']))
    {
        $time_err = "<span style='color:red'>Event time is required</span>";
        $errors[] = "Event time is required";
    }
    else
    {
        $event_time = pass_input($_POST['event_time']);
    }
    
    // Validate Location
    if(empty($_POST['location']))
    {
        $location_err = "<span style='color:red'>Location is required</span>";
        $errors[] = "Location is required";
    }
    else
    {
        $location = pass_input($_POST['location']);
    }
    
    // Validate Available Spots
    if(empty($_POST['available_spots']))
    {
        $spots_err = "<span style='color:red'>Number of spots is required</span>";
        $errors[] = "Spots required";
    }
    else
    {
        $available_spots = pass_input($_POST['available_spots']);
        if(!is_numeric($available_spots) || $available_spots < 0)
        {
            $spots_err = "<span style='color:red'>Please enter a valid number</span>";
            $errors[] = "Invalid number of spots";
        }
    }
    
    // Validate Price
    if(empty($_POST['price']))
    {
        $price_err = "<span style='color:red'>Price is required</span>";
        $errors[] = "Price required";
    }
    else
    {
        $price = pass_input($_POST['price']);
        if(!is_numeric($price) || $price < 0)
        {
            $price_err = "<span style='color:red'>Please enter a valid price</span>";
            $errors[] = "Invalid price";
        }
    }
    
    // If no errors, update database (based on your update.php prepared statement pattern)
    if(empty($errors))
    {
        // Using prepared statement (from your update.php example)
        $update_query = "UPDATE events SET event_title = ?, event_description = ?, event_date = ?, event_time = ?, location = ?, available_spots = ?, price = ? WHERE event_id = ?";
        
        $stmt = $db_connection->prepare($update_query);
        $stmt->bind_param("sssssssi", $event_title, $event_description, $event_date, $event_time, $location, $available_spots, $price, $event_id);
        $result = $stmt->execute();
        
        if($result)
        {
            $success_message = "<span style='color:green'>Event updated successfully!</span>";
            echo '<meta http-equiv="refresh" content="2;url=organiser_dashboard.php">';
        }
        else
        {
            $error_message = "<span style='color:red'>Error updating event: " . $stmt->error . "</span>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Event - Workshop Booking</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; }
        h1 { color: #333; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="date"], input[type="time"], input[type="number"], textarea { 
            width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;
        }
        textarea { resize: vertical; }
        input[type="submit"] { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
        input[type="submit"]:hover { background-color: #45a049; }
        .error { color: red; font-size: 14px; margin-top: 5px; }
        .success { color: green; text-align: center; margin-bottom: 15px; padding: 10px; background-color: #dff0d8; border-radius: 4px; }
        .delete-link { text-align: center; margin-top: 20px; }
        .delete-link a { color: #f44336; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Event</h1>
        
        <?php if(isset($success_message) && $success_message != ""): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error_message)) echo $error_message; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?event_id=" . $event_id; ?>">
            <div class="form-group">
                <label>Event Title:</label>
                <input type="text" name="event_title" value="<?php echo htmlspecialchars($event_title); ?>" required>
                <?php echo $title_err; ?>
            </div>
            
            <div class="form-group">
                <label>Event Description:</label>
                <textarea name="event_description" rows="4" required><?php echo htmlspecialchars($event_description); ?></textarea>
                <?php echo $description_err; ?>
            </div>
            
            <div class="form-group">
                <label>Event Date:</label>
                <input type="date" name="event_date" value="<?php echo htmlspecialchars($event_date); ?>" required>
                <?php echo $date_err; ?>
            </div>
            
            <div class="form-group">
                <label>Event Time:</label>
                <input type="time" name="event_time" value="<?php echo htmlspecialchars($event_time); ?>" required>
                <?php echo $time_err; ?>
            </div>
            
            <div class="form-group">
                <label>Location:</label>
                <input type="text" name="location" value="<?php echo htmlspecialchars($location); ?>" required>
                <?php echo $location_err; ?>
            </div>
            
            <div class="form-group">
                <label>Available Spots:</label>
                <input type="number" name="available_spots" min="0" value="<?php echo htmlspecialchars($available_spots); ?>" required>
                <?php echo $spots_err; ?>
            </div>
            
            <div class="form-group">
                <label>Price (€):</label>
                <input type="number" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($price); ?>" required>
                <?php echo $price_err; ?>
            </div>
            
            <div class="form-group">
                <input type="submit" value="Update Event">
            </div>
        </form>
        
        <div class="delete-link">
            <a href="delete_event.php?event_id=<?php echo $event_id; ?>" onclick="return confirm('Are you sure you want to delete this event?');">Delete this event</a>
        </div>
        
        <br>
        <a href="organiser_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>

<?php mysqli_close($db_connection); ?>