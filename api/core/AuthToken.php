<?php
require_once 'Database.php';

class AuthToken {
    public static function create($userId) {
        $db = Database::getInstance()->getConnection();
        $token = bin2hex(random_bytes(127)); // Generates a 254-character string
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

    public static function unsetAuthTokenCookie() {
        setcookie('auth_token', '', time() - 3600, '/', '', false, true);
    }

    public static function validateToken($token) {
        $db = Database::getInstance()->getConnection();
        $query = $db->prepare(<<<SQL
        SELECT user_id FROM auth_tokens
        WHERE token = :token AND expires_at > NOW()
SQL);
        $query->execute(['token' => $token]);
        $result = $query->fetch();
        return $result['user_id'] ?? null;
    }
}
