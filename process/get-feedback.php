<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!isAdmin() || !isset($_GET['id'])) {
    die("Access denied.");
}

$feedbackId = (int)$_GET['id'];

$sql = "SELECT f.*, u.username 
        FROM Feedback f
        JOIN Users u ON f.user_id = u.user_id
        WHERE f.feedback_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $feedbackId);
$stmt->execute();
$result = $stmt->get_result();
$feedback = $result->fetch_assoc();

if (!$feedback) {
    die("Feedback not found.");
}
?>

<div class="row">
    <div class="col-md-12">
        <h4><?php echo htmlspecialchars($feedback['feedback_title']); ?></h4>
        <p class="text-muted">
            Submitted by <?php echo htmlspecialchars($feedback['username']); ?> on 
            <?php echo date('M j, Y H:i', strtotime($feedback['feedback_date'])); ?>
        </p>
        
        <div class="card mb-3">
            <div class="card-body">
                <p class="card-text"><?php echo nl2br(htmlspecialchars($feedback['feedback_desc'])); ?></p>
            </div>
        </div>
        
        <div class="text-end">
            <form method="POST" action="../admin/delete-feedback.php" class="d-inline">
                <input type="hidden" name="feedback_id" value="<?php echo $feedback['feedback_id']; ?>">
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this feedback?')">
                    <i class="fas fa-trash"></i> Delete Feedback
                </button>
            </form>
        </div>
    </div>
</div>