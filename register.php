<?php
// register.php - User registration (based on signup.php and self_form.php)
session_start();
require_once 'db_connect.php';

// Initialize variables for sticky form
$firstname = "";
$lastname = "";
$email = "";
$role = "Attendee";

// Initialize error variables
$firstname_err = "";
$lastname_err = "";
$email_err = "";
$password_err = "";
$confirm_err = "";

// Function to sanitize input (from your Lecture04 - pass_input function)
function pass_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = strip_tags($data);
    return $data;
}

if($_SERVER['REQUEST_METHOD'] == "POST")
{
    // Validate First Name (from your Lecture04 - preg_match pattern)
    if(empty($_POST['firstname']))
    {
        $firstname_err = "<span style='color:red'>First name is required</span>";
    }
    else
    {
        $firstname = pass_input($_POST['firstname']);
        if(!preg_match("/^[a-zA-Z ]*$/", $firstname))
        {
            $firstname_err = "<span style='color:red'>Only letters and spaces allowed</span>";
        }
    }
    
    // Validate Last Name
    if(empty($_POST['lastname']))
    {
        $lastname_err = "<span style='color:red'>Last name is required</span>";
    }
    else
    {
        $lastname = pass_input($_POST['lastname']);
        if(!preg_match("/^[a-zA-Z ]*$/", $lastname))
        {
            $lastname_err = "<span style='color:red'>Only letters and spaces allowed</span>";
        }
    }
    
    // Validate Email (from your Lecture04 - filter_var)
    if(empty($_POST['email']))
    {
        $email_err = "<span style='color:red'>Email is required</span>";
    }
    else
    {
        $email = pass_input($_POST['email']);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $email_err = "<span style='color:red'>Enter a valid email address</span>";
        }
    }
    
    // Validate Password (from your form.php - length check)
    if(empty($_POST['password']))
    {
        $password_err = "<span style='color:red'>Password is required</span>";
    }
    else
    {
        $password = $_POST['password'];
        if(strlen($password) < 5)
        {
            $password_err = "<span style='color:red'>Password must be at least 5 characters</span>";
        }
    }
    
    // Validate Confirm Password
    if(empty($_POST['confirm_password']))
    {
        $confirm_err = "<span style='color:red'>Please confirm your password</span>";
    }
    else
    {
        $confirm = $_POST['confirm_password'];
        if($password != $confirm)
        {
            $confirm_err = "<span style='color:red'>Passwords do not match</span>";
        }
    }
    
    // Get role from form
    if(isset($_POST['role']))
    {
        $role = pass_input($_POST['role']);
    }
    
    // If no errors, insert into database (based on your signup.php)
    if(empty($firstname_err) && empty($lastname_err) && empty($email_err) && empty($password_err) && empty($confirm_err))
    {
        // Check if email already exists (from your search.php pattern)
        $check_query = "SELECT * FROM user WHERE Email = '$email'";
        $check_result = mysqli_query($db_connection, $check_query);
        
        if(mysqli_num_rows($check_result) > 0)
        {
            $email_err = "<span style='color:red'>Email already registered</span>";
        }
        else
        {
            // INSERT query (exactly like your signup.php - NO HASHING)
            $insert_query = "INSERT INTO user (FirstName, LastName, Email, Password, Role) 
                             VALUES ('$firstname', '$lastname', '$email', '$password', '$role')";
            
            $result = mysqli_query($db_connection, $insert_query);
            
            if($result)
            {
                // Registration successful - redirect to login page
                header("Location: login.php?registered=true");
                exit();
            }
            else
            {
                $error_message = "<span style='color:red'>Registration failed. Please try again.</span>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Workshop Booking</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .container { width: 450px; margin: auto; padding: 30px; background: white; border-radius: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="password"], select { 
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box;
        }
        input[type="submit"] { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px; border: none; border-radius: 8px; cursor: pointer; width: 100%; font-size: 16px; }
        .error { color: red; font-size: 14px; margin-top: 5px; }
        .login-link { text-align: center; margin-top: 20px; }
        select { width: 100%; padding: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create Account</h2>
        
        <?php if(isset($error_message)) echo $error_message; ?>
        
        <!-- Sticky form with htmlspecialchars (from your Lecture04) -->
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label>First Name:</label>
                <input type="text" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>" required>
                <?php echo $firstname_err; ?>
            </div>
            
            <div class="form-group">
                <label>Last Name:</label>
                <input type="text" name="lastname" value="<?php echo htmlspecialchars($lastname); ?>" required>
                <?php echo $lastname_err; ?>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <?php echo $email_err; ?>
            </div>
            
            <div class="form-group">
                <label>Password (min 5 characters):</label>
                <input type="password" name="password" required>
                <?php echo $password_err; ?>
            </div>
            
            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" required>
                <?php echo $confirm_err; ?>
            </div>
            
            <div class="form-group">
                <label>Register as:</label>
                <select name="role">
                    <option value="Attendee" <?php if($role == 'Attendee') echo 'selected'; ?>>Attendee</option>
                    <option value="Organiser" <?php if($role == 'Organiser') echo 'selected'; ?>>Organiser</option>
                </select>
            </div>
            
            <div class="form-group">
                <input type="submit" value="Register">
            </div>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>

<?php mysqli_close($db_connection); ?>