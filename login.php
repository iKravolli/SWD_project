<?php
// login.php - User login with Remember Me cookie (based on Lecture09 - Cookies)
session_start();
require_once 'db_connect.php';

$email = "";
$login_error = "";
$remember_me = "";

// Check if "Remember Me" cookie exists (from your Lecture09)
if(isset($_COOKIE['remember_email']) && !isset($_SESSION['user_id']))
{
    $email = $_COOKIE['remember_email'];
}

// If already logged in, redirect based on role
if(isset($_SESSION['user_id']))
{
    if($_SESSION['role'] == 'Admin')
    {
        header("Location: admin_dashboard.php");
    }
    elseif($_SESSION['role'] == 'Organiser')
    {
        header("Location: organiser_dashboard.php");
    }
    else
    {
        header("Location: events.php");
    }
    exit();
}

if($_SERVER['REQUEST_METHOD'] == "POST")
{
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if(isset($_POST['remember_me']))
    {
        $remember_me = $_POST['remember_me'];
    }
    
    // Validation (from your form.php)
    if(empty($email))
    {
        $login_error = "<span style='color:red'>Email is required</span>";
    }
    elseif(empty($password))
    {
        $login_error = "<span style='color:red'>Password is required</span>";
    }
    else
    {
        // Query to check user (from your search.php pattern)
        $query = "SELECT * FROM user WHERE Email = '$email' AND Password = '$password'";
        $result = mysqli_query($db_connection, $query);
        
        if(mysqli_num_rows($result) == 1)
        {
            $user = mysqli_fetch_assoc($result);
            
            // Store user in session (from your Sessions lecture)
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['firstname'] = $user['FirstName'];
            $_SESSION['lastname'] = $user['LastName'];
            $_SESSION['email'] = $user['Email'];
            $_SESSION['role'] = $user['Role'];
            
            // Set Remember Me cookie (from your Lecture09 - setcookie())
            if($remember_me == "on")
            {
                setcookie('remember_email', $email, time() + (86400 * 30), "/");
            }
            else
            {
                setcookie('remember_email', '', time() - 3600, "/");
            }
            
            // Redirect based on role
            if($user['Role'] == 'Admin')
            {
                header("Location: admin_dashboard.php");
            }
            elseif($user['Role'] == 'Organiser')
            {
                header("Location: organiser_dashboard.php");
            }
            else
            {
                header("Location: events.php");
            }
            exit();
        }
        else
        {
            $login_error = "<span style='color:red'>Invalid email or password</span>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Workshop Booking</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0; }
        .container { width: 400px; margin: 100px auto; padding: 30px; background: white; border-radius: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="email"], input[type="password"] { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .checkbox-group { margin-bottom: 20px; }
        .checkbox-group label { display: inline; margin-left: 8px; font-weight: normal; }
        input[type="submit"] { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px; border: none; border-radius: 8px; cursor: pointer; width: 100%; font-size: 16px; }
        .register-link { text-align: center; margin-top: 20px; }
        .error { color: #f44336; text-align: center; margin-bottom: 15px; padding: 10px; background: #ffebee; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login to WorkshopBooking</h2>
        
        <?php echo $login_error; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="checkbox-group">
                <input type="checkbox" name="remember_me" <?php if($remember_me) echo 'checked'; ?>>
                <label>Remember Me</label>
            </div>
            
            <div class="form-group">
                <input type="submit" value="Login">
            </div>
        </form>
        
        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</body>
</html>