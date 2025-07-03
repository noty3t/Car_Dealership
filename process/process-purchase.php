<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isLoggedIn() || isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$carId = (int)$_POST['car_id'];
$deliveryLocation = sanitize($_POST['delivery_location']);
$deliveryLat = isset($_POST['delivery_lat']) ? (float)$_POST['delivery_lat'] : null;
$deliveryLng = isset($_POST['delivery_lng']) ? (float)$_POST['delivery_lng'] : null;

// Check if car exists
$sql = "SELECT * FROM Cars WHERE car_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $carId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Car not found']);
    exit;
}

// Create order
$sql = "INSERT INTO Orders (user_id, car_id, delivery_location, delivery_lat, delivery_lng) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisdd", $_SESSION['user_id'], $carId, $deliveryLocation, $deliveryLat, $deliveryLng);

header('Content-Type: application/json');
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'order_id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error placing order']);
}
?>