<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$pageTitle = "Home - Car Dealership";
$featuredCars = getFeaturedCars();
?>

<head>

    <!-- Bootstrap CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="./node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <link rel="stylesheet" href="./node_modules/@fortawesome/fontawesome-free/css/all.min.css">
     
    <!-- Custom CSS -->
    <!-- <link rel="stylesheet" href="/assets/css/style.css"> -->
    <!-- Include SweetAlert CSS -->
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"> -->
    <link rel="stylesheet" href="./node_modules/sweetalert2/dist/sweetalert2.min.css">
    

            
</head>

<!-- Hero Section -->
<section class="hero text-white py-5 mb-5" style="background: url(' /car-dealership/uploads/background/car4.png') no-repeat center center; background-size: cover;">
    <div class="container text-center">
        <h1 class="display-4" style="font-weight: 500">Find Your Dream Car Today</h1>
        <p class="lead">Browse our extensive collection of new and used vehicles</p>
        <a href="listings.php" class="btn btn-light btn-lg">View All Cars</a>
    </div>
</section>

<!-- Featured Cars -->
<section class="featured-cars mb-5">
    <div class="container">
        <h2 class="text-center mb-4">Featured Cars</h2>
        <div class="row g-4">
            <?php foreach ($featuredCars as $car): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <!-- Image section - borderless with 60% height -->
                        <div class="position-relative" style="height: 220px; overflow: hidden;">
                            <?php if (!empty($car['image_url'])): ?>
                                <img src=".<?= ltrim($car['image_url'], '.') ?>" 
                                     class="w-100 h-100 object-fit-cover" 
                                     alt="<?= htmlspecialchars($car['brand_name'] . ' ' . $car['model_name']) ?>">
                            <?php else: ?>
                                <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-muted">
                                    <i class="fas fa-car fa-3x"></i>
                                </div>
                            <?php endif; ?>
                            <!-- Condition badge -->
                            <span class="position-absolute top-0 start-0 m-2 badge bg-<?= $car['car_condition'] == 'new' ? 'success' : 'info' ?>">
                                <?= ucfirst($car['car_condition']) ?>
                            </span>
                            <!-- Price badge -->
                            <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark">
                                <?= number_format($car['price'], 2) ?> Lakhs
                            </span>
                        </div>
                        
                        <!-- Content section -->
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0"><?= htmlspecialchars($car['brand_name'] . ' ' . $car['model_name']) ?></h5>
                            </div>
                            
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                <span class="badge bg-secondary"><?= htmlspecialchars($car['engine_name']) ?></span>
                                <span class="badge bg-warning text-dark"><?= htmlspecialchars($car['year']) ?></span>
                            </div>
                            
                            <p class="card-text text-muted small mb-2">
                                <?php 
                                $desc = $car['description'];
                                // Preserve new lines with nl2br and escape for security
                                echo nl2br(htmlspecialchars(substr($desc, 0, 100)));
                                if (strlen($desc) > 100) echo '...';
                                ?>
                            </p>
                        </div>
                        
                        <!-- Footer with button -->
                        <div class="card-footer bg-white p-3 border-0">
                            <a href="car-details.php?id=<?= $car['car_id'] ?>" class="btn btn-muted w-100">
                                <i class="fas fa-info-circle me-2"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="listings.php" class="btn btn-outline-primary px-4">View All Cars</a>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories mb-5">
    <div class="container">
        <h2 class="text-center mb-4">Browse By Category</h2>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card category-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-car fa-3x mb-3"></i>
                        <h5 class="card-title">New Cars</h5>
                        <a href="./listings.php?car_condition=new" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card category-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-car-side fa-3x mb-3"></i>
                        <h5 class="card-title">Used Cars</h5>
                        <a href="./listings.php?car_condition=used" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card category-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-charging-station fa-3x mb-3"></i>
                        <h5 class="card-title">Electric Vehicles</h5>
                        <a href="./listings.php?engine_type=Electric" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card category-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-gas-pump fa-3x mb-3"></i>
                        <h5 class="card-title">Gasoline Vehicles</h5>
                        <a href="./listings.php?engine_type=Gasoline" class="stretched-link"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Popular Brands Section -->
<section class="popular-brands mb-5">
    <div class="container">
        <h2 class="text-center mb-4">Popular Brands</h2>
        <div class="row g-4">
            <?php 
            $brands = getAllBrands(6); // Get only 6 brands
            
            if (!empty($brands)) {
                foreach ($brands as $brand): ?>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card brand-card h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?php echo htmlspecialchars($brand['brand_name']); ?></h5>
                                <a href="listings.php?brand_id=<?php echo $brand['id']; ?>" class="stretched-link"></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach;
            } else {
                echo '<div class="col-12 text-center">No brands found</div>';
            }
            ?>
        </div>
    </div>
</section>

    <!-- Include SweetAlert JS -->
    <!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
    <script src="./node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script> -->
    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once 'includes/footer.php'; ?>