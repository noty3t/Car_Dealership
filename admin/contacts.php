<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

$pageTitle = "Contact Messages";

// Handle admin notes update and deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_notes'])) {
        $contactId = (int)$_POST['contact_id'];
        $adminNotes = sanitize($_POST['admin_notes']);
        
        $sql = "UPDATE Contacts SET admin_notes = ? WHERE contact_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $adminNotes, $contactId);
        
        if ($stmt->execute()) {
            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Notes updated successfully.'];
            header("Location: contacts.php");
            exit();
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error updating notes.'];
            header("Location: contacts.php");
            exit();
        }
    }
    
    if (isset($_POST['delete_contact'])) {
        $contactId = (int)$_POST['contact_id'];
        
        $sql = "DELETE FROM Contacts WHERE contact_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $contactId);
        
        if ($stmt->execute()) {
            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Contact message deleted successfully.'];
            header("Location: contacts.php");
            exit();
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error deleting contact message.'];
            header("Location: contacts.php");
            exit();
        }
    }
}

// Get all contact messages with proper joins to get car make and model
$contacts = [];
$sql = "SELECT c.*, 
               b.brand_name as make, 
               m.model_name as model,
               CONCAT(b.brand_name, ' ', m.model_name) AS car_name 
        FROM Contacts c
        LEFT JOIN Cars car ON c.car_id = car.car_id
        LEFT JOIN model m ON car.model_id = m.id
        LEFT JOIN brand b ON m.brand_id = b.id
        ORDER BY c.contact_date DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $contacts[] = $row;
    }
}

require_once '../includes/header.php';
?>
<head>
    <link href="../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css">
</head>

<div class="container mt-4">
    <h2 class="mb-4">Contact Messages</h2>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Subject</th>
                    <th>Car</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i=1;
                foreach ($contacts as $contact): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $contact['name']; ?></td>
                        <td><?php echo $contact['email']; ?></td>
                        <td><?php echo $contact['phone'] ?: 'N/A'; ?></td>
                        <td><?php echo $contact['subject']; ?></td>
                        <td>
                            <?php if ($contact['car_id']): ?>
                                <?php echo $contact['make'] . ' ' . $contact['model']; ?>
                            <?php else: ?>
                                General Inquiry
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($contact['contact_date'])); ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary view-contact-btn"
                                        data-contact-id="<?php echo $contact['contact_id']; ?>"
                                        data-bs-toggle="modal" data-bs-target="#contactModal">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <form method="POST" action="contacts.php" class="d-inline" id="deleteForm<?php echo $contact['contact_id']; ?>">
                                    <input type="hidden" name="contact_id" value="<?php echo $contact['contact_id']; ?>">
                                    <input type="hidden" name="delete_contact" value="1">
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" 
                                            data-id="<?php echo $contact['contact_id']; ?>">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Contact Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Contact Message Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="contactModalBody">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.querySelectorAll('.view-contact-btn').forEach(button => {
    button.addEventListener('click', function() {
        const contactId = this.getAttribute('data-contact-id');
        
        fetch(`get-contact.php?id=${contactId}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('contactModalBody').innerHTML = data;
            });
    });
});

// SweetAlert for delete confirmation
document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function() {
        const contactId = this.getAttribute('data-id');
        
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
                document.getElementById(`deleteForm${contactId}`).submit();
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