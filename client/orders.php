<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

$pageTitle = "My Orders";

// Get client's orders with transaction details
$orders = [];
$sql = "SELECT o.order_id, o.order_date, o.status, o.delivery_location,
               c.car_id, 
               b.brand_name AS make, 
               m.model_name AS model, 
               c.year, c.price, c.image_url, 
               c.car_condition, 
               e.engine_name AS engine_type,
               t.transaction_id,
               t.transaction_code,
               t.payment_method,
               t.amount,
               t.image_url AS receipt_image
        FROM orders o
        JOIN cars c ON o.car_id = c.car_id
        JOIN model m ON c.model_id = m.id
        JOIN brand b ON m.brand_id = b.id
        JOIN engine_type e ON c.engine_id = e.id
        LEFT JOIN transaction t ON o.order_id = t.order_id
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

// Get single order for view
$viewOrder = null;
if (isset($_GET['view']) && !empty($_GET['view'])) {
    $orderId = (int)$_GET['view'];
    
    foreach ($orders as $order) {
        if ($order['order_id'] == $orderId) {
            $viewOrder = $order;
            break;
        }
    }
}

// Get single order for payment view
$paymentOrder = null;
if (isset($_GET['payment']) && !empty($_GET['payment'])) {
    $orderId = (int)$_GET['payment'];
    
    foreach ($orders as $order) {
        if ($order['order_id'] == $orderId) {
            $paymentOrder = $order;
            break;
        }
    }
}

require_once '../includes/header.php';
?>

<head>
    <link href="../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css">
</head>

<div class="container mt-4">
    <h2 class="mb-4">My Orders</h2>
    
    <?php if (empty($orders)): ?>
        <div class="alert alert-info">
            You haven't placed any orders yet. <a href="../listings.php">Browse our cars</a> to get started!
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'No Orders Found',
                html: 'You haven\'t placed any orders yet. <a href="../listings.php">Browse our cars</a> to get started!',
                icon: 'info',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6'
            });
        });
        </script>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Car</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
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
                                <?php if ($order['transaction_code']): ?>
                                    <span class="badge bg-success">
                                        Paid
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="orders.php?view=<?php echo $order['order_id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <?php if (!$order['transaction_code'] && $order['status'] == 'Pending'): ?>
                                        <a href="orders.php?payment=<?php echo $order['order_id']; ?>" 
                                           class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-credit-card"></i> Pay
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Order Details Modal -->
<?php if ($viewOrder): ?>
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">Order #<?php echo $viewOrder['order_id']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Order Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Order Date</th>
                                <td><?php echo date('M j, Y H:i', strtotime($viewOrder['order_date'])); ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-<?php 
                                        switch($viewOrder['status']) {
                                            case 'Pending': echo 'warning'; break;
                                            case 'Processing': echo 'info'; break;
                                            case 'Shipped': echo 'primary'; break;
                                            case 'Delivered': echo 'success'; break;
                                            case 'Cancelled': echo 'danger'; break;
                                        }
                                    ?>">
                                        <?php echo $viewOrder['status']; ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Delivery Location</th>
                                <td><?php echo $viewOrder['delivery_location'] ?: 'N/A'; ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-12 mt-4">
                        <h5>Car Information</h5>
                        <div class="card">
                            <div class="row g-0">
                                <div class="col-md-4 d-flex align-items-center justify-content-center p-2">
                                    <img src="..<?php echo $viewOrder['image_url'] ?: '../assets/images/car-placeholder.jpg'; ?>" 
                                         class="img-fluid rounded-start" 
                                         style="max-height: 150px; width: auto; object-fit: contain;"
                                         alt="<?php echo $viewOrder['make'] . ' ' . $viewOrder['model']; ?>">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $viewOrder['make'] . ' ' . $viewOrder['model']; ?></h5>
                                        <p class="card-text">
                                            <span class="badge bg-<?php echo $viewOrder['car_condition'] == 'new' ? 'success' : 'info'; ?>">
                                                <?php echo ucfirst($viewOrder['car_condition']); ?>
                                            </span>
                                            <span class="badge bg-secondary">
                                                <?php echo isset($viewOrder['engine_type']) ? $viewOrder['engine_type'] : 'N/A'; ?>
                                            </span>
                                            <span class="badge bg-warning text-dark"><?php echo $viewOrder['year']; ?></span>
                                        </p>
                                        <p class="card-text"><?php echo number_format($viewOrder['price'], 2); ?> Lakhs</p>
                                        <a href="../car-details.php?id=<?php echo $viewOrder['car_id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-info-circle"></i> View Car
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="../contact.php?order_id=<?php echo $viewOrder['order_id']; ?>" class="btn btn-primary">
                    <i class="fas fa-headset"></i> Contact Support
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Payment Modal -->
<?php if ($paymentOrder): ?>
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Payment for Order #<?php echo $paymentOrder['order_id']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Order Summary</h5>
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6><?php echo $paymentOrder['make'] . ' ' . $paymentOrder['model'] . ' (' . $paymentOrder['year'] . ')'; ?></h6>
                                <p class="mb-1"><strong>Price:</strong> <?php echo number_format($paymentOrder['price'], 2); ?> Lakhs</p>
                                <p class="mb-1"><strong>Status:</strong> 
                                    <span class="badge bg-<?php 
                                        switch($paymentOrder['status']) {
                                            case 'Pending': echo 'warning'; break;
                                            case 'Processing': echo 'info'; break;
                                            case 'Shipped': echo 'primary'; break;
                                            case 'Delivered': echo 'success'; break;
                                            case 'Cancelled': echo 'danger'; break;
                                        }
                                    ?>">
                                        <?php echo $paymentOrder['status']; ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                        
                        <?php if ($paymentOrder['transaction_code']): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle"></i> Payment Completed</h5>
                                <p><strong>Transaction Code:</strong> <?php echo htmlspecialchars($paymentOrder['transaction_code']); ?></p>
                                <p><strong>Amount Paid:</strong> <?php echo number_format($paymentOrder['amount'] ?? $paymentOrder['price'], 2); ?> Lakhs</p>
                                
                                <?php if (!empty($paymentOrder['receipt_image'])): ?>
                                    <div class="mt-3">
                                        <strong>Payment Receipt:</strong>
                                        <div class="mt-2">
                                            <img src="..<?php echo $paymentOrder['receipt_image']; ?>" class="img-fluid rounded" style="max-height: 200px;" alt="Payment Receipt">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <h5><i class="fas fa-exclamation-circle"></i> Payment Pending</h5>
                                <p>Please submit your payment details to complete your order.</p>
                                
                                <form id="transactionForm" enctype="multipart/form-data">
                                    <input type="hidden" name="order_id" value="<?php echo $paymentOrder['order_id']; ?>">
                                    
                                    <div class="mb-3">
                                        <label for="payment_method" class="form-label">Payment Method *</label>
                                        <select class="form-select" id="payment_method" name="payment_method" required>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="Mobile Payment">Mobile Payment</option>
                                            <option value="Cash">Cash</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">Amount Paid *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="amount" name="amount" 
                                                value="<?php echo $paymentOrder['price']; ?>" 
                                                step="0.01" min="0" max="<?php echo $paymentOrder['price']; ?>" required>
                                        </div>
                                        <small class="text-muted">Maximum: <?php echo number_format($paymentOrder['price'], 2); ?> Lakhs</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="transaction_code" class="form-label">Transaction Code *</label>
                                        <input type="text" class="form-control" id="transaction_code" name="transaction_code" required>
                                        <small class="text-muted">Enter the reference number from your payment</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="receipt_image" class="form-label">Payment Receipt *</label>
                                        <input type="file" class="form-control" id="receipt_image" name="receipt_image" accept="image/*" required>
                                        <small class="text-muted">Upload a clear image of your payment receipt (JPEG, PNG, max 2MB)</small>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-upload"></i> Submit Payment
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Payment Instructions</h5>
                        <div class="card">
                            <div class="card-body">
                                <h6><i class="fas fa-university"></i> Bank Transfer</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Bank Name:</strong> KBZ Bank</li>
                                    <li><strong>Account Name:</strong> CarDeal</li>
                                    <li><strong>Account Number:</strong> 41851248300939201</li>
                                    <li><strong>Reference:</strong> Order #<?php echo $paymentOrder['order_id']; ?></li>
                                </ul>
                                
                                <hr>
                                
                                <h6><i class="fas fa-mobile-alt"></i> Mobile Payment</h6>
                                <p>Send payment to our mobile money account:</p>
                                <ul class="list-unstyled">
                                    <li><strong>Provider:</strong> KBZ Mobile Money</li>
                                    <li><strong>Number:</strong> 09751971090</li>
                                    <li><strong>Name:</strong> CarDeal</li>
                                </ul>
                                
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle"></i> After making payment, please upload your receipt and enter the transaction details above.
                                </div>
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

<script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Reusable alert functions
function showErrorAlert(title, message) {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#d33'
    });
}

function showSuccessAlert(title, message) {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#28a745',
        timer: 3000,
        timerProgressBar: true
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize order modal if exists
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
    
    // Initialize payment modal if exists
    var paymentModalEl = document.getElementById('paymentModal');
    if (paymentModalEl) {
        var paymentModal = new bootstrap.Modal(paymentModalEl);
        paymentModal.show();
        
        paymentModalEl.addEventListener('hidden.bs.modal', function () {
            if (window.history.replaceState) {
                var url = new URL(window.location.href);
                url.searchParams.delete('payment');
                window.history.replaceState(null, null, url);
            }
        });
    }
    
    // Handle transaction form submission
    document.getElementById('transactionForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const carPrice = parseFloat('<?php echo isset($paymentOrder) ? $paymentOrder["price"] : 0; ?>');
        const amountInput = parseFloat(form.querySelector('#amount').value);
        
        // Validate amount is not greater than car price
        if (amountInput > carPrice) {
            await showErrorAlert(
                'Invalid Amount', 
                `Amount paid cannot be greater than car price ($${carPrice.toFixed(2)})`
            );
            return;
        }
        
        try {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
            
            // Show processing alert
            const processingAlert = Swal.fire({
                title: 'Processing Payment',
                html: 'Please wait while we submit your payment details...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            const response = await fetch('../process/process-transaction.php', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            
            // Close processing alert
            await processingAlert.close();
            
            if (data.success) {
                await showSuccessAlert(
                    'Payment Submitted', 
                    data.message || 'Your payment has been submitted successfully!'
                );
                location.reload();
            } else {
                throw new Error(data.message || 'Failed to submit payment');
            }
        } catch (error) {
            await showErrorAlert('Payment Error', error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-upload"></i> Submit Payment';
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>