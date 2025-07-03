<?php
require_once 'config.php';

// Function to sanitize input data
function sanitize($data) {
    global $conn;
    return htmlspecialchars(strip_tags($conn->real_escape_string(trim($data))));
}

// Function to redirect with message
function redirect($url, $message = null) {
    if ($message) {
        $_SESSION['message'] = $message;
    }
    header("Location: $url");
    exit();
}

// Function to display messages
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        return "<div class='alert alert-info'>$message</div>";
    }
    return "";
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}
// get car make function
// Function to get all brands with limit
function getAllBrands($limit = null) {
    global $conn;
    $brands = [];
    $sql = "SELECT id, brand_name FROM brand ORDER BY brand_name ASC";
    
    if ($limit) {
        $sql .= " LIMIT ?";
    }
    
    $stmt = $conn->prepare($sql);
    
    if ($limit) {
        $stmt->bind_param("i", $limit);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }
    return $brands;
}
// Function to get featured cars
function getFeaturedCars($limit = 3) {
    global $conn;
    $cars = [];
    // Order by highest price first, then by creation date for same prices
    $sql = "SELECT c.*, b.brand_name, m.model_name, et.engine_name 
            FROM cars c
            JOIN model m ON c.model_id = m.id
            JOIN brand b ON m.brand_id = b.id
            JOIN engine_type et ON c.engine_id = et.id
            ORDER BY c.price DESC, c.created_at DESC LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cars[] = $row;
        }

        // Mark the highest-priced car as featured
        if (!empty($cars)) {
            $cars[0]['featured'] = true;  // First car has highest price
            
            // Mark others as not featured
            for ($i = 1; $i < count($cars); $i++) {
                $cars[$i]['featured'] = false;
            }
        }
    }
    return $cars;
}
?>