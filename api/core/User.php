<?php
require_once 'Database.php';

class User {
    public static function getUserByEmail($email) {
        $db = Database::getInstance()->getConnection();
        $query = $db->prepare("SELECT * FROM users WHERE email = :email");
        $query->execute(['email' => $email]);
        return $query->fetch();
    }
}
