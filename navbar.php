<?php
// navbar.php - Modern navigation
$is_logged_in = isset($_SESSION['user_id']);
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        .navbar {
            background-color: #1a1a2e;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .logo a {
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }
        
        .logo span {
            color: #e94560;
        }
        
        .nav-links {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            transition: 0.3s;
            border-radius: 5px;
        }
        
        .nav-links a:hover {
            background-color: #e94560;
        }
        
        .welcome {
            color: #e94560;
            padding: 8px 16px;
        }
        
        .logout-btn {
            background-color: #e94560;
            border-radius: 5px;
        }
        
        .logout-btn:hover {
            background-color: #c72a48;
        }
        
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
            }
            .nav-links {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <a href="index.php">Workshop<span>Booking</span></a>
        </div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            
            <?php if($is_logged_in): ?>
                <a href="events.php">Browse Events</a>
                <a href="profile.php">My Profile</a>
                
                <?php if($user_role == 'Attendee'): ?>
                    <a href="my_bookings.php">My Bookings</a>
                <?php endif; ?>
                
                <?php if($user_role == 'Organiser'): ?>
                    <a href="organiser_dashboard.php">Dashboard</a>
                    <a href="create_event.php">Create Event</a>
                <?php endif; ?>
                
                <?php if($user_role == 'Admin'): ?>
                    <a href="admin_dashboard.php">Admin Panel</a>
                <?php endif; ?>
                
                <span class="welcome">Hi, <?php echo htmlspecialchars($_SESSION['firstname']); ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
                
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>