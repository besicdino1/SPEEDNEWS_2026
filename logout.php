<?php
// ============================================================
//  SpeedNews — Logout
//  Destroys the session completely and redirects to homepage.
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/speednews');
}

// 1. Clear all session variables
$_SESSION = [];

// 2. Destroy the session cookie so the browser forgets it
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// 3. Destroy the session on the server
session_destroy();

// 4. Redirect with a goodbye flash — start a fresh session just
//    long enough to store the flash, then let it expire naturally.
session_start();
$_SESSION['flash_success'] = 'You have been signed out. See you next time!';

header('Location: ' . BASE_URL . '/index.php');
exit;
