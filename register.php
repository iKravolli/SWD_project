<?php
include 'includes/db.php';

$errors = []; // Array to store validation errors

// Only run this code if the user clicks the Register button
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. get data from user and Sanitize it
    $email = mysqli_real_escape_string($conn, trim($_POST['email'])); 
    $password = $_POST['password']; 
    
    // 2. SERVER-SIDE VALIDATION
    
    // Check if email is a valid format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

 // Check for: 1 Uppercase, 1 Lowercase, 1 Number, 1 Special Char, and Min 8 length
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
    $errors[] = "Password must be at least 8 characters long and include an uppercase letter, lowercase letter, a number, and a special character.";
}

    // Check if the email already exists in the database
    $check_query = "SELECT id FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($result) > 0) {
        $errors[] = "This email is already registered. Please login or use a different email.";
    }

    // This turns "password123" into a 60-character secure hash.
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 4. Only Insert if there are no errors
    if (empty($errors)) {
        $role = 'Attendee'; 
        $sql = "INSERT INTO users (email, password, role) VALUES ('$email', '$hashed_password', '$role')";

        if (mysqli_query($conn, $sql)) {
            // Redirect to login with a success message
            header("Location: login.php?msg=registered");
            exit();
        } else {
            $errors[] = "Database Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Workshop Portal</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

<header class="site-header">
    <div class="logo">Workshop Portal</div>
    <nav class="nav">
        <a href="welcome_page.php" class="nav-link">Home</a>
        <a href="login.php" class="nav-link">Login</a>
    </nav>
</header>

<main class="page">
    <section class="hero compact">
        <p class="eyebrow">Join Us</p>
        <h1>Create an Account</h1>
        <p>Register today to start booking your educational sessions.</p>
    </section>

    <div class="table-wrapper" style="padding: 40px; max-width: 500px; margin: 20px auto;">
        
        <!-- DISPLAY ERRORS TO USER -->
        <?php if (!empty($errors)): ?>
            <div class="notice" style="color: #d9534f; background: #f9f2f2; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #ebccd1;">
                <strong>Registration Issues:</strong>
                <ul style="margin-top: 10px; margin-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="register.php">
            <div style="margin-bottom: 15px;">
                <label>Email Address</label><br>
                <input type="email" name="email" style="width: 100%; padding: 8px; margin-top: 5px;" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label>Password</label><br>
                <input type="password" name="password" style="width: 100%; padding: 8px; margin-top: 5px;" required>
                <small style="color: #666;">min 8 characters - uppercase, lowercase, special character, number</small>
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%; border: none; cursor: pointer;">Register</button>
        </form>
        
        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #deded8;">
        
        <div style="text-align: center;">
            <p>Already have an account?</p>
            <a href="login.php" class="nav-link" style="color: #202124; font-weight: 700;">Login here</a>
        </div>
    </div>
</main>

<footer class="site-footer">
    &copy; 2026 Workshop Management System
</footer>

</body>
</html>