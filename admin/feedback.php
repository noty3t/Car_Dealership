<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

$pageTitle = "Customer Feedback";

// Handle feedback deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_feedback'])) {
    if (!isAdmin() || !isset($_POST['feedback_id'])) {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Unauthorized action.'];
        header("Location: feedback.php");
        exit();
    }

    $feedbackId = (int)$_POST['feedback_id'];

    // Delete the feedback
    $sql = "DELETE FROM Feedback WHERE feedback_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $feedbackId);

    if ($stmt->execute()) {
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Feedback deleted successfully.'];
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error deleting feedback.'];
    }

    header("Location: feedback.php");
    exit();
}

// Get all feedback
$feedback = [];
$sql = "SELECT f.feedback_id, f.feedback_title, f.feedback_desc, f.feedback_date, u.username 
        FROM Feedback f
        JOIN Users u ON f.user_id = u.user_id
        ORDER BY f.feedback_date DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedback[] = $row;
    }
}

require_once '../includes/header.php';
?>
<head>
    <!-- Bootstrap CSS -->
    <link href="../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css">
     
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css">
</head>

<div class="container mt-4">
    <h2 class="mb-4">Customer Feedback</h2>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Title</th>
                    <th>Feedback</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i=1;
                foreach ($feedback as $item): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $item['username']; ?></td>
                        <td><?php echo $item['feedback_title']; ?></td>
                        <td><?php echo substr($item['feedback_desc'], 0, 50) . (strlen($item['feedback_desc']) > 50 ? '...' : ''); ?></td>
                        <td><?php echo date('M j, Y', strtotime($item['feedback_date'])); ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary view-feedback-btn" 
                                        data-feedback-id="<?php echo $item['feedback_id']; ?>"
                                        data-bs-toggle="modal" data-bs-target="#feedbackModal">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <?php if (isAdmin()): ?>
                                <form method="POST" action="feedback.php" class="d-inline" id="deleteForm<?php echo $item['feedback_id']; ?>">
                                    <input type="hidden" name="feedback_id" value="<?php echo $item['feedback_id']; ?>">
                                    <input type="hidden" name="delete_feedback" value="1">
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-feedback-btn" 
                                            data-id="<?php echo $item['feedback_id']; ?>">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Feedback Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="feedbackModalBody">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Include SweetAlert JS -->
<script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
<!-- Bootstrap JS -->
<script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.querySelectorAll('.view-feedback-btn').forEach(button => {
    button.addEventListener('click', function() {
        const feedbackId = this.getAttribute('data-feedback-id');
        
        fetch(`../process/get-feedback.php?id=${feedbackId}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('feedbackModalBody').innerHTML = data;
            });
    });
});

// SweetAlert for delete confirmation
document.querySelectorAll('.delete-feedback-btn').forEach(button => {
    button.addEventListener('click', function() {
        const feedbackId = this.getAttribute('data-id');
        
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
                document.getElementById(`deleteForm${feedbackId}`).submit();
            }
        });
    });
});

// Display toast messages from PHP
<?php if (isset($_SESSION['toast'])): ?>
    Swal.fire({
        icon: '<?php echo $_SESSION['toast']['type']; ?>',
        title: '<?php echo $_SESSION['toast']['message']; ?>',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
    <?php unset($_SESSION['toast']); ?>
<?php endif; ?>
</script>

<?php require_once '../includes/footer.php'; ?>