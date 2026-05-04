<?php
// index.php - Modern Homepage Design
session_start();
require_once 'db_connect.php';

if(isset($_GET['loggedout']) && $_GET['loggedout'] == 'true')
{
    $logout_message = "<div class='success-message'>Successfully logged out!</div>";
}

include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkshopBooking - Find & Book Workshops</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            text-align: center;
            padding: 100px 20px;
        }
        
        .hero h1 {
            font-size: 56px;
            margin-bottom: 20px;
            letter-spacing: -1px;
        }
        
        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .hero-buttons {
            margin-top: 30px;
        }
        
        .btn {
            display: inline-block;
            padding: 14px 35px;
            margin: 0 10px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #e94560;
            color: white;
            border: 2px solid #e94560;
        }
        
        .btn-primary:hover {
            background-color: transparent;
            color: #e94560;
            transform: translateY(-3px);
        }
        
        .btn-outline {
            border: 2px solid white;
            color: white;
            background: transparent;
        }
        
        .btn-outline:hover {
            background-color: white;
            color: #1a1a2e;
            transform: translateY(-3px);
        }
        
        /* Features Section */
        .features {
            background-color: #ffffff;
            padding: 80px 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: auto;
        }
        
        .section-title {
            text-align: center;
            font-size: 36px;
            margin-bottom: 15px;
            color: #1a1a2e;
        }
        
        .section-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 50px;
            font-size: 18px;
        }
        
        .feature-grid {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .feature-card {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            text-align: center;
            width: 300px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid #eee;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            font-size: 50px;
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #1a1a2e;
        }
        
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        
        /* Welcome Section for Logged In Users */
        .welcome-section {
            background: linear-gradient(135deg, #e94560 0%, #c72a48 100%);
            color: white;
            text-align: center;
            padding: 60px 20px;
        }
        
        .welcome-section h2 {
            font-size: 40px;
            margin-bottom: 15px;
        }
        
        .welcome-section p {
            font-size: 18px;
            margin-bottom: 25px;
        }
        
        .role-buttons {
            margin-top: 20px;
        }
        
        .role-buttons .btn {
            background: white;
            color: #e94560;
            border: none;
        }
        
        .role-buttons .btn:hover {
            transform: translateY(-3px);
            background: #1a1a2e;
            color: white;
        }
        
        /* Footer */
        .footer {
            background-color: #1a1a2e;
            color: #888;
            text-align: center;
            padding: 40px;
            font-size: 14px;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 20px auto;
            text-align: center;
            max-width: 600px;
        }
        
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 36px;
            }
            .hero p {
                font-size: 16px;
            }
            .feature-card {
                width: 100%;
                max-width: 350px;
            }
        }
    </style>
</head>
<body>
    <?php if(isset($_SESSION['user_id'])): ?>
        <!-- Logged In User Section -->
        <div class="welcome-section">
            <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['firstname']); ?>! 👋</h2>
            <p>You are logged in as <strong><?php echo $_SESSION['role']; ?></strong></p>
            
            <div class="role-buttons">
                <?php if($_SESSION['role'] == 'Attendee'): ?>
                    <a href="events.php" class="btn">Browse Workshops</a>
                    <a href="my_bookings.php" class="btn">My Bookings</a>
                <?php elseif($_SESSION['role'] == 'Organiser'): ?>
                    <a href="organiser_dashboard.php" class="btn">Dashboard</a>
                    <a href="create_event.php" class="btn">Create Workshop</a>
                <?php elseif($_SESSION['role'] == 'Admin'): ?>
                    <a href="admin_dashboard.php" class="btn">Admin Panel</a>
                    <a href="events.php" class="btn">View Events</a>
                <?php endif; ?>
            </div>
        </div>
        
    <?php else: ?>
        <!-- Hero Section for Visitors -->
        <div class="hero">
            <h1>Discover & Book Amazing Workshops</h1>
            <p>Find cooking classes, photography sessions, fitness workshops and more — taught by local experts</p>
            <div class="hero-buttons">
                <a href="register.php" class="btn btn-primary">Get Started</a>
                <a href="login.php" class="btn btn-outline">Login</a>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Features Section -->
    <div class="features">
        <div class="container">
            <h2 class="section-title">Why Choose WorkshopBooking?</h2>
            <p class="section-subtitle">The best platform to learn new skills and share your expertise</p>
            
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">📚</div>
                    <h3>Learn New Skills</h3>
                    <p>Browse hundreds of workshops taught by experienced instructors and local experts.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">✅</div>
                    <h3>Easy Booking</h3>
                    <p>Book your spot in seconds. Get instant confirmation and reminders.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3>Host Workshops</h3>
                    <p>Share your expertise. Create workshops, manage bookings, and grow your community.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2026 WorkshopBooking. All rights reserved. | Learn. Connect. Grow.</p>
    </div>
</body>
</html>

<?php mysqli_close($db_connection); ?>