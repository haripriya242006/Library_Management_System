<?php
require_once 'config.php';
$bookCount = (int)$pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$studentCount = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$issuedCount = (int)$pdo->query("SELECT COUNT(*) FROM issues WHERE status = 'Issued'")->fetchColumn();
$recentBooks = $pdo->query("SELECT * FROM books ORDER BY id DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Library Management System</title><link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'nav.php'; ?>
<main class="container">
  <section class="hero">
    <div><p class="eyebrow">LIBRARY PORTAL</p><h1>Library Management System</h1>
    <p>Manage books, students, and book issue records in one simple place.</p>
    <a class="button" href="books.php">Manage Books</a></div>
    <div class="hero-icon" aria-hidden="true">📚</div>
  </section>
  <section class="stats">
    <article class="stat-card"><span>Total books</span><strong><?= $bookCount ?></strong></article>
    <article class="stat-card"><span>Registered students</span><strong><?= $studentCount ?></strong></article>
    <article class="stat-card"><span>Currently issued</span><strong><?= $issuedCount ?></strong></article>
  </section>
  <section class="panel">
    <div class="section-heading"><div><h2>Recently added books</h2><p>Latest entries in your collection.</p></div><a href="books.php">View all →</a></div>
    <?php if (!$recentBooks): ?><p class="empty">No books yet. Open Manage Books to add your first book.</p>
    <?php else: ?><div class="table-wrap"><table><thead><tr><th>Title</th><th>Author</th><th>Category</th><th>Available</th></tr></thead><tbody>
    <?php foreach ($recentBooks as $book): ?><tr><td><?= e($book['title']) ?></td><td><?= e($book['author']) ?></td><td><?= e($book['category']) ?></td><td><?= (int)$book['available'] ?> / <?= (int)$book['quantity'] ?></td></tr><?php endforeach; ?>
    </tbody></table></div><?php endif; ?>
  </section>
  <p class="note">Student project demo. Run this project on a local PHP server with MySQL.</p>
</main>
<script src="script.js"></script>
</body></html>
