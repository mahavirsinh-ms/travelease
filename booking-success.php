<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$booking_id = $_GET['booking_id'] ?? 0;
$transaction_id = $_GET['transaction_id'] ?? '';

if (!$booking_id) {
    header("Location: bookings.php");
    exit();
}

$booking = mysqli_query($conn, "SELECT * FROM bookings WHERE id='$booking_id' AND user_id='{$_SESSION['user_id']}'");
$booking = mysqli_fetch_assoc($booking);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - TravelEase</title>
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/all.min.css">
   <link rel="stylesheet" href="css/fonts.css">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"> -->
 <style>
        .success-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 3rem;
        }
        .success-card {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1 style="color: #2c3e50; margin-bottom: 15px;">Booking Confirmed!</h1>
            <p style="color: #7f8c8d; margin-bottom: 30px;">Your booking has been successfully confirmed.</p>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: left; margin-bottom: 20px;">
                <p><strong>Booking Reference:</strong> <?php echo htmlspecialchars($booking['booking_reference']); ?></p>
                <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($transaction_id); ?></p>
                <p><strong>Amount Paid:</strong> ₹<?php echo number_format($booking['total_amount'], 0); ?></p>
                <p><strong>Status:</strong> <span style="color: #28a745;">Confirmed</span></p>
            </div>

            <a href="bookings.php" class="btn-primary">View My Bookings</a>
            <a href="index.php" class="btn-primary" style="background: #6c757d;">Back to Home</a>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

