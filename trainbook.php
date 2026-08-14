<?php
session_start();
include "db.php";

// Get search parameters
$from = isset($_GET['from']) ? trim($_GET['from']) : '';
$to   = isset($_GET['to']) ? trim($_GET['to']) : '';

// Base query
$sql = "SELECT * FROM trains WHERE status='active' AND available_seats > 0";

if (!empty($from) && isset($conn)) {
    $from_escaped = mysqli_real_escape_string($conn, $from);
    $sql .= " AND LOWER(departure_station) LIKE LOWER('%$from_escaped%')";
}

if (!empty($to) && isset($conn)) {
    $to_escaped = mysqli_real_escape_string($conn, $to);
    $sql .= " AND LOWER(arrival_station) LIKE LOWER('%$to_escaped%')";
}

$sql .= " ORDER BY departure_date, departure_time";

$result = mysqli_query($conn, $sql);
$trains = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $trains[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Trains - TravelEase</title>
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
        .trains-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        .train-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .train-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        }
        .train-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        .train-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2c3e50;
        }
        .train-number {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        .train-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #667eea;
        }
        .train-route {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 20px 0;
        }
        .route-station {
            flex: 1;
        }
        .route-station h3 {
            font-size: 1.1rem;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .route-station p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        .route-arrow {
            margin: 0 20px;
            color: #667eea;
            font-size: 1.5rem;
        }
        .train-details {
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
        .btn-book-train {
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
        .btn-book-train:hover {
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

        .search-form {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: relative;
    z-index: 1;
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
    text-decoration: none;
    display: inline-block;
    transition: transform 0.3s;
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

    .search-btn,
    .clear-btn {
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
            <h1><i class="fas fa-train"></i> Book Train Tickets</h1>
            <p style="color: #7f8c8d;">Choose from available trains and book with ease</p>
        </div>

        <div class="search-form">
    <form method="GET" action="trainbook.php">
        <div class="search-form-container">
            <div class="search-field">
                <label for="from">
                    <i class="fas fa-map-marker-alt"></i> From
                </label>
                <input type="text" id="from" name="from"
                       placeholder="Enter departure station"
                       value="<?php echo htmlspecialchars($from); ?>">
            </div>

            <div class="search-field">
                <label for="to">
                    <i class="fas fa-map-marker-alt"></i> To
                </label>
                <input type="text" id="to" name="to"
                       placeholder="Enter arrival station"
                       value="<?php echo htmlspecialchars($to); ?>">
            </div>

            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i> Search Trains
            </button>

            <?php if (!empty($from) || !empty($to)): ?>
                <a href="trainbook.php" class="clear-btn">
                    <i class="fas fa-times"></i> Clear
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>


        <?php if(count($trains) > 0): ?>
            <div class="trains-grid">
                <?php foreach($trains as $train): ?>
                    <div class="train-card">
                        <div class="train-header">
                            <div>
                                <div class="train-name"><?php echo htmlspecialchars($train['train_name']); ?></div>
                                <div class="train-number"><?php echo htmlspecialchars($train['train_number']); ?></div>
                            </div>
                            <div class="train-price">₹<?php echo number_format($train['price'], 0); ?></div>
                        </div>

                        <div class="train-route">
                            <div class="route-station">
                                <h3><?php echo htmlspecialchars($train['departure_station']); ?></h3>
                                <p><?php echo date('h:i A', strtotime($train['departure_time'])); ?></p>
                                <p style="font-size: 0.85rem;"><?php echo date('M d, Y', strtotime($train['departure_date'])); ?></p>
                            </div>
                            <div class="route-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                            <div class="route-station" style="text-align: right;">
                                <h3><?php echo htmlspecialchars($train['arrival_station']); ?></h3>
                                <p><?php echo date('h:i A', strtotime($train['arrival_time'])); ?></p>
                                <p style="font-size: 0.85rem;"><?php echo date('M d, Y', strtotime($train['arrival_date'])); ?></p>
                            </div>
                        </div>

                        <div class="train-details">
                            <div class="detail-item">
                                <i class="fas fa-layer-group"></i>
                                <span><?php echo strtoupper($train['class_type']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-users"></i>
                                <span><?php echo $train['available_seats']; ?> Seats Available</span>
                            </div>
                        </div>

                        <a href="booking.php?type=train&id=<?php echo $train['id']; ?>" class="btn-book-train">
                            <i class="fas fa-check"></i> Book Now
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-train"></i>
                <h2>No Trains Available</h2>
                <p style="color: #7f8c8d; margin-top: 10px;">Please check back later for available trains.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
