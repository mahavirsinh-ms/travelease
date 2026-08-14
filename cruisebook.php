<?php
session_start();
include "db.php";

// Get all active cruises
$sql = "SELECT * FROM cruises WHERE status='active' AND available_cabins > 0 ORDER BY departure_date, price";
$result = mysqli_query($conn, $sql);
$cruises = [];
while($row = mysqli_fetch_assoc($result)) {
    $cruises[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Cruises - TravelEase</title>
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
        .cruises-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        .cruise-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .cruise-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        }
        .cruise-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }
        .cruise-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .cruise-content {
            padding: 25px;
        }
        .cruise-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        .cruise-line {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .cruise-ship {
            color: #7f8c8d;
            font-size: 0.95rem;
        }
        .cruise-price {
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
        .cruise-info {
            margin: 20px 0;
        }
        .cruise-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 15px 0;
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
        .cruise-route {
            background: #e8f4f8;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .route-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 5px 0;
            color: #2c3e50;
        }
        .cruise-amenities {
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
        .btn-book-cruise {
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
            margin-top: 20px;
        }
        .btn-book-cruise:hover {
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
        .cruise-description {
            color: #7f8c8d;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-top: 10px;
        }
        .cabin-badge {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="booking-container">
        <div class="page-header">
            <h1><i class="fas fa-ship"></i> Book Your Cruise</h1>
            <p style="color: #7f8c8d;">Discover luxury cruise packages with amazing destinations</p>
        </div>

        <?php if(count($cruises) > 0): ?>
            <div class="cruises-grid">
                <?php foreach($cruises as $cruise): ?>
                    <div class="cruise-card">
                        <div class="cruise-image">
                            <?php if(!empty($cruise['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($cruise['image_url']); ?>" alt="<?php echo htmlspecialchars($cruise['ship_name']); ?>">
                            <?php else: ?>
                                <i class="fas fa-ship"></i>
                            <?php endif; ?>
                        </div>
                        <div class="cruise-content">
                            <div class="cruise-header">
                                <div style="flex: 1;">
                                    <div class="cruise-line"><?php echo htmlspecialchars($cruise['cruise_line']); ?></div>
                                    <div class="cruise-ship"><?php echo htmlspecialchars($cruise['ship_name']); ?></div>
                                    <span class="cabin-badge"><?php echo ucfirst(str_replace('_', ' ', $cruise['cabin_type'])); ?> Cabin</span>
                                </div>
                                <div class="cruise-price">
                                    <div class="price-amount">₹<?php echo number_format($cruise['price'], 0); ?></div>
                                    <div class="price-label">per person</div>
                                </div>
                            </div>

                            <div class="cruise-route">
                                <div class="route-item">
                                    <i class="fas fa-anchor"></i>
                                    <strong>Departure:</strong> <?php echo htmlspecialchars($cruise['departure_port']); ?>
                                </div>
                                <div class="route-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <strong>Date:</strong> <?php echo date('M d, Y', strtotime($cruise['departure_date'])); ?>
                                </div>
                                <div class="route-item">
                                    <i class="fas fa-route"></i>
                                    <strong>Itinerary:</strong> <?php echo ucfirst(htmlspecialchars($cruise['itinerary_type'])); ?>
                                </div>
                                <div class="route-item">
                                    <i class="fas fa-moon"></i>
                                    <strong>Duration:</strong> <?php echo $cruise['duration_nights']; ?> Nights
                                </div>
                            </div>

                            <?php if(!empty($cruise['description'])): ?>
                                <div class="cruise-description">
                                    <?php echo htmlspecialchars(substr($cruise['description'], 0, 120)); ?>
                                    <?php echo strlen($cruise['description']) > 120 ? '...' : ''; ?>
                                </div>
                            <?php endif; ?>

                            <div class="cruise-details">
                                <div class="detail-item">
                                    <i class="fas fa-door-closed"></i>
                                    <span><?php echo $cruise['available_cabins']; ?> Cabins Available</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-ship"></i>
                                    <span><?php echo $cruise['total_cabins']; ?> Total Cabins</span>
                                </div>
                            </div>

                            <?php if(!empty($cruise['amenities'])): ?>
                                <div class="cruise-amenities">
                                    <?php 
                                    $amenities = explode(',', $cruise['amenities']);
                                    foreach(array_slice($amenities, 0, 4) as $amenity): 
                                    ?>
                                        <span class="amenity-tag"><?php echo htmlspecialchars(trim($amenity)); ?></span>
                                    <?php endforeach; ?>
                                    <?php if(count($amenities) > 4): ?>
                                        <span class="amenity-tag">+<?php echo count($amenities) - 4; ?> more</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <a href="booking.php?type=cruise&id=<?php echo $cruise['id']; ?>" class="btn-book-cruise">
                                <i class="fas fa-check"></i> Book Now
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-ship"></i>
                <h2>No Cruises Available</h2>
                <p style="color: #7f8c8d; margin-top: 10px;">Please check back later for available cruise packages.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
