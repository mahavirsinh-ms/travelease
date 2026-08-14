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

// Allowed booking types
$allowed_types = [
    'flight' => 'flights',
    'hotel'  => 'hotels',
    'train'  => 'trains',
    'bus'    => 'buses',
    'cruise' => 'cruises',
    'holiday'=> 'holiday_packages'
];

if (!isset($allowed_types[$type])) {
    header("Location: index.php");
    exit();
}

$table_name = $allowed_types[$type];
$item = null;

// Get item details
$result = mysqli_query(
    $conn,
    "SELECT * FROM {$table_name} WHERE id = {$item_id}"
);

if ($result) {
    $item = mysqli_fetch_assoc($result);
}

if (!$item) {
    header("Location: index.php");
    exit();
}


// ============================================================
// INITIAL DATES
// ============================================================

$travel_date = '';
$return_date = '';

if ($type === 'cruise') {

    $travel_date = $item['departure_date'];

    $return_date = date(
        'Y-m-d',
        strtotime(
            $item['departure_date'] .
            ' + ' .
            (int)$item['duration_nights'] .
            ' days'
        )
    );

} elseif ($type === 'holiday') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $travel_date = $_POST['travel_date'] ?? '';
    }

    $duration_days = (int)($item['duration_days'] ?? 1);

    if ($travel_date) {
        $return_date = date(
            'Y-m-d',
            strtotime($travel_date . " + {$duration_days} days")
        );
    }

} elseif (in_array($type, ['flight', 'train', 'bus'], true)) {

    $travel_date = $item['departure_date'];
}


// ============================================================
// HANDLE BOOKING SUBMISSION
// ============================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = (int)$_SESSION['user_id'];

    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($quantity < 1) {
        $quantity = 1;
    }


    // --------------------------------------------------------
    // Travel date
    // --------------------------------------------------------

    if (in_array($type, ['flight', 'train', 'bus', 'cruise'], true)) {

        $travel_date = $item['departure_date'];

    } else {

        $travel_date = $_POST['travel_date'] ?? '';

        if (!$travel_date) {
            $error = "Please select a travel date.";
        }
    }


    // --------------------------------------------------------
    // Hotel dates
    // --------------------------------------------------------

    $check_in = $_POST['check_in'] ?? null;
    $check_out = $_POST['check_out'] ?? null;


    // --------------------------------------------------------
    // Calculate return date for holiday
    // --------------------------------------------------------

    if ($type === 'holiday' && $travel_date) {

        $duration_days = (int)($item['duration_days'] ?? 1);

        $return_date = date(
            'Y-m-d',
            strtotime($travel_date . " + {$duration_days} days")
        );

    }


    // --------------------------------------------------------
    // Calculate price
    // --------------------------------------------------------

    if (!isset($error)) {

        if ($type === 'hotel') {

            $unit_price = (float)$item['price_per_night'];

        } else {

            $unit_price = (float)$item['price'];

        }


        // Hotel pricing based on nights
        if ($type === 'hotel' && $check_in && $check_out) {

            $check_in_time = strtotime($check_in);
            $check_out_time = strtotime($check_out);

            $days = ($check_out_time - $check_in_time) / 86400;

            if ($days < 1) {
                $error = "Check-out date must be after check-in date.";
            } else {

                $total_amount =
                    $unit_price *
                    $days *
                    $quantity;
            }

        } else {

            $total_amount =
                $unit_price *
                $quantity;
        }
    }


    // --------------------------------------------------------
    // Create booking
    // --------------------------------------------------------

    if (!isset($error)) {

        // Generate unique-ish booking reference
        $booking_ref =
            strtoupper($type) .
            rand(1000, 9999) .
            time();


        // ----------------------------------------------------
        // Booking details
        // ----------------------------------------------------

        if ($type === 'flight') {

            $details =
                $item['airline'] .
                ' ' .
                $item['flight_number'] .
                ' - ' .
                $item['departure_city'] .
                ' to ' .
                $item['arrival_city'];

        } elseif ($type === 'hotel') {

            $details =
                $item['name'] .
                ' - ' .
                $item['city'];

        } elseif ($type === 'train') {

            $details =
                $item['train_name'] .
                ' (' .
                $item['train_number'] .
                ') - ' .
                $item['departure_station'] .
                ' to ' .
                $item['arrival_station'];

        } elseif ($type === 'bus') {

            $details =
                $item['bus_name'] .
                ' (' .
                $item['bus_number'] .
                ') - ' .
                $item['departure_city'] .
                ' to ' .
                $item['arrival_city'];

        } elseif ($type === 'cruise') {

            $details =
                $item['cruise_line'] .
                ' - ' .
                $item['ship_name'];

        } else {

            // Holiday
            $details =
                $item['package_name'] .
                ' - ' .
                $item['destination'];
        }


        // ----------------------------------------------------
        // PostgreSQL-compatible prepared INSERT
        // ----------------------------------------------------

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
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                'pending',
                'pending'
            )
        ";


        $stmt = mysqli_prepare($conn, $sql);


        if (!$stmt) {

            $error =
                "Booking failed: " .
                mysqli_error($conn);

        } else {

            /*
             * Types:
             *
             * i = user_id
             * s = booking_type
             * s = booking_reference
             * i = item_id
             * s = details
             * s = travel_date
             * s = return_date
             * s = check_in
             * s = check_out
             * i = quantity
             * d = total_amount
             */

            mysqli_stmt_bind_param(
                $stmt,
                "ississsss id",
                $user_id,
                $type,
                $booking_ref,
                $item_id,
                $details,
                $travel_date,
                $return_date,
                $check_in,
                $check_out,
                $quantity,
                $total_amount
            );


            // Remove accidental space from type string
            // and prepare again with the correct type definition.
            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {

                $error =
                    "Booking failed: " .
                    mysqli_error($conn);

            } else {

                mysqli_stmt_bind_param(
                    $stmt,
                    "ississsss id",
                    $user_id,
                    $type,
                    $booking_ref,
                    $item_id,
                    $details,
                    $travel_date,
                    $return_date,
                    $check_in,
                    $check_out,
                    $quantity,
                    $total_amount
                );

                /*
                 * The compatibility layer accepts the normal
                 * mysqli bind_param format.
                 */

                if (mysqli_stmt_execute($stmt)) {

                    // Get newly created booking ID
                    $booking_id =
                        mysqli_insert_id($conn);


                    // ------------------------------------------------
                    // Update availability
                    // ------------------------------------------------

                    if (
                        $type === 'flight' ||
                        $type === 'train' ||
                        $type === 'bus'
                    ) {

                        $update_sql = "
                            UPDATE {$table_name}
                            SET available_seats =
                                available_seats - {$quantity}
                            WHERE id = {$item_id}
                        ";

                    } elseif ($type === 'hotel') {

                        $update_sql = "
                            UPDATE {$table_name}
                            SET available_rooms =
                                available_rooms - {$quantity}
                            WHERE id = {$item_id}
                        ";

                    } elseif ($type === 'cruise') {

                        $update_sql = "
                            UPDATE {$table_name}
                            SET available_cabins =
                                available_cabins - {$quantity}
                            WHERE id = {$item_id}
                        ";

                    } else {

                        // Holiday
                        $update_sql = "
                            UPDATE {$table_name}
                            SET available_slots =
                                available_slots - {$quantity}
                            WHERE id = {$item_id}
                        ";
                    }


                    // Execute availability update
                    $availability_result =
                        mysqli_query(
                            $conn,
                            $update_sql
                        );


                    if (!$availability_result) {

                        $error =
                            "Booking was created, but availability could not be updated: " .
                            mysqli_error($conn);

                    } else {

                        mysqli_stmt_close($stmt);

                        // Go to payment
                        header(
                            "Location: payment.php?booking_id=" .
                            urlencode($booking_id)
                        );

                        exit();
                    }

                } else {

                    $error =
                        "Booking failed: " .
                        mysqli_stmt_error($stmt);
                }

                mysqli_stmt_close($stmt);
            }
        }
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


    <link
        rel="stylesheet"
        href="css/style.css"
    >

    <link
        rel="stylesheet"
        href="css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="css/fonts.css"
    >

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
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-book {
            background: linear-gradient(
                135deg,
                #667eea 0%,
                #764ba2 100%
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

        .error-box {
            background: #fee;
            color: #c33;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            word-break: break-word;
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

        <div class="error-box">

            <?php
            echo htmlspecialchars($error);
            ?>

        </div>

    <?php endif; ?>


    <div class="booking-card">

        <h2>Booking Summary</h2>


        <div class="summary-box">

            <h3>

                <?php

                if ($type === 'flight') {

                    echo htmlspecialchars(
                        $item['airline'] .
                        ' ' .
                        $item['flight_number']
                    );

                } elseif ($type === 'hotel') {

                    echo htmlspecialchars(
                        $item['name']
                    );

                } elseif ($type === 'train') {

                    echo htmlspecialchars(
                        $item['train_name']
                    );

                } elseif ($type === 'bus') {

                    echo htmlspecialchars(
                        $item['bus_name']
                    );

                } elseif ($type === 'cruise') {

                    echo htmlspecialchars(
                        $item['cruise_line'] .
                        ' - ' .
                        $item['ship_name']
                    );

                } else {

                    echo htmlspecialchars(
                        $item['package_name']
                    );
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

                    echo htmlspecialchars(
                        $item['city']
                    );

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

                    echo htmlspecialchars(
                        $item['departure_port']
                    );

                } else {

                    echo htmlspecialchars(
                        $item['destination']
                    );
                }

                ?>

            </p>


            <?php
            if (
                in_array(
                    $type,
                    ['flight', 'train', 'bus', 'cruise'],
                    true
                )
            ):
            ?>

                <p>

                    <strong>
                        Departure Date:
                    </strong>

                    <?php
                    echo date(
                        'd M Y',
                        strtotime(
                            $item['departure_date']
                        )
                    );
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
                        ? $item['price_per_night']
                        : $item['price'],
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


        <form
            method="POST"
            action=""
        >


            <?php if ($type === 'hotel'): ?>

                <div class="form-group">

                    <label>
                        Check-in Date
                    </label>

                    <input
                        type="date"
                        name="check_in"
                        class="form-control"
                        required
                        min="<?php echo date('Y-m-d'); ?>"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST['check_in'] ?? ''
                            );
                        ?>"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Check-out Date
                    </label>

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
                        value="<?php
                            echo htmlspecialchars(
                                $_POST['check_out'] ?? ''
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

                    <label>
                        Travel Date
                    </label>

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
                    <strong>
                        Return Date:
                    </strong>

                    <span id="return_date">
                        --
                    </span>
                </p>

            <?php endif; ?>


            <?php if ($type === 'cruise'): ?>

                <p style="margin-top: 8px;">

                    <strong>
                        Departure Date:
                    </strong>

                    <?php
                    echo date(
                        'd M Y',
                        strtotime(
                            $item['departure_date']
                        )
                    );
                    ?>

                </p>


                <p>

                    <strong>
                        Return Date:
                    </strong>

                    <?php
                    echo date(
                        'd M Y',
                        strtotime($return_date)
                    );
                    ?>

                </p>


                <p>

                    <strong>
                        Duration:
                    </strong>

                    <?php
                    echo (int)$item['duration_nights'];
                    ?>
                    Nights

                </p>

            <?php endif; ?>


            <?php if ($type === 'holiday'): ?>

                <?php if ($travel_date): ?>

                    <p>

                        <strong>
                            Travel Date:
                        </strong>

                        <?php
                        echo date(
                            'd M Y',
                            strtotime($travel_date)
                        );
                        ?>

                    </p>


                    <p>

                        <strong>
                            Return Date:
                        </strong>

                        <?php
                        echo date(
                            'd M Y',
                            strtotime($return_date)
                        );
                        ?>

                    </p>

                <?php endif; ?>

            <?php endif; ?>


            <div class="form-group">

                <label>
                    Quantity
                </label>

                <input
                    type="number"
                    name="quantity"
                    class="form-control"
                    value="<?php
                        echo htmlspecialchars(
                            $_POST['quantity'] ?? '1'
                        );
                    ?>"
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
                new Date(travelInput.value);

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
