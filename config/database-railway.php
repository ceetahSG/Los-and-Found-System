<?php
// Bootstrap app-wide constants if not defined elsewhere.
if (!function_exists('loadLocalEnvFile')) {
    function loadLocalEnvFile($filePath) {
        if (!is_readable($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = array_map('trim', explode('=', $line, 2));
            if ($name === '' || getenv($name) !== false) {
                continue;
            }

            $value = trim($value, "\"'");
            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

if (!getenv('DB_HOST') && !getenv('DATABASE_URL')) {
    loadLocalEnvFile(dirname(__DIR__) . '/.env');
}

if (!defined('BASE_URL')) {
    $baseUrl = getenv('BASE_URL');
    if (!$baseUrl && isset($_SERVER['SCRIPT_NAME'])) {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
        $projectBase = '';

        if (str_contains($scriptName, '/public/')) {
            $projectBase = substr($scriptName, 0, strpos($scriptName, '/public/'));
        } else {
            $projectBase = rtrim(dirname(dirname($scriptName)), '/');
        }

        $baseUrl = $projectBase !== '' ? $projectBase . '/' : '/';
    }

    if (!$baseUrl) {
        $baseUrl = '/';
    }

    define('BASE_URL', rtrim($baseUrl, '/') . '/');
}

if (!defined('MAX_FILE_SIZE')) {
    define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
}

if (!defined('UPLOAD_PATH')) {
    $defaultUploadPath = dirname(__DIR__) . '/public/uploads/';
    define('UPLOAD_PATH', getenv('UPLOAD_PATH') ?: $defaultUploadPath);
}

mysqli_report(MYSQLI_REPORT_OFF);

/**
 * Parse a MySQL connection URL into connection parts.
 */
function parseMySqlUrl($url) {
    if (empty($url) || !is_string($url)) {
        return null;
    }

    $parts = parse_url($url);
    if ($parts === false) {
        return null;
    }

    $scheme = strtolower($parts['scheme'] ?? '');
    if ($scheme !== 'mysql') {
        return null;
    }

    $path = $parts['path'] ?? '';
    $dbName = ltrim($path, '/');

    if (empty($parts['host']) || empty($parts['user']) || empty($dbName)) {
        return null;
    }

    return [
        'host' => $parts['host'],
        'port' => $parts['port'] ?? 3306,
        'user' => $parts['user'],
        'pass' => $parts['pass'] ?? '',
        'name' => $dbName,
    ];
}

/**
 * Attempt a MySQL connection and return mysqli instance or null.
 */
function tryMySqlConnection($host, $user, $pass, $name, $port) {
    try {
        $connection = new mysqli($host, $user, $pass, $name, (int)$port);
        if ($connection->connect_error) {
            return null;
        }
        return $connection;
    } catch (Throwable $e) {
        return null;
    }
}

$envHost = getenv('MYSQLHOST') ?: getenv('DB_HOST');
$envUser = getenv('MYSQLUSER') ?: getenv('DB_USER');
$envPass = getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD');
$envName = getenv('MYSQLDATABASE') ?: getenv('DB_NAME');
$envPort = getenv('MYSQLPORT') ?: getenv('DB_PORT');

$dbUrl = getenv('DATABASE_URL')
    ?: getenv('MYSQL_URL')
    ?: getenv('MYSQL_URI')
    ?: getenv('MYSQL_PRIVATE_URL')
    ?: getenv('MYSQL_PUBLIC_URL');

$parsedUrl = parseMySqlUrl($dbUrl);
if ($parsedUrl) {
    $envHost = $envHost ?: $parsedUrl['host'];
    $envUser = $envUser ?: $parsedUrl['user'];
    $envPass = $envPass ?: $parsedUrl['pass'];
    $envName = $envName ?: $parsedUrl['name'];
    $envPort = $envPort ?: $parsedUrl['port'];
}

$envPort = $envPort ?: 3306;

$isRailway = (bool)(getenv('RAILWAY_ENVIRONMENT') || getenv('RAILWAY_ENVIRONMENT_NAME') || getenv('RAILWAY_PROJECT_ID'));

if ($isRailway) {
    // Prefer Railway-injected variables, then try private networking hostname.
    $conn = null;
    if (!empty($envHost) && !empty($envUser) && !empty($envName)) {
        $conn = tryMySqlConnection($envHost, $envUser, $envPass, $envName, $envPort);
    }

    if (!$conn) {
        $conn = tryMySqlConnection('mysql.railway.internal', $envUser, $envPass, $envName, 3306);
    }

    if (!$conn) {
        $safeHost = $envHost ?: 'mysql.railway.internal';
        die('Database Connection Error: Unable to connect to MySQL host "' . $safeHost . '". Check Railway MySQL env vars and service networking.');
    }
} else {
    // Local development defaults.
    $localHost = $envHost ?: '127.0.0.1';
    $localUser = $envUser ?: 'root';
    $localPass = $envPass ?: '';
    $localName = $envName ?: 'lost_and_found';
    $localPort = $envPort ?: 3306;

    $conn = tryMySqlConnection($localHost, $localUser, $localPass, $localName, $localPort);

    if (!$conn) {
        die('Database Connection Error: Unable to connect using local database configuration.');
    }
}

$conn->set_charset('utf8mb4');
?>