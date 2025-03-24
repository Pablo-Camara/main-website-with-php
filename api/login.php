<?php
header("Content-Type: application/json");
require_once './config/debug.php';
require_once './core/Database.php';
require_once './core/User.php';
require_once './core/AuthToken.php';

try {
    $email = $_GET['email'] ?? null; //TODO: change to POST
    // GET password too (POST)
    // if email is not provided return json with success false and message
    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        exit;
    }
    
    $user = User::getUserByEmail($email);

    if ($user) { //TODO: check password
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