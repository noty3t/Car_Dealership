<?php
$common_url = "/car-dealership";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Car Dealership'; ?></title>
    <!-- Bootstrap CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="./node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    
    <!-- Font Awesome -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <!-- <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css"> -->
     
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <!-- Include SweetAlert CSS -->
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"> -->
    <!-- <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css"> -->
    
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $common_url ?>../index.php">CarDeal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $common_url ?>/index.php">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="<?php echo $common_url ?>/listings.php" >
                            CarList
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $common_url ?> /news.php">Car News</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $common_url ?> /about.php">About Us</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $common_url . (isAdmin() ? '/admin/' : '/client/') ?>dashboard.php">Dashboard</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <form class="d-flex me-3" action="<?php echo $common_url ?> ./listings.php" method="GET">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search cars...">
                    <button class="btn btn-outline-light" type="submit">Search</button>
                </form>
                <div class="d-flex">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo $common_url ?>/logout.php" class="btn btn-outline-light">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo $common_url ?> /login.php" class="btn btn-outline-light me-2">Login</a>
                        <a href="<?php echo $common_url ?> /register.php" class="btn btn-primary">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php echo displayMessage(); ?>
     </div> <!-- Close container from header -->