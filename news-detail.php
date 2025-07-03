<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('news.php', 'Invalid news article ID.');
}

$article_id = (int)$_GET['id'];
$sql = "SELECT n.*, u.username as author 
        FROM car_news n
        JOIN users u ON n.author_id = u.user_id
        WHERE n.id = ? AND n.is_published = TRUE";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('news.php', 'News article not found or not published.');
}

$article = $result->fetch_assoc();
$pageTitle = $article['title'];
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
    <div class="row">
        <div class="col-md-8 mx-auto">
            <article>
                <h1><?= htmlspecialchars($article['title']) ?></h1>
                
                <p class="text-muted">
                    Posted on <?= date('F j, Y', strtotime($article['publish_date'])) ?> by <?= htmlspecialchars($article['author']) ?>
                </p>
                
                <?php if ($article['image_url']): ?>
                    <img src="<?= htmlspecialchars($article['image_url']) ?>" class="img-fluid mb-4" alt="<?= htmlspecialchars($article['title']) ?>">
                <?php endif; ?>
                
                <div class="article-content">
                    <?= nl2br(htmlspecialchars($article['content'])) ?>
                </div>
                
                <a href="news.php" class="btn btn-outline-primary mt-4">Back to News</a>
            </article>
        </div>
    </div>
</div>

    <!-- Include SweetAlert JS -->
    <!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
    <script src="./node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script> -->
    <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once 'includes/footer.php'; ?>