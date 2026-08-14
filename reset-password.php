<?php
session_start();
include "db.php";

if (!isset($_SESSION['otp_verified'])) {
    die("Unauthorized access");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_SESSION['reset_email'];

    mysqli_query($conn, "UPDATE users SET password='$password' WHERE email='$email'");

    session_destroy();

    echo "<script>
        alert('Password reset successful');
        window.location='login.php';
    </script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>
<style>
body{font-family:Poppins;display:flex;justify-content:center;align-items:center;height:100vh;background:#eef2f3}
form{background:#fff;padding:30px;border-radius:10px;width:350px}
input,button{padding:12px;margin-top:15px}
button{background:#2980b9;color:#fff;border:none}
</style>
</head>
<body>

<form method="POST">
<h2>Reset Password</h2>
<input type="password" name="password" placeholder="New Password" required>
<button type="submit">Update Password</button>
</form>

</body>
</html>
