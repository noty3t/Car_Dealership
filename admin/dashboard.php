<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

$pageTitle = "Admin Dashboard";

// Get counts for dashboard
$carCount = $conn->query("SELECT SUM(quantity) FROM cars")->fetch_row()[0] ?? 0; // Sum of all quantities
$userCount = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'client'")->fetch_row()[0];
$orderCount = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$pendingOrders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetch_row()[0];
$contactCount = $conn->query("SELECT COUNT(*) FROM contacts")->fetch_row()[0];

// Get recent orders
$recentOrders = [];
$sql = "SELECT o.order_id, o.order_date, o.status, u.username, b.brand_name, m.model_name 
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        JOIN cars c ON o.car_id = c.car_id
        JOIN model m ON c.model_id = m.id
        JOIN brand b ON m.brand_id = b.id
        ORDER BY o.order_date DESC LIMIT 5";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recentOrders[] = $row;
    }
}

require_once '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
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

<div class="container-fluid px-4 mt-4">
    <h2 class="mb-4">Admin Dashboard</h2>
    
    <!-- Stats Cards Row - Full Width -->
    <div class="row mb-4 gx-3">
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6 mb-3">
            <div class="card text-white bg-primary h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column p-3">
                    <div class="mb-2">
                        <h5 class="card-title mb-1">Total Cars (Stock)</h5>
                        <h2 class="fw-bold mb-0"><?php echo number_format($carCount); ?></h2>
                    </div>
                    <a href="cars.php" class="text-white text-decoration-none align-self-start mt-auto">View Inventory</a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6 mb-3">
            <div class="card text-white bg-success h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column p-3">
                    <div class="mb-2">
                        <h5 class="card-title mb-1">Total Clients</h5>
                        <h2 class="fw-bold mb-0"><?php echo number_format($userCount); ?></h2>
                    </div>
                    <a href="users.php" class="text-white text-decoration-none align-self-start mt-auto">View All</a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6 mb-3">
            <div class="card text-white bg-info h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column p-3">
                    <div class="mb-2">
                        <h5 class="card-title mb-1">Total Orders</h5>
                        <h2 class="fw-bold mb-0"><?php echo number_format($orderCount); ?></h2>
                    </div>
                    <a href="orders.php" class="text-white text-decoration-none align-self-start mt-auto">View All</a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6 mb-3">
            <div class="card text-white bg-warning h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column p-3">
                    <div class="mb-2">
                        <h5 class="card-title mb-1">Pending Orders</h5>
                        <h2 class="fw-bold mb-0"><?php echo number_format($pendingOrders); ?></h2>
                    </div>
                    <a href="orders.php?status=Pending" class="text-white text-decoration-none align-self-start mt-auto">View All</a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6 mb-3">
            <div class="card text-white bg-secondary h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column p-3">
                    <div class="mb-2">
                        <h5 class="card-title mb-1">Contacts</h5>
                        <h2 class="fw-bold mb-0"><?php echo number_format($contactCount); ?></h2>
                    </div>
                    <a href="contacts.php" class="text-white text-decoration-none align-self-start mt-auto">View All</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="row gx-3">
        <!-- Recent Orders Card -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Recent Orders</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Car</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i=1;
                                foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $order['order_id']; ?></td>
                                        <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                        <td><?php echo $order['username']; ?></td>
                                        <td><?php echo $order['brand_name'] . ' ' . $order['model_name']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                switch($order['status']) {
                                                    case 'Pending': echo 'warning'; break;
                                                    case 'Processing': echo 'info'; break;
                                                    case 'Shipped': echo 'primary'; break;
                                                    case 'Delivered': echo 'success'; break;
                                                    case 'Cancelled': echo 'danger'; break;
                                                    default: echo 'secondary';
                                                }
                                            ?>">
                                                <?php echo $order['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="orders.php?view=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Column -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="cars.php?action=add" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Car
                        </a>
                        <a href="news.php" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>Add News
                        </a>
                        <a href="orders.php" class="btn btn-outline-primary">
                            <i class="fas fa-tasks me-2"></i>Manage Orders
                        </a>
                        <a href="feedback.php" class="btn btn-outline-primary">
                            <i class="fas fa-comment me-2"></i>View Feedback
                        </a>
                        <a href="map.php" class="btn btn-outline-primary">
                            <i class="fas fa-map-marked-alt me-2"></i>View Delivery Map
                        </a>
                        <a href="contacts.php" class="btn btn-outline-primary">
                            <i class="fas fa-envelope me-2"></i>View Contact Messages
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Feedback Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Recent Feedback</h5>
                </div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT f.feedback_id, f.feedback_title, u.username, f.feedback_date 
                            FROM feedback f
                            JOIN users u ON f.user_id = u.user_id
                            ORDER BY f.feedback_date DESC LIMIT 3";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="mb-3 pb-2 border-bottom">';
                            echo '<h6 class="mb-1">' . htmlspecialchars($row['feedback_title']) . '</h6>';
                            echo '<small class="text-muted">By ' . htmlspecialchars($row['username']) . ' on ' . date('M j, Y', strtotime($row['feedback_date'])) . '</small>';
                            echo '</div>';
                        }
                        echo '<a href="feedback.php" class="btn btn-sm btn-outline-primary w-100 mt-2">View All Feedback</a>';
                    } else {
                        echo '<p class="text-muted mb-0">No feedback yet.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Include SweetAlert JS -->
    <!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
    <script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script> -->
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<?php require_once '../includes/footer.php'; ?>