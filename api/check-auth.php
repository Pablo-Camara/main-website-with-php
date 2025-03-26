<?php
header('Content-Type: application/json');
require_once './config/debug.php';
require_once './core/Database.php';
require_once './core/User.php';
require_once './core/AuthToken.php';

// validate auth token from auth_token cookie
$authToken = $_COOKIE['auth_token'] ?? null;
if (!$authToken) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$userId = AuthToken::validateToken($authToken);
if (!$userId) {
    AuthToken::unsetAuthTokenCookie();
    echo json_encode(['success' => false, 'message' => 'Invalid auth token, login again']);
    exit;
}

// return user data and success true
$userData = User::getUserById($userId);
echo json_encode(['success' => true, 'user' => [
    'first_name' => $userData['first_name'],
    'last_name' => $userData['last_name'],
    'email' => $userData['email'],
    'newsletter_consent' => $userData['newsletter_consent'],
    'email_confirmed' => $userData['email_confirmed']
]]);