<?php
// admin_dashboard.php - Admin manages users (delete, change roles)
require_once 'auth_check.php';
require_once 'role_check.php';
require_once 'db_connect.php';

// Check if user is Admin
check_role('Admin');

include 'navbar.php';

$message = "";
$message_type = "";

// Handle user deletion (based on your delete.php pattern)
if(isset($_GET['delete']) && isset($_GET['user_id']))
{
    $user_id = $_GET['user_id'];
    
    // Don't allow admin to delete themselves
    if($user_id != $_SESSION['user_id'])
    {
        $delete_query = "DELETE FROM user WHERE UserID = $user_id";
        $delete_result = mysqli_query($db_connection, $delete_query);
        
        if($delete_result)
        {
            $message = "User deleted successfully!";
            $message_type = "success";
        }
        else
        {
            $message = "Error deleting user";
            $message_type = "error";
        }
    }
    else
    {
        $message = "You cannot delete your own account!";
        $message_type = "error";
    }
}

// Handle role update (based on your update.php pattern but without prepared statements for simplicity - matches your lecture style)
if(isset($_POST['update_role']))
{
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];
    
    $update_query = "UPDATE user SET Role = '$new_role' WHERE UserID = $user_id";
    $update_result = mysqli_query($db_connection, $update_query);
    
    if($update_result)
    {
        $message = "User role updated successfully!";
        $message_type = "success";
    }
    else
    {
        $message = "Error updating user role";
        $message_type = "error";
    }
}

// Get all users (based on your search.php pattern)
$query = "SELECT * FROM user ORDER BY UserID";
$result = mysqli_query($db_connection, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Workshop Booking</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #ddd; }
        .delete-btn { background-color: #f44336; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; }
        .delete-btn:hover { background-color: #d32f2f; }
        .update-btn { background-color: #008CBA; color: white; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; }
        .update-btn:hover { background-color: #007B9E; }
        select { padding: 5px; margin-right: 10px; }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .success { background-color: #dff0d8; color: #3c763d; border: 1px solid #d6e9c6; }
        .error { background-color: #f2dede; color: #a94442; border: 1px solid #ebccd1; }
        .stats { background-color: #f0f0f0; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        
        <?php if($message != ""): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="stats">
            <h3>System Statistics</h3>
            <?php
            // Count total users
            $count_query = "SELECT COUNT(*) as total FROM user";
            $count_result = mysqli_query($db_connection, $count_query);
            $total_users = mysqli_fetch_assoc($count_result)['total'];
            
            // Count by role
            $admin_count = mysqli_fetch_assoc(mysqli_query($db_connection, "SELECT COUNT(*) as count FROM user WHERE Role = 'Admin'"))['count'];
            $organiser_count = mysqli_fetch_assoc(mysqli_query($db_connection, "SELECT COUNT(*) as count FROM user WHERE Role = 'Organiser'"))['count'];
            $attendee_count = mysqli_fetch_assoc(mysqli_query($db_connection, "SELECT COUNT(*) as count FROM user WHERE Role = 'Attendee'"))['count'];
            ?>
            <p><strong>Total Users:</strong> <?php echo $total_users; ?></p>
            <p><strong>Admins:</strong> <?php echo $admin_count; ?> | 
               <strong>Organisers:</strong> <?php echo $organiser_count; ?> | 
               <strong>Attendees:</strong> <?php echo $attendee_count; ?></p>
        </div>
        
        <h2>Manage Users</h2>
        
         <?php if(mysqli_num_rows($result) > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Current Role</th>
                    <th>Change Role</th>
                    <th>Action</th>
                </tr>
                
                <?php while($user = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $user['UserID']; ?></td>
                        <td><?php echo htmlspecialchars($user['FirstName']); ?></td>
                        <td><?php echo htmlspecialchars($user['LastName']); ?></td>
                        <td><?php echo htmlspecialchars($user['Email']); ?></td>
                        <td><?php echo $user['Role']; ?></td>
                        <td>
                            <form method="POST" action="" style="margin:0;">
                                <input type="hidden" name="user_id" value="<?php echo $user['UserID']; ?>">
                                <select name="role">
                                    <option value="Attendee" <?php if($user['Role'] == 'Attendee') echo 'selected'; ?>>Attendee</option>
                                    <option value="Organiser" <?php if($user['Role'] == 'Organiser') echo 'selected'; ?>>Organiser</option>
                                    <option value="Admin" <?php if($user['Role'] == 'Admin') echo 'selected'; ?>>Admin</option>
                                </select>
                                <input type="submit" name="update_role" value="Update" class="update-btn">
                            </form>
                        </td>
                        <td>
                            <?php if($user['UserID'] != $_SESSION['user_id']): ?>
                                <a href="?delete=true&user_id=<?php echo $user['UserID']; ?>" 
                                   class="delete-btn" 
                                   onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            <?php else: ?>
                                <span style="color:gray;">Current User</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
        
        <br>
        <a href="index.php">Back to Home</a>
    </div>
</body>
</html>

<?php mysqli_close($db_connection); ?>