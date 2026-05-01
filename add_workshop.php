<?php
include 'includes/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Get the data from the form
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $date = $_POST['workshop_date'];
    
    // 2. Insert into the database
    $sql = "INSERT INTO workshops (title, description, workshop_date) 
            VALUES ('$title', '$desc', '$date')";
    
    if (mysqli_query($conn, $sql)) {
        $message = "<p style='color:green;'>Successful Workshop was added to the database.</p>";
    } else {
        $message = "<p style='color:red;'>Error: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Organiser - Add Workshop</title>
</head>
<body>
    <h1>Create a New Workshop</h1>
    <?php echo $message; ?>

    <form method="POST" action="">
        <label>Workshop Title:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" rows="4" cols="50"></textarea><br><br>

        <label>Date and Time:</label><br>
        <input type="datetime-local" name="workshop_date" required><br><br>

        <button type="submit">Add Workshop</button>
    </form>
    
    <br>
    <a href="check.php">Test Connection Again</a>
</body>
</html>