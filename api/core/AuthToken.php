<?php
require_once 'Database.php';

class AuthToken {
    public static function create($userId) {
        $db = Database::getInstance()->getConnection();
        $token = bin2hex(random_bytes(255));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        // insert auth_tokens record
        $query = $db->prepare(<<<SQL
        INSERT INTO auth_tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)
SQL);
        $result = $query->execute(['user_id' => $userId, 'token' => $token, 'expires_at' => $expiresAt]);
        if (!$result) {
            return false;
        }
        return $token;
    }

    public static function createAndSetCookie($userId) {
        $token = self::create($userId);
        if (!$token) {
            return false;
        }
        setcookie('auth_token', $token, time() + 3600, '/', '', false, true);
        return true;
    }
}
