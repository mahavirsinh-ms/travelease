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
    mysqli_query($conn, "DELETE FROM hotels WHERE id='$id'");
    header("Location: manage-hotels.php?success=deleted");
    exit();
}

// Handle add/edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $star_rating = $_POST['star_rating'];
    $price_per_night = $_POST['price_per_night'];
    $available_rooms = $_POST['available_rooms'];
    $total_rooms = $_POST['total_rooms'];
    $amenities = $_POST['amenities'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];
    $status = $_POST['status'];

    // Escape strings for SQL
    $address = mysqli_real_escape_string($conn, $address);
    $amenities = mysqli_real_escape_string($conn, $amenities);
    $description = mysqli_real_escape_string($conn, $description);

    if ($id) {
        // Update
        $sql = "UPDATE hotels SET 
                name='$name', 
                city='$city', 
                address='$address', 
                star_rating='$star_rating', 
                price_per_night='$price_per_night', 
                available_rooms='$available_rooms', 
                total_rooms='$total_rooms', 
                amenities='$amenities', 
                description='$description', 
                image_url='$image_url', 
                status='$status' 
                WHERE id='$id'";
    } else {
        // Insert
        $sql = "INSERT INTO hotels (name, city, address, star_rating, price_per_night, available_rooms, total_rooms, 
                amenities, description, image_url, status) 
                VALUES ('$name', '$city', '$address', '$star_rating', '$price_per_night', '$available_rooms', 
                '$total_rooms', '$amenities', '$description', '$image_url', '$status')";
    }
    
    mysqli_query($conn, $sql);
    header("Location: manage-hotels.php?success=" . ($id ? 'updated' : 'added'));
    exit();
}

// Get hotel for editing
$edit_hotel = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM hotels WHERE id='$id'");
    $edit_hotel = mysqli_fetch_assoc($result);
}

// Get all hotels
$hotels = mysqli_query($conn, "SELECT * FROM hotels ORDER BY star_rating DESC, name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hotels - TravelEase Admin</title>
    <link rel="stylesheet" href="../css/all.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .hotel-icon {
            color: #9C27B0;
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
        .hotel-image {
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
        .star-rating {
            color: #FFD700;
            font-size: 14px;
        }
        .star-rating-select {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }
        .star-rating-select label {
            cursor: pointer;
            font-size: 20px;
            color: #ddd;
            transition: color 0.2s;
        }
        .star-rating-select input[type="radio"] {
            display: none;
        }
        .star-rating-select input[type="radio"]:checked ~ label,
        .star-rating-select label:hover,
        .star-rating-select label:hover ~ label {
            color: #FFD700;
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
            gap: 8px;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .amenity-tag {
            display: inline-block;
            background: #e9ecef;
            color: #495057;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .price-tag {
            font-weight: bold;
            color: #28a745;
            font-size: 16px;
        }
        .room-info {
            font-size: 12px;
            color: #6c757d;
        }
        .hotel-card {
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }
        .hotel-info {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1><i class="fas fa-hotel hotel-icon"></i> Manage Hotels</h1>
                <div class="header-actions">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="../index.php" class="btn-secondary"><i class="fas fa-home"></i> View Site</a>
                </div>
            </header>

            <?php if(isset($_GET['success'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    Hotel <?php echo $_GET['success']; ?> successfully!
                </div>
            <?php endif; ?>

            <!-- Add/Edit Form -->
            <div class="content-card">
                <h2><?php echo $edit_hotel ? 'Edit Hotel' : 'Add New Hotel'; ?></h2>
                <form method="POST" action="">
                    <?php if($edit_hotel): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_hotel['id']; ?>">
                    <?php endif; ?>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                        <div class="form-group">
                            <label>Hotel Name</label>
                            <input type="text" name="name" class="form-control" required value="<?php echo $edit_hotel['name'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" class="form-control" required value="<?php echo $edit_hotel['city'] ?? ''; ?>">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Address</label>
                            <textarea name="address" class="form-control text-area" placeholder="Enter full address"><?php echo $edit_hotel['address'] ?? ''; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Star Rating</label>
                            <div class="star-rating-select">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <input type="radio" id="star<?php echo $i; ?>" name="star_rating" value="<?php echo $i; ?>" 
                                           <?php echo ($edit_hotel['star_rating'] ?? 3) == $i ? 'checked' : ''; ?>>
                                    <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" id="star_rating_value" value="<?php echo $edit_hotel['star_rating'] ?? 3; ?>">
                        </div>
                        <div class="form-group">
                            <label>Price Per Night (₹)</label>
                            <input type="number" name="price_per_night" class="form-control" step="0.01" required value="<?php echo $edit_hotel['price_per_night'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Total Rooms</label>
                            <input type="number" name="total_rooms" class="form-control" required value="<?php echo $edit_hotel['total_rooms'] ?? '10'; ?>">
                        </div>
                        <div class="form-group">
                            <label>Available Rooms</label>
                            <input type="number" name="available_rooms" class="form-control" required value="<?php echo $edit_hotel['available_rooms'] ?? '10'; ?>">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Image URL</label>
                            <input type="text" name="image_url" class="form-control" placeholder="https://example.com/hotel-image.jpg" value="<?php echo $edit_hotel['image_url'] ?? ''; ?>">
                            <small>Enter a direct link to the hotel image</small>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Amenities (comma-separated)</label>
                            <textarea name="amenities" class="form-control text-area" placeholder="Wi-Fi, Pool, Spa, Gym, Restaurant, Parking, Room Service..."><?php echo $edit_hotel['amenities'] ?? ''; ?></textarea>
                            <small>Enter amenities separated by commas</small>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Description</label>
                            <textarea name="description" class="form-control text-area" placeholder="Enter hotel description..."><?php echo $edit_hotel['description'] ?? ''; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="active" <?php echo ($edit_hotel['status'] ?? '') == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($edit_hotel['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> <?php echo $edit_hotel ? 'Update Hotel' : 'Add Hotel'; ?>
                        </button>
                        <?php if($edit_hotel): ?>
                            <a href="manage-hotels.php" class="btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Hotels List -->
            <div class="content-card">
                <h2>All Hotels</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Hotel Details</th>
                                <th>Location</th>
                                <th>Rating</th>
                                <th>Price/Night</th>
                                <th>Rooms</th>
                                <th>Amenities</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($hotel = mysqli_fetch_assoc($hotels)): ?>
                            <tr>
                                <td>
                                    <div class="hotel-card">
                                        <?php if($hotel['image_url']): ?>
                                            <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>" class="hotel-image" onerror="this.style.display='none'">
                                        <?php endif; ?>
                                        <div class="hotel-info">
                                            <strong><?php echo htmlspecialchars($hotel['name']); ?></strong><br>
                                            <?php if($hotel['address']): ?>
                                                <small class="text-muted"><?php echo substr(htmlspecialchars($hotel['address']), 0, 50); ?><?php echo strlen($hotel['address']) > 50 ? '...' : ''; ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><strong><?php echo htmlspecialchars($hotel['city']); ?></strong></td>
                                <td>
                                    <div class="star-rating">
                                        <?php 
                                        $rating = $hotel['star_rating'];
                                        for($i = 1; $i <= 5; $i++): 
                                            if($i <= $rating): ?>
                                                <i class="fas fa-star"></i>
                                            <?php else: ?>
                                                <i class="far fa-star"></i>
                                            <?php endif; 
                                        endfor; ?>
                                        <br>
                                        <small><?php echo $rating; ?> Star</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="price-tag">₹<?php echo number_format($hotel['price_per_night'], 0); ?></span><br>
                                    <small class="room-info">per night</small>
                                </td>
                                <td>
                                    <?php echo $hotel['available_rooms']; ?>/<?php echo $hotel['total_rooms']; ?><br>
                                    <small class="room-info">Available/Total</small>
                                </td>
                                <td>
                                    <?php if($hotel['amenities']): 
                                        $amenities_array = explode(',', $hotel['amenities']);
                                        $display_amenities = array_slice($amenities_array, 0, 3);
                                        foreach($display_amenities as $amenity):
                                            $amenity = trim($amenity);
                                            if(!empty($amenity)): ?>
                                                <span class="amenity-tag"><?php echo htmlspecialchars($amenity); ?></span>
                                            <?php endif;
                                        endforeach;
                                        if(count($amenities_array) > 3): ?>
                                            <span class="amenity-tag">+<?php echo count($amenities_array) - 3; ?> more</span>
                                        <?php endif;
                                    endif; ?>
                                </td>
                                <td>
                                    <?php if($hotel['status'] == 'active'): ?>
                                        <span class="badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge-warning">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="manage-hotels.php?edit=<?php echo $hotel['id']; ?>" class="btn-sm btn-primary">Edit</a>
                                    <a href="manage-hotels.php?delete=<?php echo $hotel['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Are you sure? This will also delete all bookings for this hotel.')">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Star rating selection enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const starInputs = document.querySelectorAll('.star-rating-select input[type="radio"]');
            const starLabels = document.querySelectorAll('.star-rating-select label');
            
            // Highlight stars based on selected value
            starInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const selectedValue = this.value;
                    starLabels.forEach((label, index) => {
                        if (index < selectedValue) {
                            label.style.color = '#FFD700';
                        } else {
                            label.style.color = '#ddd';
                        }
                    });
                });
            });
            
            // Set initial star colors based on selected value
            const initialRating = document.getElementById('star_rating_value')?.value || 3;
            starLabels.forEach((label, index) => {
                if (index < initialRating) {
                    label.style.color = '#FFD700';
                }
            });
            
            // Hover effect
            starLabels.forEach((label, index) => {
                label.addEventListener('mouseenter', function() {
                    const starIndex = index + 1;
                    starLabels.forEach((label2, index2) => {
                        if (index2 < starIndex) {
                            label2.style.color = '#FFD700';
                        } else {
                            label2.style.color = '#ddd';
                        }
                    });
                });
                
                label.addEventListener('mouseleave', function() {
                    const checkedInput = document.querySelector('.star-rating-select input[type="radio"]:checked');
                    const currentRating = checkedInput ? parseInt(checkedInput.value) : initialRating;
                    starLabels.forEach((label2, index2) => {
                        if (index2 < currentRating) {
                            label2.style.color = '#FFD700';
                        } else {
                            label2.style.color = '#ddd';
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>