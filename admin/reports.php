<?php
session_start();
include "../db.php";

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
$stats['total_revenue'] = (float)($row['revenue'] ?? 0);


// Bookings in Last 7 Days
$seven_days_bookings = mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings WHERE booking_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$stats['seven_days_bookings'] = mysqli_fetch_assoc($seven_days_bookings)['count'];

// Failed Payments
$failed_payments = mysqli_query($conn, "SELECT COUNT(*) as count, SUM(amount) as amount
FROM payments
WHERE payment_status='failed';
");
$failed_data = mysqli_fetch_assoc($failed_payments);
$stats['failed_payments'] = (int)($failed_data['count'] ?? 0);

$stats['failed_amount'] = (float)($failed_data['amount'] ?? 0);


// Bookings by type
$bookings_by_type = mysqli_query($conn, "SELECT 
    b.booking_type,
    COUNT(DISTINCT b.id) AS count,
    COALESCE(SUM(p.amount), 0) AS revenue

FROM bookings b
JOIN payments p ON b.id = p.booking_id
WHERE p.payment_status = 'success'
GROUP BY b.booking_type;
");

// Recent bookings (last 30 days)
$recent_bookings = mysqli_query($conn, "
SELECT COUNT(*) AS count
FROM bookings
WHERE booking_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
");
$stats['recent_bookings'] = (int)(mysqli_fetch_assoc($recent_bookings)['count'] ?? 0);


// Single optimized query for both table and chart
$monthly_result = mysqli_query($conn, "SELECT 
    DATE_FORMAT(payment_date, '%Y-%m') AS month,
    COALESCE(SUM(amount), 0) AS revenue

FROM payments
WHERE payment_status='success'
AND payment_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
GROUP BY month
ORDER BY month;
");

$monthly_labels = [];
$monthly_data = [];
$all_data = []; // Store for table display

while($row = mysqli_fetch_assoc($monthly_result)) {
    $monthly_labels[] = date('M Y', strtotime($row['month'].'-01'));
    $monthly_data[] = $row['revenue'];
    $all_data[] = $row; // Store for table
}

// For table display (reverse order)
$table_months = array_reverse($all_data);

// Booking Type for Pie Chart
$type_pie_chart = mysqli_query($conn, "SELECT booking_type, COUNT(*) as count FROM bookings GROUP BY booking_type");
$type_labels = [];
$type_data = [];
$type_colors = ['#667eea', '#f093fb', '#f5576c', '#4facfe', '#fa709a', '#fee140'];
$i = 0;
while($row = mysqli_fetch_assoc($type_pie_chart)) {
    $type_labels[] = ucfirst($row['booking_type']);
    $type_data[] = $row['count'];
    $i++;
}

// Top destinations
$top_destinations = mysqli_query($conn, "SELECT details, COUNT(*) as bookings FROM bookings GROUP BY details ORDER BY bookings DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - TravelEase Admin</title>
    <link rel="stylesheet" href="../css/all.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="admin-style.css">
    <script src="../js/chart.js"></script>
    <style>
        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .chart-container {
            padding: 20px;
        }

        .chart-container canvas {
            max-height: 350px;
        }

        .stat-card .stat-info small {
            display: block;
            font-size: 12px;
            color: #f5576c;
            margin-top: 5px;
            font-weight: 500;
        }

        @media (max-width: 1024px) {
            .charts-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1><i class="fas fa-chart-bar"></i> Reports & Analytics</h1>
                <div class="header-actions">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="../index.php" class="btn-secondary"><i class="fas fa-home"></i> View Site</a>
                </div>
            </header>

            <!-- Summary Cards -->
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
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['recent_bookings']); ?></h3>
                        <p>Bookings (Last 30 Days)</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <?php
                        $total_users = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'");
                        $users_count = mysqli_fetch_assoc($total_users)['total'];
                        ?>
                        <h3><?php echo number_format($users_count); ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['seven_days_bookings']); ?></h3>
                        <p>Bookings (Last 7 Days)</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #5a5a5a 0%, #333333 100%);">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['failed_payments']); ?></h3>
                        <p>Failed Payments</p>
                        <small>₹<?php echo number_format($stats['failed_amount'], 0); ?> lost</small>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="charts-row">
                <!-- Monthly Revenue Line Chart -->
                <div class="content-card chart-container">
                    <h2>Monthly Revenue Trend</h2>
                    <canvas id="revenueChart"></canvas>
                </div>

                <!-- Booking Type Pie Chart -->
                <div class="content-card chart-container">
                    <h2>Bookings by Type</h2>
                    <canvas id="typePieChart"></canvas>
                </div>
            </div>

            <!-- Bookings by Type -->
            <div class="content-card">
                <h2>Bookings by Type</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Travel Type</th>
                                <th>Total Bookings</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($bookings_by_type)): ?>
                            <tr>
                                <td><span class="badge badge-info"><?php echo ucfirst($row['booking_type']); ?></span></td>
                                <td><?php echo number_format($row['count']); ?></td>
                                <td>₹<?php echo number_format($row['revenue'] ? $row['revenue'] : 0, 0); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Monthly Revenue -->
            <div class="content-card">
                <h2>Monthly Revenue (Last 6 Months)</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php foreach($table_months as $row): ?>
    <tr>
        <td><?php echo date('F Y', strtotime($row['month'] . '-01')); ?></td>
        <td>₹<?php echo number_format((float)($row['revenue'] ?? 0), 0)
 ?></td>
    </tr>
    <?php endforeach; ?>
</tbody>
                    </table>
                </div>
            </div>

            <!-- Top Destinations -->
            <div class="content-card">
                <h2>Top Booked Destinations</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Destination/Service</th>
                                <th>Number of Bookings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($top_destinations)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['details']); ?></td>
                                <td><?php echo number_format($row['bookings']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
    // Monthly Revenue Line Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($monthly_labels); ?>,
            datasets: [{
                label: 'Revenue (₹)',
                data: <?php echo json_encode($monthly_data); ?>,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Booking Type Pie Chart
    const typeCtx = document.getElementById('typePieChart').getContext('2d');
    const typePieChart = new Chart(typeCtx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($type_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($type_data); ?>,
                backgroundColor: <?php echo json_encode($type_colors); ?>,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.raw + ' bookings';
                            return label;
                        }
                    }
                }
            }
        }
    });
    </script>
</body>
</html>