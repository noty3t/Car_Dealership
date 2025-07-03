<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request');
}

$contactId = (int)$_GET['id'];

$sql = "SELECT c.*, 
               b.brand_name as make, 
               m.model_name as model,
               CONCAT(b.brand_name, ' ', m.model_name) AS car_name 
        FROM Contacts c
        LEFT JOIN Cars car ON c.car_id = car.car_id
        LEFT JOIN model m ON car.model_id = m.id
        LEFT JOIN brand b ON m.brand_id = b.id
        WHERE c.contact_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $contactId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Contact message not found');
}

$contact = $result->fetch_assoc();
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
<div class="row">
    <div class="col-md-6">
        <h6>Contact Information</h6>
        <p><strong>Name:</strong> <?= htmlspecialchars($contact['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($contact['email']) ?></p>
        <p><strong>Phone:</strong> <?= $contact['phone'] ? htmlspecialchars($contact['phone']) : 'N/A' ?></p>
        <p><strong>Date:</strong> <?= date('M j, Y H:i', strtotime($contact['contact_date'])) ?></p>
        <?php if ($contact['car_id']): ?>
            <p><strong>Related Car:</strong> <?= $contact['car_name'] ? htmlspecialchars($contact['car_name']) : 'N/A' ?></p>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <h6>Message Details</h6>
        <p><strong>Subject:</strong> <?= htmlspecialchars($contact['subject']) ?></p>
        <div class="card mb-3">
            <div class="card-body">
                <h6>Message:</h6>
                <p><?= nl2br(htmlspecialchars($contact['message'])) ?></p>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="contacts.php" id="contactNotesForm">
    <input type="hidden" name="contact_id" value="<?= $contact['contact_id'] ?>">
    <div class="mb-3">
        <label for="admin_notes" class="form-label">Admin Notes</label>
        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"><?= $contact['admin_notes'] ? htmlspecialchars($contact['admin_notes']) : 'N/A' ?></textarea>
    </div>
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button type="submit" name="update_notes" class="btn btn-primary me-md-2">Save Notes</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Handle form submission
    $('#contactNotesForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            type: 'POST',
            url: 'contacts.php',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Notes updated successfully',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            },
            error: function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error updating notes',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        });
    });
});
</script>