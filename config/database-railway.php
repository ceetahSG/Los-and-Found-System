<?php
// 1. Use the Internal Hostname (Private Networking)
// This works if your PHP and MySQL are in the same Railway project
$host = 'mysql.railway.internal'; 
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$name = getenv('MYSQLDATABASE');
$port = 3306; // Always 3306 for internal connections

// 2. Determine environment and connect
if (getenv('RAILWAY_ENVIRONMENT_NAME')) {
    // ON RAILWAY
    $conn = new mysqli($host, $user, $pass, $name, $port);
} else {
    // ON LOCAL (XAMPP)
    $conn = new mysqli("127.0.0.1", "root", "", "lost_and_found");
}

// 3. Check connection
if ($conn->connect_error) {
    // Check if we are using the right fallback if internal fails
    $host_backup = getenv('MYSQLHOST'); 
    $conn = new mysqli($host_backup, $user, $pass, $name, getenv('MYSQLPORT'));

    if ($conn->connect_error) {
        die("Database Connection Error: " . $conn->connect_error);
    }
}

$conn->set_charset("utf8mb4");

// 4. Absolute path for uploads
define('UPLOAD_PATH', '/app/public/uploads/');
?>