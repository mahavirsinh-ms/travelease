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
    mysqli_query($conn, "DELETE FROM holiday_packages WHERE id='$id'");
    header("Location: manage-packages.php?success=deleted");
    exit();
}

// Handle add/edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $package_name = $_POST['package_name'];
    $destination = $_POST['destination'];
    $duration_days = $_POST['duration_days'];
    $duration_nights = $_POST['duration_nights'];
    $price = $_POST['price'];
    $hotel_category = $_POST['hotel_category'];
    $meal_plan = $_POST['meal_plan'];
    $package_type = $_POST['package_type'];
    $includes_flights = isset($_POST['includes_flights']) ? 1 : 0;
    $description = $_POST['description'];
    $itinerary = $_POST['itinerary'];
    $image_url = $_POST['image_url'];
    $available_slots = $_POST['available_slots'];
    $total_slots = $_POST['total_slots'];
    $status = $_POST['status'];

    // Escape strings for SQL
    $description = mysqli_real_escape_string($conn, $description);
    $itinerary = mysqli_real_escape_string($conn, $itinerary);

    if ($id) {
        // Update
        $sql = "UPDATE holiday_packages SET 
                package_name='$package_name', 
                destination='$destination', 
                duration_days='$duration_days', 
                duration_nights='$duration_nights', 
                price='$price', 
                hotel_category='$hotel_category', 
                meal_plan='$meal_plan', 
                package_type='$package_type', 
                includes_flights='$includes_flights', 
                description='$description', 
                itinerary='$itinerary', 
                image_url='$image_url', 
                available_slots='$available_slots', 
                total_slots='$total_slots', 
                status='$status' 
                WHERE id='$id'";
    } else {
        // Insert
        $sql = "INSERT INTO holiday_packages (package_name, destination, duration_days, duration_nights, price, 
                hotel_category, meal_plan, package_type, includes_flights, description, itinerary, image_url, 
                available_slots, total_slots, status) 
                VALUES ('$package_name', '$destination', '$duration_days', '$duration_nights', '$price', 
                '$hotel_category', '$meal_plan', '$package_type', '$includes_flights', '$description', '$itinerary', 
                '$image_url', '$available_slots', '$total_slots', '$status')";
    }
    
    mysqli_query($conn, $sql);
    header("Location: manage-packages.php?success=" . ($id ? 'updated' : 'added'));
    exit();
}

// Get package for editing
$edit_package = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM holiday_packages WHERE id='$id'");
    $edit_package = mysqli_fetch_assoc($result);
}

// Get all packages
$packages = mysqli_query($conn, "SELECT * FROM holiday_packages ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Holiday Packages - TravelEase Admin</title>
    <link rel="stylesheet" href="../css/all.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .package-icon {
            color: #FF9800;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
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
        .package-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #eee;
        }
        .text-area {
            min-height: 120px;
            resize: vertical;
        }
        .package-type-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .honeymoon { background: #ffccd5; color: #b3001e; }
        .family { background: #c8e6c9; color: #1b5e20; }
        .adventure { background: #bbdefb; color: #0d47a1; }
        .beach { background: #b3e5fc; color: #01579b; }
        .hill_station { background: #dcedc8; color: #33691e; }
        .international { background: #ffecb3; color: #ff6f00; }
        
        .hotel-badge {
            background: #e1bee7;
            color: #4a148c;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            display: inline-block;
            margin-right: 5px;
        }
        
        .meal-badge {
            background: #c8e6c9;
            color: #1b5e20;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            display: inline-block;
            margin-right: 5px;
        }
        
        .flight-badge {
            background: #b3e5fc;
            color: #01579b;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            display: inline-block;
        }
        
        .duration-badge {
            background: #f0f4c3;
            color: #827717;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            display: inline-block;
            margin-right: 5px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }
        
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        
        .amenity-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1><i class="fas fa-suitcase package-icon"></i> Manage Holiday Packages</h1>
                <div class="header-actions">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="../index.php" class="btn-secondary"><i class="fas fa-home"></i> View Site</a>
                </div>
            </header>

            <?php if(isset($_GET['success'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    Package <?php echo $_GET['success']; ?> successfully!
                </div>
            <?php endif; ?>

            <!-- Add/Edit Form -->
            <div class="content-card">
                <h2><?php echo $edit_package ? 'Edit Package' : 'Add New Package'; ?></h2>
                <form method="POST" action="">
                    <?php if($edit_package): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_package['id']; ?>">
                    <?php endif; ?>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                        <div class="form-group">
                            <label>Package Name</label>
                            <input type="text" name="package_name" class="form-control" required value="<?php echo $edit_package['package_name'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Destination</label>
                            <input type="text" name="destination" class="form-control" required value="<?php echo $edit_package['destination'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Duration Days</label>
                            <input type="number" name="duration_days" class="form-control" min="1" required value="<?php echo $edit_package['duration_days'] ?? '3'; ?>">
                        </div>
                        <div class="form-group">
                            <label>Duration Nights</label>
                            <input type="number" name="duration_nights" class="form-control" min="1" required value="<?php echo $edit_package['duration_nights'] ?? '2'; ?>">
                        </div>
                        <div class="form-group">
                            <label>Price (₹)</label>
                            <input type="number" name="price" class="form-control" step="0.01" required value="<?php echo $edit_package['price'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Hotel Category</label>
                            <select name="hotel_category" class="form-control">
                                <option value="">Select Hotel Category</option>
                                <option value="3 Star" <?php echo ($edit_package['hotel_category'] ?? '') == '3 Star' ? 'selected' : ''; ?>>3 Star</option>
                                <option value="4 Star" <?php echo ($edit_package['hotel_category'] ?? '') == '4 Star' ? 'selected' : ''; ?>>4 Star</option>
                                <option value="5 Star" <?php echo ($edit_package['hotel_category'] ?? '') == '5 Star' ? 'selected' : ''; ?>>5 Star</option>
                                <option value="Luxury" <?php echo ($edit_package['hotel_category'] ?? '') == 'Luxury' ? 'selected' : ''; ?>>Luxury</option>
                                <option value="Budget" <?php echo ($edit_package['hotel_category'] ?? '') == 'Budget' ? 'selected' : ''; ?>>Budget</option>
                                <option value="Resort" <?php echo ($edit_package['hotel_category'] ?? '') == 'Resort' ? 'selected' : ''; ?>>Resort</option>
                                <option value="Villa" <?php echo ($edit_package['hotel_category'] ?? '') == 'Villa' ? 'selected' : ''; ?>>Villa</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Meal Plan</label>
                            <select name="meal_plan" class="form-control">
                                <option value="">Select Meal Plan</option>
                                <option value="Breakfast Only" <?php echo ($edit_package['meal_plan'] ?? '') == 'Breakfast Only' ? 'selected' : ''; ?>>Breakfast Only</option>
                                <option value="Breakfast & Dinner" <?php echo ($edit_package['meal_plan'] ?? '') == 'Breakfast & Dinner' ? 'selected' : ''; ?>>Breakfast & Dinner</option>
                                <option value="All Meals" <?php echo ($edit_package['meal_plan'] ?? '') == 'All Meals' ? 'selected' : ''; ?>>All Meals</option>
                                <option value="All Inclusive" <?php echo ($edit_package['meal_plan'] ?? '') == 'All Inclusive' ? 'selected' : ''; ?>>All Inclusive</option>
                                <option value="No Meals" <?php echo ($edit_package['meal_plan'] ?? '') == 'No Meals' ? 'selected' : ''; ?>>No Meals</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Package Type</label>
                            <select name="package_type" class="form-control" required>
                                <option value="family" <?php echo ($edit_package['package_type'] ?? '') == 'family' ? 'selected' : ''; ?>>Family</option>
                                <option value="honeymoon" <?php echo ($edit_package['package_type'] ?? '') == 'honeymoon' ? 'selected' : ''; ?>>Honeymoon</option>
                                <option value="adventure" <?php echo ($edit_package['package_type'] ?? '') == 'adventure' ? 'selected' : ''; ?>>Adventure</option>
                                <option value="beach" <?php echo ($edit_package['package_type'] ?? '') == 'beach' ? 'selected' : ''; ?>>Beach</option>
                                <option value="hill_station" <?php echo ($edit_package['package_type'] ?? '') == 'hill_station' ? 'selected' : ''; ?>>Hill Station</option>
                                <option value="international" <?php echo ($edit_package['package_type'] ?? '') == 'international' ? 'selected' : ''; ?>>International</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Total Slots</label>
                            <input type="number" name="total_slots" class="form-control" required value="<?php echo $edit_package['total_slots'] ?? '10'; ?>">
                        </div>
                        <div class="form-group">
                            <label>Available Slots</label>
                            <input type="number" name="available_slots" class="form-control" required value="<?php echo $edit_package['available_slots'] ?? '10'; ?>">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Image URL</label>
                            <input type="text" name="image_url" class="form-control" placeholder="https://example.com/package-image.jpg" value="<?php echo $edit_package['image_url'] ?? ''; ?>">
                            <small>Enter a direct link to the package image</small>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <div class="checkbox-group">
                                <input type="checkbox" name="includes_flights" id="includes_flights" value="1" <?php echo ($edit_package['includes_flights'] ?? 0) ? 'checked' : ''; ?>>
                                <label for="includes_flights">Includes Flights</label>
                            </div>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Description</label>
                            <textarea name="description" class="form-control text-area" placeholder="Enter package description..."><?php echo $edit_package['description'] ?? ''; ?></textarea>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Itinerary (Day by Day)</label>
                            <textarea name="itinerary" class="form-control text-area" placeholder="Day 1: Arrival...
Day 2: Sightseeing...
Day 3: Departure..."><?php echo $edit_package['itinerary'] ?? ''; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="active" <?php echo ($edit_package['status'] ?? '') == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($edit_package['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> <?php echo $edit_package ? 'Update Package' : 'Add Package'; ?>
                        </button>
                        <?php if($edit_package): ?>
                            <a href="manage-packages.php" class="btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Packages List -->
            <div class="content-card">
                <h2>All Packages</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Package Details</th>
                                <th>Destination</th>
                                <th>Duration</th>
                                <th>Price</th>
                                <th>Inclusions</th>
                                <th>Slots</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($package = mysqli_fetch_assoc($packages)): ?>
                            <tr>
                                <td>
                                    <?php if($package['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($package['image_url']); ?>" alt="<?php echo htmlspecialchars($package['package_name']); ?>" class="package-image" onerror="this.style.display='none'">
                                    <?php endif; ?>
                                    <strong><?php echo htmlspecialchars($package['package_name']); ?></strong><br>
                                    <?php if($package['hotel_category']): ?>
                                        <span class="hotel-badge"><?php echo htmlspecialchars($package['hotel_category']); ?></span>
                                    <?php endif; ?>
                                    <?php if($package['meal_plan']): ?>
                                        <span class="meal-badge"><?php echo htmlspecialchars($package['meal_plan']); ?></span>
                                    <?php endif; ?>
                                    <?php if($package['includes_flights']): ?>
                                        <span class="flight-badge"><i class="fas fa-plane"></i> Flights Included</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($package['destination']); ?></strong></td>
                                <td>
                                    <span class="duration-badge">
                                        <?php echo $package['duration_days']; ?>D/<?php echo $package['duration_nights']; ?>N
                                    </span>
                                </td>
                                <td>
                                    <strong>₹<?php echo number_format($package['price'], 0); ?></strong><br>
                                    <small>per person</small>
                                </td>
                                <td>
                                    <?php if($package['hotel_category']): ?>
                                        <div><i class="fas fa-hotel"></i> <?php echo htmlspecialchars($package['hotel_category']); ?></div>
                                    <?php endif; ?>
                                    <?php if($package['meal_plan']): ?>
                                        <div><i class="fas fa-utensils"></i> <?php echo htmlspecialchars($package['meal_plan']); ?></div>
                                    <?php endif; ?>
                                    <?php if($package['includes_flights']): ?>
                                        <div><i class="fas fa-plane"></i> Flights Included</div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $package['available_slots']; ?>/<?php echo $package['total_slots']; ?></td>
                                <td>
                                    <?php 
                                    $type_classes = [
                                        'honeymoon' => 'honeymoon',
                                        'family' => 'family',
                                        'adventure' => 'adventure',
                                        'beach' => 'beach',
                                        'hill_station' => 'hill_station',
                                        'international' => 'international'
                                    ];
                                    $type_labels = [
                                        'honeymoon' => 'Honeymoon',
                                        'family' => 'Family',
                                        'adventure' => 'Adventure',
                                        'beach' => 'Beach',
                                        'hill_station' => 'Hill Station',
                                        'international' => 'International'
                                    ];
                                    ?>
                                    <span class="package-type-badge <?php echo $type_classes[$package['package_type']] ?? 'family'; ?>">
                                        <?php echo $type_labels[$package['package_type']] ?? ucfirst($package['package_type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($package['status'] == 'active'): ?>
                                        <span class="badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge-warning">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="manage-packages.php?edit=<?php echo $package['id']; ?>" class="btn-sm btn-primary">Edit</a>
                                    <a href="manage-packages.php?delete=<?php echo $package['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Are you sure? This will also delete all bookings for this package.')">Delete</a>
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