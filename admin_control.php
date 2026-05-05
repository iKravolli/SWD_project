<?php
include 'includes/db.php';
session_start();

/**
 * This block ensures only users with the admin role can access this file
 */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    die("Access Denied. Admins only. <a href='index.php'>Back to Home</a>");
}

/**
 * UPDATE FUNCTIONALITY
 * This handles updating a users role in the mysql database
 */
if (isset($_POST['update_role'])) {
    $uid = $_POST['user_id'];
    $new_role = $_POST['new_role'];
    $update_sql = "UPDATE users SET role = '$new_role' WHERE id = $uid";
    mysqli_query($conn, $update_sql);
    header("Location: admin_control.php?msg=updated");
    exit();
}

/**
 * DELETE FUNCTIONALITY 
 * This logic removes a user from the database.
 * Includes a safety check to prevent the logged in admin from deleting themselves.
 */
if (isset($_GET['delete_user'])) {
    $uid = $_GET['delete_user'];
    if ($uid != $_SESSION['user_id']) {
        $del_sql = "DELETE FROM users WHERE id = $uid";
        mysqli_query($conn, $del_sql);
    }
    header("Location: admin_control.php?msg=deleted");
    exit();
}

/**
 * READ FUNCTIONALITY 
 * Fetching all records from the users table to display them in the ui
 */
$user_result = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Control Center - Workshop Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <div class="logo">Workshop Portal</div>
    <nav class="nav">
        <!-- Identifying user context via Session data -->
        <span class="user-pill">Admin Privileges</span>
        <a href="index.php" class="nav-link">Workshops</a>
        <a href="logout.php" class="nav-link logout-link">Logout</a>
    </nav>
</header>

<main class="page">
    <section class="hero compact">
        <p class="eyebrow">System Control</p>
        <h1>User Administration</h1>
        <p>Manage user roles and system access permissions.</p>
    </section>

    <!-- Showing success messages based on URL parameters -->
    <?php if(isset($_GET['msg'])): ?>
        <div class="notice success" style="margin-bottom: 20px;">
            <?php 
                if($_GET['msg'] == 'updated') echo "User role updated successfully.";
                if($_GET['msg'] == 'deleted') echo "User has been removed from the system.";
            ?>
        </div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table class="main-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Current Role</th>
                    <th>Change Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                /**
                 * Looping through database results to build the table 
                 */
                while($user = mysqli_fetch_assoc($user_result)) { ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <!-- htmlspecialchars() prevents Cross Site Scripting security issues -->
                    <td><strong><?php echo htmlspecialchars($user['email']); ?></strong></td>
                    <td>
                        <span class="user-pill" style="background: #eef1f5; color: #202124;">
                            <?php echo $user['role']; ?>
                        </span>
                    </td>
                    <td>
                        <!-- Form for Role Updates -->
                        <form method="POST" style="display:flex; gap: 10px;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <select name="new_role" style="padding: 5px;">
                                <option value="Attendee" <?php if($user['role'] == 'Attendee') echo 'selected'; ?>>Attendee</option>
                                <option value="Organiser" <?php if($user['role'] == 'Organiser') echo 'selected'; ?>>Organiser</option>
                                <option value="Admin" <?php if($user['role'] == 'Admin') echo 'selected'; ?>>Admin</option>
                            </select>
                            <button type="submit" name="update_role" class="btn-small">Update</button>
                        </form>
                    </td>
                    <td>
                        <!-- Admins cannot delete their own account -->
                        <?php if($user['id'] != $_SESSION['user_id']): ?>
                            <a href="admin_control.php?delete_user=<?php echo $user['id']; ?>" 
                               onclick="return confirm('Delete this user?')" 
                               class="btn-danger" style="text-decoration: none; padding: 5px 10px;">Delete User</a>
                        <?php else: ?>
                            <span style="color: #999; font-style: italic;">(You)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</main>

<footer class="site-footer">
    &copy; 2026 Workshop Management System
</footer>

</body>
</html>