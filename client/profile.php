<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

$pageTitle = "My Profile";

// Get current user data
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    $errors = [];
    
    // Validate username
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (strlen($username) < 4) {
        $errors[] = "Username must be at least 4 characters long.";
    }
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    
    // Validate phone number
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match('/^\+?[0-9]{10,15}$/', $phone)) {
        $errors[] = "Please enter a valid phone number.";
    }
    
    // Check if username or email already exists (excluding current user)
    if (empty($errors)) {
        $sql = "SELECT * FROM users WHERE (username = ? OR email = ?) AND user_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $email, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $existingUser = $result->fetch_assoc();
            if ($existingUser['username'] === $username) {
                $errors[] = "Username already taken.";
            }
            if ($existingUser['email'] === $email) {
                $errors[] = "Email already registered.";
            }
        }
    }
    
    // Handle password change if provided
    $passwordChanged = false;
    if (!empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword)) {
        if (empty($currentPassword)) {
            $errors[] = "Current password is required to change password.";
        } elseif (!password_verify($currentPassword, $user['password'])) {
            $errors[] = "Current password is incorrect.";
        } elseif (empty($newPassword)) {
            $errors[] = "New password is required.";
        } elseif (strlen($newPassword) < 6) {
            $errors[] = "New password must be at least 6 characters long.";
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = "New passwords do not match.";
        } else {
            $passwordChanged = true;
        }
    }
    
    // Update profile if no errors
    if (empty($errors)) {
        if ($passwordChanged) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username = ?, email = ?, ph_no = ?, password = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $username, $email, $phone, $hashedPassword, $_SESSION['user_id']);
        } else {
            $sql = "UPDATE users SET username = ?, email = ?, ph_no = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $username, $email, $phone, $_SESSION['user_id']);
        }
        
        if ($stmt->execute()) {
            // Update session data
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            
            redirect('profile.php', 'Profile updated successfully.');
        } else {
            $errors[] = "Error updating profile. Please try again.";
        }
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
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>My Profile</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <p class="mb-1"><?php echo $error; ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="profile.php">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['ph_no']); ?>" required
                                   placeholder="Phone Number is required" pattern="\+?[0-9]{10,15}">
                            
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">Change Password</h5>
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password">
                            <small class="text-muted">Leave blank to keep current password</small>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
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