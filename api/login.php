<?php
header("Content-Type: application/json");
require_once './config/debug.php';
require_once './core/Database.php';
require_once './core/User.php';
require_once './core/AuthToken.php';

try {
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    // if email or password is not provided return json with success false and message
    if (!$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit;
    }
    
    $user = User::getUserByEmail($email);
 
    if ($user && password_verify($password, $user['password_hash'])) {
        $token = AuthToken::create($user['id']);
        if (!$token) {
            echo json_encode(['success' => false, 'message' => 'Could not login']);
            exit;
        }
        echo json_encode([
            'success' => true, 
            'token' => $token
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Email or password is incorrect']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}