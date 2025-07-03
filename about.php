<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$pageTitle = "About Us";
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

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h3>About CarDeal</h3>
                </div>
                <div class="card-body">
                    <h4 class="mb-4">Your Trusted Car Dealership</h4>
                    
                    <p>Welcome to CarDeal, your premier destination for buying new and used cars. With years of experience in the automotive industry, we pride ourselves on offering high-quality vehicles at competitive prices.</p>
                    
                    <h5 class="mt-4">Our Mission</h5>
                    <p>Our mission is to provide customers with a seamless car-buying experience, offering a wide selection of vehicles, transparent pricing, and exceptional customer service.</p>
                    
                    <h5 class="mt-4">Why Choose Us?</h5>
                    <ul>
                        <li>Extensive inventory of new and pre-owned vehicles</li>
                        <li>Competitive pricing and financing options</li>
                        <li>Knowledgeable and friendly staff</li>
                        <li>Transparent and hassle-free buying process</li>
                        <li>After-sales support and service</li>
                    </ul>
                    
                    <h5 class="mt-4">Our History</h5>
                    <p>Founded in 2023, CarDeal started as a small local dealership and has grown to become one of the most trusted names in the automotive industry. Our success is built on customer satisfaction and a commitment to excellence.</p>
                    
                    <div class="text-center mt-5">
                        <a href="listings.php" class="btn btn-primary btn-lg">Browse Our Inventory</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Include SweetAlert JS -->
    <!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
    <script src="./node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script> -->
    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once 'includes/footer.php'; ?>