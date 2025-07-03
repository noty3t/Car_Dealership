<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Verify admin access
// if ($_SESSION['user_role'] !== 'admin') {
//     header("Location: ../index.php");
//     exit();
// }

$pageTitle = "Manage Users";

// Handle form actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    // Add User
    if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $password = $_POST['password'];
        $role = $_POST['role'];
        
        // Validate inputs
        $errors = [];
        if (empty($username)) $errors[] = "Username is required";
        if (empty($email)) $errors[] = "Email is required";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
        if (empty($phone)) $errors[] = "Phone number is required";
        if (!preg_match('/^\+?[0-9]{10,15}$/', $phone)) $errors[] = "Invalid phone number format";
        if (empty($password)) $errors[] = "Password is required";
        if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters";
        
        if (empty($errors)) {
            // Check if username or email already exists
            $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
            $checkStmt->bind_param("ss", $username, $email);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = "Username or email already exists";
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $insertStmt = $conn->prepare("INSERT INTO users (username, email, ph_no, password, role) VALUES (?, ?, ?, ?, ?)");
                $insertStmt->bind_param("sssss", $username, $email, $phone, $hashedPassword, $role);
                
                if ($insertStmt->execute()) {
                    $_SESSION['success_message'] = "User added successfully";
                    header("Location: users.php");
                    exit();
                } else {
                    $errors[] = "Error adding user: " . $conn->error;
                }
            }
        }
    }
    
    // Edit User
    if ($action === 'edit' && isset($_GET['id'])) {
        $userId = (int)$_GET['id'];
        
        // Fetch user data for editing
        $stmt = $conn->prepare("SELECT user_id, username, email, ph_no, role FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            $_SESSION['error_message'] = "User not found";
            header("Location: users.php");
            exit();
        }
        
        // Prevent editing admin user with ID 1
        if ($user['user_id'] === 1) {
            $_SESSION['error_message'] = "Cannot edit primary admin user";
            header("Location: users.php");
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $role = $_POST['role'];
            $changePassword = isset($_POST['change_password']);
            $password = $changePassword ? $_POST['password'] : null;
            
            // Validate inputs
            $errors = [];
            if (empty($username)) $errors[] = "Username is required";
            if (empty($email)) $errors[] = "Email is required";
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
            if (empty($phone)) $errors[] = "Phone number is required";
            if (!preg_match('/^\+?[0-9]{10,15}$/', $phone)) $errors[] = "Invalid phone number format";
            if ($changePassword && strlen($password) < 8) $errors[] = "Password must be at least 8 characters";
            
            if (empty($errors)) {
                // Check if username or email already exists (excluding current user)
                $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
                $checkStmt->bind_param("ssi", $username, $email, $userId);
                $checkStmt->execute();
                $result = $checkStmt->get_result();
                
                if ($result->num_rows > 0) {
                    $errors[] = "Username or email already exists";
                } else {
                    // Update user
                    if ($changePassword) {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $updateStmt = $conn->prepare("UPDATE users SET username = ?, email = ?, ph_no = ?, password = ?, role = ? WHERE user_id = ?");
                        $updateStmt->bind_param("sssssi", $username, $email, $phone, $hashedPassword, $role, $userId);
                    } else {
                        $updateStmt = $conn->prepare("UPDATE users SET username = ?, email = ?, ph_no = ?, role = ? WHERE user_id = ?");
                        $updateStmt->bind_param("ssssi", $username, $email, $phone, $role, $userId);
                    }
                    
                    if ($updateStmt->execute()) {
                        $_SESSION['success_message'] = "User updated successfully";
                        header("Location: users.php");
                        exit();
                    } else {
                        $errors[] = "Error updating user: " . $conn->error;
                    }
                }
            }
        }
    }
    
    // Delete User
    if ($action === 'delete' && isset($_GET['id'])) {
        $userId = (int)$_GET['id'];
        
        // Prevent deleting admin user with ID 1
        if ($userId === 1) {
            $_SESSION['error_message'] = "Cannot delete primary admin user";
            header("Location: users.php");
            exit();
        }
        
        $deleteStmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $deleteStmt->bind_param("i", $userId);
        
        if ($deleteStmt->execute()) {
            $_SESSION['success_message'] = "User deleted successfully";
        } else {
            $_SESSION['error_message'] = "Error deleting user";
        }
        
        header("Location: users.php");
        exit();
    }
}

// Pagination setup
$recordsPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $recordsPerPage;

// Base query
$query = "SELECT user_id, username, email, ph_no, role, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?";
$countQuery = "SELECT COUNT(*) FROM users";

// Get total records count
$stmt = $conn->prepare($countQuery);
$stmt->execute();
$totalRecords = $stmt->get_result()->fetch_row()[0];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Get paginated results
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $recordsPerPage, $offset);
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

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

<div class="container-fluid px-4 mt-4">
    <?php if (isset($action) && ($action === 'add' || ($action === 'edit' && isset($user)))): ?>
        <!-- Add/Edit User Form -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h4 class="mb-0"><?php echo $action === 'add' ? 'Add New User' : 'Edit User'; ?></h4>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="users.php?action=<?php echo $action; ?><?php echo isset($user) ? '&id=' . $user['user_id'] : ''; ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo isset($user) ? htmlspecialchars($user['username']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo isset($user) ? htmlspecialchars($user['email']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="<?php echo isset($user) ? htmlspecialchars($user['ph_no']) : ''; ?>" required
                               placeholder="+959123456789" pattern="\+?[0-9]{10,15}">
                        <small class="text-muted">Format: +959123456789 or 09123456789</small>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="admin" <?php echo (isset($user) && $user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="client" <?php echo (isset($user) && $user['role'] === 'client') ? 'selected' : ''; ?>>Client</option>
                        </select>
                    </div>
                    <?php if ($action === 'add'): ?>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    <?php else: ?>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="change_password" name="change_password">
                            <label class="form-check-label" for="change_password">Change Password</label>
                        </div>
                        <div class="mb-3 password-field" style="display: none;">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between">
                        <a href="users.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary"><?php echo $action === 'add' ? 'Add User' : 'Update User'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <!-- User List -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Manage Users</h2>
            <a href="users.php?action=add" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add User
            </a>
        </div>

        <!-- Users Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">No users found</td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                    $i=1;
                                    foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['ph_no']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <?php if ($user['user_id'] !== 1): ?>
                                                    <a href="users.php?action=edit&id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="GET" action="users.php" class="d-inline" id="deleteForm<?php echo $user['user_id']; ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo $user['user_id']; ?>">
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-btn" 
                                                                data-id="<?php echo $user['user_id']; ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="card-footer bg-white border-0 py-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    // Show/hide password field when checkbox is clicked
    document.getElementById('change_password')?.addEventListener('change', function() {
        const passwordField = document.querySelector('.password-field');
        if (this.checked) {
            passwordField.style.display = 'block';
            passwordField.querySelector('input').required = true;
        } else {
            passwordField.style.display = 'none';
            passwordField.querySelector('input').required = false;
        }
    });

    // SweetAlert for delete confirmation
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            
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
                    document.getElementById(`deleteForm${userId}`).submit();
                }
            });
        });
    });

    // Display success/error messages with SweetAlert
    <?php if (isset($_SESSION['success_message'])): ?>
        Swal.fire({
            icon: 'success',
            title: '<?php echo $_SESSION['success_message']; ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        Swal.fire({
            icon: 'error',
            title: '<?php echo $_SESSION['error_message']; ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
</script>

<!-- Include SweetAlert JS -->
<script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

<!-- Bootstrap JS -->
<script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once '../includes/footer.php'; ?>