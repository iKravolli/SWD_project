<?php
// profile.php - User can view and update their profile (based on update.php and self_form.php)
require_once 'auth_check.php';
require_once 'db_connect.php';

include 'navbar.php';

$user_id = $_SESSION['user_id'];
$firstname = $_SESSION['firstname'];
$lastname = $_SESSION['lastname'];
$email = $_SESSION['email'];

$firstname_err = "";
$lastname_err = "";
$email_err = "";
$password_err = "";
$confirm_err = "";
$success_message = "";

// Sanitization function (from your Lecture04)
function pass_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = strip_tags($data);
    return $data;
}

// Update profile information (based on your update.php)
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['update_profile']))
{
    $new_firstname = pass_input($_POST['firstname']);
    $new_lastname = pass_input($_POST['lastname']);
    $new_email = pass_input($_POST['email']);
    $errors = array();
    
    // Validation (from your register.php)
    if(empty($new_firstname))
    {
        $firstname_err = "<span style='color:red'>First name is required</span>";
    }
    elseif(!preg_match("/^[a-zA-Z ]*$/", $new_firstname))
    {
        $firstname_err = "<span style='color:red'>Only letters allowed</span>";
    }
    
    if(empty($new_lastname))
    {
        $lastname_err = "<span style='color:red'>Last name is required</span>";
    }
    elseif(!preg_match("/^[a-zA-Z ]*$/", $new_lastname))
    {
        $lastname_err = "<span style='color:red'>Only letters allowed</span>";
    }
    
    if(empty($new_email))
    {
        $email_err = "<span style='color:red'>Email is required</span>";
    }
    elseif(!filter_var($new_email, FILTER_VALIDATE_EMAIL))
    {
        $email_err = "<span style='color:red'>Valid email required</span>";
    }
    
    if(empty($firstname_err) && empty($lastname_err) && empty($email_err))
    {
        // Update query (based on your update.php)
        $update_query = "UPDATE user SET FirstName = '$new_firstname', LastName = '$new_lastname', Email = '$new_email' WHERE UserID = $user_id";
        $result = mysqli_query($db_connection, $update_query);
        
        if($result)
        {
            // Update session variables
            $_SESSION['firstname'] = $new_firstname;
            $_SESSION['lastname'] = $new_lastname;
            $_SESSION['email'] = $new_email;
            
            $firstname = $new_firstname;
            $lastname = $new_lastname;
            $email = $new_email;
            
            $success_message = "<span style='color:green'>Profile updated successfully!</span>";
        }
        else
        {
            $error_message = "<span style='color:red'>Update failed</span>";
        }
    }
}

// Change password (NO HASHING - plain text like your signup.php)
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['change_password']))
{
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password (plain text comparison)
    $pass_query = "SELECT Password FROM user WHERE UserID = $user_id";
    $pass_result = mysqli_query($db_connection, $pass_query);
    $user_data = mysqli_fetch_assoc($pass_result);
    
    if($current_password != $user_data['Password'])
    {
        $password_err = "<span style='color:red'>Current password is incorrect</span>";
    }
    elseif(strlen($new_password) < 5)
    {
        $password_err = "<span style='color:red'>Password must be at least 5 characters</span>";
    }
    elseif($new_password != $confirm_password)
    {
        $confirm_err = "<span style='color:red'>Passwords do not match</span>";
    }
    else
    {
        // Update password in plain text (exactly like your signup.php)
        $update_pass = "UPDATE user SET Password = '$new_password' WHERE UserID = $user_id";
        
        if(mysqli_query($db_connection, $update_pass))
        {
            $success_message = "<span style='color:green'>Password changed successfully!</span>";
        }
        else
        {
            $error_message = "<span style='color:red'>Password change failed</span>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile - Workshop Booking</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 20px auto; }
        .profile-card { background: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; display: inline-block; }
        h2 { color: #555; margin: 20px 0; font-size: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="password"] { 
            width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box;
        }
        input[type="submit"] { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer;
        }
        .success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 8px; margin-bottom: 20px; }
        .error { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 8px; margin-bottom: 20px; }
        hr { margin: 30px 0; border: none; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Profile</h1>
        
        <?php if(isset($success_message)) echo "<div class='success'>$success_message</div>"; ?>
        <?php if(isset($error_message)) echo "<div class='error'>$error_message</div>"; ?>
        
        <!-- Update Profile Section -->
        <div class="profile-card">
            <h2>Edit Profile Information</h2>
            <form method="POST" action="">
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
                
                <input type="submit" name="update_profile" value="Update Profile">
            </form>
        </div>
        
        <!-- Change Password Section -->
        <div class="profile-card">
            <h2>Change Password</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Current Password:</label>
                    <input type="password" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label>New Password (min 5 characters):</label>
                    <input type="password" name="new_password" required>
                    <?php echo $password_err; ?>
                </div>
                
                <div class="form-group">
                    <label>Confirm New Password:</label>
                    <input type="password" name="confirm_password" required>
                    <?php echo $confirm_err; ?>
                </div>
                
                <input type="submit" name="change_password" value="Change Password">
            </form>
        </div>
        
        <br>
        <a href="index.php">← Back to Home</a>
    </div>
</body>
</html>

<?php mysqli_close($db_connection); ?>