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
    mysqli_query($conn, "DELETE FROM cruises WHERE id='$id'");
    header("Location: manage-cruises.php?success=deleted");
    exit();
}

// Handle add/edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $cruise_line = $_POST['cruise_line'];
    $ship_name = $_POST['ship_name'];
    $departure_port = $_POST['departure_port'];
    $itinerary_type = $_POST['itinerary_type'];
    $departure_date = $_POST['departure_date'];
    $duration_nights = $_POST['duration_nights'];
    $price = $_POST['price'];
    $available_cabins = $_POST['available_cabins'];
    $total_cabins = $_POST['total_cabins'];
    $cabin_type = $_POST['cabin_type'];
    $amenities = $_POST['amenities'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];
    $status = $_POST['status'];

    // Escape strings for SQL
    $amenities = mysqli_real_escape_string($conn, $amenities);
    $description = mysqli_real_escape_string($conn, $description);

    if ($id) {
        // Update
        $sql = "UPDATE cruises SET 
                cruise_line='$cruise_line', 
                ship_name='$ship_name', 
                departure_port='$departure_port', 
                itinerary_type='$itinerary_type', 
                departure_date='$departure_date', 
                duration_nights='$duration_nights', 
                price='$price', 
                available_cabins='$available_cabins', 
                total_cabins='$total_cabins', 
                cabin_type='$cabin_type', 
                amenities='$amenities', 
                description='$description', 
                image_url='$image_url', 
                status='$status' 
                WHERE id='$id'";
    } else {
        // Insert
        $sql = "INSERT INTO cruises (cruise_line, ship_name, departure_port, itinerary_type, departure_date, 
                duration_nights, price, available_cabins, total_cabins, cabin_type, amenities, description, image_url, status) 
                VALUES ('$cruise_line', '$ship_name', '$departure_port', '$itinerary_type', '$departure_date', 
                '$duration_nights', '$price', '$available_cabins', '$total_cabins', '$cabin_type', '$amenities', 
                '$description', '$image_url', '$status')";
    }
    
    mysqli_query($conn, $sql);
    header("Location: manage-cruises.php?success=" . ($id ? 'updated' : 'added'));
    exit();
}

// Get cruise for editing
$edit_cruise = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM cruises WHERE id='$id'");
    $edit_cruise = mysqli_fetch_assoc($result);
}

// Get all cruises
$cruises = mysqli_query($conn, "SELECT * FROM cruises ORDER BY departure_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cruises - TravelEase Admin</title>
    <link rel="stylesheet" href="../css/all.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .cruise-icon {
            color: #2196F3;
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
        .cabin-icon {
            margin-right: 5px;
            color: #6c757d;
        }
        .ship-image {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }
        .text-area {
            min-height: 100px;
            resize: vertical;
        }
        .amenities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 5px;
        }
        .amenity-tag {
            background: #e9ecef;
            padding: 3px 8px;
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
                <h1><i class="fas fa-ship cruise-icon"></i> Manage Cruises</h1>
                <div class="header-actions">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="../index.php" class="btn-secondary"><i class="fas fa-home"></i> View Site</a>
                </div>
            </header>

            <?php if(isset($_GET['success'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    Cruise <?php echo $_GET['success']; ?> successfully!
                </div>
            <?php endif; ?>

            <!-- Add/Edit Form -->
            <div class="content-card">
                <h2><?php echo $edit_cruise ? 'Edit Cruise' : 'Add New Cruise'; ?></h2>
                <form method="POST" action="">
                    <?php if($edit_cruise): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_cruise['id']; ?>">
                    <?php endif; ?>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                        <div class="form-group">
                            <label>Cruise Line</label>
                            <input type="text" name="cruise_line" class="form-control" required value="<?php echo $edit_cruise['cruise_line'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Ship Name</label>
                            <input type="text" name="ship_name" class="form-control" required value="<?php echo $edit_cruise['ship_name'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Departure Port</label>
                            <input type="text" name="departure_port" class="form-control" required value="<?php echo $edit_cruise['departure_port'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Itinerary Type</label>
                            <select name="itinerary_type" class="form-control" required>
                                <option value="Caribbean" <?php echo ($edit_cruise['itinerary_type'] ?? '') == 'Caribbean' ? 'selected' : ''; ?>>Caribbean</option>
                                <option value="Mediterranean" <?php echo ($edit_cruise['itinerary_type'] ?? '') == 'Mediterranean' ? 'selected' : ''; ?>>Mediterranean</option>
                                <option value="Alaska" <?php echo ($edit_cruise['itinerary_type'] ?? '') == 'Alaska' ? 'selected' : ''; ?>>Alaska</option>
                                <option value="Bahamas" <?php echo ($edit_cruise['itinerary_type'] ?? '') == 'Bahamas' ? 'selected' : ''; ?>>Bahamas</option>
                                <option value="European" <?php echo ($edit_cruise['itinerary_type'] ?? '') == 'European' ? 'selected' : ''; ?>>European</option>
                                <option value="Transatlantic" <?php echo ($edit_cruise['itinerary_type'] ?? '') == 'Transatlantic' ? 'selected' : ''; ?>>Transatlantic</option>
                                <option value="Asia" <?php echo ($edit_cruise['itinerary_type'] ?? '') == 'Asia' ? 'selected' : ''; ?>>Asia</option>
                                <option value="South Pacific" <?php echo ($edit_cruise['itinerary_type'] ?? '') == 'South Pacific' ? 'selected' : ''; ?>>South Pacific</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Departure Date</label>
                            <input type="date" name="departure_date" class="form-control" required value="<?php echo $edit_cruise['departure_date'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Duration (Nights)</label>
                            <input type="number" name="duration_nights" class="form-control" min="1" required value="<?php echo $edit_cruise['duration_nights'] ?? '7'; ?>">
                        </div>
                        <div class="form-group">
                            <label>Price (₹)</label>
                            <input type="number" name="price" class="form-control" step="0.01" required value="<?php echo $edit_cruise['price'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Cabin Type</label>
                            <select name="cabin_type" class="form-control" required>
                                <option value="inside" <?php echo ($edit_cruise['cabin_type'] ?? '') == 'inside' ? 'selected' : ''; ?>>Inside Cabin</option>
                                <option value="ocean_view" <?php echo ($edit_cruise['cabin_type'] ?? '') == 'ocean_view' ? 'selected' : ''; ?>>Ocean View</option>
                                <option value="balcony" <?php echo ($edit_cruise['cabin_type'] ?? '') == 'balcony' ? 'selected' : ''; ?>>Balcony</option>
                                <option value="suite" <?php echo ($edit_cruise['cabin_type'] ?? '') == 'suite' ? 'selected' : ''; ?>>Suite</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Total Cabins</label>
                            <input type="number" name="total_cabins" class="form-control" required value="<?php echo $edit_cruise['total_cabins'] ?? '20'; ?>">
                        </div>
                        <div class="form-group">
                            <label>Available Cabins</label>
                            <input type="number" name="available_cabins" class="form-control" required value="<?php echo $edit_cruise['available_cabins'] ?? '20'; ?>">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Image URL</label>
                            <input type="text" name="image_url" class="form-control" placeholder="https://example.com/ship-image.jpg" value="<?php echo $edit_cruise['image_url'] ?? ''; ?>">
                            <small>Enter a direct link to the ship image</small>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Amenities (comma-separated)</label>
                            <textarea name="amenities" class="form-control text-area" placeholder="Wi-Fi, Pool, Spa, Gym, Restaurant, Entertainment..."><?php echo $edit_cruise['amenities'] ?? ''; ?></textarea>
                            <small>Enter amenities separated by commas</small>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Description</label>
                            <textarea name="description" class="form-control text-area" placeholder="Enter cruise description..."><?php echo $edit_cruise['description'] ?? ''; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="active" <?php echo ($edit_cruise['status'] ?? '') == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($edit_cruise['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                <option value="cancelled" <?php echo ($edit_cruise['status'] ?? '') == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> <?php echo $edit_cruise ? 'Update Cruise' : 'Add Cruise'; ?>
                        </button>
                        <?php if($edit_cruise): ?>
                            <a href="manage-cruises.php" class="btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Cruises List -->
            <div class="content-card">
                <h2>All Cruises</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Cruise Details</th>
                                <th>Itinerary</th>
                                <th>Date & Duration</th>
                                <th>Price</th>
                                <th>Cabins</th>
                                <th>Cabin Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($cruise = mysqli_fetch_assoc($cruises)): ?>
                            <tr>
                                <td>
                                    <?php if($cruise['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($cruise['image_url']); ?>" alt="<?php echo htmlspecialchars($cruise['ship_name']); ?>" class="ship-image" onerror="this.style.display='none'">
                                    <?php endif; ?>
                                    <strong><?php echo htmlspecialchars($cruise['cruise_line']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($cruise['ship_name']); ?></small><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($cruise['departure_port']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($cruise['itinerary_type']); ?></td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($cruise['departure_date'])); ?><br>
                                    <small><?php echo $cruise['duration_nights']; ?> nights</small>
                                </td>
                                <td>₹<?php echo number_format($cruise['price'], 0); ?></td>
                                <td><?php echo $cruise['available_cabins']; ?>/<?php echo $cruise['total_cabins']; ?></td>
                                <td>
                                    <?php 
                                    $cabin_icons = [
                                        'inside' => '<i class="fas fa-door-closed cabin-icon" title="Inside Cabin"></i>',
                                        'ocean_view' => '<i class="fas fa-water cabin-icon" title="Ocean View"></i>',
                                        'balcony' => '<i class="fas fa-umbrella-beach cabin-icon" title="Balcony"></i>',
                                        'suite' => '<i class="fas fa-crown cabin-icon" title="Suite"></i>'
                                    ];
                                    echo $cabin_icons[$cruise['cabin_type']] ?? '';
                                    ?>
                                    <?php echo ucfirst(str_replace('_', ' ', $cruise['cabin_type'])); ?>
                                </td>
                                <td>
                                    <?php if($cruise['status'] == 'active'): ?>
                                        <span class="badge-success">Active</span>
                                    <?php elseif($cruise['status'] == 'inactive'): ?>
                                        <span class="badge-warning">Inactive</span>
                                    <?php else: ?>
                                        <span class="badge-danger">Cancelled</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="manage-cruises.php?edit=<?php echo $cruise['id']; ?>" class="btn-sm btn-primary">Edit</a>
                                    <a href="manage-cruises.php?delete=<?php echo $cruise['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Are you sure? This will also delete all bookings for this cruise.')">Delete</a>
                                </td>
                            </tr>
                            <?php if($cruise['amenities']): ?>
                            <tr class="amenities-row">
                                <td colspan="8" style="padding-top: 0; border-top: none;">
                                    <div class="amenities-list">
                                        <?php 
                                        $amenities_array = explode(',', $cruise['amenities']);
                                        foreach($amenities_array as $amenity):
                                            $amenity = trim($amenity);
                                            if(!empty($amenity)):
                                        ?>
                                            <span class="amenity-tag"><?php echo htmlspecialchars($amenity); ?></span>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>