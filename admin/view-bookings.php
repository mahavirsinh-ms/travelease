<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle status update
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE bookings SET status='$status' WHERE id='$booking_id'");
    header("Location: view-bookings.php?success=updated");
    exit();
}

// Get all bookings with user info
$bookings = mysqli_query($conn, "SELECT b.*, u.full_name, u.email FROM bookings b JOIN users u ON b.user_id = u.id ORDER BY b.booking_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings - TravelEase Admin</title>
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
                <h1><i class="fas fa-book"></i> View All Bookings</h1>
                <div class="header-actions">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="../index.php" class="btn-secondary"><i class="fas fa-home"></i> View Site</a>
                </div>
            </header>

            <div class="content-card">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Booking Ref</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Details</th>
                                <th>Travel Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($bookings) > 0): ?>
                                <?php while($booking = mysqli_fetch_assoc($bookings)): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($booking['booking_reference']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($booking['full_name']); ?><br>
                                        <small style="color: #7f8c8d;"><?php echo htmlspecialchars($booking['email']); ?></small>
                                    </td>
                                    <td><span class="badge badge-info"><?php echo ucfirst($booking['booking_type']); ?></span></td>
                                    <td><?php echo htmlspecialchars($booking['details']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($booking['travel_date'])); ?></td>
                                    <td>₹<?php echo number_format($booking['total_amount'], 0); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <select name="status" class="form-control" style="width: auto; display: inline-block;" onchange="this.form.submit()">
                                                <option value="pending" <?php echo $booking['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="confirmed" <?php echo $booking['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                <option value="cancelled" <?php echo $booking['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                <option value="completed" <?php echo $booking['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php 
                                            echo $booking['payment_status'] == 'paid' ? 'success' : 
                                                ($booking['payment_status'] == 'pending' ? 'warning' : 'danger'); 
                                        ?>"><?php echo ucfirst($booking['payment_status']); ?></span>
                                    </td>
                                    <td>
                                        <a href="view-booking-details.php?id=<?php echo $booking['id']; ?>" class="btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">No bookings found</td>
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

