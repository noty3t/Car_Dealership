<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

$pageTitle = "Client Dashboard";

// Get client's orders with proper table joins
$orders = [];
$sql = "SELECT o.order_id, o.order_date, o.status, 
               b.brand_name as make, 
               m.model_name as model, 
               c.year, c.price, c.image_url
        FROM orders o
        JOIN cars c ON o.car_id = c.car_id
        JOIN model m ON c.model_id = m.id
        JOIN brand b ON m.brand_id = b.id
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>
        <a href="profile.php" class="btn btn-outline-primary">
            <i class="fas fa-user"></i> My Profile
        </a>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">My Orders</h5>
                    <h2><?php echo count($orders); ?></h2>
                    <a href="orders.php" class="text-white">View All</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5>Recent Orders</h5>
        </div>
        <div class="card-body">
            <?php if (empty($orders)): ?>
                <div class="alert alert-info">
                    You haven't placed any orders yet. <a href="../listings.php">Browse our cars</a> to get started!
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Car</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i=1;
                            foreach (array_slice($orders, 0, 5) as $order): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $order['order_id']; ?></td>
                                    <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                    <td>
                                        <?php echo $order['make'] . ' ' . $order['model'] . ' (' . $order['year'] . ')'; ?>
                                    </td>
                                    <td><?php echo number_format($order['price'], 2); ?> Lakhs</td>
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
                                        <a href="orders.php?view=<?php echo $order['order_id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="orders.php" class="btn btn-primary">View All Orders</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5>Quick Actions</h5>
        </div>
        <div class="card-body">
            <div class="d-grid gap-2">
                <a href="../listings.php" class="btn btn-outline-primary">
                    <i class="fas fa-car"></i> Browse Cars
                </a>
                <a href="profile.php" class="btn btn-outline-primary">
                    <i class="fas fa-user"></i> Update Profile
                </a>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                    <i class="fas fa-comment"></i> Submit Feedback
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="feedbackForm" method="POST" action="../process/process-feedback.php">
                    <div class="mb-3">
                        <label for="feedback_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="feedback_title" name="feedback_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="feedback_desc" class="form-label">Feedback</label>
                        <textarea class="form-control" id="feedback_desc" name="feedback_desc" rows="5" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="feedbackForm" class="btn btn-primary">Submit Feedback</button>
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