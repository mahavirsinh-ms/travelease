<?php
session_start();
// Destroy all session data
session_unset();
session_destroy();
// Redirect based on referrer or default to index
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'admin') !== false) {
    header("Location: admin/login.php");
} else {
    header("Location: index.php");
}
exit();
?>