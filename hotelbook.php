<?php
session_start();
include "db.php";

// Get search values
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$star    = isset($_GET['star']) ? trim($_GET['star']) : '';

// Base query
$sql = "SELECT * FROM hotels WHERE status='active' AND available_rooms > 0";

// Keyword search (city / hotel / address)
if (!empty($keyword) && isset($conn)) {
    $key = mysqli_real_escape_string($conn, $keyword);
    $sql .= " AND (
        LOWER(city) LIKE LOWER('%$key%')
        OR LOWER(name) LIKE LOWER('%$key%')
        OR LOWER(address) LIKE LOWER('%$key%')
    )";
}

// Star rating filter
if (!empty($star)) {
    $star = (int)$star;
    $sql .= " AND star_rating = $star";
}

$sql .= " ORDER BY city, star_rating DESC";

$result = mysqli_query($conn, $sql);
$hotels = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $hotels[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Hotels - TravelEase</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/all.min.css">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"> -->

    <style>
        .booking-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 20px;
        }
        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .page-header h1 {
            font-size: 2.5rem;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .hotels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        .hotel-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .hotel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        }
        .hotel-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }
        .hotel-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .hotel-content {
            padding: 25px;
        }
        .hotel-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        .hotel-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .hotel-location {
            color: #7f8c8d;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
        }
        .hotel-rating {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .stars {
            color: #f39c12;
        }
        .hotel-price {
            text-align: right;
        }
        .price-amount {
            font-size: 1.8rem;
            font-weight: 700;
            color: #667eea;
        }
        .price-label {
            font-size: 0.85rem;
            color: #7f8c8d;
        }
        .hotel-info {
            margin: 20px 0;
        }
        .hotel-address {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: flex;
            align-items: start;
            gap: 8px;
        }
        .hotel-amenities {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 15px;
        }
        .amenity-tag {
            background: #f8f9fa;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #2c3e50;
        }
        .hotel-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .detail-item i {
            color: #667eea;
            width: 20px;
        }
        .detail-item span {
            color: #2c3e50;
            font-size: 0.95rem;
        }
        .btn-book-hotel {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        .btn-book-hotel:hover {
            transform: translateY(-2px);
        }
        .no-results {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
        }
        .no-results i {
            font-size: 4rem;
            color: #bdc3c7;
            margin-bottom: 20px;
        }
        .hotel-description {
            color: #7f8c8d;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-top: 10px;
        }


/* =========================
   SEARCH FORM – CLEAN STYLE
   ========================= */

   .search-form {
    background: #ffffff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    margin-bottom: 40px;
}

.search-form-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: flex-end;
}

.search-field {
    flex: 1;
    min-width: 220px;
}

.search-field label {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 6px;
    color: #2c3e50;
}

.search-field i {
    color: #667eea;
    margin-right: 5px;
}

/* INPUT + SELECT */
.search-field input,
.search-field select {
    width: 100%;
    padding: 12px 14px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.95rem;
    background: #fff;
    color: #2c3e50;
}

.search-field input:focus,
.search-field select:focus {
    outline: none;
    border-color: #667eea;
}

/* SEARCH BUTTON */
.search-btn {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    padding: 12px 22px;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
}

.search-btn:hover {
    opacity: 0.9;
}

/* CLEAR BUTTON */
.clear-btn {
    background: #f1f2f6;
    color: #2c3e50;
    padding: 12px 18px;
    border-radius: 8px;
    font-size: 0.9rem;
    text-decoration: none;
    white-space: nowrap;
}

.clear-btn:hover {
    background: #e0e0e0;
}

/* ===============================
   HOTEL SEARCH – HARD FIX
   =============================== */

   .hotel-search-wrapper {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;

    background: #ffffff;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 40px;

    position: relative !important;
    z-index: 10 !important;

    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
}

/* layout */
.hotel-search-wrapper form {
    display: block !important;
}

.hotel-search-wrapper .search-form-container {
    display: flex !important;
    flex-wrap: wrap;
    gap: 20px;
    align-items: flex-end;
}

/* fields */
.hotel-search-wrapper .search-field {
    flex: 1;
    min-width: 220px;
}

.hotel-search-wrapper label {
    display: block;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 6px;
}

.hotel-search-wrapper input,
.hotel-search-wrapper select {
    width: 100%;
    padding: 12px 14px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
}

/* buttons */
.hotel-search-wrapper .search-btn {
    background: linear-gradient(135deg,#667eea,#764ba2);
    color: white;
    padding: 12px 22px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
}

.hotel-search-wrapper .clear-btn {
    padding: 12px 18px;
    background: #f1f2f6;
    border-radius: 8px;
    text-decoration: none;
    color: #333;
}


    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="booking-container">
        <div class="page-header">
            <h1><i class="fas fa-hotel"></i> Book Your Hotel</h1>
            <p style="color: #7f8c8d;">Choose from our selection of luxury and budget hotels</p>
        </div>

        <div class="hotel-search-wrapper">

    <form method="GET" action="hotelbook.php">
        <div class="search-form-container">

            <div class="search-field">
                <label>
                    <i class="fas fa-map-marker-alt"></i> City / Hotel Name
                </label>
                <input type="text" name="q"
                       placeholder="Enter city or hotel name"
                       value="<?php echo htmlspecialchars($keyword); ?>">
            </div>

            <div class="search-field">
                <label>
                    <i class="fas fa-star"></i> Star Rating
                </label>
                <select name="star" class="search-select">
                    <option value="">Any</option>
                    <option value="5" <?php if($star=="5") echo "selected"; ?>>5 Star</option>
                    <option value="4" <?php if($star=="4") echo "selected"; ?>>4 Star</option>
                    <option value="3" <?php if($star=="3") echo "selected"; ?>>3 Star</option>
                    <option value="2" <?php if($star=="2") echo "selected"; ?>>2 Star</option>
                    <option value="1" <?php if($star=="1") echo "selected"; ?>>1 Star</option>
                </select>
            </div>

            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i> Search Hotels
            </button>

            <?php if (!empty($keyword) || !empty($star)): ?>
                <a href="hotelbook.php" class="clear-btn">
                    <i class="fas fa-times"></i> Clear
                </a>
            <?php endif; ?>

        </div>
    </form>
</div>


        <?php if(count($hotels) > 0): ?>
            <div class="hotels-grid">
                <?php foreach($hotels as $hotel): ?>
                    <div class="hotel-card">
                        <div class="hotel-image">
                            <?php if(!empty($hotel['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
                            <?php else: ?>
                                <i class="fas fa-hotel"></i>
                            <?php endif; ?>
                        </div>
                        <div class="hotel-content">
                            <div class="hotel-header">
                                <div style="flex: 1;">
                                    <div class="hotel-name"><?php echo htmlspecialchars($hotel['name']); ?></div>
                                    <div class="hotel-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($hotel['city']); ?>
                                    </div>
                                    <div class="hotel-rating">
                                        <?php 
                                        $stars = $hotel['star_rating'];
                                        for($i = 0; $i < 5; $i++): 
                                        ?>
                                            <i class="fas fa-star <?php echo $i < $stars ? 'stars' : ''; ?>" style="color: <?php echo $i < $stars ? '#f39c12' : '#ddd'; ?>;"></i>
                                        <?php endfor; ?>
                                        <span style="margin-left: 5px; color: #7f8c8d; font-size: 0.9rem;"><?php echo $stars; ?> Star</span>
                                    </div>
                                </div>
                                <div class="hotel-price">
                                    <div class="price-amount">₹<?php echo number_format($hotel['price_per_night'], 0); ?></div>
                                    <div class="price-label">per night</div>
                                </div>
                            </div>

                            <?php if(!empty($hotel['address'])): ?>
                                <div class="hotel-address">
                                    <i class="fas fa-map-pin"></i>
                                    <span><?php echo htmlspecialchars($hotel['address']); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if(!empty($hotel['description'])): ?>
                                <div class="hotel-description">
                                    <?php echo htmlspecialchars(substr($hotel['description'], 0, 120)); ?>
                                    <?php echo strlen($hotel['description']) > 120 ? '...' : ''; ?>
                                </div>
                            <?php endif; ?>

                            <div class="hotel-details">
                                <div class="detail-item">
                                    <i class="fas fa-door-open"></i>
                                    <span><?php echo $hotel['available_rooms']; ?> Rooms Available</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-building"></i>
                                    <span><?php echo $hotel['total_rooms']; ?> Total Rooms</span>
                                </div>
                            </div>

                            <?php if(!empty($hotel['amenities'])): ?>
                                <div class="hotel-amenities">
                                    <?php 
                                    $amenities = explode(',', $hotel['amenities']);
                                    foreach(array_slice($amenities, 0, 4) as $amenity): 
                                    ?>
                                        <span class="amenity-tag"><?php echo htmlspecialchars(trim($amenity)); ?></span>
                                    <?php endforeach; ?>
                                    <?php if(count($amenities) > 4): ?>
                                        <span class="amenity-tag">+<?php echo count($amenities) - 4; ?> more</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <a href="booking.php?type=hotel&id=<?php echo $hotel['id']; ?>" class="btn-book-hotel" style="margin-top: 20px;">
                                <i class="fas fa-check"></i> Book Now
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-hotel"></i>
                <h2>No Hotels Available</h2>
                <p style="color: #7f8c8d; margin-top: 10px;">Please check back later for available hotels.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
