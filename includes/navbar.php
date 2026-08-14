<!-- Navigation Bar -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar">
    <div class="container">
        <!-- Logo -->
        <div class="nav-brand">
            <a href="index.php">
                <img src="travelEASEonly.png" alt="TravelEase Logo" class="nav-logo">
                <span class="nav-brand-text">Travel<span class="brand-highlight">Ease</span></span>
            </a>
        </div>

        <!-- Mobile Toggle -->
        <div class="mobile-toggle" id="mobileToggle">
            <i class="fas fa-bars"></i>
        </div>

        <!-- Menu -->
        <ul class="nav-menu" id="navMenu">
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php#services-section">Services</a></li>
            <li><a href="holidaybook.php" id="nav-holiday-packages">Packages</a></li>
            <li><a href="index.php#footer-contact">Contact</a></li>
            <li><a href="index.php#footer-about">About</a></li>
            <li><a href="faq.html">Help</a></li>

            <!-- Profile Button -->
            <li class="profile-btn">
                <div class="profile-container">
                    <button id="profileButton" class="profile-icon-btn">
                        <i class="fas fa-user-circle"></i>
                        <?php if (isset($_SESSION['user'])): ?>
                            <span class="profile-username"><?php echo htmlspecialchars($_SESSION['user']); ?></span>
                        <?php endif; ?>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="profileMenu" class="profile-dropdown">
                        <?php if (isset($_SESSION['user'])): ?>
                            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                            <a href="bookings.php"><i class="fas fa-suitcase"></i> My Bookings</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        <?php else: ?>
                            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                            <a href="signup.php"><i class="fas fa-user-plus"></i> Signup</a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>


