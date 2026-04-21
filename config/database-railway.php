<?php
// 1. Get Railway variables (Railway uses MYSQLHOST, not DB_HOST)
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$name = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT') ?: '3306'; // Default to 3306 if port isn't set

// 2. Critical: Check if we are on Railway or Local
if ($host) {
    // ON RAILWAY: This will now use TCP/IP because $host is an actual address (like xxx.proxy.rlwy.net)
    $conn = new mysqli($host, $user, $pass, $name, $port);
} else {
    // ON LOCAL (XAMPP): Fallback to local settings
    $conn = new mysqli("127.0.0.1", "root", "", "lost_and_found");
}

// 3. Check connection
if ($conn->connect_error) {
    die("Database Connection Error: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// 4. Absolute path for uploads (avoids folder confusion)
define('UPLOAD_PATH', '/app/public/uploads/');
?>