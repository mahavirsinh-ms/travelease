<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle payment status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $payment_status = $_POST['payment_status'];
    $transaction_id = $_POST['transaction_id'] ?? null;
    
    $sql = "UPDATE payments SET payment_status='$payment_status', transaction_id='$transaction_id' WHERE id='$id'";
    mysqli_query($conn, $sql);
    
    header("Location: manage-payments.php?success=updated");
    exit();
}

// Handle refund
if (isset($_GET['refund'])) {
    $id = $_GET['refund'];
    
    // Get payment details before refunding
    $payment_query = "SELECT * FROM payments WHERE id='$id'";
    $payment_result = mysqli_query($conn, $payment_query);
    
    if ($payment_result && mysqli_num_rows($payment_result) > 0) {
        $payment_data = mysqli_fetch_assoc($payment_result);
        $booking_id = $payment_data['booking_id'];
        $amount = $payment_data['amount'];
        
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Update payment status to refunded
            mysqli_query($conn, "UPDATE payments SET payment_status='refunded' WHERE id='$id'");
            
            // Update booking payment_status to refunded
            mysqli_query($conn, "UPDATE bookings SET payment_status='refunded' WHERE id='$booking_id'");
            
            // Commit transaction
            mysqli_commit($conn);
            
            header("Location: manage-payments.php?success=refunded");
            exit();
        } catch (Exception $e) {
            mysqli_rollback($conn);
            header("Location: manage-payments.php?error=refund_failed");
            exit();
        }
    } else {
        header("Location: manage-payments.php?error=payment_not_found");
        exit();
    }
}

// Get filters
$filter_status = $_GET['status'] ?? '';
$filter_method = $_GET['method'] ?? '';
$search = $_GET['search'] ?? '';

// Build query with filters
$where = [];
if ($filter_status) {
    $where[] = "p.payment_status = '$filter_status'";
}
if ($filter_method) {
    $where[] = "p.payment_method = '$filter_method'";
}
if ($search) {
    $where[] = "(p.transaction_id LIKE '%$search%' OR u.full_name LIKE '%$search%' OR u.email LIKE '%$search%' OR b.booking_reference LIKE '%$search%')";
}

$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Get payments with user and booking info - UPDATED with correct column names
$payments = mysqli_query($conn, "
    SELECT p.*, 
           u.full_name AS user_name,
           u.email AS user_email, 
           b.booking_reference, 
           b.total_amount,
           b.status AS booking_status
    FROM payments p
    LEFT JOIN users u ON p.user_id = u.id
    LEFT JOIN bookings b ON p.booking_id = b.id
    $where_clause
    ORDER BY p.payment_date DESC
");


// Get stats (deduct refunded amounts from profit)
$stats = mysqli_query($conn, "
    SELECT 
        COUNT(*) as total_payments,
        SUM(CASE WHEN payment_status = 'success' THEN amount ELSE 0 END) as total_success_amount,
        SUM(CASE WHEN payment_status = 'refunded' THEN amount ELSE 0 END) as total_refunded_amount,
        SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN payment_status = 'success' THEN 1 ELSE 0 END) as success_count,
        SUM(CASE WHEN payment_status = 'failed' THEN 1 ELSE 0 END) as failed_count,
        SUM(CASE WHEN payment_status = 'refunded' THEN 1 ELSE 0 END) as refunded_count
    FROM payments
");
$stat_data = mysqli_fetch_assoc($stats);

// Calculate net profit (successful payments minus refunds)
$net_profit = $stat_data['total_success_amount'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments - TravelEase Admin</title>
    <link rel="stylesheet" href="../css/all.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .payment-icon {
            color: #4CAF50;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-warning {
            background: #fff3cd;
            color: #856404;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .method-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            margin-right: 5px;
        }
        .credit_card { background: #e3f2fd; color: #1565c0; }
        .debit_card { background: #e8f5e9; color: #2e7d32; }
        .net_banking { background: #fff3e0; color: #ef6c00; }
        .upi { background: #f3e5f5; color: #7b1fa2; }
        .wallet { background: #e0f7fa; color: #006064; }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }
        
        .stat-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .total-amount {
            color: #4CAF50;
        }
        
        .filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .filter-group {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-item {
            flex: 1;
            min-width: 200px;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        
        .search-box input {
            padding-left: 35px;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .payment-details-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }
        
        .user-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .export-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .no-data i {
            font-size: 48px;
            margin-bottom: 20px;
            color: #ddd;
        }
        
        /* Highlight rows that need refunds */
        .refund-required-row {
            background-color: #fff3cd !important;
            border-left: 4px solid #ffc107;
        }
        
        .refund-required-row:hover {
            background-color: #ffe69c !important;
        }
        
        .refund-badge {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1><i class="fas fa-credit-card payment-icon"></i> Manage Payments</h1>
                <div class="header-actions">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="../index.php" class="btn-secondary"><i class="fas fa-home"></i> View Site</a>
                </div>
            </header>

            <?php if(isset($_GET['success'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    Payment <?php echo $_GET['success']; ?> successfully!
                </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['error'])): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    <?php 
                    if ($_GET['error'] == 'refund_failed') {
                        echo "Failed to process refund. Please try again.";
                    } elseif ($_GET['error'] == 'payment_not_found') {
                        echo "Payment not found.";
                    } else {
                        echo "An error occurred. Please try again.";
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stat_data['total_payments'] ?? 0; ?></div>
                    <div class="stat-label">Total Payments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value total-amount">₹<?php echo number_format($net_profit, 0); ?></div>
                    <div class="stat-label">Net Profit</div>
                    <small style="color: #666; font-size: 11px;">
                        Revenue: ₹<?php echo number_format($stat_data['total_success_amount'] ?? 0, 0); ?> | 
                        Refunds: ₹<?php echo number_format($stat_data['total_refunded_amount'] ?? 0, 0); ?>
                    </small>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stat_data['success_count'] ?? 0; ?></div>
                    <div class="stat-label">Successful</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stat_data['pending_count'] ?? 0; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color: #dc3545;"><?php echo $stat_data['refunded_count'] ?? 0; ?></div>
                    <div class="stat-label">Refunded</div>
                    <small style="color: #666; font-size: 11px;">
                        ₹<?php echo number_format($stat_data['total_refunded_amount'] ?? 0, 0); ?>
                    </small>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters">
                <form method="GET" action="">
                    <div class="filter-group">
                        <div class="filter-item">
                            <label>Payment Status</label>
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="pending" <?php echo $filter_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="success" <?php echo $filter_status == 'success' ? 'selected' : ''; ?>>Success</option>
                                <option value="failed" <?php echo $filter_status == 'failed' ? 'selected' : ''; ?>>Failed</option>
                                <option value="refunded" <?php echo $filter_status == 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label>Payment Method</label>
                            <select name="method" class="form-control" onchange="this.form.submit()">
                                <option value="">All Methods</option>
                                <option value="credit_card" <?php echo $filter_method == 'credit_card' ? 'selected' : ''; ?>>Credit Card</option>
                                <option value="debit_card" <?php echo $filter_method == 'debit_card' ? 'selected' : ''; ?>>Debit Card</option>
                                <option value="net_banking" <?php echo $filter_method == 'net_banking' ? 'selected' : ''; ?>>Net Banking</option>
                                <option value="upi" <?php echo $filter_method == 'upi' ? 'selected' : ''; ?>>UPI</option>
                                <option value="wallet" <?php echo $filter_method == 'wallet' ? 'selected' : ''; ?>>Wallet</option>
                            </select>
                        </div>
                        <div class="filter-item search-box">
                            <label>Search</label>
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" class="form-control" placeholder="Transaction ID, Name, Email, or Booking Reference" value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="filter-item" style="align-self: flex-end;">
                            <button type="submit" class="btn-primary" style="padding: 10px 20px;">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="manage-payments.php" class="btn-secondary" style="padding: 10px 20px;">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Payments List -->
            <div class="content-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Payment Transactions</h2>
                    <div class="export-buttons">
                        <button class="btn-sm btn-success" onclick="exportToCSV()">
                            <i class="fas fa-file-excel"></i> Export CSV
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <?php if(mysqli_num_rows($payments) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>User</th>
                                <th>Booking</th>
                                <th>Booking Status</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Payment Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($payment = mysqli_fetch_assoc($payments)): 
                                // Check if refund is required (booking cancelled but payment not refunded)
                                $needs_refund = ($payment['booking_status'] == 'cancelled' && $payment['payment_status'] == 'success');
                                $row_class = $needs_refund ? 'refund-required-row' : '';
                            ?>
                            <tr class="<?php echo $row_class; ?>">
                                <td>
                                    <?php if($payment['transaction_id']): ?>
                                        <code><?php echo htmlspecialchars($payment['transaction_id']); ?></code>
                                    <?php else: ?>
                                        <span class="text-muted">Not Generated</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($payment['user_name'] ?? 'User #' . $payment['user_id']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($payment['user_email'] ?? 'No email'); ?></small>
                                </td>
                                <td>
                                    <?php if($payment['booking_reference']): ?>
                                        <strong><?php echo htmlspecialchars($payment['booking_reference']); ?></strong><br>
                                        <small>Booking</small>
                                    <?php else: ?>
                                        <span class="text-muted">No Booking Data</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $booking_status_badges = [
                                        'pending' => 'badge-warning',
                                        'confirmed' => 'badge-success',
                                        'cancelled' => 'badge-danger',
                                        'completed' => 'badge-info'
                                    ];
                                    $booking_status_labels = [
                                        'pending' => 'Pending',
                                        'confirmed' => 'Confirmed',
                                        'cancelled' => 'Cancelled',
                                        'completed' => 'Completed'
                                    ];
                                    $booking_status = $payment['booking_status'] ?? 'N/A';
                                    ?>
                                    <span class="<?php echo $booking_status_badges[$booking_status] ?? 'badge-warning'; ?>">
                                        <?php echo $booking_status_labels[$booking_status] ?? ucfirst($booking_status); ?>
                                    </span>
                                    <?php if($needs_refund): ?>
                                        <br><small class="refund-badge" style="color: #dc3545; font-weight: bold; margin-top: 5px; display: inline-block;">
                                            <i class="fas fa-exclamation-triangle"></i> Refund Required
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong>₹<?php echo number_format($payment['amount'], 0); ?></strong>
                                </td>
                                <td>
                                    <?php 
                                    $method_classes = [
                                        'credit_card' => 'credit_card',
                                        'debit_card' => 'debit_card',
                                        'net_banking' => 'net_banking',
                                        'upi' => 'upi',
                                        'wallet' => 'wallet'
                                    ];
                                    $method_labels = [
                                        'credit_card' => 'Credit Card',
                                        'debit_card' => 'Debit Card',
                                        'net_banking' => 'Net Banking',
                                        'upi' => 'UPI',
                                        'wallet' => 'Wallet'
                                    ];
                                    ?>
                                    <span class="method-badge <?php echo $method_classes[$payment['payment_method']] ?? ''; ?>">
                                        <?php echo $method_labels[$payment['payment_method']] ?? $payment['payment_method']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $status_badges = [
                                        'pending' => 'badge-warning',
                                        'success' => 'badge-success',
                                        'failed' => 'badge-danger',
                                        'refunded' => 'badge-info'
                                    ];
                                    $status_labels = [
                                        'pending' => 'Pending',
                                        'success' => 'Success',
                                        'failed' => 'Failed',
                                        'refunded' => 'Refunded'
                                    ];
                                    ?>
                                    <span class="<?php echo $status_badges[$payment['payment_status']] ?? 'badge-warning'; ?>">
                                        <?php echo $status_labels[$payment['payment_status']] ?? $payment['payment_status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($payment['payment_date'])); ?><br>
                                    <small><?php echo date('H:i:s', strtotime($payment['payment_date'])); ?></small>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-sm btn-primary view-details" data-payment='<?php echo htmlspecialchars(json_encode($payment), ENT_QUOTES, 'UTF-8'); ?>'>
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if(($payment['payment_status'] == 'pending' || $payment['payment_status'] == 'failed') && $payment['payment_status'] != 'refunded'): ?>
                                            <button class="btn-sm btn-success mark-success" data-id="<?php echo $payment['id']; ?>">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if($payment['payment_status'] == 'success' && ($payment['booking_status'] == 'cancelled' || $payment['booking_status'] == 'pending')): ?>
                                            <a href="manage-payments.php?refund=<?php echo $payment['id']; ?>" class="btn-sm btn-warning" onclick="return confirm('Are you sure you want to refund this payment? This will deduct from your profits.')">
                                                <i class="fas fa-undo"></i> Refund
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-receipt"></i>
                        <h3>No Payment Transactions Found</h3>
                        <p>No payment records match your search criteria.</p>
                        <?php if($filter_status || $filter_method || $search): ?>
                            <a href="manage-payments.php" class="btn-primary">Clear Filters</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Payment Details Modal -->
    <div id="paymentDetailsModal" class="payment-details-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Payment Details</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div id="modalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div id="updateStatusModal" class="payment-details-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Payment Status</h3>
                <button class="modal-close" onclick="closeUpdateModal()">&times;</button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="id" id="updatePaymentId">
                <input type="hidden" name="update_status" value="1">
                
                <div class="form-group">
                    <label>Payment Status</label>
                    <select name="payment_status" class="form-control" required>
                        <option value="pending">Pending</option>
                        <option value="success">Success</option>
                        <option value="failed">Failed</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Transaction ID (Optional)</label>
                    <input type="text" name="transaction_id" class="form-control" placeholder="Enter transaction ID">
                </div>
                
                <div style="margin-top: 20px; text-align: right;">
                    <button type="button" class="btn-secondary" onclick="closeUpdateModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // View payment details
        document.querySelectorAll('.view-details').forEach(button => {
            button.addEventListener('click', function() {
                const payment = JSON.parse(this.getAttribute('data-payment'));
                showPaymentDetails(payment);
            });
        });
        
        // Mark as success
        document.querySelectorAll('.mark-success').forEach(button => {
            button.addEventListener('click', function() {
                const paymentId = this.getAttribute('data-id');
                document.getElementById('updatePaymentId').value = paymentId;
                document.querySelector('#updateStatusModal select[name="payment_status"]').value = 'success';
                document.getElementById('updateStatusModal').style.display = 'flex';
            });
        });
        
        function showPaymentDetails(payment) {
            const modalBody = document.getElementById('modalBody');
            
            const html = `
                <div class="user-info">
                    <h4>User Information</h4>
                    <p><strong>Full Name:</strong> ${payment.user_name || 'User #' + payment.user_id}</p>
                    <p><strong>Email:</strong> ${payment.user_email || 'No email'}</p>
                    <p><strong>User ID:</strong> ${payment.user_id}</p>
                </div>
                
                <h4>Payment Information</h4>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Payment ID:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">${payment.id}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Amount:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">₹${parseFloat(payment.amount).toLocaleString('en-IN')}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Payment Method:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">${getMethodLabel(payment.payment_method)}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Status:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;"><span class="${getStatusClass(payment.payment_status)}">${getStatusLabel(payment.payment_status)}</span></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Transaction ID:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;"><code>${payment.transaction_id || 'N/A'}</code></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Payment Date:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">${new Date(payment.payment_date).toLocaleString()}</td>
                    </tr>
                </table>
                
                <h4 style="margin-top: 20px;">Booking Information</h4>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Booking ID:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">${payment.booking_id}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Booking Reference:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">${payment.booking_reference || 'N/A'}</td>
                    </tr>
                   
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Booking Amount:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">₹${parseFloat(payment.total_amount || 0).toLocaleString('en-IN')}</td>
                    </tr>
                </table>
            `;
            
            modalBody.innerHTML = html;
            document.getElementById('paymentDetailsModal').style.display = 'flex';
        }
        
        function getMethodLabel(method) {
            const labels = {
                'credit_card': 'Credit Card',
                'debit_card': 'Debit Card',
                'net_banking': 'Net Banking',
                'upi': 'UPI',
                'wallet': 'Wallet'
            };
            return labels[method] || method;
        }
        
        function getStatusLabel(status) {
            const labels = {
                'pending': 'Pending',
                'success': 'Success',
                'failed': 'Failed',
                'refunded': 'Refunded'
            };
            return labels[status] || status;
        }
        
        function getStatusClass(status) {
            const classes = {
                'pending': 'badge-warning',
                'success': 'badge-success',
                'failed': 'badge-danger',
                'refunded': 'badge-info'
            };
            return classes[status] || '';
        }
        
        function closeModal() {
            document.getElementById('paymentDetailsModal').style.display = 'none';
        }
        
        function closeUpdateModal() {
            document.getElementById('updateStatusModal').style.display = 'none';
        }
        
        function exportToCSV() {
            // Simple CSV export function
            let csv = [];
            let rows = document.querySelectorAll(".data-table tr");
            
            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll("td, th");
                
                for (let j = 0; j < cols.length - 1; j++) { // -1 to exclude actions column
                    let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").replace(/(\s\s)/gm, " ");
                    data = data.replace(/"/g, '""');
                    row.push('"' + data + '"');
                }
                csv.push(row.join(","));        
            }
            
            let csvContent = csv.join("\n");
            let blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            let url = URL.createObjectURL(blob);
            let link = document.createElement("a");
            link.setAttribute("href", url);
            link.setAttribute("download", "payments_export_" + new Date().toISOString().slice(0,10) + ".csv");
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('paymentDetailsModal');
            const updateModal = document.getElementById('updateStatusModal');
            
            if (event.target == modal) {
                modal.style.display = 'none';
            }
            if (event.target == updateModal) {
                updateModal.style.display = 'none';
            }
        }
    </script>
</body>
</html>