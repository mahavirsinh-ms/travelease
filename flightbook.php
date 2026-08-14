<?php
session_start();
include "db.php";

// Get search parameters
$from = isset($_GET['from']) ? trim($_GET['from']) : '';
$to = isset($_GET['to']) ? trim($_GET['to']) : '';

// Build SQL query with search filters
$sql = "SELECT * FROM flights WHERE status='active' AND available_seats > 0";

if (!empty($from) && isset($conn)) {
    $from_escaped = mysqli_real_escape_string($conn, $from);
    $sql .= " AND LOWER(departure_city) LIKE LOWER('%$from_escaped%')";
}

if (!empty($to) && isset($conn)) {
    $to_escaped = mysqli_real_escape_string($conn, $to);
    $sql .= " AND LOWER(arrival_city) LIKE LOWER('%$to_escaped%')";
}

$sql .= " ORDER BY departure_date, departure_time";

$result = mysqli_query($conn, $sql);
$flights = [];

// Check for query errors
if (!$result) {
    // Uncomment the line below to see database errors during development
    // die("Query failed: " . mysqli_error($conn));
    $flights = [];
} else {
    while($row = mysqli_fetch_assoc($result)) {
        $flights[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Flights - TravelEase</title>
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
        .flights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        .flight-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .flight-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        }
        .flight-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        .flight-airline {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2c3e50;
        }
        .flight-number {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        .flight-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #667eea;
        }
        .flight-route {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 20px 0;
        }
        .route-city {
            flex: 1;
        }
        .route-city h3 {
            font-size: 1.1rem;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .route-city p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        .route-arrow {
            margin: 0 20px;
            color: #667eea;
            font-size: 1.5rem;
        }
        .flight-details {
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
        .btn-book-flight {
    width: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.3s;
    text-decoration: none;   /* IMPORTANT */
    display: block;          /* IMPORTANT */
    text-align: center;      /* IMPORTANT */
}

.btn-book-flight:hover {
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
        .btn-service {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: transform 0.3s;
        }
        .btn-service:hover {
            transform: translateY(-2px);
        }
        .search-form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .search-form-container {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .search-field {
            flex: 1;
            min-width: 200px;
        }
        .search-field label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .search-field input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        .search-field input:focus {
            outline: none;
            border-color: #667eea;
        }
        .search-btn {
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            white-space: nowrap;
        }
        .search-btn:hover {
            transform: translateY(-2px);
        }
        .search-btn i {
            margin-right: 8px;
        }
        .clear-btn {
            padding: 12px 20px;
            background: #95a5a6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .clear-btn:hover {
            transform: translateY(-2px);
            background: #7f8c8d;
        }
        @media (max-width: 768px) {
            .search-form-container {
                flex-direction: column;
            }
            .search-field {
                width: 100%;
            }
            .search-btn, .clear-btn {
                width: 100%;
            }
        }
        .search-form {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: relative;
    z-index: 1;
}

    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="booking-container">
        <div class="page-header">
            <h1><i class="fas fa-plane"></i> Book Your Flight</h1>
            <p style="color: #7f8c8d;">Choose from available flights and book with ease</p>
        </div>

        <div class="search-form">
            <form method="GET" action="flightbook.php">
                <div class="search-form-container">
                    <div class="search-field">
                        <label for="from"><i class="fas fa-map-marker-alt"></i> From</label>
                        <input type="text" id="from" name="from" placeholder="Enter departure city" value="<?php echo htmlspecialchars($from); ?>">
                    </div>
                    <div class="search-field">
                        <label for="to"><i class="fas fa-map-marker-alt"></i> To</label>
                        <input type="text" id="to" name="to" placeholder="Enter arrival city" value="<?php echo htmlspecialchars($to); ?>">
                    </div>
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i> Search Flights
                    </button>
                    <?php if(!empty($from) || !empty($to)): ?>
                        <a href="flightbook.php" class="clear-btn">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php if(count($flights) > 0): ?>
            <div class="flights-grid">
                <?php foreach($flights as $flight): ?>
                    <div class="flight-card">
                        <div class="flight-header">
                            <div>
                                <div class="flight-airline"><?php echo htmlspecialchars($flight['airline']); ?></div>
                                <div class="flight-number"><?php echo htmlspecialchars($flight['flight_number']); ?></div>
                            </div>
                            <div class="flight-price">₹<?php echo number_format($flight['price'], 0); ?></div>
                        </div>

                        <div class="flight-route">
                            <div class="route-city">
                                <h3><?php echo htmlspecialchars($flight['departure_city']); ?></h3>
                                <p><?php echo date('h:i A', strtotime($flight['departure_time'])); ?></p>
                                <p style="font-size: 0.85rem;"><?php echo date('M d, Y', strtotime($flight['departure_date'])); ?></p>
                            </div>
                            <div class="route-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                            <div class="route-city" style="text-align: right;">
                                <h3><?php echo htmlspecialchars($flight['arrival_city']); ?></h3>
                                <p><?php echo date('h:i A', strtotime($flight['arrival_time'])); ?></p>
                                <p style="font-size: 0.85rem;"><?php echo date('M d, Y', strtotime($flight['arrival_date'])); ?></p>
                            </div>
                        </div>

                        <div class="flight-details">
                            <div class="detail-item">
                                <i class="fas fa-chair"></i>
                                <span><?php echo ucfirst($flight['class_type']); ?> Class</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-users"></i>
                                <span><?php echo $flight['available_seats']; ?> Seats Available</span>
                            </div>
                        </div>

                        <a href="booking.php?type=flight&id=<?php echo $flight['id']; ?>" class="btn-book-flight">
                            <i class="fas fa-check"></i> Book Now
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-plane-slash"></i>
                <h2>No Flights Found</h2>
                <?php if(!empty($from) || !empty($to)): ?>
                    <p style="color: #7f8c8d; margin-top: 10px;">No flights found matching your search criteria.</p>
                    <p style="color: #7f8c8d; margin-top: 5px;">Try adjusting your search or <a href="flightbook.php" style="color: #667eea;">view all flights</a>.</p>
                <?php else: ?>
                    <p style="color: #7f8c8d; margin-top: 10px;">Please check back later for available flights.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
