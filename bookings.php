<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

include "db.php";

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']); // cast to int for safety

// Handle cancel booking
// Handle cancel booking
if (isset($_POST['cancel_booking'])) {
    $booking_id = intval($_POST['booking_id']);

    // Select the booking and ensure it belongs to user AND is within 24 hours AND status allows cancellation
    $sql_check = "
        SELECT id, booking_type, item_id, quantity, status, booking_date
        FROM bookings
        WHERE id = ? 
          AND user_id = ?
          AND status IN ('pending', 'confirmed')
          AND TIMESTAMPDIFF(HOUR, booking_date, NOW()) <= 24
        LIMIT 1
    ";

    if ($stmt = $conn->prepare($sql_check)) {
        $stmt->bind_param("ii", $booking_id, $user_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $booking = $res->fetch_assoc();

            // Proceed to cancel
            $update_sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ?";
            if ($u_stmt = $conn->prepare($update_sql)) {
                $u_stmt->bind_param("ii", $booking_id, $user_id);
                if ($u_stmt->execute()) {
                    // Restore availability using a whitelist for table names
                    $type = $booking['booking_type'];
                    $item_id = intval($booking['item_id']);
                    $quantity = intval($booking['quantity']);

                    $table_map = [
                        'flight'   => 'flights',
                        'train'    => 'trains',
                        'bus'      => 'buses',
                        'hotel'    => 'hotels',
                        'cruise'   => 'cruises',
                        'holiday'  => 'holiday_packages'
                    ];

                    if (isset($table_map[$type])) {
                        $table_name = $table_map[$type];

                        // Decide which column to increment
                        if (in_array($type, ['flight', 'train', 'bus'])) {
                            $col = 'available_seats';
                        } elseif ($type === 'hotel') {
                            $col = 'available_rooms';
                        } elseif ($type === 'cruise') {
                            $col = 'available_cabins';
                        } else {
                            $col = 'available_slots';
                        }

                        // Use prepared statement for increment (parameterize values; table/column from whitelist)
                        $update_avail = "UPDATE `$table_name` SET `$col` = `$col` + ? WHERE id = ?";
                        if ($a_stmt = $conn->prepare($update_avail)) {
                            $a_stmt->bind_param("ii", $quantity, $item_id);
                            $a_stmt->execute();
                            $a_stmt->close();
                        }
                    }

                    $_SESSION['success_message'] = "Booking cancelled successfully! To request a refund, please visit the admin panel at manage-payments.php or contact support.";
                } else {
                    $_SESSION['error_message'] = "Failed to cancel booking. Please try again.";
                }
                $u_stmt->close();
            } else {
                $_SESSION['error_message'] = "Failed to prepare cancellation query.";
            }
        } else {
            // No eligible booking found
            $_SESSION['error_message'] = "Cancellation not allowed: either booking not found, not yours, already processed, or the 24-hour cancellation window has expired.";
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Failed to prepare booking check.";
    }

    header("Location: bookings.php");
    exit();
}


// Check if bookings table exists, if not create it
$check_table = "SHOW TABLES LIKE 'bookings'";
$table_exists = mysqli_query($conn, $check_table);

if (!$table_exists || mysqli_num_rows($table_exists) == 0) {
    // Create bookings table
    $create_table = "CREATE TABLE bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        booking_type ENUM('flight', 'hotel', 'train', 'bus', 'cruise', 'holiday') NOT NULL,
        booking_reference VARCHAR(20) UNIQUE,
        item_id INT NOT NULL,
        details TEXT,
        booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        travel_date DATE,
        check_in_date DATE,
        check_out_date DATE,
        quantity INT DEFAULT 1,
        total_amount DECIMAL(10,2),
        status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
        payment_status ENUM('paid', 'pending', 'failed', 'refunded') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    mysqli_query($conn, $create_table);
}

// Fetch user's bookings with actual payment status from payments table
$sql = "
SELECT b.*,
       TIMESTAMPDIFF(HOUR, b.booking_date, NOW()) AS hours_passed,
       (SELECT payment_status FROM payments WHERE booking_id = b.id ORDER BY payment_date DESC LIMIT 1) as actual_payment_status
FROM bookings b
WHERE b.user_id = ?
ORDER BY b.booking_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


// Get some sample bookings if table is empty (for demo)

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - TravelEase</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/all.min.css">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"> -->
<link rel="stylesheet" href="css/fonts.css">
    <link rel="icon" href="travelEASEonly.png" type="image/png">
   <style>
     
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: #333;
        }

        .bookings-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }

        .bookings-header {
            background: linear-gradient(135deg, #2c3e50, #4a6491);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .bookings-header h1 {
            font-size: 2.8rem;
            margin-bottom: 15px;
        }

        .bookings-header p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #2980b9;
        }

        .stat-card .number {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .stat-card .label {
            color: #7f8c8d;
            font-size: 1rem;
        }

        .bookings-list {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .bookings-list h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2980b9;
            font-size: 1.8rem;
        }

        .booking-card {
            border: 1px solid #e1e8ed;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .booking-card:hover {
            border-color: #2980b9;
            box-shadow: 0 8px 25px rgba(41, 128, 185, 0.15);
            transform: translateY(-3px);
        }

        .booking-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 5px;
            background: #2980b9;
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .booking-type {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .booking-type-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .flight-icon { background: linear-gradient(135deg, #3498db, #2980b9); }
        .hotel-icon { background: linear-gradient(135deg, #e74c3c, #c0392b); }
        .train-icon { background: linear-gradient(135deg, #27ae60, #219653); }
        .bus-icon { background: linear-gradient(135deg, #f39c12, #d35400); }
        .cruise-icon { background: linear-gradient(135deg, #8e44ad, #9b59b6); }
        .holiday-icon { background: linear-gradient(135deg, #1abc9c, #16a085); }

        .booking-type-text h3 {
            color: #2c3e50;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .booking-type-text p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .booking-reference {
            background: #f8f9fa;
            padding: 8px 15px;
            border-radius: 20px;
            font-family: monospace;
            font-weight: 600;
            color: #2c3e50;
            border: 1px dashed #bdc3c7;
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .detail-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .detail-label {
            display: block;
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .detail-value {
            color: #2c3e50;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .booking-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
            flex-wrap: wrap;
            gap: 15px;
        }

        .status-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-confirmed { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .status-completed { background: #d1ecf1; color: #0c5460; }

        .payment-status {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .payment-paid { background: #d4edda; color: #155724; }
        .payment-success { background: #d4edda; color: #155724; }
        .payment-pending { background: #fff3cd; color: #856404; }
        .payment-failed { background: #f8d7da; color: #721c24; }
        .payment-refunded { background: #d1ecf1; color: #0c5460; }

        .booking-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(to right, #2980b9, #2c3e50);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #1a5276, #1c2833);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(41, 128, 185, 0.3);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #2c3e50;
            border: 2px solid #e1e8ed;
        }

        .btn-secondary:hover {
            background: #e9ecef;
            border-color: #2980b9;
            color: #2980b9;
        }

        .btn-danger {
            background: linear-gradient(to right, #e74c3c, #c0392b);
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(to right, #c0392b, #a93226);
        }

        .no-bookings {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }

        .no-bookings i {
            font-size: 4rem;
            color: #bdc3c7;
            margin-bottom: 20px;
        }

        .no-bookings h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .no-bookings p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .quick-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 700px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #2c3e50, #4a6491);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.8rem;
        }

        .close-modal {
            color: white;
            font-size: 2rem;
            font-weight: bold;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.3s;
        }

        .close-modal:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .modal-body {
            padding: 30px;
        }

        .detail-row {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-row-label {
            font-weight: 600;
            color: #7f8c8d;
        }

        .detail-row-value {
            color: #2c3e50;
            font-weight: 500;
        }

        /* Print Styles */
        @media print {
            body * {
                visibility: hidden;
            }
            .print-ticket, .print-ticket * {
                visibility: visible;
            }
            .print-ticket {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: white;
                padding: 20px;
            }
            .no-print {
                display: none !important;
            }
        }

        .print-ticket {
            display: none;
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            border: 2px solid #2c3e50;
        }

        .ticket-header {
            text-align: center;
            border-bottom: 3px solid #2980b9;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .ticket-header h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .ticket-body {
            margin-bottom: 30px;
        }

        .ticket-section {
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .ticket-section h3 {
            color: #2980b9;
            margin-bottom: 15px;
            border-bottom: 2px solid #2980b9;
            padding-bottom: 10px;
        }

        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .bookings-header h1 {
                font-size: 2.2rem;
            }
            
            .booking-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .booking-footer {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .booking-actions {
                width: 100%;
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar (same as your index.php) -->
    <?php include '.\includes\navbar.php'; ?>

    <div class="bookings-container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="bookings-header">
            <h1>My Bookings</h1>
            <p>Manage all your travel bookings in one place</p>
            <div class="stats-cards">
                <?php
                // Count different types of bookings
                $total_bookings = mysqli_num_rows($result);
                $confirmed_bookings = 0;
                $total_spent = 0;
                
                if ($result) {
                    mysqli_data_seek($result, 0); // Reset pointer
                    while($row = mysqli_fetch_assoc($result)) {
                        if ($row['status'] == 'confirmed' || $row['status'] == 'completed') {
                            $confirmed_bookings++;
                        }
                        // Only count confirmed/completed bookings with successful, non-refunded payments towards total spent
                        // Check if payment is successful and not refunded by querying payments table
                        $payment_check = mysqli_query($conn, "SELECT payment_status FROM payments WHERE booking_id = '{$row['id']}' ORDER BY payment_date DESC LIMIT 1");
                        $payment_data = mysqli_fetch_assoc($payment_check);
                        
                        // Get actual payment status
                        $actual_payment_status = $payment_data['payment_status'] ?? $row['payment_status'] ?? 'pending';
                        
                        // Only add to total_spent if:
                        // 1. Booking is confirmed or completed (not pending)
                        // 2. Payment status is 'success' or 'paid' (not pending/failed)
                        // 3. Payment is not refunded
                        if ( 
                            ($actual_payment_status == 'success' || $actual_payment_status == 'paid') && 
                            $actual_payment_status != 'refunded') {
                            $total_spent += $row['total_amount'];
                        }
                    }
                    mysqli_data_seek($result, 0); // Reset pointer again
                }
                ?>
                <div class="stat-card">
                    <i class="fas fa-suitcase"></i>
                    <div class="number"><?php echo $total_bookings; ?></div>
                    <div class="label">Total Bookings</div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-check-circle"></i>
                    <div class="number"><?php echo $confirmed_bookings; ?></div>
                    <div class="label">Confirmed</div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-rupee-sign"></i>
                    <div class="number">₹<?php echo number_format($total_spent, 0); ?></div>
                    <div class="label">Total Spent</div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-calendar-alt"></i>
                    <div class="number"><?php echo date('M Y'); ?></div>
                    <div class="label">Current Month</div>
                </div>
            </div>
        </div>

        <div class="bookings-list">
            <h2>All Travel Bookings</h2>
            
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while($booking = mysqli_fetch_assoc($result)): 
                    // Determine icon class based on booking type
                    $icon_class = '';
                    $icon_text = '';
                    switch($booking['booking_type']) {
                        case 'flight': $icon_class = 'flight-icon'; $icon_text = 'Flight'; break;
                        case 'hotel': $icon_class = 'hotel-icon'; $icon_text = 'Hotel'; break;
                        case 'train': $icon_class = 'train-icon'; $icon_text = 'Train'; break;
                        case 'bus': $icon_class = 'bus-icon'; $icon_text = 'Bus'; break;
                        case 'cruise': $icon_class = 'cruise-icon'; $icon_text = 'Cruise'; break;
                        case 'holiday': $icon_class = 'holiday-icon'; $icon_text = 'Holiday Package'; break;
                    }
                ?>
                <div class="booking-card" 
                     data-booking-id="<?php echo $booking['id']; ?>"
                     data-booking-ref="<?php echo htmlspecialchars($booking['booking_reference']); ?>"
                     data-booking-type="<?php echo $booking['booking_type']; ?>"
                     data-booking-details="<?php echo htmlspecialchars($booking['details']); ?>"
                     data-travel-date="<?php echo $booking['travel_date']; ?>"
                     data-check-in="<?php echo $booking['check_in_date'] ?? ''; ?>"
                     data-check-out="<?php echo $booking['check_out_date'] ?? ''; ?>"
                     data-quantity="<?php echo $booking['quantity']; ?>"
                     data-amount="<?php echo $booking['total_amount']; ?>"
                     data-status="<?php echo $booking['status']; ?>"
                     data-payment-status="<?php echo $booking['actual_payment_status'] ?? $booking['payment_status'] ?? 'pending'; ?>"
                     data-booking-date="<?php echo $booking['booking_date']; ?>"
     data-hours-passed="<?php echo isset($booking['hours_passed']) ? $booking['hours_passed'] : ''; ?>"
                     >
                    <div class="booking-header">
                        <div class="booking-type">
                            <div class="booking-type-icon <?php echo $icon_class; ?>">
                                <?php 
                                switch($booking['booking_type']) {
                                    case 'flight': echo '<i class="fas fa-plane"></i>'; break;
                                    case 'hotel': echo '<i class="fas fa-hotel"></i>'; break;
                                    case 'train': echo '<i class="fas fa-train"></i>'; break;
                                    case 'bus': echo '<i class="fas fa-bus"></i>'; break;
                                    case 'cruise': echo '<i class="fas fa-ship"></i>'; break;
                                    case 'holiday': echo '<i class="fas fa-suitcase-rolling"></i>'; break;
                                }
                                ?>
                            </div>
                            <div class="booking-type-text">
                                <h3><?php echo htmlspecialchars($booking['details']); ?></h3>
                                <p><?php echo $icon_text; ?> • Booked on <?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></p>
                            </div>
                        </div>
                        <div class="booking-reference">
                            #<?php echo htmlspecialchars($booking['booking_reference']); ?>
                        </div>
                    </div>
                    
                    <div class="booking-details">
                        <div class="detail-item">
                            <span class="detail-label">Travel Date</span>
                            <span class="detail-value">
                                <?php echo date('M j, Y', strtotime($booking['travel_date'])); ?>
                            </span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">Amount</span>
                            <span class="detail-value">
                                ₹<?php echo number_format($booking['total_amount'], 0); ?>
                            </span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">Booking Type</span>
                            <span class="detail-value">
                                <?php echo ucfirst($booking['booking_type']); ?>
                            </span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">Booking ID</span>
                            <span class="detail-value">
                                <?php echo $booking['id']; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="booking-footer">
                        <div>
                            <span class="status-badge status-<?php echo $booking['status']; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                            <?php 
                            // Get actual payment status (from payments table if available, otherwise from bookings table)
                            $display_payment_status = $booking['actual_payment_status'] ?? $booking['payment_status'] ?? 'pending';
                            if ($display_payment_status): ?>
                            <span class="payment-status payment-<?php echo $display_payment_status; ?>" style="margin-left: 10px;">
                                Payment: <?php echo ucfirst($display_payment_status); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="booking-actions">
                            <button class="btn btn-primary view-details-btn" data-booking-id="<?php echo $booking['id']; ?>">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                            <button class="btn btn-secondary print-ticket-btn" data-booking-id="<?php echo $booking['id']; ?>">
                                <i class="fas fa-print"></i> Print Ticket
                            </button>
                            <?php 
                            // Show cancel button if booking is pending/confirmed AND booking was made within last 24 hours
                            // Note: Payment status does NOT affect cancel button visibility - users can cancel regardless of payment status
                            $show_cancel = false;

// Check if booking status allows cancellation
if (in_array($booking['status'], ['pending', 'confirmed'])) {
    // Use DB-provided hours_passed if available
    $hours_passed = isset($booking['hours_passed']) ? intval($booking['hours_passed']) : PHP_INT_MAX;
    $show_cancel = ($hours_passed <= 24);
}

                            
                            if ($show_cancel): ?>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to cancel this booking? You will need to request a refund separately. This action cannot be undone.');">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <button type="submit" name="cancel_booking" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-bookings">
                    <i class="fas fa-suitcase-rolling"></i>
                    <h3>No Bookings Yet</h3>
                    <p>You haven't made any bookings yet. Start planning your next adventure!</p>
                    <div class="quick-links">
                        <a href="flightbook.php" class="btn btn-primary">
                            <i class="fas fa-plane"></i> Book a Flight
                        </a>
                        <a href="hotelbook.php" class="btn btn-primary">
                            <i class="fas fa-hotel"></i> Find a Hotel
                        </a>
                        <a href="holidaybook.php" class="btn btn-primary">
                            <i class="fas fa-suitcase-rolling"></i> Explore Packages
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>



    <!-- View Details Modal -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-info-circle"></i> Booking Details</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Print Ticket (hidden by default) -->
    <div id="printTicket" class="print-ticket">
        <!-- Content will be loaded here -->
    </div>

    <script>
        // Simple dropdown toggle for profile menu
        document.addEventListener('DOMContentLoaded', function() {
            const profileButton = document.getElementById('profileButton');
            const profileMenu = document.getElementById('profileMenu');
            
            if (profileButton && profileMenu) {
                profileButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function() {
                    profileMenu.style.display = 'none';
                });
            }
            
            // Mobile menu toggle (if you have this functionality)
            const mobileToggle = document.getElementById('mobileToggle');
            const navMenu = document.getElementById('navMenu');
            
            if (mobileToggle && navMenu) {
                mobileToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                });
            }
            
            // View Details Button
            document.querySelectorAll('.view-details-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const bookingId = this.getAttribute('data-booking-id');
                    showBookingDetails(bookingId);
                });
            });

            // Print Ticket Button
            document.querySelectorAll('.print-ticket-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const bookingId = this.getAttribute('data-booking-id');
                    printBookingTicket(bookingId);
                });
            });

            // Close modal when clicking outside
            window.onclick = function(event) {
                const modal = document.getElementById('detailsModal');
                if (event.target == modal) {
                    closeModal();
                }
            }
        });

        // Function to show booking details in modal
        function showBookingDetails(bookingId) {
            const booking = getBookingData(bookingId);
            if (!booking) return;

            const modalBody = document.getElementById('modalBody');
            const iconClass = getIconClass(booking.booking_type);
            const icon = getIcon(booking.booking_type);

            modalBody.innerHTML = `
                <div class="detail-row">
                    <div class="detail-row-label">Booking Reference</div>
                    <div class="detail-row-value"><strong>#${booking.booking_reference}</strong></div>
                </div>
                <div class="detail-row">
                    <div class="detail-row-label">Booking Type</div>
                    <div class="detail-row-value">
                        <span class="booking-type-icon ${iconClass}" style="display: inline-flex; width: 30px; height: 30px; align-items: center; justify-content: center; border-radius: 5px; margin-right: 10px;">
                            <i class="${icon}"></i>
                        </span>
                        ${booking.booking_type.charAt(0).toUpperCase() + booking.booking_type.slice(1)}
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-row-label">Details</div>
                    <div class="detail-row-value">${booking.details}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-row-label">Travel Date</div>
                    <div class="detail-row-value">${formatDate(booking.travel_date)}</div>
                </div>
                ${booking.check_in_date ? `
                <div class="detail-row">
                    <div class="detail-row-label">Check-in Date</div>
                    <div class="detail-row-value">${formatDate(booking.check_in_date)}</div>
                </div>
                ` : ''}
                ${booking.check_out_date ? `
                <div class="detail-row">
                    <div class="detail-row-label">Check-out Date</div>
                    <div class="detail-row-value">${formatDate(booking.check_out_date)}</div>
                </div>
                ` : ''}
                <div class="detail-row">
                    <div class="detail-row-label">Quantity</div>
                    <div class="detail-row-value">${booking.quantity}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-row-label">Total Amount</div>
                    <div class="detail-row-value"><strong>₹${parseFloat(booking.total_amount).toLocaleString('en-IN')}</strong></div>
                </div>
                <div class="detail-row">
                    <div class="detail-row-label">Booking Status</div>
                    <div class="detail-row-value">
                        <span class="status-badge status-${booking.status}">${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}</span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-row-label">Payment Status</div>
                    <div class="detail-row-value">
                        ${booking.payment_status ? `
                        <span class="payment-status payment-${booking.payment_status}">${booking.payment_status.charAt(0).toUpperCase() + booking.payment_status.slice(1)}</span>
                        ` : '<span class="payment-status payment-pending">Pending</span>'}
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-row-label">Booking Date</div>
                    <div class="detail-row-value">${formatDateTime(booking.booking_date)}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-row-label">Booking ID</div>
                    <div class="detail-row-value">${booking.id}</div>
                </div>
            `;

            document.getElementById('detailsModal').style.display = 'block';
        }

        // Function to print booking ticket
        function printBookingTicket(bookingId) {
            const booking = getBookingData(bookingId);
            if (!booking) return;

            const icon = getIcon(booking.booking_type);
            const printDiv = document.getElementById('printTicket');
            
            printDiv.innerHTML = `
                <div class="ticket-header">
                    <h1>TravelEase Booking Ticket</h1>
                    <p>Booking Reference: <strong>#${booking.booking_reference}</strong></p>
                </div>
                <div class="ticket-body">
                    <div class="ticket-section">
                        <h3><i class="${icon}"></i> Booking Information</h3>
                        <div class="detail-row">
                            <div class="detail-row-label">Booking Type:</div>
                            <div class="detail-row-value">${booking.booking_type.charAt(0).toUpperCase() + booking.booking_type.slice(1)}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-row-label">Details:</div>
                            <div class="detail-row-value">${booking.details}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-row-label">Booking ID:</div>
                            <div class="detail-row-value">${booking.id}</div>
                        </div>
                    </div>
                    <div class="ticket-section">
                        <h3><i class="fas fa-calendar"></i> Travel Dates</h3>
                        <div class="detail-row">
                            <div class="detail-row-label">Travel Date:</div>
                            <div class="detail-row-value">${formatDate(booking.travel_date)}</div>
                        </div>
                        ${booking.check_in_date ? `
                        <div class="detail-row">
                            <div class="detail-row-label">Check-in:</div>
                            <div class="detail-row-value">${formatDate(booking.check_in_date)}</div>
                        </div>
                        ` : ''}
                        ${booking.check_out_date ? `
                        <div class="detail-row">
                            <div class="detail-row-label">Check-out:</div>
                            <div class="detail-row-value">${formatDate(booking.check_out_date)}</div>
                        </div>
                        ` : ''}
                    </div>
                    <div class="ticket-section">
                        <h3><i class="fas fa-rupee-sign"></i> Payment Details</h3>
                        <div class="detail-row">
                            <div class="detail-row-label">Quantity:</div>
                            <div class="detail-row-value">${booking.quantity}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-row-label">Total Amount:</div>
                            <div class="detail-row-value"><strong>₹${parseFloat(booking.total_amount).toLocaleString('en-IN')}</strong></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-row-label">Payment Status:</div>
                            <div class="detail-row-value">
                                ${booking.payment_status ? `
                                <span class="payment-status payment-${booking.payment_status}" style="padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-block;">
                                    ${booking.payment_status.charAt(0).toUpperCase() + booking.payment_status.slice(1)}
                                </span>
                                ` : '<span class="payment-status payment-pending" style="padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-block;">Pending</span>'}
                            </div>
                        </div>
                    </div>
                    <div class="ticket-section">
                        <h3><i class="fas fa-info-circle"></i> Status</h3>
                        <div class="detail-row">
                            <div class="detail-row-label">Booking Status:</div>
                            <div class="detail-row-value">${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-row-label">Booked On:</div>
                            <div class="detail-row-value">${formatDateTime(booking.booking_date)}</div>
                        </div>
                    </div>
                </div>
                <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;">
                    <p style="color: #7f8c8d;">Thank you for choosing TravelEase!</p>
                    <p style="color: #7f8c8d; font-size: 0.9rem;">For support, contact: support@travelease.com</p>
                </div>
            `;

            printDiv.style.display = 'block';
            window.print();
            printDiv.style.display = 'none';
        }

        // Helper function to get booking data
        function getBookingData(bookingId) {
            const bookingCard = document.querySelector(`.booking-card[data-booking-id="${bookingId}"]`);
            if (!bookingCard) return null;

            return {
                id: bookingCard.getAttribute('data-booking-id'),
                booking_reference: bookingCard.getAttribute('data-booking-ref'),
                booking_type: bookingCard.getAttribute('data-booking-type'),
                details: bookingCard.getAttribute('data-booking-details'),
                travel_date: bookingCard.getAttribute('data-travel-date'),
                check_in_date: bookingCard.getAttribute('data-check-in') || null,
                check_out_date: bookingCard.getAttribute('data-check-out') || null,
                quantity: bookingCard.getAttribute('data-quantity'),
                total_amount: bookingCard.getAttribute('data-amount'),
                status: bookingCard.getAttribute('data-status'),
                payment_status: bookingCard.getAttribute('data-payment-status'),
                booking_date: bookingCard.getAttribute('data-booking-date')
            };
        }

        // Better approach: Store booking data in data attributes
        function closeModal() {
            document.getElementById('detailsModal').style.display = 'none';
        }

        // Helper functions
        function getIconClass(type) {
            const classes = {
                'flight': 'flight-icon',
                'hotel': 'hotel-icon',
                'train': 'train-icon',
                'bus': 'bus-icon',
                'cruise': 'cruise-icon',
                'holiday': 'holiday-icon'
            };
            return classes[type] || 'flight-icon';
        }

        function getIcon(type) {
            const icons = {
                'flight': 'fas fa-plane',
                'hotel': 'fas fa-hotel',
                'train': 'fas fa-train',
                'bus': 'fas fa-bus',
                'cruise': 'fas fa-ship',
                'holiday': 'fas fa-suitcase-rolling'
            };
            return icons[type] || 'fas fa-plane';
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        }

        function formatDateTime(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        }
    </script>
</body>
</html>