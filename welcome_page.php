<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Workshop Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- 
  1. PUBLIC NAVIGATION 
  Since the user isnt logged in yet we show generic login and register
  This provides a clear entry point
-->
<header class="site-header">
    <div class="logo">Workshop Portal</div>
<nav class="nav">
    <a href="login.php" class="nav-link">Login</a>
    <a href="register.php" class="btn-primary" style="text-decoration:none; padding: 8px 15px; color: #fff; background-color: #202124; border-radius: 4px;">Join Now</a>
</nav>
    
</header>

<main class="page">
    <!-- 
      
      this is the hook of the website it explains the purpose of the application and its goal is
      to interest the user and why they should use it 
    -->
<section class="hero" style="text-align: center; padding: 60px 20px; background: #f9f9f9; border-radius: 12px; margin-bottom: 40px;">
    <p class="eyebrow" style="color: #666; text-transform: uppercase; letter-spacing: 1px; margin: 0 auto; display: block;">Discover & Learn</p>
    
    <h1 style="font-size: 2.5rem; margin: 10px 0;">Advance Your Career with Expert Workshops</h1>
    <p style="max-width: 700px; margin: 20px auto; color: #555; line-height: 1.6;">
        Welcome to the ultimate hub for professional development. Whether you're an 
        Attendee looking to learn or an Organiser wanting to share knowledge, 
        our portal makes management simple and efficient.
    </p>
    <div style="margin-top: 30px;">
        <a href="register.php" class="btn-primary" style="padding: 12px 25px; text-decoration:none; font-size: 1.1rem;">Create Your Account</a>
    </div>
</section>

    <!-- 
      FEATURE GRID 
      This demonstrates the list of functions that this application will provide to its users
      such as booking management and role based access
    -->
    <section class="features-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
        
        <div class="card" style="padding: 25px; border: 1px solid #eee; border-radius: 8px; text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 10px;">🛡️</div>
            <h3>Role-Based Access</h3>
            <p>Secure accounts for Attendees, Organisers, and Admins to ensure data integrity.</p>
        </div>

        <div class="card" style="padding: 25px; border: 1px solid #eee; border-radius: 8px; text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 10px;">📅</div>
            <h3>Easy Bookings</h3>
            <p>Browse available workshops and reserve your spot with a single click.</p>
        </div>

        <div class="card" style="padding: 25px; border: 1px solid #eee; border-radius: 8px; text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 10px;">⚙️</div>
            <h3>Admin Control</h3>
            <p>Complete system oversight to manage user permissions and workshop listings.</p>
        </div>

    </section>
</main>

<footer class="site-footer" style="margin-top: 50px; text-align: center; border-top: 1px solid #eee; padding: 20px;">
    &copy; 2026 Workshop Management System | <a href="login.php" style="color: #333;">Admin Login</a>
</footer>

</body>
</html>