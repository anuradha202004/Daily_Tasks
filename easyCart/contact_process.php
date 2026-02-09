<?php
// contact_process.php

require_once 'app/bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get and sanitize inputs
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validation
$errors = [];
if (empty($name)) $errors[] = 'Name is required';
if (empty($email)) $errors[] = 'Email is required';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';
if (empty($message)) $errors[] = 'Message is required';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Save to database
try {
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message, created_at) VALUES (:name, :email, :message, NOW())");
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':message' => $message
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Message sent successfully! We will get back to you soon.']);
    
} catch (PDOException $e) {
    error_log("Contact Form Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again later.']);
}
?>
