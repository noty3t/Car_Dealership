<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate required fields
$required = ['name', 'email', 'phone', 'message', 'car_id'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
}

// Sanitize inputs
$name = trim($_POST['name']);
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$phone = trim($_POST['phone']);
$message = trim($_POST['message']);
$car_id = (int)$_POST['car_id'];
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : 'Car Inquiry';

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Insert into database
try {
    $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, subject, message, car_id, contact_date) 
                           VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssssi", $name, $email, $phone, $subject, $message, $car_id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}