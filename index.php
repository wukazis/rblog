<?php include 'inc/db.php'; ?>
<?php include 'inc/header.php'; ?>

<div class="container mt-5">
    <h1>我的博客</h1>
    <?php
    $stmt = $pdo->query('SELECT * FROM posts ORDER BY created_at DESC');
    while ($row = $stmt->fetch()):
    ?>
        <div class="card mb-3">
            <div class="card-body">
                <h3><a href="post.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a></h3>
                <p><?= mb_substr(strip_tags($row['content']), 0, 100) ?>...</p>
                <small><?= $row['created_at'] ?></small>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php include 'inc/footer.php'; ?>