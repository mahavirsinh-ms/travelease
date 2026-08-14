<!-- Sidebar -->
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <h2><div>
    <img src="../travelEASEonly.png" 
        
         style="width: 150px; height: auto; margin: 0; padding: 0; vertical-align: middle;">
</div> TravelEase</h2>
        <p>Admin Panel</p>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a href="manage-flights.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'manage-flights.php' ? 'active' : ''; ?>">
            <i class="fas fa-plane"></i> Manage Flights
        </a>
        <a href="manage-hotels.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'manage-hotels.php' ? 'active' : ''; ?>">
            <i class="fas fa-hotel"></i> Manage Hotels
        </a>
        <a href="manage-trains.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'manage-trains.php' ? 'active' : ''; ?>">
            <i class="fas fa-train"></i> Manage Trains
        </a>
        <a href="manage-buses.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'manage-buses.php' ? 'active' : ''; ?>">
            <i class="fas fa-bus"></i> Manage Buses
        </a>
        <a href="manage-cruises.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'manage-cruises.php' ? 'active' : ''; ?>">
            <i class="fas fa-ship"></i> Manage Cruises
        </a>
        <a href="manage-packages.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'manage-packages.php' ? 'active' : ''; ?>">
            <i class="fas fa-suitcase-rolling"></i> Manage Packages
        </a>
        <a href="manage-payments.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'manage-payments.php' ? 'active' : ''; ?>">
            <i class="fas fa-credit-card"></i> Manage Payments
        </a>
        <a href="view-bookings.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'view-bookings.php' ? 'active' : ''; ?>">
            <i class="fas fa-book"></i> View Bookings
        </a>
        <a href="view-users.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'view-users.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Manage Users
        </a>
        <a href="manage-admins.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'manage-admins.php' ? 'active' : ''; ?>">
            <i class="fas fa-users-cog"></i> Manage Admins
        </a>
        <a href="reports.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-bar"></i> Reports
        </a>
        <a href="../logout.php" class="nav-item">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</aside>

