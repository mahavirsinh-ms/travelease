<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $insert = "INSERT INTO users (full_name, email, phone, password) 
               VALUES ('$fullName', '$email', '$phone', '$password')";

    if (mysqli_query($conn, $insert)) {
        echo "<script>alert('Account created successfully!'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Error: Email already exists');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelEase | Sign Up</title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/fonts.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet"> -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('./images/bgimg3.jpeg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            display: flex;
            max-width: 1100px;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
            min-height: 650px;
        }

        .welcome-section {
            flex: 1;
            background: linear-gradient(135deg, #1e5799 0%, #207cca 51%, #2989d8 100%);
            color: white;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('./images/bgimg4.jpeg');
            background-size: cover;
            background-position: center;
            opacity: 0.2;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .logo span {
            color: #ffd166;
        }

        .welcome-title {
            font-size: 28px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .welcome-text {
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .features {
            list-style: none;
            margin-top: 30px;
            position: relative;
            z-index: 1;
        }

        .features li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .features i {
            margin-right: 12px;
            color: #ffd166;
            font-size: 18px;
        }

        .signup-section {
            flex: 1;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
        }

        .signup-title {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 10px;
            text-align: center;
        }

        .signup-subtitle {
            color: #7f8c8d;
            margin-bottom: 30px;
            text-align: center;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
            font-size: 14px;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            font-size: 18px;
        }

        .input-with-icon input {
            width: 100%;
            padding: 14px 14px 14px 50px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .input-with-icon input:focus {
            border-color: #2980b9;
            box-shadow: 0 0 0 2px rgba(41, 128, 185, 0.2);
            outline: none;
        }

        .password-strength {
            margin-top: 8px;
            height: 5px;
            border-radius: 5px;
            background-color: #eee;
            overflow: hidden;
            position: relative;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            border-radius: 5px;
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        .strength-text {
            font-size: 12px;
            margin-top: 5px;
            color: #7f8c8d;
        }

        .terms {
            display: flex;
            align-items: flex-start;
            margin: 20px 0 25px;
            font-size: 14px;
        }

        .terms input {
            margin-right: 10px;
            margin-top: 4px;
        }

        .terms a {
            color: #2980b9;
            text-decoration: none;
            transition: color 0.2s;
        }

        .terms a:hover {
            color: #1a5276;
            text-decoration: underline;
        }

        .signup-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(to right, #27ae60, #219653);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .signup-button:hover {
            background: linear-gradient(to right, #219653, #1e874b);
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(0, 0, 0, 0.1);
        }

        .signup-button:disabled {
            background: #95a5a6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

      

        .social-signup {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .social-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            text-decoration: none;
            transition: transform 0.3s;
        }

        .social-btn:hover {
            transform: translateY(-3px);
        }

       

        .login-link {
            text-align: center;
            color: #7f8c8d;
            font-size: 15px;
        }

        .login-link a {
            color: #2980b9;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .login-link a:hover {
            color: #1a5276;
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .container {
                flex-direction: column;
                max-width: 500px;
            }
            
            .welcome-section {
                padding: 30px;
            }
            
            .signup-section {
                padding: 40px 30px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .container {
                border-radius: 15px;
            }
            
            .welcome-section, .signup-section {
                padding: 30px 20px;
            }
            
            .social-signup {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="logo">Travel<span>Ease</span></div>
            <h1 class="welcome-title">Join Our Travel Community!</h1>
            <p class="welcome-text">Create your TravelEase account and unlock a world of seamless travel experiences. From booking flights to discovering hidden gems, we're here to make your journey unforgettable.</p>
            
            <ul class="features">
                <li><i class="fas fa-check-circle"></i> Personalized travel recommendations</li>
                <li><i class="fas fa-check-circle"></i> Exclusive member-only deals</li>
                <li><i class="fas fa-check-circle"></i> Easy booking management</li>
                <li><i class="fas fa-check-circle"></i> 24/7 customer support</li>
                <li><i class="fas fa-check-circle"></i> Free travel guides and tips</li>
            </ul>
        </div>
        
        <!-- Sign Up Form Section -->
        <div class="signup-section">
            <h2 class="signup-title">Create Your Account</h2>
            <p class="signup-subtitle">Fill in your details to get started</p>
            
            <form id="signupForm" method="POST" action="">
                <div class="form-group">
                    <label>Full Name</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" name="fullName" id="fullName" required placeholder="Enter your full name">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" id="email" required placeholder="Enter your email address">
                    </div>
                    <div id="emailError" class="error-message" style="color: #e74c3c; font-size: 12px; margin-top: 5px; display: none;"></div>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <div class="input-with-icon">
                        <i class="fas fa-phone"></i>
                        <input type="text" name="phone" id="phone" required placeholder="Enter your phone number">
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" required placeholder="Create a strong password">
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                    </div>
                    <div class="strength-text" id="strengthText">Password strength: Very weak</div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="confirmPassword" id="confirmPassword" required placeholder="Re-enter your password">
                    </div>
                    <div id="passwordMatch" class="error-message" style="color: #e74c3c; font-size: 12px; margin-top: 5px; display: none;">Passwords do not match</div>
                </div>

                <div class="terms">
                    <input type="checkbox" id="terms" required>
                    <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                </div>

                <button type="submit" class="signup-button" id="submitButton">Create Account</button>
            </form>
            
           
            
            <div class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
    
    <script>
        // Password strength checker
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('passwordStrengthBar');
        const strengthText = document.getElementById('strengthText');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const passwordMatch = document.getElementById('passwordMatch');
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        const submitButton = document.getElementById('submitButton');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Check password length
            if (password.length >= 8) strength += 1;
            if (password.length >= 12) strength += 1;
            
            // Check for mixed case
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;
            
            // Check for numbers
            if (/\d/.test(password)) strength += 1;
            
            // Check for special characters
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            // Update strength bar and text
            let width = 0;
            let color = '';
            let text = '';
            
            switch(strength) {
                case 0:
                case 1:
                    width = 20;
                    color = '#e74c3c';
                    text = 'Very weak';
                    break;
                case 2:
                    width = 40;
                    color = '#e67e22';
                    text = 'Weak';
                    break;
                case 3:
                    width = 60;
                    color = '#f1c40f';
                    text = 'Fair';
                    break;
                case 4:
                    width = 80;
                    color = '#2ecc71';
                    text = 'Good';
                    break;
                case 5:
                    width = 100;
                    color = '#27ae60';
                    text = 'Strong';
                    break;
            }
            
            strengthBar.style.width = width + '%';
            strengthBar.style.backgroundColor = color;
            strengthText.textContent = 'Password strength: ' + text;
            strengthText.style.color = color;
        });
        
        // Password match validation
        function validatePasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (confirmPassword === '') {
                passwordMatch.style.display = 'none';
                return true;
            }
            
            if (password === confirmPassword) {
                passwordMatch.style.display = 'none';
                confirmPasswordInput.style.borderColor = '#2ecc71';
                return true;
            } else {
                passwordMatch.style.display = 'block';
                confirmPasswordInput.style.borderColor = '#e74c3c';
                return false;
            }
        }
        
        confirmPasswordInput.addEventListener('input', validatePasswordMatch);
        passwordInput.addEventListener('input', function() {
            if (confirmPasswordInput.value !== '') {
                validatePasswordMatch();
            }
        });
        
        // Email validation
        function validateEmail() {
            const email = emailInput.value;
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email === '') {
                emailError.style.display = 'none';
                emailInput.style.borderColor = '#ddd';
                return true;
            }
            
            if (!emailPattern.test(email)) {
                emailError.textContent = 'Please enter a valid email address';
                emailError.style.display = 'block';
                emailInput.style.borderColor = '#e74c3c';
                return false;
            } else {
                emailError.style.display = 'none';
                emailInput.style.borderColor = '#ddd';
                return true;
            }
        }
        
        emailInput.addEventListener('blur', validateEmail);
        emailInput.addEventListener('input', function() {
            if (emailError.textContent === 'Email already exists') {
                validateEmail();
            }
        });
        
        // Form validation before submission
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            // Validate all fields
            const isEmailValid = validateEmail();
            const isPasswordMatch = validatePasswordMatch();
            const isTermsChecked = document.getElementById('terms').checked;
            
            // Check if password is at least 6 characters
            const isPasswordLengthValid = passwordInput.value.length >= 6;
            
            if (!isPasswordLengthValid) {
                e.preventDefault();
                alert('Password must be at least 6 characters long.');
                return;
            }
            
            if (!isPasswordMatch) {
                e.preventDefault();
                alert('Passwords do not match.');
                return;
            }
            
            if (!isTermsChecked) {
                e.preventDefault();
                alert('Please agree to the Terms of Service and Privacy Policy.');
                return;
            }
            
            if (!isEmailValid) {
                e.preventDefault();
                return;
            }
            
            // If all validations pass, the form will submit normally
        });
        
        // Add focus effects
        const inputs = document.querySelectorAll('.input-with-icon input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('i').style.color = '#2980b9';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.querySelector('i').style.color = '#7f8c8d';
            });
        });
    </script>
</body>
</html>