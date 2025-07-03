<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Validate inputs
$orderId = (int)($_POST['order_id'] ?? 0);
$transactionCode = sanitize($_POST['transaction_code'] ?? '');
$paymentMethod = sanitize($_POST['payment_method'] ?? 'Bank Transfer');
$amount = (float)($_POST['amount'] ?? 0);

if (empty($orderId) || empty($transactionCode) || empty($paymentMethod) || $amount <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit;
}

// Verify the order belongs to the current user and get car details
$orderCheck = $conn->prepare("SELECT o.user_id, o.status, o.car_id FROM orders o WHERE o.order_id = ?");
$orderCheck->bind_param("i", $orderId);
$orderCheck->execute();
$orderResult = $orderCheck->get_result();

if ($orderResult->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

$orderData = $orderResult->fetch_assoc();
if ($orderData['user_id'] != $_SESSION['user_id']) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if payment already exists for this order
$paymentCheck = $conn->prepare("SELECT transaction_id FROM transaction WHERE order_id = ?");
$paymentCheck->bind_param("i", $orderId);
$paymentCheck->execute();
if ($paymentCheck->get_result()->num_rows > 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Payment already submitted for this order']);
    exit;
}

// Handle file upload
$uploadDir = '../assets/images/receipts/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$maxFileSize = 2 * 1024 * 1024; // 2MB

if (!isset($_FILES['receipt_image']) || $_FILES['receipt_image']['error'] !== UPLOAD_ERR_OK) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Please upload a valid receipt image']);
    exit;
}

$file = $_FILES['receipt_image'];
$fileType = mime_content_type($file['tmp_name']);
$fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);

// Validate file type
if (!in_array($fileType, $allowedTypes)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Only JPEG, PNG, and GIF images are allowed']);
    exit;
}

// Validate file size
if ($file['size'] > $maxFileSize) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'File size must be less than 2MB']);
    exit;
}

// Create upload directory if it doesn't exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate unique filename
$filename = 'receipt_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $fileExt;
$filePath = $uploadDir . $filename;

// Start transaction
$conn->begin_transaction();

try {
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('Failed to save receipt image');
    }

    // Insert transaction record
    $stmt = $conn->prepare("INSERT INTO transaction (user_id, car_id, order_id, transaction_code, payment_method, amount, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $imageUrl = '/assets/images/receipts/' . $filename;
    $stmt->bind_param("iiissds", $_SESSION['user_id'], $orderData['car_id'], $orderId, $transactionCode, $paymentMethod, $amount, $imageUrl);

    if (!$stmt->execute()) {
        throw new Exception('Failed to record transaction: ' . $conn->error);
    }

    // Update order status to Processing if Pending
    if ($orderData['status'] === 'Pending') {
        $updateOrder = $conn->prepare("UPDATE orders SET status = 'Processing' WHERE order_id = ?");
        $updateOrder->bind_param("i", $orderId);

        if (!$updateOrder->execute()) {
            throw new Exception('Failed to update order status');
        }

        // Decrease car quantity
        $decreaseQty = $conn->prepare("UPDATE cars SET quantity = quantity - 1 WHERE car_id = ? AND quantity > 0");
        $decreaseQty->bind_param("i", $orderData['car_id']);
        $decreaseQty->execute();

        if ($decreaseQty->affected_rows == 0) {
            throw new Exception("Failed to update car quantity - possibly out of stock");
        }
    }

    $conn->commit();

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Payment submitted successfully!']);
} catch (Exception $e) {
    $conn->rollback();
    if (isset($filePath) && file_exists($filePath)) {
        @unlink($filePath);
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
