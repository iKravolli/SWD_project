<?php
// create_event.php - Organiser creates new events
// Based on your signup.php (INSERT), self_form.php (validation), and Chapter 14 (PDO/Prepared Statements)

require_once 'auth_check.php';
require_once 'role_check.php';
require_once 'db_connect.php';

// Only Organiser or Admin can create events
check_role(array('Organiser', 'Admin'));

include 'navbar.php';

// Initialize variables for sticky form
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
        if(!is_numeric($available_spots) || $available_spots < 1)
        {
            $spots_err = "<span style='color:red'>Please enter a valid number (at least 1)</span>";
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
    
    // If no errors, insert into database (based on your signup.php pattern)
    if(empty($errors))
    {
        $organiser_id = $_SESSION['user_id'];
        
        // Using MySQLi query (from your signup.php pattern)
        $insert_query = "INSERT INTO events (organiser_id, event_title, event_description, event_date, event_time, location, available_spots, price, created_at) 
                         VALUES ('$organiser_id', '$event_title', '$event_description', '$event_date', '$event_time', '$location', '$available_spots', '$price', NOW())";
        
        $result = mysqli_query($db_connection, $insert_query);
        
        if($result)
        {
            $success_message = "<span style='color:green'>Event created successfully!</span>";
            // Clear form fields after successful submission
            $event_title = "";
            $event_description = "";
            $event_date = "";
            $event_time = "";
            $location = "";
            $available_spots = "";
            $price = "";
            
            // Redirect after 2 seconds (optional)
            echo '<meta http-equiv="refresh" content="2;url=organiser_dashboard.php">';
        }
        else
        {
            $error_message = "<span style='color:red'>Error creating event: " . mysqli_error($db_connection) . "</span>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Event - Workshop Booking</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; }
        h1 { color: #333; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="date"], input[type="time"], input[type="number"], textarea, select { 
            width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;
        }
        textarea { resize: vertical; }
        input[type="submit"] { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
        input[type="submit"]:hover { background-color: #45a049; }
        .error { color: red; font-size: 14px; margin-top: 5px; }
        .success { color: green; text-align: center; margin-bottom: 15px; padding: 10px; background-color: #dff0d8; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create New Event</h1>
        
        <?php if(isset($success_message) && $success_message != ""): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error_message)) echo $error_message; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
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
                <input type="number" name="available_spots" min="1" value="<?php echo htmlspecialchars($available_spots); ?>" required>
                <?php echo $spots_err; ?>
            </div>
            
            <div class="form-group">
                <label>Price (€):</label>
                <input type="number" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($price); ?>" required>
                <?php echo $price_err; ?>
            </div>
            
            <div class="form-group">
                <input type="submit" value="Create Event">
            </div>
        </form>
        
        <br>
        <a href="organiser_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>

<?php mysqli_close($db_connection); ?>