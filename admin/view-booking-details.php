<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$booking = null;
$user = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT b.*, u.full_name, u.email, u.phone 
            FROM bookings b 
            JOIN users u ON b.user_id = u.id 
            WHERE b.id = '$id' 
            LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) === 1) {
        $booking = mysqli_fetch_assoc($result);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - TravelEase Admin</title>
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
                <h1><i class="fas fa-info-circle"></i> Booking Details</h1>
                <div class="header-actions">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="view-bookings.php" class="btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                </div>
            </header>

            <?php if(!$booking): ?>
                <div class="content-card">
                    <p style="color: #7f8c8d;">Booking not found.</p>
                </div>
            <?php else: ?>
                <div class="content-card">
                    <h2>Summary</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px;">
                        <div class="detail-item">
                            <span class="detail-label">Reference</span>
                            <span class="detail-value">#<?php echo htmlspecialchars($booking['booking_reference']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Type</span>
                            <span class="detail-value"><?php echo ucfirst($booking['booking_type']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status</span>
                            <span class="detail-value"><?php echo ucfirst($booking['status']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Payment</span>
                            <span class="detail-value"><?php echo ucfirst($booking['payment_status']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Amount</span>
                            <span class="detail-value">₹<?php echo number_format($booking['total_amount'], 0); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Booked On</span>
                            <span class="detail-value"><?php echo date('M j, Y H:i', strtotime($booking['booking_date'])); ?></span>
                        </div>
                    </div>
                </div>

                <div class="content-card">
                    <h2>Customer</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px;">
                        <div class="detail-item">
                            <span class="detail-label">Name</span>
                            <span class="detail-value"><?php echo htmlspecialchars($booking['full_name']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email</span>
                            <span class="detail-value"><?php echo htmlspecialchars($booking['email']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Phone</span>
                            <span class="detail-value"><?php echo htmlspecialchars($booking['phone'] ?? 'N/A'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="content-card">
                    <h2>Travel & Item</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px;">
                        <div class="detail-item">
                            <span class="detail-label">Details</span>
                            <span class="detail-value"><?php echo htmlspecialchars($booking['details']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Travel Date</span>
                            <span class="detail-value"><?php echo date('M j, Y', strtotime($booking['travel_date'])); ?></span>
                        </div>
                        <?php if($booking['check_in_date']): ?>
                        <div class="detail-item">
                            <span class="detail-label">Check-in</span>
                            <span class="detail-value"><?php echo date('M j, Y', strtotime($booking['check_in_date'])); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($booking['check_out_date']): ?>
                        <div class="detail-item">
                            <span class="detail-label">Check-out</span>
                            <span class="detail-value"><?php echo date('M j, Y', strtotime($booking['check_out_date'])); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="detail-item">
                            <span class="detail-label">Quantity</span>
                            <span class="detail-value"><?php echo $booking['quantity']; ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Item ID</span>
                            <span class="detail-value"><?php echo $booking['item_id']; ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>

