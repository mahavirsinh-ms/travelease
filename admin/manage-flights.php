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
    mysqli_query($conn, "DELETE FROM flights WHERE id='$id'");
    header("Location: manage-flights.php?success=deleted");
    exit();
}

// Handle add/edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $airline = $_POST['airline'];
    $flight_number = $_POST['flight_number'];
    $departure_city = $_POST['departure_city'];
    $arrival_city = $_POST['arrival_city'];
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
        $sql = "UPDATE flights SET airline='$airline', flight_number='$flight_number', departure_city='$departure_city', 
                arrival_city='$arrival_city', departure_time='$departure_time', arrival_time='$arrival_time', 
                departure_date='$departure_date', arrival_date='$arrival_date', price='$price', 
                available_seats='$available_seats', total_seats='$total_seats', class_type='$class_type', status='$status' 
                WHERE id='$id'";
    } else {
        // Insert
        $sql = "INSERT INTO flights (airline, flight_number, departure_city, arrival_city, departure_time, arrival_time, 
                departure_date, arrival_date, price, available_seats, total_seats, class_type, status) 
                VALUES ('$airline', '$flight_number', '$departure_city', '$arrival_city', '$departure_time', '$arrival_time', 
                '$departure_date', '$arrival_date', '$price', '$available_seats', '$total_seats', '$class_type', '$status')";
    }
    
    mysqli_query($conn, $sql);
    header("Location: manage-flights.php?success=" . ($id ? 'updated' : 'added'));
    exit();
}

// Get flight for editing
$edit_flight = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM flights WHERE id='$id'");
    $edit_flight = mysqli_fetch_assoc($result);
}

// Get all flights
$flights = mysqli_query($conn, "SELECT * FROM flights ORDER BY departure_date DESC, departure_time ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Flights - TravelEase Admin</title>
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
                <h1><i class="fas fa-plane"></i> Manage Flights</h1>
                <div class="header-actions">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="../index.php" class="btn-secondary"><i class="fas fa-home"></i> View Site</a>
                </div>
            </header>

            <?php if(isset($_GET['success'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    Flight <?php echo $_GET['success']; ?> successfully!
                </div>
            <?php endif; ?>

            <!-- Add/Edit Form -->
            <div class="content-card">
                <h2><?php echo $edit_flight ? 'Edit Flight' : 'Add New Flight'; ?></h2>
                <form method="POST" action="">
                    <?php if($edit_flight): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_flight['id']; ?>">
                    <?php endif; ?>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                        <div class="form-group">
                            <label>Airline</label>
                            <input type="text" name="airline" class="form-control" required value="<?php echo $edit_flight['airline'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Flight Number</label>
                            <input type="text" name="flight_number" class="form-control" required value="<?php echo $edit_flight['flight_number'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Departure City</label>
                            <input type="text" name="departure_city" class="form-control" required value="<?php echo $edit_flight['departure_city'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Arrival City</label>
                            <input type="text" name="arrival_city" class="form-control" required value="<?php echo $edit_flight['arrival_city'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Departure Date</label>
                            <input type="date" name="departure_date" class="form-control" required value="<?php echo $edit_flight['departure_date'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Arrival Date</label>
                            <input type="date" name="arrival_date" class="form-control" required value="<?php echo $edit_flight['arrival_date'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Departure Time</label>
                            <input type="time" name="departure_time" class="form-control" required value="<?php echo $edit_flight['departure_time'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Arrival Time</label>
                            <input type="time" name="arrival_time" class="form-control" required value="<?php echo $edit_flight['arrival_time'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Price (₹)</label>
                            <input type="number" name="price" class="form-control" step="0.01" required value="<?php echo $edit_flight['price'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Class Type</label>
                            <select name="class_type" class="form-control" required>
                                <option value="economy" <?php echo ($edit_flight['class_type'] ?? '') == 'economy' ? 'selected' : ''; ?>>Economy</option>
                                <option value="business" <?php echo ($edit_flight['class_type'] ?? '') == 'business' ? 'selected' : ''; ?>>Business</option>
                                <option value="first" <?php echo ($edit_flight['class_type'] ?? '') == 'first' ? 'selected' : ''; ?>>First</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Total Seats</label>
                            <input type="number" name="total_seats" class="form-control" required value="<?php echo $edit_flight['total_seats'] ?? '180'; ?>">
                        </div>
                        <div class="form-group">
                            <label>Available Seats</label>
                            <input type="number" name="available_seats" class="form-control" required value="<?php echo $edit_flight['available_seats'] ?? '180'; ?>">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="active" <?php echo ($edit_flight['status'] ?? '') == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($edit_flight['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                <option value="cancelled" <?php echo ($edit_flight['status'] ?? '') == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> <?php echo $edit_flight ? 'Update Flight' : 'Add Flight'; ?>
                        </button>
                        <?php if($edit_flight): ?>
                            <a href="manage-flights.php" class="btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Flights List -->
            <div class="content-card">
                <h2>All Flights</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Flight Number</th>
                                <th>Route</th>
                                <th>Date & Time</th>
                                <th>Price</th>
                                <th>Seats</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($flight = mysqli_fetch_assoc($flights)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($flight['airline']); ?></strong><br><?php echo htmlspecialchars($flight['flight_number']); ?></td>
                                <td><?php echo htmlspecialchars($flight['departure_city']); ?> → <?php echo htmlspecialchars($flight['arrival_city']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($flight['departure_date'])); ?><br><?php echo date('H:i', strtotime($flight['departure_time'])); ?></td>
                                <td>₹<?php echo number_format($flight['price'], 0); ?></td>
                                <td><?php echo $flight['available_seats']; ?>/<?php echo $flight['total_seats']; ?></td>
                                <td><span class="badge badge-<?php echo $flight['status'] == 'active' ? 'success' : 'danger'; ?>"><?php echo ucfirst($flight['status']); ?></span></td>
                                <td>
                                    <a href="manage-flights.php?edit=<?php echo $flight['id']; ?>" class="btn-sm btn-primary">Edit</a>
                                    <a href="manage-flights.php?delete=<?php echo $flight['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
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

