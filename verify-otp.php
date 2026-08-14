<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['otp'] == $_SESSION['reset_otp']) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset-password.php");
        exit;
    } else {
        echo "<script>alert('Invalid OTP');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Verify OTP</title>
<style>
body{font-family:Poppins;display:flex;justify-content:center;align-items:center;height:100vh;background:#eef2f3}
form{background:#fff;padding:30px;border-radius:10px;width:350px}
input,button{padding:12px;margin-top:15px}
button{background:#27ae60;color:#fff;border:none}
</style>
</head>
<body>

<form method="POST">
<h2>Verify OTP</h2>
<input type="text" name="otp" placeholder="Enter OTP" required>
<button type="submit">Verify</button>
</form>

</body>
</html>
