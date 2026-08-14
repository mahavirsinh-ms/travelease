<?php
session_start();
include "db.php";

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);
$success_message = '';
$error_message = '';

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    $sql = "UPDATE users SET full_name='$full_name', phone='$phone' WHERE id='$user_id'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['user'] = $full_name; // Update session
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Failed to update profile. Please try again.";
    }
}

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Get current password hash
    $sql = "SELECT password FROM users WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);
    
    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 6) {
                $hash = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password='$hash' WHERE id='$user_id'";
                if (mysqli_query($conn, $sql)) {
                    $success_message = "Password changed successfully!";
                } else {
                    $error_message = "Failed to change password. Please try again.";
                }
            } else {
                $error_message = "Password must be at least 6 characters long.";
            }
        } else {
            $error_message = "New passwords do not match.";
        }
    } else {
        $error_message = "Current password is incorrect.";
    }
}

// Get user details from database
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Get recent bookings
$bookings_sql = "SELECT * FROM bookings WHERE user_id = '$user_id' ORDER BY booking_date DESC LIMIT 5";
$bookings_result = mysqli_query($conn, $bookings_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - TravelEase</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/fonts.css">
    <link rel="icon" href="travelEASEonly.png" type="image/png">
    <style>
        .profile-page {
            min-height: 100vh;
            background: #f5f7fa;
            padding-top: 80px;
            padding-bottom: 50px;
        }
        
       
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .profile-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .profile-header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .profile-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 768px) {
            .profile-content {
                grid-template-columns: 1fr;
            }
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .profile-card h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .profile-card h2 i {
            color: #667eea;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: #666;
            font-weight: 500;
        }
        
        .info-value {
            color: #333;
            font-weight: 600;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
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
        
        .bookings-list {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .booking-item {
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .booking-item:last-child {
            margin-bottom: 0;
        }
        
        .booking-info h4 {
            margin-bottom: 5px;
            color: #333;
        }
        
        .booking-info p {
            color: #666;
            font-size: 14px;
            margin: 5px 0;
        }
        
        .booking-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .no-bookings {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .section-divider {
            margin: 30px 0;
            border-top: 2px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include '.\includes\navbar.php'; ?>


    <div class="profile-page">
        <div class="profile-container">
            <!-- Profile Header -->
            <div class="profile-header">
                <h1><i class="fas fa-user-circle"></i> My Profile</h1>
                <p>Manage your account information and preferences</p>
            </div>

            <!-- Success/Error Messages -->
            <?php if($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Profile Content -->
            <div class="profile-content">
                <!-- Personal Information -->
                <div class="profile-card">
                    <h2><i class="fas fa-user"></i> Personal Information</h2>
                    <form method="POST" action="">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            <small style="color: #666;">Email cannot be changed</small>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                </div>

                <!-- Account Information -->
                <div class="profile-card">
                    <h2><i class="fas fa-info-circle"></i> Account Information</h2>
                    <div class="info-item">
                        <span class="info-label">Member Since:</span>
                        <span class="info-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Account Status:</span>
                        <span class="info-value" style="color: #28a745;">Active</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Bookings:</span>
                        <span class="info-value">
                            <?php 
                            $total_bookings = mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE user_id='$user_id'");
                            $total = mysqli_fetch_assoc($total_bookings)['total'];
                            echo $total;
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="profile-card">
                <h2><i class="fas fa-lock"></i> Change Password</h2>
                <form method="POST" action="">
                    <input type="hidden" name="change_password" value="1">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                    </div>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </form>
            </div>

            <!-- Recent Bookings -->
            <div class="bookings-list">
                <h2><i class="fas fa-suitcase"></i> Recent Bookings</h2>
                <?php if(mysqli_num_rows($bookings_result) > 0): ?>
                    <?php while($booking = mysqli_fetch_assoc($bookings_result)): ?>
                        <div class="booking-item">
                            <div class="booking-info">
                                <h4><?php echo htmlspecialchars($booking['details']); ?></h4>
                                <p><strong>Booking ID:</strong> #<?php echo htmlspecialchars($booking['booking_reference']); ?></p>
                                <p><strong>Date:</strong> <?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></p>
                                <p><strong>Amount:</strong> ₹<?php echo number_format($booking['total_amount'], 0); ?></p>
                            </div>
                            <div>
                                <span class="booking-status status-<?php echo $booking['status']; ?>">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="bookings.php" class="btn-secondary">
                            <i class="fas fa-eye"></i> View All Bookings
                        </a>
                    </div>
                <?php else: ?>
                    <div class="no-bookings">
                        <i class="fas fa-suitcase" style="font-size: 48px; color: #ddd; margin-bottom: 15px;"></i>
                        <p>No bookings yet. Start planning your next adventure!</p>
                        <a href="index.php" class="btn-primary" style="margin-top: 15px; display: inline-block;">
                            <i class="fas fa-search"></i> Browse Travel Options
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '.\includes\footer.php'; ?>


    <script>
        // Profile dropdown toggle
        const profileButton = document.getElementById('profileButton');
        const profileMenu = document.getElementById('profileMenu');

        if (profileButton && profileMenu) {
            profileButton.addEventListener('click', function(e) {
                e.stopPropagation();
                profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
            });

            document.addEventListener('click', function(e) {
                if (!profileButton.contains(e.target) && !profileMenu.contains(e.target)) {
                    profileMenu.style.display = 'none';
                }
            });
        }

        // Mobile menu toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const navMenu = document.getElementById('navMenu');

        if (mobileToggle && navMenu) {
            mobileToggle.addEventListener('click', function() {
                navMenu.classList.toggle('active');
            });
        }
    </script>
</body>
</html>
