<?php
session_start();
include "db.php";

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if users table exists
$table_check = "SHOW TABLES LIKE 'users'";
$result = mysqli_query($conn, $table_check);

if (mysqli_num_rows($result) == 0) {
    die("Error: 'users' table does not exist!");
}

// Check columns in users table
$column_check = "SHOW COLUMNS FROM users";
$result = mysqli_query($conn, $column_check);

echo "<h2>Users Table Structure:</h2>";
echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check if 'id' column exists and has data
$id_check = "SELECT id, full_name, email FROM users LIMIT 5";
$result = mysqli_query($conn, $id_check);

echo "<h2>Sample User Data (First 5 records):</h2>";
if (mysqli_num_rows($result) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Full Name</th><th>Email</th></tr>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['full_name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No users found in the database.";
}
?>