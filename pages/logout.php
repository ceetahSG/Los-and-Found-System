<?php
require_once __DIR__ . '/../includes/functions.php';

// Destroy session
session_destroy();

// Redirect to home
header('Location: ' . BASE_URL . 'index.php');
exit;
?>