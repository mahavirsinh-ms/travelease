<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            // SET ALL SESSION VARIABLES
            $_SESSION['user'] = $row['full_name'];
            $_SESSION['user_id'] = $row['id'];  // THIS IS MISSING
            $_SESSION['user_email'] = $row['email'];
            
            echo "<script>
                alert('Login successful! Welcome " . addslashes($row['full_name']) . "');
                window.location='index.php';
            </script>";
        } else {
            echo "<script>alert('Incorrect Password');</script>";
        }
    } else {
        echo "<script>alert('Email not found');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelEase | Login</title>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/fonts.css">
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
                        url('./images/bgimg.jpeg');
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
            background: url('./images/bgimg2.jpeg');
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

        .login-section {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-title {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 10px;
            text-align: center;
        }

        .login-subtitle {
            color: #7f8c8d;
            margin-bottom: 40px;
            text-align: center;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
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
            padding: 16px 16px 16px 50px;
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

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .remember {
            display: flex;
            align-items: center;
        }

        .remember input {
            margin-right: 8px;
        }

        .forgot-link {
            color: #2980b9;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #1a5276;
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(to right, #2980b9, #2c3e50);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 25px;
        }

        .login-button:hover {
            background: linear-gradient(to right, #1a5276, #1c2833);
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(0, 0, 0, 0.1);
        }

        

        

        

        .signup-link {
            text-align: center;
            color: #7f8c8d;
            font-size: 15px;
        }

        .signup-link a {
            color: #2980b9;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .signup-link a:hover {
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
            
            .login-section {
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
            
            .welcome-section, .login-section {
                padding: 30px 20px;
            }
            
            .social-login {
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
            <h1 class="welcome-title">Welcome Back!</h1>
            <p class="welcome-text">Sign in to continue your journey with TravelEase. Access your personalized travel dashboard, manage bookings, and discover new destinations.</p>
            
            <ul class="features">
                <li><i class="fas fa-check-circle"></i> Access to exclusive travel deals</li>
                <li><i class="fas fa-check-circle"></i> Manage your bookings and itineraries</li>
                <li><i class="fas fa-check-circle"></i> Get personalized travel recommendations</li>
                <li><i class="fas fa-check-circle"></i> 24/7 customer support for travelers</li>
            </ul>
        </div>
        
        <!-- Login Form Section -->
        <div class="login-section">
            <h2 class="login-title">Login to Your Account</h2>
            <p class="login-subtitle">Enter your credentials to access your account</p>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" required placeholder="Enter your email address">
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" required placeholder="Enter your password">
                    </div>
                </div>

                <div class="remember-forgot">
                    <div class="remember">
                        <input type="checkbox" id="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="login-button">Login</button>
            </form>
            
          
            
            <div class="signup-link">
                Don't have an account? <a href="signup.php">Create now</a>
            </div>
        </div>
    </div>
    
    <script>
        // Simple form validation enhancement
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = document.querySelector('input[name="email"]');
            const password = document.querySelector('input[name="password"]');
            
            // Reset previous error styles
            email.style.borderColor = '#ddd';
            password.style.borderColor = '#ddd';
            
            let isValid = true;
            
            // Email validation
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email.value)) {
                email.style.borderColor = '#e74c3c';
                isValid = false;
            }
            
            // Password length validation
            if (password.value.length < 6) {
                password.style.borderColor = '#e74c3c';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please check your email format and ensure password is at least 6 characters.');
            }
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