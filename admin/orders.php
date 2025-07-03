<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Define order status constants
define('ORDER_PENDING', 'Pending');
define('ORDER_PROCESSING', 'Processing');
define('ORDER_SHIPPED', 'Shipped');
define('ORDER_DELIVERED', 'Delivered');
define('ORDER_CANCELLED', 'Cancelled');

$pageTitle = "Manage Orders";

// Handle order status update
if (isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = sanitize($_POST['status']);
    $carId = (int)$_POST['car_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // First, get current status to check if we need to update quantity
        $checkSql = "SELECT status, car_id FROM orders WHERE order_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("i", $orderId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $currentOrder = $result->fetch_assoc();
        $currentStatus = $currentOrder['status'];
        
        // Validate status transition
        $validTransitions = [
            ORDER_PENDING => [ORDER_PROCESSING, ORDER_CANCELLED],
            ORDER_PROCESSING => [ORDER_SHIPPED, ORDER_DELIVERED, ORDER_CANCELLED],
            ORDER_SHIPPED => [ORDER_DELIVERED, ORDER_CANCELLED],
            ORDER_DELIVERED => [],
            ORDER_CANCELLED => [ORDER_PROCESSING]
        ];
        
        if ($currentStatus != $status && !in_array($status, $validTransitions[$currentStatus])) {
            throw new Exception("Invalid status transition from $currentStatus to $status");
        }

        // Update order status
        $updateOrderSql = "UPDATE orders SET status = ? WHERE order_id = ?";
        $updateOrderStmt = $conn->prepare($updateOrderSql);
        $updateOrderStmt->bind_param("si", $status, $orderId);
        $updateOrderStmt->execute();
        
        // Handle inventory changes based on status transitions
        if ($currentStatus != $status) {
            // Moving to Processing (reserve the car)
            if ($status == ORDER_PROCESSING) {
                $updateCarSql = "UPDATE cars SET quantity = quantity - 1 WHERE car_id = ? AND quantity > 0";
                $updateCarStmt = $conn->prepare($updateCarSql);
                $updateCarStmt->bind_param("i", $carId);
                $updateCarStmt->execute();
                
                if ($updateCarStmt->affected_rows == 0) {
                    throw new Exception("Failed to update car quantity - possibly out of stock");
                }
            }
            // Moving out of Processing (except to Delivered - release the car)
            elseif ($currentStatus == ORDER_PROCESSING && $status != ORDER_DELIVERED) {
                $updateCarSql = "UPDATE cars SET quantity = quantity + 1 WHERE car_id = ?";
                $updateCarStmt = $conn->prepare($updateCarSql);
                $updateCarStmt->bind_param("i", $carId);
                $updateCarStmt->execute();
            }
            // Cancelling a delivered order (restock the car)
            elseif ($status == ORDER_CANCELLED && $currentStatus == ORDER_DELIVERED) {
                $updateCarSql = "UPDATE cars SET quantity = quantity + 1 WHERE car_id = ?";
                $updateCarStmt = $conn->prepare($updateCarSql);
                $updateCarStmt->bind_param("i", $carId);
                $updateCarStmt->execute();
            }
        }
        
        $conn->commit();
        redirect('orders.php', 'Order status updated successfully.');
    } catch (Exception $e) {
        $conn->rollback();
        redirect('orders.php', 'Error updating order status: ' . $e->getMessage(), 'error');
    }
}

// Handle order deletion
if (isset($_GET['delete'])) {
    $orderId = (int)$_GET['delete'];
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // First, get order details to check if we need to restore quantity
        $checkSql = "SELECT status, car_id FROM orders WHERE order_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("i", $orderId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows > 0) {
            $order = $result->fetch_assoc();
            
            // If order was in Processing or Delivered, restore the car quantity
            if ($order['status'] == ORDER_PROCESSING || $order['status'] == ORDER_DELIVERED) {
                $updateCarSql = "UPDATE cars SET quantity = quantity + 1 WHERE car_id = ?";
                $updateCarStmt = $conn->prepare($updateCarSql);
                $updateCarStmt->bind_param("i", $order['car_id']);
                $updateCarStmt->execute();
            }
            
            // Delete the order
            $deleteSql = "DELETE FROM orders WHERE order_id = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bind_param("i", $orderId);
            $deleteStmt->execute();
            
            $conn->commit();
            redirect('orders.php', 'Order deleted successfully.');
        } else {
            redirect('orders.php', 'Order not found.', 'error');
        }
    } catch (Exception $e) {
        $conn->rollback();
        redirect('orders.php', 'Error deleting order: ' . $e->getMessage(), 'error');
    }
}

// Get all orders with filters
$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$searchQuery = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$orders = [];
$sql = "SELECT o.order_id, o.order_date, o.status, o.delivery_location, 
               u.username, u.email, u.ph_no,
               c.car_id, b.brand_name, m.model_name, c.year, c.price, c.image_url, 
               c.car_condition, et.engine_name, c.quantity,
               t.transaction_id, t.transaction_code, t.payment_method, t.amount, t.status as payment_status,
               t.image_url as payment_proof, t.created_at as payment_date
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        JOIN cars c ON o.car_id = c.car_id
        JOIN model m ON c.model_id = m.id
        JOIN brand b ON m.brand_id = b.id
        JOIN engine_type et ON c.engine_id = et.id
        LEFT JOIN transaction t ON o.order_id = t.order_id
        WHERE 1=1";

if ($statusFilter) {
    $sql .= " AND o.status = ?";
}

if ($searchQuery) {
    $sql .= " AND (u.username LIKE ? OR b.brand_name LIKE ? OR m.model_name LIKE ? OR u.ph_no LIKE ? OR t.transaction_code LIKE ?)";
}

$sql .= " ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);

if ($statusFilter && $searchQuery) {
    $searchParam = "%$searchQuery%";
    $stmt->bind_param("ssssss", $statusFilter, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
} elseif ($statusFilter) {
    $stmt->bind_param("s", $statusFilter);
} elseif ($searchQuery) {
    $searchParam = "%$searchQuery%";
    $stmt->bind_param("sssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

// Get single order for view
$viewOrder = null;
if (isset($_GET['view']) && !empty($_GET['view'])) {
    $orderId = (int)$_GET['view'];
    
    $sql = "SELECT o.*, 
                   u.username, u.email, u.ph_no,
                   c.*, b.brand_name, m.model_name, et.engine_name,
                   t.transaction_id, t.transaction_code, t.payment_method, 
                   t.amount, t.image_url as payment_proof,
                   t.created_at as payment_date
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            JOIN cars c ON o.car_id = c.car_id
            JOIN model m ON c.model_id = m.id
            JOIN brand b ON m.brand_id = b.id
            JOIN engine_type et ON c.engine_id = et.id
            LEFT JOIN transaction t ON o.order_id = t.order_id
            WHERE o.order_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $viewOrder = $result->fetch_assoc();
}

require_once '../includes/header.php';
?>

<head>
    <!-- Bootstrap CSS -->
    <link href="../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css">
     
    <!-- Include SweetAlert CSS -->
    <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css">
</head>

<div class="container mt-4">
    <h2 class="mb-4">Manage Orders</h2>
    
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="orders.php" class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Filter by Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="<?= ORDER_PENDING ?>" <?= $statusFilter == ORDER_PENDING ? 'selected' : '' ?>><?= ORDER_PENDING ?></option>
                        <option value="<?= ORDER_PROCESSING ?>" <?= $statusFilter == ORDER_PROCESSING ? 'selected' : '' ?>><?= ORDER_PROCESSING ?></option>
                        <option value="<?= ORDER_SHIPPED ?>" <?= $statusFilter == ORDER_SHIPPED ? 'selected' : '' ?>><?= ORDER_SHIPPED ?></option>
                        <option value="<?= ORDER_DELIVERED ?>" <?= $statusFilter == ORDER_DELIVERED ? 'selected' : '' ?>><?= ORDER_DELIVERED ?></option>
                        <option value="<?= ORDER_CANCELLED ?>" <?= $statusFilter == ORDER_CANCELLED ? 'selected' : '' ?>><?= ORDER_CANCELLED ?></option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Search by customer, car, phone or transaction code..." value="<?= htmlspecialchars($searchQuery) ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Car</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Delivery Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $i => $order): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= date('M j, Y', strtotime($order['order_date'])) ?></td>
                        <td>
                            <?= htmlspecialchars($order['username']) ?><br>
                            <small class="text-muted"><?= htmlspecialchars($order['email']) ?></small><br>
                            <small class="text-muted"><?= htmlspecialchars($order['ph_no']) ?></small>
                        </td>
                        <td>
                            <?= htmlspecialchars($order['brand_name'] . ' ' . $order['model_name'] . ' (' . $order['year'] . ')') ?>
                            <?php if ($order['quantity'] <= 0): ?>
                                <span class="badge bg-danger ms-2">Out of Stock</span>
                            <?php endif; ?>
                        </td>
                        <td><?= number_format($order['price'], 2) ?> Lakhs</td>
                        <td>
                            <span class="badge bg-<?= 
                                match($order['status']) {
                                    ORDER_PENDING => 'warning',
                                    ORDER_PROCESSING => 'info',
                                    ORDER_SHIPPED => 'primary',
                                    ORDER_DELIVERED => 'success',
                                    ORDER_CANCELLED => 'danger',
                                    default => 'secondary'
                                }
                            ?>">
                                <?= $order['status'] ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($order['transaction_code']): ?>
                                <small><?= htmlspecialchars($order['payment_method']) ?></small><br>
                                <small><?= htmlspecialchars($order['transaction_code']) ?></small>
                            <?php else: ?>
                                <span class="badge bg-secondary">No payment</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $order['delivery_location'] ? htmlspecialchars($order['delivery_location']) : 'N/A' ?></td>
                        <td>
                            <a href="orders.php?view=<?= $order['order_id'] ?>" 
                            class="btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <form method="GET" action="orders.php" class="d-inline" id="deleteForm<?= $order['order_id'] ?>">
                                <input type="hidden" name="delete" value="<?= $order['order_id'] ?>">
                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn w-100" 
                                        data-id="<?= $order['order_id'] ?>">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Order Details Modal -->
<?php if ($viewOrder): ?>
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order #<?= $viewOrder['order_id'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <!-- Order Information and Car Information in same row -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Order Information</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Order Date</th>
                                        <td><?= date('M j, Y H:i', strtotime($viewOrder['order_date'])) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge bg-<?= 
                                                match($viewOrder['status']) {
                                                    ORDER_PENDING => 'warning',
                                                    ORDER_PROCESSING => 'info',
                                                    ORDER_SHIPPED => 'primary',
                                                    ORDER_DELIVERED => 'success',
                                                    ORDER_CANCELLED => 'danger',
                                                    default => 'secondary'
                                                }
                                            ?>">
                                                <?= $viewOrder['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Delivery Location</th>
                                        <td><?= $viewOrder['delivery_location'] ? htmlspecialchars($viewOrder['delivery_location']) : 'N/A' ?></td>
                                    </tr>
                                    <?php if ($viewOrder['delivery_lat'] && $viewOrder['delivery_lng']): ?>
                                        <tr>
                                            <th>Map Coordinates</th>
                                            <td>
                                                <?= htmlspecialchars($viewOrder['delivery_lat'] . ', ' . $viewOrder['delivery_lng']) ?>
                                                <a href="map.php?order_id=<?= $viewOrder['order_id'] ?>" class="btn btn-sm btn-outline-primary ms-2">
                                                    View on Map
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Car Information</h5>
                            </div>
                            <div class="card-body">
                                <!-- Car Image Row -->
                                <div class="row mb-3">
                                    <div class="col-12 d-flex justify-content-center">
                                        <img src="..<?= htmlspecialchars($viewOrder['image_url'] ?: '../assets/images/car-placeholder.jpg') ?>" 
                                            class="img-fluid rounded" 
                                            style="max-height: 200px; width: auto; object-fit: contain;"
                                            alt="<?= htmlspecialchars($viewOrder['brand_name'] . ' ' . $viewOrder['model_name']) ?>">
                                    </div>
                                </div>
                                
                                <!-- Car Details Row -->
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="card-title text-center"><?= htmlspecialchars($viewOrder['brand_name'] . ' ' . $viewOrder['model_name']) ?></h5>
                                        <div class="d-flex justify-content-center flex-wrap mb-2">
                                            <span class="badge bg-<?= $viewOrder['car_condition'] == 'new' ? 'success' : 'info' ?> mx-1">
                                                <?= ucfirst($viewOrder['car_condition']) ?>
                                            </span>
                                            <span class="badge bg-secondary mx-1"><?= htmlspecialchars($viewOrder['engine_name']) ?></span>
                                            <span class="badge bg-warning text-dark mx-1"><?= htmlspecialchars($viewOrder['year']) ?></span>
                                            <?php if ($viewOrder['quantity'] <= 0): ?>
                                                <span class="badge bg-danger mx-1">Out of Stock</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <p class="card-text text-center"><?= substr(htmlspecialchars($viewOrder['description']), 0, 100) . '...' ?></p>
                                        
                                        <div class="d-flex justify-content-center align-items-center mt-3">
                                            <p class="h4 mb-0"><?= number_format($viewOrder['price'], 2) ?> Lakhs</p>
                                            <p class="ms-3 mb-0"><i class="fas fa-box"></i> Stock: <?= $viewOrder['quantity'] ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($viewOrder['transaction_id']): ?>
                <div class="row g-4 mt-2">
                    <!-- Customer Information and Bank Receipt in same row -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Customer & Payment Information</h5>
                            </div>
                            <div class="card-body">
                                <h5 class="mb-3">Customer Information</h5>
                                <table class="table table-bordered mb-4">
                                    <tr>
                                        <th>Name</th>
                                        <td><?= htmlspecialchars($viewOrder['username']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><?= htmlspecialchars($viewOrder['email']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td><?= $viewOrder['ph_no'] ? htmlspecialchars($viewOrder['ph_no']) : 'N/A' ?></td>
                                    </tr>
                                </table>
                                
                                <h5 class="mb-3">Payment Information</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Transaction Code</th>
                                        <td><?= htmlspecialchars($viewOrder['transaction_code']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Payment Method</th>
                                        <td><?= htmlspecialchars($viewOrder['payment_method']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Amount</th>
                                        <td><?= number_format($viewOrder['amount'], 2) ?> Lakhs</td>
                                    </tr>
                                    <tr>
                                        <th>Payment Date</th>
                                        <td><?= date('M j, Y H:i', strtotime($viewOrder['payment_date'])) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Bank Receipt</h5>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <?php if ($viewOrder['payment_proof']): ?>
                                    <div class="text-center flex-grow-1 d-flex align-items-center justify-content-center">
                                        <img src="..<?= htmlspecialchars($viewOrder['payment_proof']) ?>" 
                                            class="img-fluid" 
                                            style="max-height: auto; width: auto;"
                                            alt="Bank Receipt">
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning flex-grow-1 d-flex align-items-center justify-content-center">
                                        No receipt uploaded
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Update Order Status</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="orders.php">
                                    <input type="hidden" name="order_id" value="<?= $viewOrder['order_id'] ?>">
                                    <input type="hidden" name="car_id" value="<?= $viewOrder['car_id'] ?>">
                                    <div class="input-group">
                                        <select class="form-select" name="status" required>
                                            <?php foreach ([ORDER_PENDING, ORDER_PROCESSING, ORDER_SHIPPED, ORDER_DELIVERED, ORDER_CANCELLED] as $status): ?>
                                                <option value="<?= $status ?>" <?= $viewOrder['status'] == $status ? 'selected' : '' ?>>
                                                    <?= $status ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn-primary" name="update_status">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Order modal handling
    var orderModalEl = document.getElementById('orderModal');
    if (orderModalEl) {
        var orderModal = new bootstrap.Modal(orderModalEl);
        orderModal.show();
        
        orderModalEl.addEventListener('hidden.bs.modal', function () {
            if (window.history.replaceState) {
                var url = new URL(window.location.href);
                url.searchParams.delete('view');
                window.history.replaceState(null, null, url);
            }
        });
    }

    // SweetAlert for delete confirmation
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`deleteForm${orderId}`).submit();
                }
            });
        });
    });

    // Display toast messages from PHP
    <?php if (isset($_SESSION['toast'])): ?>
        Swal.fire({
            icon: '<?= $_SESSION['toast']['type'] ?>',
            title: '<?= $_SESSION['toast']['message'] ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        <?php unset($_SESSION['toast']); ?>
    <?php endif; ?>
});
</script>

<!-- Include SweetAlert JS -->
<script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

<!-- Bootstrap JS -->
<script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once '../includes/footer.php'; ?>