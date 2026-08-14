<?php
session_start();
include "db.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$type = $_GET['type'] ?? '';
$item_id = (int)($_GET['id'] ?? 0);

if (!$type || !$item_id) {
    header("Location: index.php");
    exit();
}


// ============================================================
// DETERMINE TABLE
// ============================================================

$table_name = '';

switch ($type) {
    case 'flight':
        $table_name = 'flights';
        break;

    case 'hotel':
        $table_name = 'hotels';
        break;

    case 'train':
        $table_name = 'trains';
        break;

    case 'bus':
        $table_name = 'buses';
        break;

    case 'cruise':
        $table_name = 'cruises';
        break;

    case 'holiday':
        $table_name = 'holiday_packages';
        break;

    default:
        header("Location: index.php");
        exit();
}


// ============================================================
// GET ITEM
// ============================================================

$result = mysqli_query(
    $conn,
    "SELECT * FROM {$table_name} WHERE id = {$item_id}"
);

$item = mysqli_fetch_assoc($result);

if (!$item) {
    header("Location: index.php");
    exit();
}


// ============================================================
// INITIALIZE DATES
// ============================================================

$travel_date = null;
$return_date = null;


// Cruise
if ($type === 'cruise') {

    $travel_date = !empty($item['departure_date'])
        ? $item['departure_date']
        : null;

    if ($travel_date) {

        $duration_nights = (int)($item['duration_nights'] ?? 0);

        $return_date = date(
            'Y-m-d',
            strtotime(
                $travel_date . " + {$duration_nights} days"
            )
        );
    }
}


// Holiday
elseif ($type === 'holiday') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $travel_date = !empty($_POST['travel_date'])
            ? $_POST['travel_date']
            : null;
    }

    $duration_days = (int)($item['duration_days'] ?? 1);

    if ($travel_date) {

        $return_date = date(
            'Y-m-d',
            strtotime(
                $travel_date . " + {$duration_days} days"
            )
        );
    }
}


// Flight / Train / Bus
elseif (
    in_array(
        $type,
        ['flight', 'train', 'bus'],
        true
    )
) {

    $travel_date = !empty($item['departure_date'])
        ? $item['departure_date']
        : null;
}


// ============================================================
// HANDLE BOOKING
// ============================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = (int)$_SESSION['user_id'];

    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($quantity < 1) {
        $quantity = 1;
    }


    // --------------------------------------------------------
    // TRAVEL DATE
    // --------------------------------------------------------

    if (
        in_array(
            $type,
            ['flight', 'train', 'bus', 'cruise'],
            true
        )
    ) {

        $travel_date = !empty($item['departure_date'])
            ? $item['departure_date']
            : null;

    } else {

        $travel_date = !empty($_POST['travel_date'])
            ? $_POST['travel_date']
            : null;
    }


    // --------------------------------------------------------
    // HOTEL DATES
    // --------------------------------------------------------

    $check_in = !empty($_POST['check_in'])
        ? $_POST['check_in']
        : null;

    $check_out = !empty($_POST['check_out'])
        ? $_POST['check_out']
        : null;


    // --------------------------------------------------------
    // CALCULATE PRICE
    // --------------------------------------------------------

    if ($type === 'hotel') {

        $unit_price = (float)$item['price_per_night'];

    } else {

        $unit_price = (float)$item['price'];
    }


    if (
        $type === 'hotel' &&
        $check_in &&
        $check_out
    ) {

        $days = (
            strtotime($check_out) -
            strtotime($check_in)
        ) / 86400;

        if ($days < 1) {
            $days = 1;
        }

        $total_amount =
            $unit_price *
            $days *
            $quantity;

    } else {

        $total_amount =
            $unit_price *
            $quantity;
    }


    // --------------------------------------------------------
    // BOOKING REFERENCE
    // --------------------------------------------------------

    $booking_ref =
        strtoupper($type) .
        rand(1000, 9999) .
        time();


    // --------------------------------------------------------
    // BOOKING DETAILS
    // --------------------------------------------------------

    if ($type === 'flight') {

        $details =
            $item['airline'] . ' ' .
            $item['flight_number'] . ' - ' .
            $item['departure_city'] . ' to ' .
            $item['arrival_city'];

    } elseif ($type === 'hotel') {

        $details =
            $item['name'] . ' - ' .
            $item['city'];

    } elseif ($type === 'train') {

        $details =
            $item['train_name'] . ' (' .
            $item['train_number'] . ') - ' .
            $item['departure_station'] . ' to ' .
            $item['arrival_station'];

    } elseif ($type === 'bus') {

        $details =
            $item['bus_name'] . ' (' .
            $item['bus_number'] . ') - ' .
            $item['departure_city'] . ' to ' .
            $item['arrival_city'];

    } elseif ($type === 'cruise') {

        $details =
            $item['cruise_line'] . ' - ' .
            $item['ship_name'];

    } else {

        $details =
            $item['package_name'] . ' - ' .
            $item['destination'];
    }


    // ========================================================
    // INSERT BOOKING
    // ========================================================

    try {

        /*
         * IMPORTANT:
         *
         * PostgreSQL does not accept an empty string for a DATE.
         *
         * Therefore we use NULL for missing dates.
         */

        $travel_date_sql =
            $travel_date !== null
            ? "'" . mysqli_real_escape_string(
                $conn,
                $travel_date
            ) . "'"
            : "NULL";


        $return_date_sql =
            $return_date !== null
            ? "'" . mysqli_real_escape_string(
                $conn,
                $return_date
            ) . "'"
            : "NULL";


        $check_in_sql =
            $check_in !== null
            ? "'" . mysqli_real_escape_string(
                $conn,
                $check_in
            ) . "'"
            : "NULL";


        $check_out_sql =
            $check_out !== null
            ? "'" . mysqli_real_escape_string(
                $conn,
                $check_out
            ) . "'"
            : "NULL";


        $safe_type =
            mysqli_real_escape_string(
                $conn,
                $type
            );

        $safe_booking_ref =
            mysqli_real_escape_string(
                $conn,
                $booking_ref
            );

        $safe_details =
            mysqli_real_escape_string(
                $conn,
                $details
            );


        $sql = "
            INSERT INTO bookings (
                user_id,
                booking_type,
                booking_reference,
                item_id,
                details,
                travel_date,
                return_date,
                check_in_date,
                check_out_date,
                quantity,
                total_amount,
                status,
                payment_status
            )
            VALUES (
                {$user_id},
                '{$safe_type}',
                '{$safe_booking_ref}',
                {$item_id},
                '{$safe_details}',
                {$travel_date_sql},
                {$return_date_sql},
                {$check_in_sql},
                {$check_out_sql},
                {$quantity},
                {$total_amount},
                'pending',
                'pending'
            )
            RETURNING id
        ";


        /*
         * We cannot use the old mysqli_query() wrapper for
         * RETURNING id because it currently doesn't expose it.
         *
         * Execute it directly through the underlying PDO.
         */

        $stmt = $conn->pdo->query(
            travelEaseNormalizeSql($sql)
        );

        $booking_id = $stmt->fetchColumn();


        if (!$booking_id) {
            throw new Exception(
                "Booking was inserted but booking ID was not returned."
            );
        }


        // ====================================================
        // UPDATE AVAILABILITY
        // ====================================================

        if (
            in_array(
                $type,
                ['flight', 'train', 'bus'],
                true
            )
        ) {

            $availability_column =
                'available_seats';

        } elseif ($type === 'hotel') {

            $availability_column =
                'available_rooms';

        } elseif ($type === 'cruise') {

            $availability_column =
                'available_cabins';

        } else {

            $availability_column =
                'available_slots';
        }


        $update_sql = "
            UPDATE {$table_name}
            SET {$availability_column} =
                {$availability_column} - {$quantity}
            WHERE id = {$item_id}
        ";


        mysqli_query(
            $conn,
            $update_sql
        );


        // ====================================================
        // PAYMENT
        // ====================================================

        header(
            "Location: payment.php?booking_id=" .
            urlencode($booking_id)
        );

        exit();


    } catch (Throwable $e) {

        error_log(
            "Booking error: " .
            $e->getMessage()
        );

        $error =
            "Booking failed. Please try again.";
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Complete Booking - TravelEase</title>

    <link rel="stylesheet" href="css/style.css">

    <link rel="stylesheet" href="css/all.min.css">

    <link rel="stylesheet" href="css/fonts.css">

    <link
        rel="icon"
        href="travelEASEonly.png"
        type="image/png"
    >

    <style>

        .booking-container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
        }

        .booking-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-book {
            background: linear-gradient(
                135deg,
                #667eea 0%,
                #764ba1 100%
            );

            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
        }

        .btn-book:hover {
            transform: translateY(-2px);
        }

        .summary-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

    </style>

</head>

<body>

<?php include 'includes/navbar.php'; ?>


<div class="booking-container">

    <h1 style="margin-bottom: 30px;">
        Complete Your Booking
    </h1>


    <?php if (isset($error)): ?>

        <div
            style="
                background: #fee;
                color: #c33;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
            "
        >
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <div class="booking-card">

        <h2>Booking Summary</h2>


        <div class="summary-box">

            <h3>

                <?php

                if ($type === 'flight') {

                    echo htmlspecialchars(
                        $item['airline'] . ' ' .
                        $item['flight_number']
                    );

                } elseif ($type === 'hotel') {

                    echo htmlspecialchars($item['name']);

                } elseif ($type === 'train') {

                    echo htmlspecialchars($item['train_name']);

                } elseif ($type === 'bus') {

                    echo htmlspecialchars($item['bus_name']);

                } elseif ($type === 'cruise') {

                    echo htmlspecialchars(
                        $item['cruise_line'] .
                        ' - ' .
                        $item['ship_name']
                    );

                } else {

                    echo htmlspecialchars($item['package_name']);
                }

                ?>

            </h3>


            <p
                style="
                    color: #7f8c8d;
                    margin-top: 10px;
                "
            >

                <?php

                if ($type === 'flight') {

                    echo htmlspecialchars(
                        $item['departure_city'] .
                        ' → ' .
                        $item['arrival_city']
                    );

                } elseif ($type === 'hotel') {

                    echo htmlspecialchars($item['city']);

                } elseif ($type === 'train') {

                    echo htmlspecialchars(
                        $item['departure_station'] .
                        ' → ' .
                        $item['arrival_station']
                    );

                } elseif ($type === 'bus') {

                    echo htmlspecialchars(
                        $item['departure_city'] .
                        ' → ' .
                        $item['arrival_city']
                    );

                } elseif ($type === 'cruise') {

                    echo htmlspecialchars($item['departure_port']);

                } else {

                    echo htmlspecialchars($item['destination']);
                }

                ?>

            </p>


            <?php if (
                in_array(
                    $type,
                    ['flight', 'train', 'bus', 'cruise'],
                    true
                )
            ): ?>

                <p>

                    <strong>Departure Date:</strong>

                    <?php

                    if (!empty($item['departure_date'])) {

                        echo date(
                            'd M Y',
                            strtotime($item['departure_date'])
                        );

                    } else {

                        echo 'Not specified';
                    }

                    ?>

                </p>

            <?php endif; ?>


            <p
                style="
                    font-size: 1.5rem;
                    font-weight: 700;
                    color: #667eea;
                    margin-top: 15px;
                "
            >

                ₹<?php

                echo number_format(
                    ($type === 'hotel')
                        ? (float)$item['price_per_night']
                        : (float)$item['price'],
                    0
                );

                ?>


                <?php if ($type === 'hotel'): ?>

                    <small
                        style="
                            font-size: 0.9rem;
                            color: #7f8c8d;
                        "
                    >
                        per night
                    </small>

                <?php endif; ?>

            </p>

        </div>


        <form method="POST" action="">


            <?php if ($type === 'hotel'): ?>


                <div class="form-group">

                    <label>Check-in Date</label>

                    <input
                        type="date"
                        name="check_in"
                        class="form-control"
                        required
                        min="<?php echo date('Y-m-d'); ?>"
                    >

                </div>


                <div class="form-group">

                    <label>Check-out Date</label>

                    <input
                        type="date"
                        name="check_out"
                        class="form-control"
                        required
                        min="<?php
                            echo date(
                                'Y-m-d',
                                strtotime('+1 day')
                            );
                        ?>"
                    >

                </div>


            <?php elseif (
                !in_array(
                    $type,
                    ['flight', 'train', 'bus', 'cruise'],
                    true
                )
            ): ?>


                <div class="form-group">

                    <label>Travel Date</label>

                    <input
                        type="date"
                        name="travel_date"
                        id="travel_date"
                        class="form-control"
                        required
                        min="<?php echo date('Y-m-d'); ?>"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST['travel_date'] ?? ''
                            );
                        ?>"
                    >

                </div>


                <p>

                    <strong>Return Date:</strong>

                    <span id="return_date">
                        --
                    </span>

                </p>


            <?php endif; ?>


            <?php if ($type === 'cruise'): ?>


                <p style="margin-top: 8px;">

                    <strong>Departure Date:</strong>

                    <?php

                    echo date(
                        'd M Y',
                        strtotime($item['departure_date'])
                    );

                    ?>

                </p>


                <p>

                    <strong>Return Date:</strong>

                    <?php

                    if ($return_date) {

                        echo date(
                            'd M Y',
                            strtotime($return_date)
                        );

                    } else {

                        echo 'Not specified';
                    }

                    ?>

                </p>


                <p>

                    <strong>Duration:</strong>

                    <?php
                    echo (int)$item['duration_nights'];
                    ?>

                    Nights

                </p>


            <?php endif; ?>


            <?php if ($type === 'holiday'): ?>


                <?php if ($travel_date): ?>

                    <p>

                        <strong>Travel Date:</strong>

                        <?php

                        echo date(
                            'd M Y',
                            strtotime($travel_date)
                        );

                        ?>

                    </p>


                    <?php if ($return_date): ?>

                        <p>

                            <strong>Return Date:</strong>

                            <?php

                            echo date(
                                'd M Y',
                                strtotime($return_date)
                            );

                            ?>

                        </p>

                    <?php endif; ?>

                <?php endif; ?>


            <?php endif; ?>


            <div class="form-group">

                <label>Quantity</label>

                <input
                    type="number"
                    name="quantity"
                    class="form-control"
                    value="1"
                    min="1"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn-book"
            >

                <i class="fas fa-lock"></i>

                Proceed to Payment

            </button>


        </form>

    </div>

</div>


<?php include 'includes/footer.php'; ?>


<script>

const travelInput =
    document.getElementById('travel_date');

const returnSpan =
    document.getElementById('return_date');


if (travelInput && returnSpan) {

    const durationDays =
        <?php echo (int)($item['duration_days'] ?? 0); ?>;


    function updateReturnDate() {

        if (travelInput.value) {

            const d =
                new Date(
                    travelInput.value + 'T00:00:00'
                );

            d.setDate(
                d.getDate() + durationDays
            );


            returnSpan.textContent =
                d.toLocaleDateString(
                    'en-GB',
                    {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    }
                );

        } else {

            returnSpan.textContent = '--';
        }
    }


    travelInput.addEventListener(
        'change',
        updateReturnDate
    );


    updateReturnDate();
}

</script>


</body>
</html>
