<?php
/**
 * SESSION INITIALIZATION
 * session_start() is required as it gives the user a digital wristband so the server remembers 
 * who they are and what their role is as they move between different pages.
 */
include 'includes/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    /**
     * INPUT SANITIZATION
     * mysqli_real_escape_string() cleans the user input to prevent SQL Injection attacks.
     */
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    /**
     * DATABASE AUTHENTICATION (SECURE VERSION)
     * We fetch the user by email first. We can no longer check the password in the SQL string
     * because it is stored as a secure hash, not plain text.
     */
    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    /**
     * PASSWORD VERIFICATION
     * password_verify() takes the plain text input and checks it against the hash in the DB.
     */
    if ($user && password_verify($password, $user['password'])) {
        /**
         * SETTING SESSION STATE
         * If the password matches the hash, we store their ID and Role in a session
         */
        $_SESSION['user_id'] = $user['id']; 
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        // Redirecting to the main dashboard after successful authentication
        header("Location: index.php");
        exit();
    } else {
        /**
         * SERVER-SIDE VALIDATION & FEEDBACK
         */
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Workshop Portal</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

<header class="site-header">
    <div class="logo">Workshop Portal</div>
    <nav class="nav">
        <!-- Home link added for navigation flow -->
        <a href="welcome_page.php" class="nav-link">Home</a>
        <a href="register.php" class="nav-link">Create Account</a>
    </nav>
</header>

<main class="page">
    <section class="hero compact">
        <p class="eyebrow">Welcome Back</p>
        <h1>Login</h1>
        <p>Please enter your credentials to manage your workshops.</p>
    </section>

    <div class="table-wrapper" style="padding: 40px; max-width: 500px; margin: 20px auto;">
        
        <!-- Successful Registration Message -->
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'registered'): ?>
            <div class="notice" style="color: #3c763d; background: #dff0d8; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #d6e9c6;">
                Registration successful! Please login below.
            </div>
        <?php endif; ?>

        <!-- DISPLAYING ERROR MESSAGES -->
        <?php if(isset($error)): ?>
            <div class="notice" style="color: #d9534f; background: #f9f2f2; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #ebccd1;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="login.php">
            <div style="margin-bottom: 15px;">
                <label>Email Address</label><br>
                <input type="email" name="email" style="width: 100%; padding: 8px; margin-top: 5px;" required>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label>Password</label><br>
                <input type="password" name="password" style="width: 100%; padding: 8px; margin-top: 5px;" required>
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%; border: none; cursor: pointer;">Login</button>
        </form>

        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #deded8;">
        
        <div style="text-align: center;">
            <p>Don't have an account?</p>
            <a href="register.php" class="nav-link" style="color: #202124; font-weight: 700;">Sign Up / Register Here</a>
        </div>
    </div>
</main>

<footer class="site-footer">
    &copy; 2026 Workshop Management System
</footer>

</body>
</html>