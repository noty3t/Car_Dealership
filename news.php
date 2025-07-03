<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$pageTitle = "Car News";

// Get all published news articles
$news = [];
$sql = "SELECT n.*, u.username as author 
        FROM car_news n
        JOIN users u ON n.author_id = u.user_id
        WHERE n.is_published = TRUE
        ORDER BY n.publish_date DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $news[] = $row;
    }
}
?>

<head>

    <!-- Bootstrap CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="./node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <link rel="stylesheet" href="./node_modules/@fortawesome/fontawesome-free/css/all.min.css">
     
    <!-- Custom CSS -->
    <!-- <link rel="stylesheet" href="/assets/css/style.css"> -->
    <!-- Include SweetAlert CSS -->
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"> -->
    <link rel="stylesheet" href="./node_modules/sweetalert2/dist/sweetalert2.min.css">
    
</head>

<div class="container mt-4">
    <h2 class="mb-4">Latest Car News</h2>
    
    <div class="row">
        <?php foreach ($news as $article): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <?php if ($article['image_url']): ?>
                        <img src="<?= htmlspecialchars($article['image_url']) ?>" class="card-img-top h-30 " alt="<?= htmlspecialchars($article['title']) ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($article['title']) ?></h5>
                        <p class="card-text">
                            <?= substr(htmlspecialchars($article['content']), 0, 150) ?>
                            <?= strlen($article['content']) > 150 ? '...' : '' ?>
                        </p>
                        <a href="news-detail.php?id=<?= $article['id'] ?>" class="btn btn-primary">Read More</a>
                    </div>
                    <div class="card-footer text-muted">
                        Posted on <?= date('F j, Y', strtotime($article['publish_date'])) ?> by <?= htmlspecialchars($article['author']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

    <!-- Include SweetAlert JS -->
    <!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
    <script src="./node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script> -->
    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once 'includes/footer.php'; ?>