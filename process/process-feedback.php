<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isLoggedIn() || isAdmin()) {
    redirect('/', 'Invalid request.');
}

$feedbackTitle = sanitize($_POST['feedback_title']);
$feedbackDesc = sanitize($_POST['feedback_desc']);

$sql = "INSERT INTO Feedback (user_id, feedback_title, feedback_desc) 
        VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $_SESSION['user_id'], $feedbackTitle, $feedbackDesc);

if ($stmt->execute()) {
    redirect('../client/dashboard.php', 'Feedback submitted successfully. Thank you!');
} else {
    redirect('../client/dashboard.php', 'Error submitting feedback.', 'error');
}
?>