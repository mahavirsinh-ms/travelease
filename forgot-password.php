<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) == 1) {

        $otp = rand(100000, 999999);
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_otp'] = $otp;

        echo "<script>
            alert('Your OTP is: $otp');
            window.location='verify-otp.php';
        </script>";
    } else {
        echo "<script>alert('Email not found');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
<style>
body{font-family:Poppins;display:flex;justify-content:center;align-items:center;height:100vh;background:#eef2f3}
form{background:#fff;padding:30px;border-radius:10px;width:350px}
input,button{width:100%;padding:12px;margin-top:15px}
button{background:#2980b9;color:#fff;border:none}
</style>
</head>
<body>

<form method="POST">
<h2>Forgot Password</h2>
<input type="email" name="email" placeholder="Enter registered email" required>
<button type="submit">Generate OTP</button>
</form>

</body>
</html>
