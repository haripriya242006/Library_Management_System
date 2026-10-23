<?php
require_once 'config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
        if ($title === '' || $author === '' || $category === '' || $quantity === false || $quantity === null || $quantity < 1) {
            $error = 'Please enter all book details and a quantity of at least 1.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO books (title, author, category, quantity, available) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$title, $author, $category, $quantity, $quantity]);
            redirect_to('books.php?success=added');
        }
    } elseif ($action === 'delete') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $check = $pdo->prepare("SELECT COUNT(*) FROM issues WHERE book_id = ? AND status = 'Issued'");
            $check->execute([$id]);
            if ((int)$check->fetchColumn() === 0) {
                $stmt = $pdo->prepare('DELETE FROM books WHERE id = ?');
                $stmt->execute([$id]);
                redirect_to('books.php?success=deleted');
            } else {
                $error = 'This book has an active issue record and cannot be deleted yet.';
            }
        }
    }
}
$search = trim($_GET['search'] ?? '');
$stmt = $pdo->prepare('SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR category LIKE ? ORDER BY title');
$pattern = '%' . $search . '%';
$stmt->execute([$pattern, $pattern, $pattern]);
$books = $stmt->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Manage Books</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'nav.php'; ?><main class="container page">
  <div class="page-heading"><div><p class="eyebrow">COLLECTION</p><h1>Manage Books</h1><p>Add books, search the catalogue, or remove entries.</p></div></div>
  <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
  <?php if (isset($_GET['success'])): ?><div class="alert success"><?= $_GET['success'] === 'added' ? 'Book added successfully.' : 'Book deleted successfully.' ?></div><?php endif; ?>
  <section class="panel"><h2>Add a book</h2><form method="post" class="form-grid" data-validate>
    <input type="hidden" name="action" value="add">
    <label>Book title<input name="title" required maxlength="150" placeholder="e.g. The Alchemist"></label>
    <label>Author<input name="author" required maxlength="120" placeholder="e.g. Paulo Coelho"></label>
    <label>Category<input name="category" required maxlength="80" placeholder="e.g. Fiction"></label>
    <label>Quantity<input type="number" name="quantity" required min="1" max="10000" value="1"></label>
    <div class="form-actions"><button class="button" type="submit">Add Book</button></div>
  </form></section>
  <section class="panel"><div class="section-heading"><div><h2>Book catalogue</h2><p><?= count($books) ?> result(s)</p></div>
    <form method="get" class="search-form"><input name="search" value="<?= e($search) ?>" placeholder="Search title, author, category" aria-label="Search books"><button class="button secondary" type="submit">Search</button></form>
  </div>
  <?php if (!$books): ?><p class="empty">No matching books found.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Title</th><th>Author</th><th>Category</th><th>Available</th><th>Action</th></tr></thead><tbody>
  <?php foreach ($books as $book): ?><tr><td><?= e($book['title']) ?></td><td><?= e($book['author']) ?></td><td><?= e($book['category']) ?></td><td><?= (int)$book['available'] ?> / <?= (int)$book['quantity'] ?></td><td><form method="post" data-confirm="Delete this book?"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$book['id'] ?>"><button class="button danger small" type="submit">Delete</button></form></td></tr><?php endforeach; ?>
  </tbody></table></div><?php endif; ?></section>
</main><script src="script.js"></script></body></html>
