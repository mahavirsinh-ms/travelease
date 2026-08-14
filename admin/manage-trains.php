<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM trains WHERE id='$id'");
    header("Location: manage-trains.php?success=deleted");
    exit();
}

// Handle add/edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $train_name = $_POST['train_name'];
    $train_number = $_POST['train_number'];
    $departure_station = $_POST['departure_station'];
    $arrival_station = $_POST['arrival_station'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $departure_date = $_POST['departure_date'];
    $arrival_date = $_POST['arrival_date'];
    $price = $_POST['price'];
    $available_seats = $_POST['available_seats'];
    $total_seats = $_POST['total_seats'];
    $class_type = $_POST['class_type'];
    $status = $_POST['status'];

    if ($id) {
        // Update
        $sql = "UPDATE trains SET 
                train_name='$train_name', 
                train_number='$train_number', 
                departure_station='$departure_station', 
                arrival_station='$arrival_station', 
                departure_time='$departure_time', 
                arrival_time='$arrival_time', 
                departure_date='$departure_date', 
                arrival_date='$arrival_date', 
                price='$price', 
                available_seats='$available_seats', 
                total_seats='$total_seats', 
                class_type='$class_type', 
                status='$status' 
                WHERE id='$id'";
    } else {
        // Insert
        $sql = "INSERT INTO trains (train_name, train_number, departure_station, arrival_station, 
                departure_time, arrival_time, departure_date, arrival_date, price, available_seats, 
                total_seats, class_type, status) 
                VALUES ('$train_name', '$train_number', '$departure_station', '$arrival_station', 
                '$departure_time', '$arrival_time', '$departure_date', '$arrival_date', '$price', 
                '$available_seats', '$total_seats', '$class_type', '$status')";
    }
    
    mysqli_query($conn, $sql);
    header("Location: manage-trains.php?success=" . ($id ? 'updated' : 'added'));
    exit();
}

// Get train for editing
$edit_train = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM trains WHERE id='$id'");
    $edit_train = mysqli_fetch_assoc($result);
}

// Get all trains
$trains = mysqli_query($conn, "SELECT * FROM trains ORDER BY departure_date DESC, departure_time ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Trains - TravelEase Admin</title>
    <link rel="stylesheet" href="../css/all.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .train-icon {
            color: #FF5722;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-warning {
            background: #fff3cd;
            color: #856404;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .class-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .sleeper { background: #e8f5e9; color: #2e7d32; }
        .ac3 { background: #e3f2fd; color: #1565c0; }
        .ac2 { background: #f3e5f5; color: #7b1fa2; }
        .ac1 { background: #fff8e1; color: #ff8f00; }
        .chair_car { background: #fce4ec; color: #c2185b; }
        
        .train-type-icon {
            margin-right: 5px;
            font-size: 14px;
        }
        
        .route-display {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .route-arrow {
            color: #666;
            font-size: 14px;
        }
        
        .station-code {
            font-family: monospace;
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            color: #666;
        }
        
        .time-display {
            font-family: monospace;
            font-weight: bold;
        }
        
        .duration-badge {
            background: #e0f2f1;
            color: #00695c;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            display: inline-block;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1><i class="fas fa-train train-icon"></i> Manage Trains</h1>
                <div class="header-actions">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="../index.php" class="btn-secondary"><i class="fas fa-home"></i> View Site</a>
                </div>
            </header>

            <?php if(isset($_GET['success'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    Train <?php echo $_GET['success']; ?> successfully!
                </div>
            <?php endif; ?>

            <!-- Add/Edit Form -->
            <div class="content-card">
                <h2><?php echo $edit_train ? 'Edit Train' : 'Add New Train'; ?></h2>
                <form method="POST" action="">
                    <?php if($edit_train): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_train['id']; ?>">
                    <?php endif; ?>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                        <div class="form-group">
                            <label>Train Name</label>
                            <input type="text" name="train_name" class="form-control" required value="<?php echo $edit_train['train_name'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Train Number</label>
                            <input type="text" name="train_number" class="form-control" required value="<?php echo $edit_train['train_number'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Departure Station</label>
                            <input type="text" name="departure_station" class="form-control" required value="<?php echo $edit_train['departure_station'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Arrival Station</label>
                            <input type="text" name="arrival_station" class="form-control" required value="<?php echo $edit_train['arrival_station'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Departure Date</label>
                            <input type="date" name="departure_date" class="form-control" required value="<?php echo $edit_train['departure_date'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Arrival Date</label>
                            <input type="date" name="arrival_date" class="form-control" required value="<?php echo $edit_train['arrival_date'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Departure Time</label>
                            <input type="time" name="departure_time" class="form-control" required value="<?php echo $edit_train['departure_time'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Arrival Time</label>
                            <input type="time" name="arrival_time" class="form-control" required value="<?php echo $edit_train['arrival_time'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Price (₹)</label>
                            <input type="number" name="price" class="form-control" step="0.01" required value="<?php echo $edit_train['price'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Class Type</label>
                            <select name="class_type" class="form-control" required>
                                <option value="sleeper" <?php echo ($edit_train['class_type'] ?? '') == 'sleeper' ? 'selected' : ''; ?>>Sleeper Class</option>
                                <option value="3AC" <?php echo ($edit_train['class_type'] ?? '') == '3AC' ? 'selected' : ''; ?>>3AC (3-Tier AC)</option>
                                <option value="2AC" <?php echo ($edit_train['class_type'] ?? '') == '2AC' ? 'selected' : ''; ?>>2AC (2-Tier AC)</option>
                                <option value="1AC" <?php echo ($edit_train['class_type'] ?? '') == '1AC' ? 'selected' : ''; ?>>1AC (First AC)</option>
                                <option value="chair_car" <?php echo ($edit_train['class_type'] ?? '') == 'chair_car' ? 'selected' : ''; ?>>Chair Car</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Total Seats</label>
                            <input type="number" name="total_seats" class="form-control" required value="<?php echo $edit_train['total_seats'] ?? '50'; ?>">
                        </div>
                        <div class="form-group">
                            <label>Available Seats</label>
                            <input type="number" name="available_seats" class="form-control" required value="<?php echo $edit_train['available_seats'] ?? '50'; ?>">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="active" <?php echo ($edit_train['status'] ?? '') == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($edit_train['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                <option value="cancelled" <?php echo ($edit_train['status'] ?? '') == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> <?php echo $edit_train ? 'Update Train' : 'Add Train'; ?>
                        </button>
                        <?php if($edit_train): ?>
                            <a href="manage-trains.php" class="btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Trains List -->
            <div class="content-card">
                <h2>All Trains</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Train Details</th>
                                <th>Route</th>
                                <th>Date & Time</th>
                                <th>Price</th>
                                <th>Seats</th>
                                <th>Class</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($train = mysqli_fetch_assoc($trains)): 
                                // Calculate duration
                                $departure_datetime = strtotime($train['departure_date'] . ' ' . $train['departure_time']);
                                $arrival_datetime = strtotime($train['arrival_date'] . ' ' . $train['arrival_time']);
                                $duration_hours = round(($arrival_datetime - $departure_datetime) / 3600, 1);
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($train['train_name']); ?></strong><br>
                                    <span class="station-code"><?php echo htmlspecialchars($train['train_number']); ?></span>
                                </td>
                                <td>
                                    <div class="route-display">
                                        <div>
                                            <div><?php echo htmlspecialchars($train['departure_station']); ?></div>
                                            <small class="text-muted">Departure</small>
                                        </div>
                                        <div class="route-arrow"><i class="fas fa-long-arrow-alt-right"></i></div>
                                        <div>
                                            <div><?php echo htmlspecialchars($train['arrival_station']); ?></div>
                                            <small class="text-muted">Arrival</small>
                                        </div>
                                    </div>
                                    <div class="duration-badge">
                                        <i class="fas fa-clock"></i> <?php echo $duration_hours; ?> hours
                                    </div>
                                </td>
                                <td>
                                    <div class="time-display">
                                        <?php echo date('H:i', strtotime($train['departure_time'])); ?>
                                    </div>
                                    <?php echo date('M j, Y', strtotime($train['departure_date'])); ?><br>
                                    <small>to <?php echo date('H:i', strtotime($train['arrival_time'])); ?> 
                                    <?php if($train['departure_date'] != $train['arrival_date']): ?>
                                        (<?php echo date('M j', strtotime($train['arrival_date'])); ?>)
                                    <?php endif; ?></small>
                                </td>
                                <td>₹<?php echo number_format($train['price'], 0); ?></td>
                                <td><?php echo $train['available_seats']; ?>/<?php echo $train['total_seats']; ?></td>
                                <td>
                                    <?php 
                                    $class_icons = [
                                        'sleeper' => '<i class="fas fa-bed train-type-icon" title="Sleeper"></i>',
                                        '3AC' => '<i class="fas fa-snowflake train-type-icon" title="3AC"></i>',
                                        '2AC' => '<i class="fas fa-snowflake train-type-icon" title="2AC"></i>',
                                        '1AC' => '<i class="fas fa-crown train-type-icon" title="1AC"></i>',
                                        'chair_car' => '<i class="fas fa-chair train-type-icon" title="Chair Car"></i>'
                                    ];
                                    $class_labels = [
                                        'sleeper' => 'Sleeper',
                                        '3AC' => '3AC',
                                        '2AC' => '2AC',
                                        '1AC' => '1AC',
                                        'chair_car' => 'Chair Car'
                                    ];
                                    $class_colors = [
                                        'sleeper' => 'sleeper',
                                        '3AC' => 'ac3',
                                        '2AC' => 'ac2',
                                        '1AC' => 'ac1',
                                        'chair_car' => 'chair_car'
                                    ];
                                    ?>
                                    <span class="class-badge <?php echo $class_colors[$train['class_type']]; ?>">
                                        <?php echo $class_icons[$train['class_type']] ?? ''; ?>
                                        <?php echo $class_labels[$train['class_type']]; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($train['status'] == 'active'): ?>
                                        <span class="badge-success">Active</span>
                                    <?php elseif($train['status'] == 'inactive'): ?>
                                        <span class="badge-warning">Inactive</span>
                                    <?php else: ?>
                                        <span class="badge-danger">Cancelled</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="manage-trains.php?edit=<?php echo $train['id']; ?>" class="btn-sm btn-primary">Edit</a>
                                    <a href="manage-trains.php?delete=<?php echo $train['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Are you sure? This will also delete all bookings for this train.')">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>