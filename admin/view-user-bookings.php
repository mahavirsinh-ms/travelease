<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$user = null;
$bookings = null;

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $user_result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id' LIMIT 1");
    if ($user_result && mysqli_num_rows($user_result) === 1) {
        $user = mysqli_fetch_assoc($user_result);
        $bookings = mysqli_query($conn, "SELECT * FROM bookings WHERE user_id='$user_id' ORDER BY booking_date DESC");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Bookings - TravelEase Admin</title>
    <link rel="stylesheet" href="../css/all.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1><i class="fas fa-suitcase"></i> User Bookings</h1>
                <div class="header-actions">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="view-users.php" class="btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                </div>
            </header>

            <?php if(!$user): ?>
                <div class="content-card">
                    <p style="color: #7f8c8d;">User not found.</p>
                </div>
            <?php else: ?>
                <div class="content-card">
                    <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                    <p style="color: #7f8c8d; margin-top: 5px;"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>

                <div class="content-card">
                    <h2>Bookings</h2>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Ref</th>
                                    <th>Type</th>
                                    <th>Details</th>
                                    <th>Travel Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Booked On</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($bookings && mysqli_num_rows($bookings) > 0): ?>
                                    <?php while($booking = mysqli_fetch_assoc($bookings)): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($booking['booking_reference']); ?></td>
                                        <td><span class="badge badge-info"><?php echo ucfirst($booking['booking_type']); ?></span></td>
                                        <td><?php echo htmlspecialchars($booking['details']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($booking['travel_date'])); ?></td>
                                        <td>₹<?php echo number_format($booking['total_amount'], 0); ?></td>
                                        <td><span class="badge badge-<?php echo $booking['status'] == 'confirmed' ? 'success' : ($booking['status'] == 'pending' ? 'warning' : 'danger'); ?>"><?php echo ucfirst($booking['status']); ?></span></td>
                                        <td><span class="badge badge-<?php echo $booking['payment_status'] == 'paid' ? 'success' : ($booking['payment_status'] == 'pending' ? 'warning' : 'danger'); ?>"><?php echo ucfirst($booking['payment_status']); ?></span></td>
                                        <td><?php echo date('M j, Y H:i', strtotime($booking['booking_date'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No bookings found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>

