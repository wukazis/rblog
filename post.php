<?php
include 'inc/db.php';
include 'inc/header.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    echo "<div class='container mt-5'><h2>文章不存在</h2></div>";
} else {
?>
<div class="container mt-5">
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <div><?= nl2br($post['content']) ?></div>
    <small><?= $post['created_at'] ?></small>
</div>
<?php
}
include 'inc/footer.php';
?>