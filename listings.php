<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

// SweetAlert CDN

$pageTitle = "Car Listings";
$where = [];
$params = [];
$types = "";

// Process filters
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = sanitize($_GET['search']);
    $where[] = "(b.brand_name LIKE ? OR m.model_name LIKE ? OR c.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "sss";
}

if (isset($_GET['brand_id']) && !empty($_GET['brand_id'])) {
    $brand_id = (int)$_GET['brand_id'];
    $where[] = "b.id = ?";
    $params[] = $brand_id;
    $types .= "i";
    
    // Get brand name for page title
    $brand_stmt = $conn->prepare("SELECT brand_name FROM brand WHERE id = ?");
    $brand_stmt->bind_param("i", $brand_id);
    $brand_stmt->execute();
    $brand_result = $brand_stmt->get_result();
    if ($brand_row = $brand_result->fetch_assoc()) {
        $pageTitle .= " - " . htmlspecialchars($brand_row['brand_name']);
    }
}

if (isset($_GET['car_condition']) && !empty($_GET['car_condition'])) {
    $car_condition = sanitize($_GET['car_condition']);
    $where[] = "c.car_condition = ?";
    $params[] = $car_condition;
    $types .= "s";
    $pageTitle .= " - " . ucfirst($car_condition);
}

if (isset($_GET['engine_type']) && !empty($_GET['engine_type'])) {
    $engineType = sanitize($_GET['engine_type']);
    $where[] = "et.engine_name = ?";
    $params[] = $engineType;
    $types .= "s";
    $pageTitle .= " - " . htmlspecialchars($engineType);
}

if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
    $minPrice = (float)$_GET['min_price'];
    $where[] = "c.price >= ?";
    $params[] = $minPrice;
    $types .= "d";
}

if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
    $maxPrice = (float)$_GET['max_price'];
    $where[] = "c.price <= ?";
    $params[] = $maxPrice;
    $types .= "d";
}

if (isset($_GET['min_year']) && !empty($_GET['min_year'])) {
    $minYear = (int)$_GET['min_year'];
    $where[] = "c.year >= ?";
    $params[] = $minYear;
    $types .= "i";
}

// Build query with proper table joins
$sql = "SELECT 
            c.car_id,
            b.brand_name,
            m.model_name,
            c.year, 
            et.engine_name, 
            c.car_condition, 
            c.price, 
            col.color,
            c.quantity as inventory_count,
            c.image_url,
            c.description,
            c.mileage
        FROM cars c
        JOIN model m ON c.model_id = m.id
        JOIN brand b ON m.brand_id = b.id
        JOIN engine_type et ON c.engine_id = et.id
        JOIN color col ON c.color_id = col.id";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY c.year DESC, c.price ASC";

// Prepare and execute
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$cars = $result->fetch_all(MYSQLI_ASSOC);
?>

<head>
    <!-- Bootstrap CSS -->
    <link href="./node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="./node_modules/@fortawesome/fontawesome-free/css/all.min.css">
     
    <!-- Include SweetAlert CSS -->
    <link rel="stylesheet" href="./node_modules/sweetalert2/dist/sweetalert2.min.css">
    
</head>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <!-- Filters Sidebar -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="listings.php">
                        <div class="mb-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="brand_id" class="form-label">Brand</label>
                            <select class="form-select" id="brand_id" name="brand_id">
                                <option value="">All Brands</option>
                                <?php 
                                $brands = $conn->query("SELECT id, brand_name FROM brand ORDER BY brand_name");
                                while ($brand = $brands->fetch_assoc()): ?>
                                    <option value="<?php echo $brand['id']; ?>" 
                                        <?php echo isset($_GET['brand_id']) && $_GET['brand_id'] == $brand['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($brand['brand_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="car_condition" class="form-label">Condition</label>
                            <select class="form-select" id="car_condition" name="car_condition">
                                <option value="">All Conditions</option>
                                <option value="new" <?php echo isset($_GET['car_condition']) && $_GET['car_condition'] == 'new' ? 'selected' : ''; ?>>New</option>
                                <option value="used" <?php echo isset($_GET['car_condition']) && $_GET['car_condition'] == 'used' ? 'selected' : ''; ?>>Used</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="engine_type" class="form-label">Engine Type</label>
                            <select class="form-select" id="engine_type" name="engine_type">
                                <option value="">All Types</option>
                                <?php 
                                $engine_types = $conn->query("SELECT engine_name FROM engine_type");
                                while ($engine = $engine_types->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($engine['engine_name']); ?>"
                                        <?php echo isset($_GET['engine_type']) && $_GET['engine_type'] == $engine['engine_name'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($engine['engine_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="min_price" class="form-label">Min Price (Lakhs)</label>
                                <input type="number" class="form-control" id="min_price" name="min_price" 
                                       value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="max_price" class="form-label">Max Price (Lakhs)</label>
                                <input type="number" class="form-control" id="max_price" name="max_price" 
                                       value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        <a href="listings.php" class="btn btn-outline-secondary w-100 mt-2">Reset Filters</a>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo htmlspecialchars($pageTitle); ?></h2>
                <div>
                    <span class="badge bg-primary"><?php echo count($cars); ?> Models Found</span>
                    <span class="badge bg-secondary ms-2"><?php echo array_sum(array_column($cars, 'inventory_count')); ?> Total Cars</span>
                </div>
            </div>
            
            <?php if (empty($cars)): ?>
                <div class="alert alert-info">
                    No cars found matching your criteria. Please try different filters.
                </div>
                <script>
                Swal.fire({
                    title: 'No Results',
                    text: 'No cars found matching your criteria. Please try different filters.',
                    icon: 'info',
                    confirmButtonText: 'OK',
                    toast: true,
                    position: 'top-end',
                    timer: 5000,
                    timerProgressBar: true
                });
                </script>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($cars as $car): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-150 shadow-sm">
                                <!-- Image section - borderless with 60% height -->
                                <div style="height: 60%; overflow: hidden; border: none !important;">
                                    <?php if (!empty($car['image_url'])): ?>
                                        <img style="width:100% !important; height:150px !important;" src=".<?php echo htmlspecialchars($car['image_url']); ?>" 
                                            class="card-img-top h-100 w-100 object-fit-cover border-0" 
                                            alt="<?php echo htmlspecialchars($car['brand_name'] . ' ' . $car['model_name']); ?>">
                                    <?php else: ?>
                                        <div class="h-100 w-100 bg-light d-flex align-items-center justify-content-center text-muted border-0">
                                            <i class="fas fa-car fa-3x"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Content section - 40% height with subtle padding -->
                                <div class="card-body p-3" style="height: 40%;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0 fs-6"><?php echo htmlspecialchars($car['brand_name'] . ' ' . $car['model_name']); ?></h5>
                                        <p class="h5 text-primary mb-0 fs-6"><?php echo number_format($car['price'], 2); ?> Lakhs</p>
                                    </div>
                                    
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        <span class="badge bg-<?php echo $car['car_condition'] == 'new' ? 'success' : 'info'; ?>">
                                            <?php echo htmlspecialchars(ucfirst($car['car_condition'])); ?>
                                        </span>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($car['engine_name']); ?></span>
                                        <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($car['year']); ?></span>
                                        <?php if ($car['inventory_count'] > 0): ?>
                                            <span class="badge bg-success"><?php echo htmlspecialchars($car['inventory_count']); ?> in stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Out of stock</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <p class="card-text text-muted small mb-2">
                                        <?php 
                                        $desc = $car['description'];
                                        // Preserve new lines with nl2br and escape for security
                                        echo nl2br(htmlspecialchars(substr($desc, 0, 100)));
                                        if (strlen($desc) > 100) echo '...';
                                        ?>
                                    </p>
                                    
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php if (!empty($car['color'])): ?>
                                            <span class="badge bg-light text-dark border">
                                                <i class="fas fa-palette me-1"></i> <?php echo htmlspecialchars($car['color']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($car['mileage']) && $car['car_condition'] == 'used'): ?>
                                            <span class="badge bg-light text-dark border">
                                                <i class="fas fa-tachometer-alt me-1"></i> <?php echo number_format($car['mileage']); ?> miles
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Footer with buttons -->
                                <div class="card-footer bg-white p-3 d-flex justify-content-between">
                                    <a href="car-details.php?id=<?php echo $car['car_id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-info-circle me-1"></i> Details
                                    </a>
                                    <?php if ($car['inventory_count'] > 0): ?>
                                        <button class="btn btn-sm btn-primary buy-now-btn" 
                                                data-car-id="<?php echo $car['car_id']; ?>" 
                                                data-car-name="<?php echo htmlspecialchars($car['brand_name'] . ' ' . $car['model_name']); ?>"
                                                data-car-price="<?php echo number_format($car['price'], 2); ?>">
                                            <i class="fas fa-shopping-cart me-1"></i> Buy Now
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>  
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Buy Now Modal -->
<div class="modal fade" id="buyNowModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Purchase Car</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle"></i> You are purchasing:<br> <strong id="purchasing-car-name"></strong><br>
                    Price: <strong id="purchasing-car-price"></strong>
                </div>
                <form id="purchaseForm">
                    <input type="hidden" id="car_id" name="car_id">
                    <div class="mb-3">
                        <label for="delivery_location" class="form-label">Delivery Location</label>
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
                <button type="button" class="btn btn-primary" id="confirmPurchaseBtn">Confirm Purchase</button>
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

// Buy Now button click handler - MODIFIED FOR LOGIN CHECK
document.querySelectorAll('.buy-now-btn').forEach(button => {
    button.addEventListener('click', function() {
        const carId = this.getAttribute('data-car-id');
        const carName = this.getAttribute('data-car-name');
        const carPrice = this.getAttribute('data-car-price');
        
        // Check if user is logged in
        <?php if (!isLoggedIn()): ?>
            // Show login prompt for non-logged in users
            Swal.fire({
                title: 'Login Required',
                text: 'You need to log in to purchase this car.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Go to Login',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to login page with return URL
                    window.location.href = 'login.php?return_to=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>';
                }
            });
            return;
        <?php endif; ?>
        
        <?php if (isAdmin()): ?>
            // Admins shouldn't purchase cars
            Swal.fire({
                title: 'Admin Account',
                text: 'Admin accounts cannot purchase cars. Please use a customer account.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        <?php endif; ?>
        
        // Proceed with purchase for logged-in non-admin users
        document.getElementById('car_id').value = carId;
        document.getElementById('purchasing-car-name').textContent = carName;
        document.getElementById('purchasing-car-price').textContent = '$' + carPrice;
        
        // Reset form when modal is shown
        document.getElementById('purchaseForm').reset();
        document.getElementById('map').style.display = 'none';
        document.getElementById('map').innerHTML = '';
        
        const modal = new bootstrap.Modal(document.getElementById('buyNowModal'));
        modal.show();
    });
});

// Get location button
document.getElementById('getLocationBtn').addEventListener('click', function() {
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
                        showErrorAlert('Error', 'Could not retrieve address details. Please enter manually.');
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

// Confirm purchase with SweetAlert
document.getElementById('confirmPurchaseBtn').addEventListener('click', async function() {
    const deliveryLocation = document.getElementById('delivery_location').value;
    const carId = document.getElementById('car_id').value;
    const carName = document.getElementById('purchasing-car-name').textContent;
    const carPrice = document.getElementById('purchasing-car-price').textContent;
    
    // Validate form
    if (!deliveryLocation) {
        showErrorAlert('Missing Information', 'Please enter a delivery location.');
        return;
    }
    
    // Show confirmation dialog
    const { isConfirmed } = await Swal.fire({
        title: 'Confirm Purchase',
        html: `<div class="text-start">
            <p>You are about to purchase:</p>
            <ul>
                <li><strong>Vehicle:</strong> ${carName}</li>
                <li><strong>Price:</strong> ${carPrice}</li>
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
                    <p><strong>Order ID:</strong> ${data.order_id}</p>
                    <p>You will be redirected to your orders page.</p>
                </div>`,
                icon: 'success',
                confirmButtonText: 'Great!',
                timer: 5000,
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
        purchaseBtn.innerHTML = 'Confirm Purchase';
    }
});
</script>

    <!-- Include SweetAlert JS -->
    <script src="./node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once 'includes/footer.php'; ?>