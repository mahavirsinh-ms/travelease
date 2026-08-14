<?php
session_start();
include "db.php";

// Get filter parameters
$destination_filter = $_GET['destination'] ?? '';
$type_filter = $_GET['package_type'] ?? '';
$duration_filter = $_GET['duration'] ?? '';
$price_min = $_GET['price_min'] ?? '';
$price_max = $_GET['price_max'] ?? '';

// Build query with filters
$sql = "SELECT * FROM holiday_packages WHERE status='active' AND available_slots > 0";

if (!empty($destination_filter)) {
    $sql .= " AND destination LIKE '%$destination_filter%'";
}
if (!empty($type_filter) && $type_filter != 'all') {
    $sql .= " AND package_type='$type_filter'";
}
if (!empty($duration_filter)) {
    if ($duration_filter == 'short') {
        $sql .= " AND duration_days <= 4";
    } elseif ($duration_filter == 'medium') {
        $sql .= " AND duration_days > 4 AND duration_days <= 7";
    } elseif ($duration_filter == 'long') {
        $sql .= " AND duration_days > 7";
    }
}
if (!empty($price_min)) {
    $sql .= " AND price >= $price_min";
}
if (!empty($price_max)) {
    $sql .= " AND price <= $price_max";
}

$sql .= " ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
$packages = [];
while($row = mysqli_fetch_assoc($result)) {
    $packages[] = $row;
}

// Get unique destinations for filter
$destinations_query = "SELECT DISTINCT destination FROM holiday_packages WHERE status='active' ORDER BY destination";
$destinations_result = mysqli_query($conn, $destinations_query);
$destinations = [];
while($row = mysqli_fetch_assoc($destinations_result)) {
    $destinations[] = $row['destination'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holiday Packages - TravelEase</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/all.min.css">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"> -->
 <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-bottom: 50px;
        }

        .holiday-hero {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)), url('./images/bgimg.jpeg') center/cover;
            padding: 80px 20px;
            text-align: center;
            color: white;
            margin-bottom: 50px;
        }

        .holiday-hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .holiday-hero p {
            font-size: 1.3rem;
            opacity: 0.95;
            max-width: 700px;
            margin: 0 auto;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 7px;
        }

        .filters-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .filters-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .filter-group select,
        .filter-group input {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .btn-filter {
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .btn-apply {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            flex: 1;
        }

        .btn-apply:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-clear {
            background: #f8f9fa;
            color: #2c3e50;
            border: 2px solid #e0e0e0;
        }

        .btn-clear:hover {
            background: #e9ecef;
        }

        .packages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 30px;
        }

        .package-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        .package-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .package-image {
            width: 100%;
            height: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }

        .package-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .package-card:hover .package-image img {
            transform: scale(1.1);
        }

        .package-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.95);
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            color: #667eea;
            backdrop-filter: blur(10px);
        }

        .package-content {
            padding: 25px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .package-header {
            margin-bottom: 15px;
        }

        .package-name {
            font-size: 1.6rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .package-destination {
            color: #667eea;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .package-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-item i {
            color: #667eea;
            font-size: 1.2rem;
        }

        .info-item span {
            color: #2c3e50;
            font-size: 0.95rem;
        }

        .package-description {
            color: #7f8c8d;
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 15px 0;
            flex: 1;
        }

        .package-features {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 15px 0;
        }

        .feature-tag {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .package-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .package-price {
            display: flex;
            flex-direction: column;
        }

        .price-label {
            font-size: 0.85rem;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .price-amount {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
        }

        .btn-book-package {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-book-package:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .no-results {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }

        .no-results i {
            font-size: 5rem;
            color: #bdc3c7;
            margin-bottom: 20px;
        }

        .no-results h2 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .no-results p {
            color: #7f8c8d;
        }

        .results-count {
            color: white;
            font-size: 1.1rem;
            margin-top: 20px;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .holiday-hero h1 {
                font-size: 2.5rem;
            }

            .holiday-hero p {
                font-size: 1.1rem;
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }

            .packages-grid {
                grid-template-columns: 1fr;
            }

            .package-footer {
                flex-direction: column;
                gap: 15px;
            }

            .btn-book-package {
                width: 100%;
            }
        }



    </style>

</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="holiday-hero">
        <div class="container">
            <h1><i class="fas fa-suitcase-rolling"></i> Discover Your Perfect Holiday</h1>
            <p>Choose from our handpicked holiday packages and create memories that last a lifetime</p>
            <div class="results-count">
                <?php echo count($packages); ?> Amazing Packages Available
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filters-title">
                <i class="fas fa-filter"></i> Customize Your Search
            </div>
            <form method="GET" action="" id="filterForm">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label><i class="fas fa-map-marker-alt"></i> Destination</label>
                        <select name="destination">
                            <option value="">All Destinations</option>
                            <?php foreach($destinations as $dest): ?>
                                <option value="<?php echo htmlspecialchars($dest); ?>" <?php echo $destination_filter == $dest ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dest); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label><i class="fas fa-tags"></i> Package Type</label>
                        <select name="package_type">
                            <option value="all">All Types</option>
                            <option value="honeymoon" <?php echo $type_filter == 'honeymoon' ? 'selected' : ''; ?>>Honeymoon</option>
                            <option value="family" <?php echo $type_filter == 'family' ? 'selected' : ''; ?>>Family</option>
                            <option value="adventure" <?php echo $type_filter == 'adventure' ? 'selected' : ''; ?>>Adventure</option>
                            <option value="beach" <?php echo $type_filter == 'beach' ? 'selected' : ''; ?>>Beach</option>
                            <option value="hill_station" <?php echo $type_filter == 'hill_station' ? 'selected' : ''; ?>>Hill Station</option>
                            <option value="international" <?php echo $type_filter == 'international' ? 'selected' : ''; ?>>International</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label><i class="fas fa-calendar-alt"></i> Duration</label>
                        <select name="duration">
                            <option value="">Any Duration</option>
                            <option value="short" <?php echo $duration_filter == 'short' ? 'selected' : ''; ?>>Short (1-4 Days)</option>
                            <option value="medium" <?php echo $duration_filter == 'medium' ? 'selected' : ''; ?>>Medium (5-7 Days)</option>
                            <option value="long" <?php echo $duration_filter == 'long' ? 'selected' : ''; ?>>Long (8+ Days)</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label><i class="fas fa-rupee-sign"></i> Min Price</label>
                        <input type="number" name="price_min" placeholder="Min Price" value="<?php echo htmlspecialchars($price_min); ?>">
                    </div>

                    <div class="filter-group">
                        <label><i class="fas fa-rupee-sign"></i> Max Price</label>
                        <input type="number" name="price_max" placeholder="Max Price" value="<?php echo htmlspecialchars($price_max); ?>">
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-filter btn-apply">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <a href="holidaybook.php" class="btn-filter btn-clear">
                        <i class="fas fa-times"></i> Clear All
                    </a>
                </div>
            </form>
        </div>

        <!-- Packages Grid -->
        <?php if(count($packages) > 0): ?>
            <div class="packages-grid">
                <?php foreach($packages as $package): ?>
                    <div class="package-card">
                        <div class="package-image">
                            <?php if(!empty($package['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($package['image_url']); ?>" alt="<?php echo htmlspecialchars($package['package_name']); ?>">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem;">
                                    <i class="fas fa-umbrella-beach"></i>
                                </div>
                            <?php endif; ?>
                            <div class="package-badge">
                                <?php echo ucfirst(str_replace('_', ' ', $package['package_type'])); ?>
                            </div>
                        </div>

                        <div class="package-content">
                            <div class="package-header">
                                <div class="package-name"><?php echo htmlspecialchars($package['package_name']); ?></div>
                                <div class="package-destination">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($package['destination']); ?>
                                </div>
                            </div>

                            <div class="package-info">
                                <div class="info-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <span><?php echo $package['duration_days']; ?> Days / <?php echo $package['duration_nights']; ?> Nights</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-hotel"></i>
                                    <span><?php echo ucfirst($package['hotel_category'] ?? 'Standard'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-utensils"></i>
                                    <span><?php echo ucfirst(str_replace('-', ' ', $package['meal_plan'] ?? 'Not Included')); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-plane"></i>
                                    <span><?php echo $package['includes_flights'] ? 'Flights Included' : 'Flights Not Included'; ?></span>
                                </div>
                            </div>

                            <?php if(!empty($package['description'])): ?>
                                <div class="package-description">
                                    <?php echo htmlspecialchars(substr($package['description'], 0, 150)); ?>
                                    <?php echo strlen($package['description']) > 150 ? '...' : ''; ?>
                                    
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($package['itinerary'])): ?>
    <details style="margin-top: 15px;">
        <summary style="
            cursor: pointer;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 8px;
        ">
            <i class="fas fa-route"></i> View Itinerary
        </summary>
        <div style="
            margin-top: 10px;
            font-size: 0.9rem;
            color: #2c3e50;
            line-height: 1.6;
            background: #f8f9fa;
            padding: 12px;
            border-radius: 10px;
        ">
            <?php echo nl2br(htmlspecialchars($package['itinerary'])); ?>
        </div>
    </details>
<?php endif; ?>


                            <div class="package-features">
                                <?php if($package['hotel_category']): ?>
                                    <span class="feature-tag"><?php echo ucfirst($package['hotel_category']); ?> Hotel</span>
                                <?php endif; ?>
                                <?php if($package['includes_flights']): ?>
                                    <span class="feature-tag"><i class="fas fa-plane"></i> Flights</span>
                                <?php endif; ?>
                                <span class="feature-tag"><?php echo $package['available_slots']; ?> Slots Left</span>
                            </div>

                            <div class="package-footer">
                                <div class="package-price">
                                    <span class="price-label">Starting from</span>
                                    <span class="price-amount">₹<?php echo number_format($package['price'], 0); ?></span>
                                </div>
                                <a href="booking.php?type=holiday&id=<?php echo $package['id']; ?>" class="btn-book-package">
                                    <i class="fas fa-check"></i> Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h2>No Packages Found</h2>
                <p>Try adjusting your filters to find more holiday packages</p>
                <a href="holidaybook.php" class="btn-book-package" style="margin-top: 20px;">
                    <i class="fas fa-redo"></i> Clear Filters
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto-submit form on filter change (optional - you can remove this if you prefer manual Apply button)
        // document.querySelectorAll('.filter-group select, .filter-group input').forEach(element => {
        //     element.addEventListener('change', function() {
        //         document.getElementById('filterForm').submit();
        //     });
        // });

       
document.addEventListener("DOMContentLoaded", function () {
    const profileButton = document.getElementById("profileButton");
    const profileMenu = document.getElementById("profileMenu");

    if (profileButton && profileMenu) {
        profileButton.addEventListener("click", function (e) {
            e.stopPropagation();
            profileMenu.classList.toggle("show");
        });

        // Close dropdown when clicking outside
        document.addEventListener("click", function () {
            profileMenu.classList.remove("show");
        });
    }
});


    </script>

<script src="js/script.js"></script>

</body>
</html>
