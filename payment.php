<?php
ob_start();
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$booking_id = $_GET['booking_id'] ?? 0;

if (!$booking_id) {
    header("Location: bookings.php");
    exit();
}

// Get booking details
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    header("Location: bookings.php");
    exit();
}

$bookingType = $booking['booking_type'];






// Check if already paid
if ($booking['payment_status'] == 'paid') {
    header("Location: booking-success.php?booking_id=$booking_id");
    exit();
}

$error = '';
$success = '';
$payment_method = 'credit_card';

// Handle payment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = $_POST['payment_method'];
    $transaction_id = 'TXN' . time() . rand(1000, 9999);
    
    // Validate based on payment method
  // Validate based on payment method
if ($payment_method === 'credit_card' || $payment_method === 'debit_card') {

    $card_number  = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
    $card_holder  = trim($_POST['card_holder'] ?? '');
    $expiry_month = $_POST['expiry_month'] ?? '';
    $expiry_year  = $_POST['expiry_year'] ?? '';
    $cvv          = $_POST['cvv'] ?? '';

    if (strlen($card_number) != 16) {
        $error = "Card number must be 16 digits";
    } elseif (!ctype_digit($card_number)) {
        $error = "Card number must contain only digits";
    } elseif (empty($card_holder)) {
        $error = "Please enter card holder name";
    } elseif (empty($expiry_month) || empty($expiry_year)) {
        $error = "Please select expiry date";
    } elseif (!preg_match('/^[0-9]{3,4}$/', $cvv)) {
        $error = "Invalid CVV";
    }

} elseif ($payment_method === 'upi') {

    $upi_id = trim($_POST['upi_id'] ?? '');
    if (!preg_match('/^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/', $upi_id)) {
        $error = "Invalid UPI ID format";
    }

} 

    
    // Process payment if no errors
    if (empty($error)) {
        // Re-check payment status inside POST (important)
$checkStmt = $conn->prepare("SELECT payment_status FROM bookings WHERE id=?");
$checkStmt->bind_param("i", $booking_id);
$checkStmt->execute();
$check = $checkStmt->get_result()->fetch_assoc();

if ($check['payment_status'] === 'paid') {
    throw new Exception("This booking is already paid.");
}

        mysqli_begin_transaction($conn);
        
        try {
            $paymentDetails = [
     'method' => $payment_method,
    'masked_card' => in_array($payment_method, ['credit_card', 'debit_card'])
        ? substr($card_number, -4)
        : null,
    'upi_id' => ($payment_method === 'upi') ? ($_POST['upi_id'] ?? null) : null
];

            $stmt = $conn->prepare("
    INSERT INTO payments 
    (booking_id, user_id, amount, payment_method, transaction_id, payment_status, payment_details)
    VALUES (?, ?, ?, ?, ?, 'success', ?)
");

$details = json_encode($paymentDetails);

$stmt->bind_param(
    "iidsss",
    $booking_id,
    $_SESSION['user_id'],
    $booking['total_amount'],
    $payment_method,
    $transaction_id,
    $details
);

$stmt->execute();


            
            
                // Update booking status
// Update booking status
mysqli_query(
    $conn,
    "UPDATE bookings 
     SET payment_status='paid', status='confirmed', payment_date=NOW() 
     WHERE id='$booking_id'"
);

// Update room availability if applicable
if (!empty($booking['room_id'])) {
    mysqli_query(
        $conn,
        "UPDATE rooms 
         SET available_slots = available_slots - 1 
         WHERE id = '{$booking['room_id']}' AND available_slots > 0"
    );
}

mysqli_commit($conn);

header("Location: booking-success.php?booking_id=$booking_id&transaction_id=$transaction_id");
exit();

             
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Payment processing failed. Please try again. " . $e->getMessage();
        }
    }

}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - TravelEase</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/all.min.css">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"> -->
<style>
        .payment-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
        }
        .payment-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        .amount-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
        }
        .amount-box h2 {
            font-size: 2.5rem;
            margin-bottom: 5px;
        }
        .payment-section {
            display: none;
        }
        .payment-section.active {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }
        .payment-method {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-method:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        .payment-method.active {
            border-color: #667eea;
            background: #f0f3ff;
        }
        .payment-method input[type="radio"] {
            margin-right: 15px;
            transform: scale(1.2);
        }
        .payment-method i {
            margin-right: 10px;
            font-size: 1.2rem;
            color: #667eea;
            width: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .form-row {
            display: flex;
            gap: 20px;
        }
        .form-row .form-group {
            flex: 1;
        }
        .card-icons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .card-icon {
            width: 50px;
            height: 30px;
            background: #f0f0f0;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #666;
        }
        .card-icon.visa { color: #1a1f71; }
        .card-icon.mastercard { color: #eb001b; }
        .card-icon.amex { color: #2e77bc; }
        .btn-pay {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 40px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-pay:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
        }
        .success-message {
            background: #efe;
            color: #0a0;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #0a0;
        }
        .payment-summary {
            background: #f8f9ff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .payment-summary h3 {
            margin-bottom: 15px;
            color: #333;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .summary-item:last-child {
            border-bottom: none;
            font-weight: 600;
            font-size: 1.1rem;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card-input-wrapper {
            position: relative;
        }
        .card-type-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5rem;
        }
        .upi-banks {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }
        .upi-bank {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .upi-bank:hover {
            border-color: #667eea;
        }
        .upi-bank.selected {
            border-color: #667eea;
            background: #f0f3ff;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="payment-container">
        <div class="payment-card">
            <h1 style="margin-bottom: 20px; color: #333;">Complete Payment</h1>
            
            <?php if(!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($success)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <div class="payment-summary">
    <h3>Booking Summary</h3>

    <div class="summary-item">
        <span>Booking Reference:</span>
        <span><strong><?= htmlspecialchars($booking['booking_reference']); ?></strong></span>
    </div>

    <?php if ($bookingType === 'hotel'): ?>
    <div class="summary-item">
        <span>Check-in:</span>
        <span>
            <?= !empty($booking['check_in_date'])
                ? date('M d, Y', strtotime($booking['check_in_date']))
                : 'N/A'; ?>
        </span>
    </div>

    <div class="summary-item">
        <span>Check-out:</span>
        <span>
            <?= !empty($booking['check_out_date'])
                ? date('M d, Y', strtotime($booking['check_out_date']))
                : 'N/A'; ?>
        </span>
    </div>

    <div class="summary-item">
        <span>Guests:</span>
        <span><?= $booking['quantity'] ?? 'N/A'; ?> person(s)</span>
    </div>
<?php endif; ?>

<?php if ($bookingType === 'holiday' || $bookingType === 'cruise'): ?>
    <div class="summary-item">
        <span>Travel Date:</span>
        <span><?= !empty($booking['travel_date']) ? date('M d, Y', strtotime($booking['travel_date'])) : 'N/A'; ?></span>
    </div>
    <div class="summary-item">
        <span>Return Date:</span>
        <span><?= !empty($booking['return_date']) ? date('M d, Y', strtotime($booking['return_date'])) : 'N/A'; ?></span>
    </div>
<?php endif; ?>


    <div class="summary-item">
        <span>Total Amount:</span>
        <span style="color:#667eea;font-size:1.2rem;">
            ₹<?= number_format($booking['total_amount'], 2); ?>
        </span>
    </div>
</div>


            <form method="POST" action="" id="paymentForm">
                <h3 style="margin-bottom: 15px; color: #333;">Select Payment Method</h3>
                
                <div id="paymentMethods">
                    <label class="payment-method <?php echo $payment_method == 'credit_card' ? 'active' : ''; ?>">
                        <input type="radio" name="payment_method" value="credit_card" <?php echo $payment_method == 'credit_card' ? 'checked' : ''; ?>>
                        <i class="fas fa-credit-card"></i> 
                        <div>
                            <strong>Credit Card</strong>
                            <div style="font-size: 0.9rem; color: #666; margin-top: 5px;">Visa, MasterCard, Amex</div>
                        </div>
                    </label>
                    
                    <label class="payment-method <?php echo $payment_method == 'debit_card' ? 'active' : ''; ?>">
                        <input type="radio" name="payment_method" value="debit_card" <?php echo $payment_method == 'debit_card' ? 'checked' : ''; ?>>
                        <i class="fas fa-credit-card"></i>
                        <div>
                            <strong>Debit Card</strong>
                            <div style="font-size: 0.9rem; color: #666; margin-top: 5px;">Visa, MasterCard, RuPay</div>
                        </div>
                    </label>
                    
                    
                    
                    <label class="payment-method <?php echo $payment_method == 'upi' ? 'active' : ''; ?>">
                        <input type="radio" name="payment_method" value="upi" <?php echo $payment_method == 'upi' ? 'checked' : ''; ?>>
                        <i class="fas fa-mobile-alt"></i>
                        <div>
                            <strong>UPI</strong>
                            <div style="font-size: 0.9rem; color: #666; margin-top: 5px;">Google Pay, PhonePe, Paytm</div>
                        </div>
                    </label>
                    
                    
                </div>

                <!-- Credit/Debit Card Section -->
                <div id="cardSection" class="payment-section <?php echo in_array($payment_method, ['credit_card', 'debit_card']) ? 'active' : ''; ?>">
                    <div class="form-group">
                        <label for="card_number">Card Number</label>
                        <div class="card-input-wrapper">
                            <input type="text" id="card_number" name="card_number" 
                                   placeholder="1234 5678 9012 3456" 
                                   maxlength="19"
                                   value="<?php echo isset($_POST['card_number']) ? htmlspecialchars($_POST['card_number']) : ''; ?>">
                            <div id="cardTypeIcon" class="card-type-icon"></div>
                        </div>
                        <div class="card-icons">
                            <div class="card-icon visa"><i class="fab fa-cc-visa"></i></div>
                            <div class="card-icon mastercard"><i class="fab fa-cc-mastercard"></i></div>
                            <div class="card-icon amex"><i class="fab fa-cc-amex"></i></div>
                            <div class="card-icon"><i class="fab fa-cc-discover"></i></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="card_holder">Card Holder Name</label>
                        <input type="text" id="card_holder" name="card_holder" 
                               placeholder="John Doe"
                               value="<?php echo isset($_POST['card_holder']) ? htmlspecialchars($_POST['card_holder']) : ''; ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiry_month">Expiry Month</label>
                            <select id="expiry_month" name="expiry_month">
                                <option value="">MM</option>
                                <?php for($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>"
                                            <?php echo (isset($_POST['expiry_month']) && $_POST['expiry_month'] == str_pad($i, 2, '0', STR_PAD_LEFT)) ? 'selected' : ''; ?>>
                                        <?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="expiry_year">Expiry Year</label>
                            <select id="expiry_year" name="expiry_year">
                                <option value="">YYYY</option>
                                <?php 
                                $currentYear = date('Y');
                                for($i = $currentYear; $i <= $currentYear + 10; $i++): ?>
                                    <option value="<?php echo $i; ?>"
                                            <?php echo (isset($_POST['expiry_year']) && $_POST['expiry_year'] == $i) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="password" id="cvv" name="cvv" 
                                   placeholder="123" 
                                   maxlength="4"
                                   value="<?php echo isset($_POST['cvv']) ? htmlspecialchars($_POST['cvv']) : ''; ?>">
                        </div>
                    </div>
                </div>

                <!-- UPI Section -->
                <div id="upiSection" class="payment-section <?php echo $payment_method == 'upi' ? 'active' : ''; ?>">
                    <div class="form-group">
                        <label for="upi_id">UPI ID</label>
                        <input type="text" id="upi_id" name="upi_id" 
                               placeholder="username@bankname"
                               value="<?php echo isset($_POST['upi_id']) ? htmlspecialchars($_POST['upi_id']) : ''; ?>">
                        <small style="color: #666; display: block; margin-top: 5px;">Enter your UPI ID (e.g., username@okicici)</small>
                    </div>
                    <div class="upi-banks">
                        <div class="upi-bank" data-bank="googlepay">
                            <i class="fab fa-google-pay" style="font-size: 2rem; color: #5f6368;"></i>
                            <div style="margin-top: 5px; font-weight: 600;">Google Pay</div>
                        </div>
                        <div class="upi-bank" data-bank="phonepe">
                            <i class="fas fa-mobile-alt" style="font-size: 2rem; color: #5f2eea;"></i>
                            <div style="margin-top: 5px; font-weight: 600;">PhonePe</div>
                        </div>
                        <div class="upi-bank" data-bank="paytm">
                            <i class="fas fa-wallet" style="font-size: 2rem; color: #002e6e;"></i>
                            <div style="margin-top: 5px; font-weight: 600;">Paytm</div>
                        </div>
                    </div>
                </div>

               

                <div style="margin-top: 30px; padding: 20px; background: #f8f9ff; border-radius: 8px;">
                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <i class="fas fa-shield-alt" style="color: #28a745; font-size: 1.5rem; margin-right: 10px;"></i>
                        <div>
                            <strong style="color: #333;">Secure Payment</strong>
                            <div style="color: #666; font-size: 0.9rem;">Your payment information is encrypted and secure</div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-pay" id="submitBtn">
                    <i class="fas fa-lock"></i> 
                    <span id="payButtonText">Pay ₹<?php echo number_format($booking['total_amount'], 2); ?></span>
                    <span id="processingText" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Processing...
                    </span>
                </button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
            const cardNumberInput = document.getElementById('card_number');
            const cardTypeIcon = document.getElementById('cardTypeIcon');
            const paymentSections = document.querySelectorAll('.payment-section');
            const submitBtn = document.getElementById('submitBtn');
            const payButtonText = document.getElementById('payButtonText');
            const processingText = document.getElementById('processingText');
            const upiBanks = document.querySelectorAll('.upi-bank');
            
            // Format card number
            cardNumberInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                let formattedValue = '';
                
                for (let i = 0; i < value.length; i++) {
                    if (i > 0 && i % 4 === 0) {
                        formattedValue += ' ';
                    }
                    formattedValue += value[i];
                }
                
                e.target.value = formattedValue.substring(0, 19);
                
                // Detect card type
                detectCardType(value);
            });
            
            // Card type detection
            function detectCardType(number) {
                const cleanNumber = number.replace(/\D/g, '');
                cardTypeIcon.innerHTML = '';
                
                if (/^4/.test(cleanNumber)) {
                    cardTypeIcon.innerHTML = '<i class="fab fa-cc-visa" style="color: #1a1f71;"></i>';
                } else if (/^5[1-5]/.test(cleanNumber)) {
                    cardTypeIcon.innerHTML = '<i class="fab fa-cc-mastercard" style="color: #eb001b;"></i>';
                } else if (/^3[47]/.test(cleanNumber)) {
                    cardTypeIcon.innerHTML = '<i class="fab fa-cc-amex" style="color: #2e77bc;"></i>';
                } else if (/^6(?:011|5)/.test(cleanNumber)) {
                    cardTypeIcon.innerHTML = '<i class="fab fa-cc-discover" style="color: #f70;"></i>';
                }
            }
            
            // Payment method selection
            paymentMethods.forEach(method => {
                method.addEventListener('change', function() {
                    const value = this.value;
                    
                    // Update active class on labels
                    document.querySelectorAll('.payment-method').forEach(label => {
                        label.classList.remove('active');
                    });
                    this.closest('.payment-method').classList.add('active');
                    
                    // Show corresponding section
                    paymentSections.forEach(section => {
                        section.classList.remove('active');
                    });
                    
                    if (value === 'credit_card' || value === 'debit_card') {
    document.getElementById('cardSection').classList.add('active');
} else {
    document.getElementById(value + 'Section')?.classList.add('active');
}

                    
                    // Update button text
                    const amount = '<?php echo number_format($booking["total_amount"], 2); ?>';
                    payButtonText.textContent = `Pay ₹${amount} with ${getMethodName(value)}`;
                });
            });
            
            // UPI bank selection
            upiBanks.forEach(bank => {
                bank.addEventListener('click', function() {
                    const section = this.closest('.payment-section');
                    if (section.id === 'upiSection') {
                        document.querySelectorAll('#upiSection .upi-bank').forEach(b => {
                            b.classList.remove('selected');
                        });
                        this.classList.add('selected');
                        
                        const bankName = this.getAttribute('data-bank');
                        let upiSuggestion = '';
                        switch(bankName) {
                            case 'googlepay': upiSuggestion = 'username@okicici'; break;
                            case 'phonepe': upiSuggestion = 'username@ybl'; break;
                            case 'paytm': upiSuggestion = 'username@paytm'; break;
                        }
                        document.getElementById('upi_id').placeholder = upiSuggestion;
                    } 
                });
            });
            
            // Form validation
            document.getElementById('paymentForm').addEventListener('submit', function(e) {
                const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
                let isValid = true;
                
                if (paymentMethod === 'credit_card' || paymentMethod === 'debit_card') {
                    const cardNumber = document.getElementById('card_number').value.replace(/\s/g, '');
                    const cardHolder = document.getElementById('card_holder').value.trim();
                    const expiryMonth = document.getElementById('expiry_month').value;
                    const expiryYear = document.getElementById('expiry_year').value;
                    const cvv = document.getElementById('cvv').value;
                    
                    if (cardNumber.length !== 16) {
                        alert('Please enter a valid card number');
                        isValid = false;
                    } else if (!cardHolder) {
                        alert('Please enter card holder name');
                        isValid = false;
                    } else if (!expiryMonth || !expiryYear) {
                        alert('Please select expiry date');
                        isValid = false;
                    } else if (cvv.length < 3) {
                        alert('Please enter a valid CVV');
                        isValid = false;
                    }
                } else if (paymentMethod === 'upi') {
                    const upiId = document.getElementById('upi_id').value.trim();
                    if (!upiId.includes('@')) {
                        alert('Please enter a valid UPI ID');
                        isValid = false;
                    }
                }  
                
                if (isValid) {
                    // Show processing state
                    payButtonText.style.display = 'none';
                    processingText.style.display = 'inline';
                    submitBtn.disabled = true;
                    
                    // Form will submit normally
                } else {
                    e.preventDefault();
                }
            });
            
            // Auto-advance in expiry inputs
            document.getElementById('expiry_month').addEventListener('change', function() {
                if (this.value.length === 2) {
                    document.getElementById('expiry_year').focus();
                }
            });
            
            function getMethodName(method) {
            const names = {
        'credit_card': 'Credit Card',
        'debit_card': 'Debit Card',
        'upi': 'UPI'
                        };
    return names[method] || method;
                                            }

            
            // Initialize
            const initialMethod = document.querySelector('input[name="payment_method"]:checked').value;
            payButtonText.textContent = `Pay ₹<?php echo number_format($booking["total_amount"], 2); ?> with ${getMethodName(initialMethod)}`;
        });
    </script>
</body>
</html>