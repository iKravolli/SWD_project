<?php
/**
 *  SESSION INITIALIZATION
 * session_start() is required as it gives the user a digital wristband so the server remembers 
* who they are and what their role is as they move between different pages.
 */
include 'includes/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    /**
     * INPUT SANITIZATION [secuirty]
     * mysqli_real_escape_string() cleans the user input to prevent SQL Injection attacks.
     */
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    /**
     * DATABASE AUTHENTICATION
     * We check the mysql database to verify if the provided credentials match a stored user record
     */
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        /**
         * SETTING SESSION STATE
         * If the user is found, we store their ID and Role in a session.
         * This allows other pages to check who is logged in and what they are allowed to see.
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
         * If the credentials don't match, we provide appropriate feedback to the user.
         */
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login - Workshop Portal</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

<header class="site-header">
    <div class="logo">Workshop Portal</div>
    <nav class="nav">
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
        <!-- DISPLAYING ERROR MESSAGES -->
        <?php if(isset($error)): ?>
            <div class="notice" style="color: #d9534f; background: #f9f2f2; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!--  Login Form -->
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