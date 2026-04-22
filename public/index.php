<?php
/**
 * Front router for environments where /public is the document root.
 */

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$normalizedPath = trim((string)$requestPath, '/');

if ($normalizedPath === '' || $normalizedPath === 'index.php') {
    require __DIR__ . '/../pages/index.php';
    exit;
}

if (preg_match('#^pages/([a-zA-Z0-9_-]+)\.php$#', $normalizedPath, $matches)) {
    $target = __DIR__ . '/../pages/' . $matches[1] . '.php';
    if (is_file($target)) {
        require $target;
        exit;
    }
}

if (preg_match('#^api/([a-zA-Z0-9_-]+)\.php$#', $normalizedPath, $matches)) {
    $target = __DIR__ . '/../api/' . $matches[1] . '.php';
    if (is_file($target)) {
        require $target;
        exit;
    }
}

http_response_code(404);
echo '404 Not Found';
?>