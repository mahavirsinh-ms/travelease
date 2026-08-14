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
    mysqli_query($conn, "DELETE FROM buses WHERE id='$id'");
    header("Location: manage-buses.php?success=deleted");
    exit();
}

// Handle add/edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $bus_name = $_POST['bus_name'];
    $bus_number = $_POST['bus_number'];
    $departure_city = $_POST['departure_city'];
    $arrival_city = $_POST['arrival_city'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $departure_date = $_POST['departure_date'];
    $arrival_date = $_POST['arrival_date'];
    $price = $_POST['price'];
    $available_seats = $_POST['available_seats'];
    $total_seats = $_POST['total_seats'];
    $bus_type = $_POST['bus_type'];
    $status = $_POST['status'];

    if ($id) {
        // Update
        $sql = "UPDATE buses SET bus_name='$bus_name', bus_number='$bus_number', departure_city='$departure_city', 
                arrival_city='$arrival_city', departure_time='$departure_time', arrival_time='$arrival_time', 
                departure_date='$departure_date', arrival_date='$arrival_date', price='$price', 
                available_seats='$available_seats', total_seats='$total_seats', bus_type='$bus_type', status='$status' 
                WHERE id='$id'";
    } else {
        // Insert
        $sql = "INSERT INTO buses (bus_name, bus_number, departure_city, arrival_city, departure_time, arrival_time, 
                departure_date, arrival_date, price, available_seats, total_seats, bus_type, status) 
                VALUES ('$bus_name', '$bus_number', '$departure_city', '$arrival_city', '$departure_time', '$arrival_time', 
                '$departure_date', '$arrival_date', '$price', '$available_seats', '$total_seats', '$bus_type', '$status')";
    }
    
    mysqli_query($conn, $sql);
    header("Location: manage-buses.php?success=" . ($id ? 'updated' : 'added'));
    exit();
}

// Get bus for editing
$edit_bus = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM buses WHERE id='$id'");
    $edit_bus = mysqli_fetch_assoc($result);
}

// Get all buses
$buses = mysqli_query($conn, "SELECT * FROM buses ORDER BY departure_date DESC, departure_time ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Buses - TravelEase Admin</title>
    <link rel="stylesheet" href="../css/all.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .bus-icon {
            color: #4CAF50;
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
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1><i class="fas fa-bus bus-icon"></i> Manage Buses</h1>
                <div class="header-actions">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="../index.php" class="btn-secondary"><i class="fas fa-home"></i> View Site</a>
                </div>
            </header>

            <?php if(isset($_GET['success'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    Bus <?php echo $_GET['success']; ?> successfully!
                </div>
            <?php endif; ?>

            <!-- Add/Edit Form -->
            <div class="content-card">
                <h2><?php echo $edit_bus ? 'Edit Bus' : 'Add New Bus'; ?></h2>
                <form method="POST" action="">
                    <?php if($edit_bus): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_bus['id']; ?>">
                    <?php endif; ?>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                        <div class="form-group">
                            <label>Bus Name</label>
                            <input type="text" name="bus_name" class="form-control" required value="<?php echo $edit_bus['bus_name'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Bus Number</label>
                            <input type="text" name="bus_number" class="form-control" required value="<?php echo $edit_bus['bus_number'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Departure City</label>
                            <input type="text" name="departure_city" class="form-control" required value="<?php echo $edit_bus['departure_city'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Arrival City</label>
                            <input type="text" name="arrival_city" class="form-control" required value="<?php echo $edit_bus['arrival_city'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Departure Date</label>
                            <input type="date" name="departure_date" class="form-control" required value="<?php echo $edit_bus['departure_date'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Arrival Date</label>
                            <input type="date" name="arrival_date" class="form-control" required value="<?php echo $edit_bus['arrival_date'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Departure Time</label>
                            <input type="time" name="departure_time" class="form-control" required value="<?php echo $edit_bus['departure_time'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Arrival Time</label>
                            <input type="time" name="arrival_time" class="form-control" required value="<?php echo $edit_bus['arrival_time'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Price (₹)</label>
                            <input type="number" name="price" class="form-control" step="0.01" required value="<?php echo $edit_bus['price'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Bus Type</label>
                            <select name="bus_type" class="form-control" required>
                                <option value="seater" <?php echo ($edit_bus['bus_type'] ?? '') == 'seater' ? 'selected' : ''; ?>>Seater</option>
                                <option value="sleeper" <?php echo ($edit_bus['bus_type'] ?? '') == 'sleeper' ? 'selected' : ''; ?>>Sleeper</option>
                                <option value="semi_sleeper" <?php echo ($edit_bus['bus_type'] ?? '') == 'semi_sleeper' ? 'selected' : ''; ?>>Semi Sleeper</option>
                                <option value="ac" <?php echo ($edit_bus['bus_type'] ?? '') == 'ac' ? 'selected' : ''; ?>>AC</option>
                                <option value="non_ac" <?php echo ($edit_bus['bus_type'] ?? '') == 'non_ac' ? 'selected' : ''; ?>>Non-AC</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Total Seats</label>
                            <input type="number" name="total_seats" class="form-control" required value="<?php echo $edit_bus['total_seats'] ?? '40'; ?>">
                        </div>
                        <div class="form-group">
                            <label>Available Seats</label>
                            <input type="number" name="available_seats" class="form-control" required value="<?php echo $edit_bus['available_seats'] ?? '40'; ?>">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="active" <?php echo ($edit_bus['status'] ?? '') == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($edit_bus['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                <option value="cancelled" <?php echo ($edit_bus['status'] ?? '') == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> <?php echo $edit_bus ? 'Update Bus' : 'Add Bus'; ?>
                        </button>
                        <?php if($edit_bus): ?>
                            <a href="manage-buses.php" class="btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Buses List -->
            <div class="content-card">
                <h2>All Buses</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Bus Details</th>
                                <th>Route</th>
                                <th>Date & Time</th>
                                <th>Price</th>
                                <th>Seats</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($bus = mysqli_fetch_assoc($buses)): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($bus['bus_name']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($bus['bus_number']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($bus['departure_city']); ?> → <?php echo htmlspecialchars($bus['arrival_city']); ?></td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($bus['departure_date'])); ?><br>
                                    <?php echo date('H:i', strtotime($bus['departure_time'])); ?> - 
                                    <?php echo date('H:i', strtotime($bus['arrival_time'])); ?>
                                </td>
                                <td>₹<?php echo number_format($bus['price'], 0); ?></td>
                                <td><?php echo $bus['available_seats']; ?>/<?php echo $bus['total_seats']; ?></td>
                                <td>
                                    <?php 
                                    $type_icons = [
                                        'sleeper' => '<i class="fas fa-bed" title="Sleeper"></i>',
                                        'semi_sleeper' => '<i class="fas fa-couch" title="Semi Sleeper"></i>',
                                        'seater' => '<i class="fas fa-chair" title="Seater"></i>',
                                        'ac' => '<i class="fas fa-snowflake" title="AC"></i>',
                                        'non_ac' => '<i class="fas fa-fan" title="Non-AC"></i>'
                                    ];
                                    echo $type_icons[$bus['bus_type']] ?? '<i class="fas fa-bus"></i>';
                                    ?>
                                    <?php echo ucfirst(str_replace('_', ' ', $bus['bus_type'])); ?>
                                </td>
                                <td>
                                    <?php if($bus['status'] == 'active'): ?>
                                        <span class="badge-success">Active</span>
                                    <?php elseif($bus['status'] == 'inactive'): ?>
                                        <span class="badge-warning">Inactive</span>
                                    <?php else: ?>
                                        <span class="badge-danger">Cancelled</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="manage-buses.php?edit=<?php echo $bus['id']; ?>" class="btn-sm btn-primary">Edit</a>
                                    <a href="manage-buses.php?delete=<?php echo $bus['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Are you sure? This will also delete all bookings for this bus.')">Delete</a>
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