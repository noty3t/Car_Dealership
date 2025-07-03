<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

$pageTitle = "Manage Cars";

// Handle actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'delete':
            if (isset($_GET['id'])) {
                $carId = (int)$_GET['id'];
                $sql = "DELETE FROM Cars WHERE car_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $carId);
                if ($stmt->execute()) {
                    $_SESSION['swal'] = [
                        'title' => 'Success',
                        'text' => 'Car deleted successfully.',
                        'icon' => 'success'
                    ];
                } else {
                    $_SESSION['swal'] = [
                        'title' => 'Error',
                        'text' => 'Error deleting car: ' . $conn->error,
                        'icon' => 'error'
                    ];
                }
                header('Location: cars.php');
                exit();
            }
            break;
            
        case 'delete_brand':
            if (isset($_GET['id'])) {
                $brandId = (int)$_GET['id'];
                
                // First delete all models belonging to this brand
                $deleteModelsSql = "DELETE FROM model WHERE brand_id = ?";
                $stmt = $conn->prepare($deleteModelsSql);
                $stmt->bind_param("i", $brandId);
                $modelsDeleted = $stmt->execute();
                
                // Then delete the brand
                $sql = "DELETE FROM brand WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $brandId);
                if ($stmt->execute()) {
                    $_SESSION['swal'] = [
                        'title' => 'Success',
                        'text' => 'Brand and all its models deleted successfully.',
                        'icon' => 'success'
                    ];
                } else {
                    $_SESSION['swal'] = [
                        'title' => 'Error',
                        'text' => 'Error deleting brand: ' . $conn->error,
                        'icon' => 'error'
                    ];
                }
                header('Location: cars.php');
                exit();
            }
            break;
            
        case 'delete_model':
            if (isset($_GET['id'])) {
                $modelId = (int)$_GET['id'];
                $sql = "DELETE FROM model WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $modelId);
                if ($stmt->execute()) {
                    $_SESSION['swal'] = [
                        'title' => 'Success',
                        'text' => 'Model deleted successfully.',
                        'icon' => 'success'
                    ];
                } else {
                    $_SESSION['swal'] = [
                        'title' => 'Error',
                        'text' => 'Error deleting model: ' . $conn->error,
                        'icon' => 'error'
                    ];
                }
                header('Location: cars.php');
                exit();
            }
            break;
            
        case 'delete_color':
            if (isset($_GET['id'])) {
                $colorId = (int)$_GET['id'];
                $sql = "DELETE FROM color WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $colorId);
                if ($stmt->execute()) {
                    $_SESSION['swal'] = [
                        'title' => 'Success',
                        'text' => 'Color deleted successfully.',
                        'icon' => 'success'
                    ];
                } else {
                    $_SESSION['swal'] = [
                        'title' => 'Error',
                        'text' => 'Error deleting color: ' . $conn->error,
                        'icon' => 'error'
                    ];
                }
                header('Location: cars.php');
                exit();
            }
            break;
    }
}

// Handle form submission for adding/editing cars
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle brand addition
    if (isset($_POST['add_brand'])) {
        $brandName = sanitize($_POST['brand_name']);
        
        // Check for duplicate brand
        $checkSql = "SELECT id FROM brand WHERE brand_name = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $brandName);
        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            $_SESSION['swal'] = [
                'title' => 'Error',
                'text' => 'Brand "'.$brandName.'" already exists!',
                'icon' => 'error'
            ];
            header('Location: cars.php');
            exit();
        }
        
        $sql = "INSERT INTO brand (brand_name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $brandName);
        if ($stmt->execute()) {
            $_SESSION['swal'] = [
                'title' => 'Success',
                'text' => 'Brand "'.$brandName.'" added successfully.',
                'icon' => 'success'
            ];
        } else {
            $_SESSION['swal'] = [
                'title' => 'Error',
                'text' => 'Error adding brand: ' . $conn->error,
                'icon' => 'error'
            ];
        }
        header('Location: cars.php');
        exit();
    }
    
    // Handle model addition
    if (isset($_POST['add_model'])) {
        $modelName = sanitize($_POST['model_name']);
        $brandId = (int)$_POST['brand_id'];
        
        // Check for duplicate model
        $checkSql = "SELECT id FROM model WHERE model_name = ? AND brand_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("si", $modelName, $brandId);
        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            $_SESSION['swal'] = [
                'title' => 'Error',
                'text' => 'Model "'.$modelName.'" already exists for this brand!',
                'icon' => 'error'
            ];
            header('Location: cars.php');
            exit();
        }
        
        $sql = "INSERT INTO model (model_name, brand_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $modelName, $brandId);
        if ($stmt->execute()) {
            $_SESSION['swal'] = [
                'title' => 'Success',
                'text' => 'Model "'.$modelName.'" added successfully.',
                'icon' => 'success'
            ];
        } else {
            $_SESSION['swal'] = [
                'title' => 'Error',
                'text' => 'Error adding model: ' . $conn->error,
                'icon' => 'error'
            ];
        }
        header('Location: cars.php');
        exit();
    }
    
    // Handle color addition
    if (isset($_POST['add_color'])) {
        $colorName = sanitize($_POST['color_name']);
        
        // Check for duplicate color
        $checkSql = "SELECT id FROM color WHERE color = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $colorName);
        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            $_SESSION['swal'] = [
                'title' => 'Error',
                'text' => 'Color "'.$colorName.'" already exists!',
                'icon' => 'error'
            ];
            header('Location: cars.php');
            exit();
        }
        
        $sql = "INSERT INTO color (color) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $colorName);
        if ($stmt->execute()) {
            $_SESSION['swal'] = [
                'title' => 'Success',
                'text' => 'Color "'.$colorName.'" added successfully.',
                'icon' => 'success'
            ];
        } else {
            $_SESSION['swal'] = [
                'title' => 'Error',
                'text' => 'Error adding color: ' . $conn->error,
                'icon' => 'error'
            ];
        }
        header('Location: cars.php');
        exit();
    }
    
    // Handle car addition/editing
    if (isset($_POST['add_car']) || isset($_POST['edit_car'])) {
        $carId = isset($_POST['car_id']) ? (int)$_POST['car_id'] : 0;
        $modelId = (int)$_POST['model_id'];
        $engineId = (int)$_POST['engine_type'];
        $colorId = (int)$_POST['color'];
        $year = (int)$_POST['year'];
        $car_condition = strtolower(sanitize($_POST['car_condition']));
        $price = (float)$_POST['price'];
        $quantity = (int)$_POST['quantity'];
        $description = $_POST['description'];
        $mileage = isset($_POST['mileage']) ? (int)$_POST['mileage'] : null;
        
        // Check for duplicate car
        $excludeClause = $carId > 0 ? " AND car_id != ?" : "";
        $checkSql = "SELECT car_id FROM Cars WHERE model_id = ? AND engine_id = ? AND year = ? 
                    AND color_id = ? AND car_condition = ?" . $excludeClause;
        $checkStmt = $conn->prepare($checkSql);
        
        if ($carId > 0) {
            $checkStmt->bind_param("iiiisi", $modelId, $engineId, $year, $colorId, $car_condition, $carId);
        } else {
            $checkStmt->bind_param("iiiis", $modelId, $engineId, $year, $colorId, $car_condition);
        }
        
        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            $_SESSION['swal'] = [
                'title' => 'Error',
                'text' => 'A car with these specifications already exists!',
                'icon' => 'error'
            ];
            header('Location: cars.php');
            exit();
        }
        
        // Handle image upload
        $imageUrl = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/images/cars/';
            $uploadFile = $uploadDir . basename($_FILES['image']['name']);
            
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($imageFileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    $imageUrl = '/assets/images/cars/' . basename($_FILES['image']['name']);
                } else {
                    $_SESSION['swal'] = [
                        'title' => 'Error',
                        'text' => 'Error uploading image file.',
                        'icon' => 'error'
                    ];
                    header('Location: cars.php');
                    exit();
                }
            } else {
                $_SESSION['swal'] = [
                    'title' => 'Error',
                    'text' => 'Only JPG, JPEG, PNG & GIF files are allowed.',
                    'icon' => 'error'
                ];
                header('Location: cars.php');
                exit();
            }
        } elseif ($carId > 0 && isset($_POST['existing_image'])) {
            $imageUrl = $_POST['existing_image'];
        }
        
        if ($carId > 0) {
            // Update existing car
            if ($imageUrl) {
                $sql = "UPDATE Cars SET model_id = ?, engine_id = ?, year = ?, 
                        car_condition = ?, price = ?, quantity = ?, description = ?, image_url = ?, 
                        mileage = ?, color_id = ? WHERE car_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiisdisssii", $modelId, $engineId, $year, $car_condition, 
                                 $price, $quantity, $description, $imageUrl, $mileage, $colorId, $carId);
            } else {
                $sql = "UPDATE Cars SET model_id = ?, engine_id = ?, year = ?, 
                        car_condition = ?, price = ?, quantity = ?, description = ?, mileage = ?, 
                        color_id = ? WHERE car_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiisdisiii", $modelId, $engineId, $year, $car_condition, 
                                 $price, $quantity, $description, $mileage, $colorId, $carId);
            }
        } else {
            // Add new car
            $sql = "INSERT INTO Cars (model_id, engine_id, year, car_condition, price, quantity,
                    description, image_url, mileage, color_id, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiisdsssii", $modelId, $engineId, $year, $car_condition, 
                             $price, $quantity, $description, $imageUrl, $mileage, $colorId);
        }
        
        if ($stmt->execute()) {
            $_SESSION['swal'] = [
                'title' => $carId > 0 ? 'Car Updated' : 'Car Added',
                'text' => $carId > 0 ? 'Car updated successfully.' : 'Car added successfully.',
                'icon' => 'success'
            ];
        } else {
            $_SESSION['swal'] = [
                'title' => 'Error',
                'text' => 'Error ' . ($carId > 0 ? 'updating' : 'adding') . ' car: ' . $conn->error,
                'icon' => 'error'
            ];
        }
        header('Location: cars.php');
        exit();
    }
}

// Get all data needed for the page
$cars = [];
$sql = "SELECT c.*, b.brand_name, m.model_name, e.engine_name, co.color 
        FROM Cars c
        LEFT JOIN model m ON c.model_id = m.id
        LEFT JOIN brand b ON m.brand_id = b.id
        LEFT JOIN engine_type e ON c.engine_id = e.id
        LEFT JOIN color co ON c.color_id = co.id
        ORDER BY c.created_at DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
}

// Get all brands
$brands = [];
$sql = "SELECT * FROM brand ORDER BY brand_name";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }
}

// Get all models
$models = [];
$sql = "SELECT m.*, b.brand_name FROM model m LEFT JOIN brand b ON m.brand_id = b.id ORDER BY b.brand_name, m.model_name";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $models[] = $row;
    }
}

// Get all colors
$colors = [];
$sql = "SELECT * FROM color ORDER BY color";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $colors[] = $row;
    }
}

// Get all engine types
$engineTypes = [];
$sql = "SELECT * FROM engine_type";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $engineTypes[] = $row;
    }
}

// Get car for editing
$editCar = null;
$isEditMode = false;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $carId = (int)$_GET['id'];
    $sql = "SELECT c.*, m.brand_id, b.brand_name, m.model_name, e.engine_name, co.color 
            FROM Cars c
            LEFT JOIN model m ON c.model_id = m.id
            LEFT JOIN brand b ON m.brand_id = b.id
            LEFT JOIN engine_type e ON c.engine_id = e.id
            LEFT JOIN color co ON c.color_id = co.id
            WHERE c.car_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $carId);
    $stmt->execute();
    $result = $stmt->get_result();
    $editCar = $result->fetch_assoc();
    $isEditMode = true;
}

require_once '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Bootstrap CSS -->
    <link href="../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css">
     
    <!-- Include SweetAlert CSS -->
    <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css">
    
    <!-- Custom CSS -->
    <style>
        .current-image-container {
            text-align: center;
            margin-top: 10px;
        }
        .current-image {
            max-width: 200px;
            max-height: 150px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }
        .current-image-label {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .badge-new {
            background-color: #28a745;
        }
        .badge-used {
            background-color: #17a2b8;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Cars</h2>
        <div>
            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#carModal" id="addCarBtn">
                <i class="fas fa-plus"></i> Add New Car
            </button>
            <button class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#brandModal">
                <i class="fas fa-plus"></i> Add Brand
            </button>
            <button class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#modelModal">
                <i class="fas fa-plus"></i> Add Model
            </button>
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#colorModal">
                <i class="fas fa-plus"></i> Add Color
            </button>
        </div>
    </div>
    
    <!-- Tabs for different sections -->
    <ul class="nav nav-tabs mb-4" id="managementTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="cars-tab" data-bs-toggle="tab" data-bs-target="#cars" type="button" role="tab">Cars</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="brand-models-tab" data-bs-toggle="tab" data-bs-target="#brand-models" type="button" role="tab">Brand & Models</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="colors-tab" data-bs-toggle="tab" data-bs-target="#colors" type="button" role="tab">Colors</button>
        </li>
    </ul>
    
    <div class="tab-content" id="managementTabsContent">
        
        <!-- Cars Tab -->
        <div class="tab-pane fade show active" id="cars" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Brand & Model</th>
                            <th>Year</th>
                            <th>Engine</th>
                            <th>Condition</th>
                            <th>Color</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($cars as $car): ?>
                            <tr>
                                <td><?php echo $i++ ?></td>
                                <td>
                                    <?php if (!empty($car['image_url'])): ?>
                                        <img src="..<?= $car['image_url']?>" 
                                             alt="<?php echo $car['brand_name'] . ' ' . $car['model_name']; ?>" 
                                             style="width: 80px; height: auto;">
                                    <?php else: ?>
                                        <span class="text-muted">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo $car['brand_name']; ?></strong><br>
                                    <?php echo $car['model_name']; ?>
                                </td>
                                <td><?php echo $car['year']; ?></td>
                                <td><?php echo $car['engine_name']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($car['car_condition']) == 'new' ? 'new' : 'used'; ?>">
                                        <?php echo ucfirst($car['car_condition']); ?>
                                    </span>
                                </td>
                                <td><?php echo $car['color'] ?? 'N/A'; ?></td>
                                <td><?php echo number_format($car['price'], 2); ?> Lakhs</td>
                                <td><?php echo $car['quantity']; ?></td>
                                <td>
                                    <a href="cars.php?action=edit&id=<?php echo $car['car_id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="cars.php?action=delete&id=<?php echo $car['car_id']; ?>" 
                                       class="btn btn-sm btn-outline-danger delete-car-btn" 
                                       data-car-name="<?php echo htmlspecialchars($car['brand_name'] . ' ' . $car['model_name']); ?>">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Brand & Models Tab -->
        <div class="tab-pane fade" id="brand-models" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Brand</th>
                            <th>Models</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        // Group models by brand
                        $groupedModels = [];
                        foreach ($models as $model) {
                            $brandId = $model['brand_id'];
                            if (!isset($groupedModels[$brandId])) {
                                $groupedModels[$brandId] = [
                                    'brand_name' => $model['brand_name'],
                                    'brand_id' => $brandId,
                                    'models' => []
                                ];
                            }
                            $groupedModels[$brandId]['models'][] = $model;
                        }
                        
                        foreach ($groupedModels as $brandId => $brandData): ?>
                            <tr>
                                <td><?php echo $i++ ?></td>
                                <td>
                                    <strong><?php echo $brandData['brand_name']; ?></strong>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                id="dropdownBrand<?php echo $brandId; ?>" data-bs-toggle="dropdown" 
                                                aria-expanded="false">
                                            View Models (<?php echo count($brandData['models']); ?>)
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownBrand<?php echo $brandId; ?>">
                                            <?php foreach ($brandData['models'] as $model): ?>
                                                <li>
                                                    <span class="dropdown-item-text">
                                                        <?php echo $model['model_name']; ?>
                                                        <a href="cars.php?action=delete_model&id=<?php echo $model['id']; ?>" 
                                                        class="btn btn-sm btn-outline-danger float-end ms-2 delete-model-btn" 
                                                        data-model-name="<?php echo htmlspecialchars($model['model_name']); ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <a href="cars.php?action=delete_brand&id=<?php echo $brandId; ?>" 
                                    class="btn btn-sm btn-outline-danger delete-brand-btn" 
                                    data-brand-name="<?php echo htmlspecialchars($brandData['brand_name']); ?>">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Colors Tab -->
        <div class="tab-pane fade" id="colors" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Color</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        foreach ($colors as $color): ?>
                            <tr>
                                <td><?php echo $i++ ?></td>
                                <td><?php echo $color['color']; ?></td>
                                <td>
                                    <a href="cars.php?action=delete_color&id=<?php echo $color['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger delete-color-btn" 
                                       data-color-name="<?php echo htmlspecialchars($color['color']); ?>">
                                        <i class="fas fa-trash"></i> Delete
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

<!-- Car Modal -->
<div class="modal fade" id="carModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $editCar ? 'Edit Car' : 'Add New Car'; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="modalCloseBtn"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="cars.php" enctype="multipart/form-data" id="carForm">
                    <input type="hidden" name="car_id" value="<?php echo $editCar ? $editCar['car_id'] : ''; ?>">
                    <?php if ($editCar && isset($editCar['image_url']) && !empty($editCar['image_url'])): ?>
                        <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($editCar['image_url']); ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="brand" class="form-label">Brand</label>
                            <select class="form-select" id="brand" name="brand" required>
                                <option value="">Select Brand</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo $brand['id']; ?>" 
                                        <?php echo ($isEditMode && isset($editCar['brand_id']) && $editCar['brand_id'] == $brand['id'] ? 'selected' : ''); ?>>
                                        <?php echo $brand['brand_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="model_id" class="form-label">Model</label>
                            <select class="form-select" id="model_id" name="model_id" required>
                                <option value="">Select Model</option>
                                <?php foreach ($models as $model): ?>
                                    <option value="<?php echo $model['id']; ?>" 
                                        data-brand="<?php echo $model['brand_id']; ?>"
                                        <?php echo ($isEditMode && isset($editCar['model_id']) && $editCar['model_id'] == $model['id'] ? 'selected' : ''); ?>>
                                        <?php echo $model['model_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="year" class="form-label">Year</label>
                            <input type="number" class="form-control" id="year" name="year" 
                                   value="<?php echo $editCar ? $editCar['year'] : ''; ?>" required min="1900" max="<?php echo date('Y') + 1; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="engine_type" class="form-label">Engine Type</label>
                            <select class="form-select" id="engine_type" name="engine_type" required>
                                <?php foreach ($engineTypes as $engine): ?>
                                    <option value="<?php echo $engine['id']; ?>" 
                                        <?php echo $editCar && $editCar['engine_id'] == $engine['id'] ? 'selected' : ''; ?>>
                                        <?php echo $engine['engine_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                            
                        <div class="col-md-4 mb-3">
                            <label for="car_condition" class="form-label">Car Condition</label>
                            <select class="form-select" id="car_condition" name="car_condition" required>
                                <option value="new" <?php echo ($isEditMode && isset($editCar['car_condition']) && strtolower($editCar['car_condition']) == 'new' ? 'selected' : ''); ?>>New</option>
                                <option value="used" <?php echo ($isEditMode && isset($editCar['car_condition']) && strtolower($editCar['car_condition']) == 'used' ? 'selected' : ''); ?>>Used</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">Price (Lakhs)</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" 
                                   value="<?php echo $editCar ? $editCar['price'] : ''; ?>" required min="0.01">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                   value="<?php echo $editCar ? $editCar['quantity'] : '1'; ?>" min="1" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="color" class="form-label">Color</label>
                            <select class="form-select" id="color" name="color" required>
                                <?php foreach ($colors as $color): ?>
                                    <option value="<?php echo $color['id']; ?>" 
                                        <?php echo $editCar && $editCar['color_id'] == $color['id'] ? 'selected' : ''; ?>>
                                        <?php echo $color['color']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3" id="mileageField" style="display: none;">
                            <label for="mileage" class="form-label">Mileage (for used cars)</label>
                            <input type="number" class="form-control" id="mileage" name="mileage" 
                                value="<?php echo $editCar ? $editCar['mileage'] : ''; ?>" min="0">
                        </div>
                    </div>
                        
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required><?php echo $editCar ? $editCar['description'] : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Car Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <?php if ($editCar && isset($editCar['image_url']) && !empty($editCar['image_url'])): ?>
                            <div class="current-image-container mt-2">
                                <img src="../<?php echo htmlspecialchars($editCar['image_url']); ?>" 
                                    alt="Current Car Image" 
                                    class="current-image">
                                <p class="current-image-label">
                                    Current image preview
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="<?php echo $editCar ? 'edit_car' : 'add_car'; ?>">Save Car</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Brand Modal -->
<div class="modal fade" id="brandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Brand</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="cars.php" id="brandForm">
                    <div class="mb-3">
                        <label for="brand_name" class="form-label">Brand Name</label>
                        <input type="text" class="form-control" id="brand_name" name="brand_name" required minlength="2">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_brand">Save Brand</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Model Modal -->
<div class="modal fade" id="modelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Model</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="cars.php" id="modelForm">
                    <div class="mb-3">
                        <label for="brand_id" class="form-label">Brand</label>
                        <select class="form-select" id="brand_id" name="brand_id" required>
                            <option value="">Select Brand</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?php echo $brand['id']; ?>"><?php echo $brand['brand_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="model_name" class="form-label">Model Name</label>
                        <input type="text" class="form-control" id="model_name" name="model_name" required minlength="2">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_model">Save Model</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Color Modal -->
<div class="modal fade" id="colorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Color</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="cars.php" id="colorForm">
                    <div class="mb-3">
                        <label for="color_name" class="form-label">Color Name</label>
                        <input type="text" class="form-control" id="color_name" name="color_name" required minlength="2">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_color">Save Color</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include SweetAlert JS -->
<script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
<script>
// Show SweetAlert notifications from PHP
<?php if (isset($_SESSION['swal'])): ?>
    Swal.fire({
        title: '<?php echo $_SESSION['swal']['title']; ?>',
        text: '<?php echo $_SESSION['swal']['text']; ?>',
        icon: '<?php echo $_SESSION['swal']['icon']; ?>',
        confirmButtonText: 'OK',
        timer: <?php echo $_SESSION['swal']['icon'] === 'success' ? '3000' : '5000'; ?>,
        timerProgressBar: true
    });
    <?php unset($_SESSION['swal']); ?>
<?php endif; ?>

// Main DOMContentLoaded handler
document.addEventListener('DOMContentLoaded', function() {
    // ===== Delete Confirmation Handlers =====
    // Car delete
    document.querySelectorAll('.delete-car-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Delete Car',
                html: `Are you sure you want to delete <strong>${this.dataset.carName}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = this.href;
            });
        });
    });
    
    // Brand delete (with cascade warning)
    document.querySelectorAll('.delete-brand-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Delete Brand',
                html: `Are you sure you want to delete <strong>${this.dataset.brandName}</strong> and all its models?<br>
                      <span class="text-danger">All associated cars will be deleted!</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = this.href;
            });
        });
    });
    
    // Model delete (with cascade warning)
    document.querySelectorAll('.delete-model-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Delete Model',
                html: `Are you sure you want to delete <strong>${this.dataset.modelName}</strong>?<br>
                      <span class="text-danger">All associated cars will be deleted!</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = this.href;
            });
        });
    });
    
    // Color delete
    document.querySelectorAll('.delete-color-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Delete Color',
                html: `Are you sure you want to delete <strong>${this.dataset.colorName}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = this.href;
            });
        });
    });
    
    // ===== Form Field Logic =====
    // Condition-based mileage display
    const conditionSelect = document.getElementById('car_condition');
    const mileageField = document.getElementById('mileageField');
    
    function updateMileageField() {
        const isUsed = conditionSelect.value === 'used';
        mileageField.style.display = isUsed ? 'block' : 'none';
        document.getElementById('mileage').toggleAttribute('required', isUsed);
    }
    
    // Brand-model relationship
    const brandSelect = document.getElementById('brand');
    const modelSelect = document.getElementById('model_id');
    
    function filterModels() {
        const brandId = brandSelect.value;
        let hasValidSelection = false;
        
        modelSelect.querySelectorAll('option').forEach(option => {
            const show = option.value === '' || option.dataset.brand === brandId;
            option.style.display = show ? 'block' : 'none';
            if (option.selected && show) hasValidSelection = true;
        });
        
        if (!hasValidSelection) modelSelect.value = '';
    }
    
    // Initialize fields
    updateMileageField();
    conditionSelect.addEventListener('change', updateMileageField);
    brandSelect.addEventListener('change', filterModels);
    
    // ===== Form Validation =====
    // Car form validation
    document.getElementById('carForm')?.addEventListener('submit', function(e) {
        const year = parseInt(document.getElementById('year').value);
        const currentYear = new Date().getFullYear();
        
        if (year < 1900 || year > currentYear + 1) {
            e.preventDefault();
            Swal.fire('Invalid Year', `Year must be between 1900-${currentYear + 1}`, 'error');
            return false;
        }
        
        // Other validations (price, quantity, etc)...
        // Keep your existing validation logic here
    });

    // Duplicate checks (brands, models, etc)...
    // Keep your existing duplicate validation logic here
    
    // ===== Textarea Line Break Preservation =====
    const descriptionTextarea = document.getElementById('description');
    if (descriptionTextarea) {
        descriptionTextarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.execCommand('insertLineBreak');
            }
        });
    }
    
    // ===== Edit Mode Initialization =====
    <?php if ($isEditMode && $editCar): ?>
        // Initialize edit form values
        brandSelect.value = <?= $editCar['brand_id'] ?? 0 ?>;
        filterModels();
        setTimeout(() => {
            modelSelect.value = <?= $editCar['model_id'] ?? 0 ?>;
            conditionSelect.value = '<?= strtolower($editCar['car_condition'] ?? 'new') ?>';
            updateMileageField();
        }, 100);
        
        // Show edit modal
        new bootstrap.Modal(document.getElementById('carModal')).show();
        
        // Clear edit state on modal close
        document.getElementById('modalCloseBtn').addEventListener('click', () => {
            window.location.href = 'cars.php';
        });
    <?php endif; ?>
    
    // ===== Add New Car Button Handler =====
    document.getElementById('addCarBtn')?.addEventListener('click', function() {
        document.getElementById('carForm').reset();
        brandSelect.value = '';
        modelSelect.value = '';
        conditionSelect.value = 'new';
        updateMileageField();
        
        // Reset image preview
        const imagePreview = document.querySelector('.current-image-container');
        if (imagePreview) imagePreview.style.display = 'none';
        
        // Update modal UI
        document.querySelector('#carModal .modal-title').textContent = 'Add New Car';
        document.querySelector('#carForm button[type="submit"]').textContent = 'Save Car';
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>