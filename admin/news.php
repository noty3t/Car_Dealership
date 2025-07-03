<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

$pageTitle = "Manage News";

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $newsId = (int)$_GET['id'];
    $sql = "DELETE FROM car_news WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $newsId);
    if ($stmt->execute()) {
        redirect('news.php', 'News article deleted successfully.');
    } else {
        redirect('news.php', 'Error deleting news article.', 'error');
    }
}

// Handle form submission for adding/editing news
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newsId = isset($_POST['news_id']) ? (int)$_POST['news_id'] : 0;
    $title = $_POST['title'];
    $content = $_POST['content'];
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $author_id = $_SESSION['user_id'];
    
    // Handle image upload
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/news/';
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $imageName;
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Check file type
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $image_url = 'uploads/news/' . $imageName;
            }
        }
    }
    
    if ($newsId > 0) {
        // Update existing news
        if ($image_url) {
            $sql = "UPDATE car_news SET title = ?, content = ?, image_url = ?, is_published = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssii", $title, $content, $image_url, $is_published, $newsId);
        } else {
            $sql = "UPDATE car_news SET title = ?, content = ?, is_published = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssii", $title, $content, $is_published, $newsId);
        }
    } else {
        // Add new news
        $sql = "INSERT INTO car_news (title, content, image_url, author_id, is_published) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $title, $content, $image_url, $author_id, $is_published);
    }
    
    if ($stmt->execute()) {
        redirect('news.php', $newsId > 0 ? 'News article updated successfully.' : 'News article added successfully.');
    } else {
        redirect('news.php', 'Error saving news article.', 'error');
    }
}

// Get all news articles
$news = [];
$sql = "SELECT n.*, u.username as author 
        FROM car_news n
        JOIN users u ON n.author_id = u.user_id
        ORDER BY n.publish_date DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $news[] = $row;
    }
}

// Get news article for editing
$editNews = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $newsId = (int)$_GET['id'];
    $sql = "SELECT * FROM car_news WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $newsId);
    $stmt->execute();
    $result = $stmt->get_result();
    $editNews = $result->fetch_assoc();
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage News</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newsModal">
            <i class="fas fa-plus"></i> Add New Article
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($news as $article): ?>
                    <tr>
                        <td><?= $article['id'] ?></td>
                        <td>
                            <?php if ($article['image_url']): ?>
                                <img src="../<?= htmlspecialchars($article['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($article['title']) ?>" 
                                     style="width: 80px; height: auto;">
                            <?php else: ?>
                                <span class="text-muted">No image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($article['title']) ?></td>
                        <td><?= htmlspecialchars($article['author']) ?></td>
                        <td><?= date('M j, Y', strtotime($article['publish_date'])) ?></td>
                        <td>
                            <span class="badge bg-<?= $article['is_published'] ? 'success' : 'warning' ?>">
                                <?= $article['is_published'] ? 'Published' : 'Draft' ?>
                            </span>
                        </td>
                        <td>
                            <a href="news.php?action=edit&id=<?= $article['id'] ?>" 
                               class="btn btn-sm btn-outline-primary w-75">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="news.php?action=delete&id=<?= $article['id'] ?>" 
                               class="btn btn-sm btn-outline-danger w-75" 
                               onclick="return confirm('Are you sure you want to delete this article?');">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- News Modal -->
<div class="modal fade" id="newsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $editNews ? 'Edit News Article' : 'Add New Article' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="news_id" value="<?= $editNews ? $editNews['id'] : '' ?>">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?= $editNews ? htmlspecialchars($editNews['title']) : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="8" required><?= $editNews ? htmlspecialchars($editNews['content']) : '' ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Article Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        
                        <?php if ($editNews && $editNews['image_url']): ?>
                            <div class="current-image-container mt-2">
                                <img src="../<?= htmlspecialchars($editNews['image_url']) ?>" 
                                     alt="Current Article Image" 
                                     class="current-image"
                                     style="max-width: 200px;">
                                <p class="current-image-label text-muted mt-1">
                                    Current image preview
                                </p>
                                <input type="hidden" name="current_image" value="<?= htmlspecialchars($editNews['image_url']) ?>">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_published" name="is_published" 
                            <?= ($editNews && $editNews['is_published']) || !$editNews ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_published">Publish this article</label>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Article</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Open modal if editing
<?php if ($editNews): ?>
    document.addEventListener('DOMContentLoaded', function() {
        var newsModal = new bootstrap.Modal(document.getElementById('newsModal'));
        newsModal.show();
    });
<?php endif; ?>
</script>
    <!-- Include SweetAlert JS -->
    <!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
    <script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script> -->
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once '../includes/footer.php'; ?>