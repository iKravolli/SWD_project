<?php
include 'includes/db.php';
session_start();

// SECURITY: Only allow logged-in Organisers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Organiser') {
    die("Access Denied. <a href='index.php'>Back to Home</a>");
}

$errors = []; // Array to store validation errors

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Get and Sanitize data
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $desc = mysqli_real_escape_string($conn, trim($_POST['description']));
    $date = mysqli_real_escape_string($conn, $_POST['workshop_date']);
    
    //SERVER-SIDE VALIDATION
    if (empty($title)) {
        $errors[] = "Workshop title cannot be left blank.";
    }
    if (strlen($desc) < 10) {
        $errors[] = "The description must be at least 10 characters long.";
    }
    if (empty($date)) {
        $errors[] = "Please select a date and time.";
    } elseif (strtotime($date) < time()) {
        $errors[] = "You cannot schedule a workshop in the past.";
    }

    // 3. Only Insert if there are no errors
    if (empty($errors)) {
        $sql = "INSERT INTO workshops (title, description, workshop_date) 
                VALUES ('$title', '$desc', '$date')";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?msg=added");
            exit(); 
        } else {
            $errors[] = "Database Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Organiser - Add Workshop</title>
    <!-- Link to your CSS file -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <div class="logo">Workshop Portal</div>
    <nav class="nav">
        <span class="user-pill">Organiser Mode</span>
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="logout.php" class="nav-link logout-link">Logout</a>
    </nav>
</header>

<main class="page">
    <section class="hero compact">
        <p class="eyebrow">Organiser Tools</p>
        <h1>Create a New Workshop</h1>
        <p>Fill out the details below to publish a new session for attendees.</p>
    </section>

    <div class="table-wrapper" style="padding: 40px; max-width: 700px; margin: 20px auto;">
        
        <!-- display current errors to the user -->
        <?php if (!empty($errors)): ?>
            <div class="notice" style="color: #d9534f; background: #f9f2f2; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #ebccd1;">
                <strong>Please fix the following:</strong>
                <ul style="margin-top: 10px; margin-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="add_workshop.php">
            <div style="margin-bottom: 15px;">
                <label>Workshop Title:</label><br>
                <input type="text" name="title" style="width: 100%; padding: 10px; margin-top: 5px;" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" placeholder="e.g. Intro to PHP" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label>Description:</label><br>
                <textarea name="description" rows="5" style="width: 100%; padding: 10px; margin-top: 5px; font-family: inherit;" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>

            <div style="margin-bottom: 25px;">
                <label>Date and Time:</label><br>
                <input type="datetime-local" name="workshop_date" style="width: 100%; padding: 10px; margin-top: 5px;" value="<?php echo isset($_POST['workshop_date']) ? $_POST['workshop_date'] : ''; ?>" required>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; border: none; cursor: pointer;">+ Publish Workshop</button>
        </form>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="index.php" class="nav-link">⬅ Back to Workshop List</a>
        </div>
    </div>
</main>

<footer class="site-footer">
    &copy; 2026 Workshop Management System
</footer>

</body>
</html>