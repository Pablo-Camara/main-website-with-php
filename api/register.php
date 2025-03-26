<?php
header("Content-Type: application/json");
require_once './config/debug.php';
require_once './core/Database.php';
require_once './core/User.php';

try {
    $firstName = $_POST['first_name'] ?? null;
    $lastName = $_POST['last_name'] ?? null;
    $email = $_POST['email'] ?? null;
    $emailConfirm = $_POST['email_confirm'] ?? null;
    $password = $_POST['password'] ?? null;
    $passwordConfirm = $_POST['password_confirm'] ?? null;
    $newsletterConsent = $_POST['newsletter_consent'] ?? 0;

    //validations
    $errors = [];

    if (User::getUserByEmail($email)) {
        $errors['email'] = 'Email is already registered';
    }

    if ($emailConfirm !== $email) {
        $errors['email_confirm'] = 'Emails do not match';
    }

    
    if (empty($firstName)) {
        $errors['first_name'] = 'First name is required';
    } elseif (strlen($firstName) > 50 || strpos($firstName,' ')!== false) { // TODO: Maybe Improve validation in the future
        $errors['first_name'] = 'Must be less than 50 characters and without spaces';
    }
    
    if (empty($lastName)) {
        $errors['last_name'] = 'Last name is required';
    } elseif (strlen($lastName) > 50 || strpos($lastName,' ')!== false) { // TODO: Maybe Improve validation in the future
        $errors['last_name'] = 'Must be less than 50 characters and without spaces';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email is required and must be a valid email address';
    }
    if (strlen($password) < 8) {
        $errors['password'] = 'Password is required and must be at least 8 characters long';
    }

    if ($passwordConfirm !== $password) {
        $errors['password_confirm'] = 'Passwords do not match';
    }

    if (!empty($errors)) {
        // we will auth the user even if not email confirmed
        // website will have restrictions for unconfirmed emails
        // confirmation email will be sent to the user through other platform since
        // we are using free host that does not support sending emails natively
        // TODO: find alternative to send emails through a free api or service
        $result = AuthToken::createAndSetCookie($user['id']);
        echo json_encode([
            'success' => $result,
            'errors' => $errors
        ]);
        exit;
    }

    $db = Database::getInstance()->getConnection();
    $registerSql = <<<SQL
        INSERT INTO users (first_name, last_name, email, password_hash, newsletter_consent)
        VALUES (:first_name, :last_name, :email, :password_hash, :newsletter_consent)
    SQL;

    $query = $db->prepare($registerSql);
    $registerResult = $query->execute([
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_BCRYPT),
        'newsletter_consent' => $newsletterConsent
    ]);

    if (!$registerResult) {
        echo json_encode(['success' => false, 'error' => 'Could not register user']);
        exit;
    }
    // TODO: create auth token and return it
    echo json_encode(['success' => true, 'message' => 'User registered successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Something went wrong could not register user']);
}