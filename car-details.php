<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('/listings.php', 'Invalid car ID.');
}

$carId = (int)$_GET['id'];
$sql = "SELECT c.*, 
               b.brand_name AS make, 
               m.model_name AS model, 
               e.engine_name AS engine_type, 
               col.color
        FROM Cars c
        JOIN model m ON c.model_id = m.id
        JOIN brand b ON m.brand_id = b.id
        JOIN engine_type e ON c.engine_id = e.id
        LEFT JOIN color col ON c.color_id = col.id
        WHERE c.car_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $carId);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();

if (!$car) {
    redirect('/listings.php', 'Car not found.');
}

$pageTitle = $car['make'] . ' ' . $car['model'] . ' - Car Details';

// Determine stock status
$inStock = (int)$car['quantity'];
$stockStatus = $inStock > 0 ? 'In Stock' : 'Out of Stock';
$stockBadgeClass = $inStock > 0 ? 'bg-success' : 'bg-danger';
$stockBadgeText = $inStock > 0 ? $inStock . ' Available' : 'Sold Out';
?>

<head>
    <link href="./node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./node_modules/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="./node_modules/sweetalert2/dist/sweetalert2.min.css">
</head>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <!-- Car Details -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3>Vehicle Details</h3>
                </div>
                <div class="card-body">
                    <h4 class="card-title"><?php echo $car['make'] . ' ' . $car['model']; ?></h4>
                    <p class="card-text">
                        <!-- Car Images -->  
                        <div>
                            <img src=".<?= $car['image_url']?>" alt="" class="img-fluid">
                        </div>
                        <span class="badge bg-<?php echo $car['car_condition'] == 'new' ? 'success' : 'info'; ?>">
                            <?php echo ucfirst($car['car_condition']); ?>
                        </span>
                        <span class="badge bg-secondary"><?php echo $car['engine_type']; ?></span>
                        <span class="badge bg-warning text-dark"><?php echo $car['year']; ?></span>
                        <span class="badge <?php echo $stockBadgeClass; ?>"><?php echo $stockBadgeText; ?></span>
                        <?php if ($car['car_condition'] == 'used' && isset($car['mileage'])): ?>
                            <span class="badge bg-light text-dark"><?php echo number_format($car['mileage']); ?> miles</span>
                        <?php endif; ?>
                    </p>
                    
                    <h3 class="text-primary my-4"><?php echo number_format($car['price'], 2); ?> Lakhs</h3>
                    
                    <!-- Modified section: Description and Specifications side by side -->
                    <div class="row mt-4">
                        <!-- Description Column -->
                        <div class="col-md-6">
                            <h5>Description</h5>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($car['description'])); ?></p>
                        </div>
                        
                        <!-- Specifications Column -->
                        <div class="col-md-6">
                            <h5>Specifications</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Brand:</span>
                                    <span><?php echo $car['make']; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Model:</span>
                                    <span><?php echo $car['model']; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Year:</span>
                                    <span><?php echo $car['year']; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Engine Type:</span>
                                    <span><?php echo $car['engine_type']; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Availability:</span>
                                    <span><?php echo $stockBadgeText; ?></span>
                                </li>
                                <?php if ($car['car_condition'] == 'used'): ?>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Mileage:</span>
                                        <span><?php echo isset($car['mileage']) ? number_format($car['mileage']) . ' miles' : 'N/A'; ?></span>
                                    </li>
                                <?php endif; ?>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Color:</span>
                                    <span><?php echo isset($car['color']) ? $car['color'] : 'N/A'; ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Purchase Box -->
            <div class="card mb-4" style="top: 20px;">
                <div class="card-header bg-success text-white">
                    <h4>Purchase This Vehicle</h4>
                </div>
                <div class="card-body">
                    <h3 class="text-center text-primary"><?php echo number_format($car['price'], 2); ?> Lakhs</h3>
                    
                    <?php if ($inStock > 0): ?>
                        <?php if (!(isLoggedIn() && isAdmin())): // Show button for non-admin users ?>
                            <button class="btn btn-primary w-100 mb-3 buy-now-btn" 
                                    data-car-id="<?php echo $car['car_id']; ?>"
                                    data-is-logged-in="<?php echo isLoggedIn() ? '1' : '0'; ?>">
                                <i class="fas fa-shopping-cart"></i> Buy Now
                            </button>
                        <?php endif; ?>
                        
                        <?php if (!(isLoggedIn() && isAdmin())): // Show contact button for non-admin users ?>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary contact-btn">
                                    <i class="fas fa-phone"></i> Contact Dealer
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            This vehicle is currently out of stock. Please contact us for availability updates.
                        </div>
                        <button class="btn btn-outline-primary w-100 contact-btn">
                            <i class="fas fa-envelope"></i> Contact About Availability
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Dealer Info -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4>Dealer Information</h4>
                </div>
                <div class="card-body">
                    <h5>CarDeal Showroom</h5>
                    <address>
                        <p><i class="fas fa-map-marker-alt"></i> 123 Car Street, Auto City</p>
                        <p><i class="fas fa-phone"></i> (123) 456-7890</p>
                        <p><i class="fas fa-envelope"></i> sales@cardeal.com</p>
                    </address>
                    <?php if (isLoggedIn() && !isAdmin()): ?>
                        <div class="d-grid">
                            <button class="btn btn-outline-primary contact-btn">
                                <i class="fas fa-envelope"></i> Contact Dealer
                            </button>
                        </div>
                    <?php elseif (isLoggedIn() && isAdmin()): ?>
                        <div class="alert alert-info">
                            You are viewing this page as an administrator.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Buy Now Modal -->
<div class="modal fade" id="buyNowModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Purchase Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle"></i> You are purchasing: <strong><?php echo $car['make'] . ' ' . $car['model']; ?></strong>
                </div>
                <form id="purchaseForm">
                    <input type="hidden" id="car_id" name="car_id" value="<?php echo $car['car_id']; ?>">
                    <div class="mb-3">
                        <label for="delivery_location" class="form-label">Delivery Location *</label>
                        <input type="text" class="form-control" id="delivery_location" name="delivery_location" required>
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary" id="getLocationBtn">
                            <i class="fas fa-map-marker-alt"></i> Use Current Location
                        </button>
                    </div>
                    <div id="map" style="height: 200px; display: none;" class="mb-3"></div>
                    <input type="hidden" id="delivery_lat" name="delivery_lat">
                    <input type="hidden" id="delivery_lng" name="delivery_lng">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmPurchaseBtn">
                    <i class="fas fa-check-circle"></i> Confirm Purchase
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Contact Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Contact Dealer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="contactForm">
                    <input type="hidden" name="car_id" value="<?php echo $car['car_id']; ?>">
                    <div class="mb-3">
                        <label for="contact_name" class="form-label">Your Name *</label>
                        <input type="text" class="form-control" id="contact_name" name="name" 
                               value="<?php echo isLoggedIn() ? $_SESSION['username'] : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="contact_email" name="email" 
                               value="<?php echo isLoggedIn() ? $_SESSION['email'] : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_phone" class="form-label">Phone *</label>
                        <input type="tel" class="form-control" id="contact_phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_subject" class="form-label">Subject *</label>
                        <input type="text" class="form-control" id="contact_subject" name="subject" 
                               value="Inquiry about <?php echo $car['make'] . ' ' . $car['model']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_message" class="form-label">Message *</label>
                        <textarea class="form-control" id="contact_message" name="message" rows="5" required>I'm interested in the <?php echo $car['make'] . ' ' . $car['model']; ?> (ID: <?php echo $car['car_id']; ?>). Please contact me with more information.</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="sendMessageBtn">Send Message</button>
            </div>
        </div>
    </div>
</div>

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
        confirmButtonColor: '#28a745'
    });
}

// Buy Now button handler
document.querySelectorAll('.buy-now-btn').forEach(button => {
    button.addEventListener('click', function() {
        const isLoggedIn = this.getAttribute('data-is-logged-in') === '1';
        const carId = this.getAttribute('data-car-id');
        
        if (!isLoggedIn) {
            // Show login prompt for non-logged in users
            Swal.fire({
                title: 'Login Required',
                text: 'You need to login to purchase this vehicle.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Go to Login',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });
        } else {
            // Regular logged-in user - open modal
            document.getElementById('car_id').value = carId;
            const modal = new bootstrap.Modal(document.getElementById('buyNowModal'));
            modal.show();
        }
    });
});

// Enhanced purchase confirmation with validation
document.getElementById('confirmPurchaseBtn')?.addEventListener('click', async function() {
    const deliveryLocation = document.getElementById('delivery_location').value;
    const carName = "<?php echo $car['make'] . ' ' . $car['model']; ?>";
    const carPrice = "<?php echo number_format($car['price'], 2); ?>";
    
    // Validate form
    if (!deliveryLocation) {
        showErrorAlert('Missing Information', 'Please enter a delivery location');
        return;
    }
    
    // Show confirmation dialog
    const { isConfirmed } = await Swal.fire({
        title: 'Confirm Purchase',
        html: `<div class="text-start">
            <p>You are about to purchase:</p>
            <ul>
                <li><strong>Vehicle:</strong> ${carName}</li>
                <li><strong>Price:</strong> $${carPrice}</li>
                <li><strong>Delivery to:</strong> ${deliveryLocation}</li>
            </ul>
            <p class="text-danger">This action cannot be undone.</p>
        </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed with purchase',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        reverseButtons: true
    });
    
    if (!isConfirmed) {
        return;
    }
    
    const form = document.getElementById('purchaseForm');
    const formData = new FormData(form);
    const purchaseBtn = document.getElementById('confirmPurchaseBtn');
    
    try {
        // Show loading state
        purchaseBtn.disabled = true;
        purchaseBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
        
        // Show processing alert
        const processingAlert = Swal.fire({
            title: 'Processing Purchase',
            html: 'Please wait while we process your order...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Send purchase request
        const response = await fetch('./process/process-purchase.php', {
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
            // Show success message
            await Swal.fire({
                title: 'Purchase Successful!',
                html: `<div class="text-start">
                    <p>Your order has been placed successfully.</p>
                    <p>You will be redirected to your orders page.</p>
                </div>`,
                icon: 'success',
                confirmButtonText: 'Great!',
                timer: 3000,
                timerProgressBar: true,
                willClose: () => {
                    // Reset form and close modal
                    form.reset();
                    document.getElementById('map').style.display = 'none';
                    document.getElementById('map').innerHTML = '';
                    
                    // Hide modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('buyNowModal'));
                    modal.hide();
                    
                    // Redirect to orders page
                    window.location.href = './client/orders.php';
                }
            });
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }
    } catch (error) {
        showErrorAlert('Purchase Error', 'Error processing purchase: ' + error.message);
    } finally {
        // Reset button state
        purchaseBtn.disabled = false;
        purchaseBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Purchase';
    }
});

// Get location button
document.getElementById('getLocationBtn')?.addEventListener('click', function() {
    if (navigator.geolocation) {
        Swal.fire({
            title: 'Getting Location',
            text: 'Please wait while we retrieve your location...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        navigator.geolocation.getCurrentPosition(
            position => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                document.getElementById('delivery_lat').value = lat;
                document.getElementById('delivery_lng').value = lng;
                
                // Reverse geocode to get address
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                    .then(response => response.json())
                    .then(data => {
                        const address = data.display_name;
                        document.getElementById('delivery_location').value = address;
                        
                        // Show map
                        const mapDiv = document.getElementById('map');
                        mapDiv.style.display = 'block';
                        
                        // Initialize map
                        mapDiv.innerHTML = `<iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" 
                            src="https://maps.google.com/maps?q=${lat},${lng}&z=15&output=embed"></iframe>`;
                        
                        Swal.close();
                    })
                    .catch(error => {
                        console.error('Geocoding error:', error);
                        showErrorAlert('Location Error', 'Could not retrieve address details. Please enter manually.');
                    });
            },
            error => {
                showErrorAlert('Location Error', 'Unable to retrieve your location: ' + error.message);
            }
        );
    } else {
        showErrorAlert('Not Supported', 'Geolocation is not supported by your browser.');
    }
});

// Contact buttons
document.querySelectorAll('.contact-btn').forEach(button => {
    button.addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('contactModal'));
        modal.show();
    });
});

// Send message
document.getElementById('sendMessageBtn')?.addEventListener('click', async function() {
    const form = document.getElementById('contactForm');
    const formData = new FormData(form);
    const btn = this;
    
    try {
        // Show loading state
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
        
        const response = await fetch('./process/process-contact.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            await showSuccessAlert('Success', 'Message sent successfully!');
            bootstrap.Modal.getInstance(document.getElementById('contactModal')).hide();
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }
    } catch (error) {
        showErrorAlert('Error', error.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Send Message';
    }
});
</script>

<script src="./node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once 'includes/footer.php'; ?>