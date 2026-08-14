<?php
session_start();
include "../db.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get statistics
$stats = [];

// Total bookings
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings");
$stats['total_bookings'] = mysqli_fetch_assoc($result)['total'];

// Total revenue
$result = mysqli_query($conn, "SELECT 
    SUM(CASE WHEN payment_status='success' THEN amount ELSE 0 END) AS revenue
FROM payments
");
$row = mysqli_fetch_assoc($result);
$stats['total_revenue'] = $row['revenue'] ? $row['revenue'] : 0;

// Total users
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'");
$stats['total_users'] = mysqli_fetch_assoc($result)['total'];

// Pending bookings
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE status='pending'");
$stats['pending_bookings'] = mysqli_fetch_assoc($result)['total'];

// Recent bookings
$recent_bookings = mysqli_query($conn, "SELECT b.*, u.full_name, u.email FROM bookings b JOIN users u ON b.user_id = u.id ORDER BY b.booking_date DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TravelEase</title>
    <link rel="stylesheet" href="../css/all.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="admin-style.css">
    <style>
         /* Poppins Regular */
@font-face {
  font-family: 'Poppins';
  font-style: normal;
  font-weight: 400;
  src: url('../fonts/poppins-v24-latin-regular.woff2') format('woff2'); 
}

/* Montserrat Bold */
@font-face {
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 700;
  src: url('../fonts/montserrat-v31-latin-700.woff2') format('woff2');
}
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1>Dashboard</h1>
                <div class="header-actions">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="../index.php" class="btn-secondary"><i class="fas fa-home"></i> View Site</a>
                </div>
            </header>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['total_bookings']); ?></h3>
                        <p>Total Bookings</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>₹<?php echo number_format($stats['total_revenue'], 0); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['total_users']); ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['pending_bookings']); ?></h3>
                        <p>Pending Bookings</p>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Recent Bookings</h2>
                    <a href="view-bookings.php" class="btn-link">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($recent_bookings) > 0): ?>
                                <?php while($booking = mysqli_fetch_assoc($recent_bookings)): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($booking['booking_reference']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['full_name']); ?></td>
                                    <td><span class="badge badge-info"><?php echo ucfirst($booking['booking_type']); ?></span></td>
                                    <td>₹<?php echo number_format($booking['total_amount'], 0); ?></td>
                                    <td>
                                        <span class="badge badge-<?php 
                                            echo $booking['status'] == 'confirmed' ? 'success' : 
                                                ($booking['status'] == 'pending' ? 'warning' : 'danger'); 
                                        ?>"><?php echo ucfirst($booking['status']); ?></span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></td>
                                    <td>
                                        <a href="view-booking-details.php?id=<?php echo $booking['id']; ?>" class="btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No bookings found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

