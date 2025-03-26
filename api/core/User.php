<?php
require_once 'Database.php';

class User {

    public static function getUserById($id) {
        $db = Database::getInstance()->getConnection();
        $query = $db->prepare("SELECT * FROM users WHERE id = :id");
        $query->execute(['id' => $id]);
        return $query->fetch();
    }
    
    public static function getUserByEmail($email) {
        $db = Database::getInstance()->getConnection();
        $query = $db->prepare("SELECT * FROM users WHERE email = :email");
        $query->execute(['email' => $email]);
        return $query->fetch();
    }
}
