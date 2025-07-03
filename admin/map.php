<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

$pageTitle = "Delivery Map";

// Get all orders with delivery locations
$orders = [];
$sql = "SELECT o.order_id, o.delivery_location, o.delivery_lat, o.delivery_lng, o.status,
               b.brand_name as make, m.model_name as model, c.image_url,
               u.username, u.ph_no
        FROM Orders o
        JOIN Cars c ON o.car_id = c.car_id
        JOIN model m ON c.model_id = m.id
        JOIN brand b ON m.brand_id = b.id
        JOIN Users u ON o.user_id = u.user_id
        WHERE o.delivery_lat IS NOT NULL AND o.delivery_lng IS NOT NULL";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

// Get single order if specified
$focusOrder = null;
if (isset($_GET['order_id']) && !empty($_GET['order_id'])) {
    $orderId = (int)$_GET['order_id'];
    foreach ($orders as $order) {
        if ($order['order_id'] == $orderId) {
            $focusOrder = $order;
            break;
        }
    }
}

require_once '../includes/header.php';
?>
<head>

    <!-- Bootstrap CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css">
     
    <!-- Custom CSS -->
    <!-- <link rel="stylesheet" href="/assets/css/style.css"> -->
    <!-- Include SweetAlert CSS -->
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"> -->
    <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css">
    
</head>
<div class="container mt-4">
    <h2 class="mb-4">Delivery Map</h2>
    
    <div class="card mb-4">
        <div class="card-body">
            <div id="map" style="height: 500px;"></div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5>Orders with Delivery Locations</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Car</th>
                            <th>Delivery Location</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo $order['order_id']; ?></td>
                                <td><?php echo $order['username']; ?></td>
                                <td><?php echo $order['ph_no']; ?></td>
                                <td>
                                    <?php echo $order['make'] . ' ' . $order['model']; ?>
                                </td>
                                <td><?php echo $order['delivery_location']; ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        switch($order['status']) {
                                            case 'Pending': echo 'warning'; break;
                                            case 'Processing': echo 'info'; break;
                                            case 'Shipped': echo 'primary'; break;
                                            case 'Delivered': echo 'success'; break;
                                            case 'Cancelled': echo 'danger'; break;
                                        }
                                    ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="map.php?order_id=<?php echo $order['order_id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        Focus on Map
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<script>
// Initialize map
const map = L.map('map').setView([0, 0], 2);

// Add tile layer (OpenStreetMap)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Add markers for each order
const markers = [];
<?php foreach ($orders as $order): ?>
    const marker<?php echo $order['order_id']; ?> = L.marker([<?php echo $order['delivery_lat']; ?>, <?php echo $order['delivery_lng']; ?>])
        .addTo(map)
        .bindPopup(`
            <div style="max-width: 200px;">
                <h6>Order #<?php echo $order['order_id']; ?></h6>
                <p><strong>Customer:</strong> <?php echo $order['username']; ?></p>
                <p><strong>Phone:</strong> <?php echo $order['ph_no']; ?></p>
                <p><strong>Car:</strong> <?php echo $order['make'] . ' ' . $order['model']; ?></p>
                <p><strong>Status:</strong> <span class="badge bg-<?php 
                    switch($order['status']) {
                        case 'Pending': echo 'warning'; break;
                        case 'Processing': echo 'info'; break;
                        case 'Shipped': echo 'primary'; break;
                        case 'Delivered': echo 'success'; break;
                        case 'Cancelled': echo 'danger'; break;
                    }
                ?>"><?php echo $order['status']; ?></span></p>
                <a href="orders.php?view=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-primary w-100 mt-2">
                    View Order
                </a>
            </div>
        `);
    
    markers.push(marker<?php echo $order['order_id']; ?>);
<?php endforeach; ?>

// Focus on specific order if specified
<?php if ($focusOrder): ?>
    map.setView([<?php echo $focusOrder['delivery_lat']; ?>, <?php echo $focusOrder['delivery_lng']; ?>], 12);
    marker<?php echo $focusOrder['order_id']; ?>.openPopup();
<?php elseif (!empty($orders)): ?>
    // Fit map to show all markers if no specific order is focused
    const group = new L.featureGroup(markers);
    map.fitBounds(group.getBounds());
<?php endif; ?>
</script>

    <!-- Include SweetAlert JS -->
    <!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
    <script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script> -->
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once '../includes/footer.php'; ?>