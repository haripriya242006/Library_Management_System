<?php
require_once 'config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'issue') {
        $bookId = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
        $studentId = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
        if (!$bookId || !$studentId) {
            $error = 'Please choose a book and a student.';
        } else {
            try {
                $pdo->beginTransaction();
                $bookStmt = $pdo->prepare('SELECT available FROM books WHERE id = ? FOR UPDATE');
                $bookStmt->execute([$bookId]);
                $available = $bookStmt->fetchColumn();
                $studentStmt = $pdo->prepare('SELECT id FROM students WHERE id = ?');
                $studentStmt->execute([$studentId]);
                if ($available === false || !$studentStmt->fetch()) {
                    throw new RuntimeException('Choose a valid book and student.');
                }
                if ((int)$available < 1) {
                    throw new RuntimeException('No copies of this book are currently available.');
                }
                $pdo->prepare("INSERT INTO issues (book_id, student_id, status) VALUES (?, ?, 'Issued')")->execute([$bookId, $studentId]);
                $pdo->prepare('UPDATE books SET available = available - 1 WHERE id = ?')->execute([$bookId]);
                $pdo->commit();
                redirect_to('issues.php?success=issued');
            } catch (Throwable $ex) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $error = $ex instanceof RuntimeException ? $ex->getMessage() : 'Could not issue the book. Please try again.';
            }
        }
    } elseif ($action === 'return') {
        $issueId = filter_input(INPUT_POST, 'issue_id', FILTER_VALIDATE_INT);
        if ($issueId) {
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("SELECT book_id FROM issues WHERE id = ? AND status = 'Issued' FOR UPDATE");
                $stmt->execute([$issueId]);
                $bookId = $stmt->fetchColumn();
                if ($bookId) {
                    $pdo->prepare("UPDATE issues SET status = 'Returned', returned_at = NOW() WHERE id = ?")->execute([$issueId]);
                    $pdo->prepare('UPDATE books SET available = available + 1 WHERE id = ?')->execute([$bookId]);
                }
                $pdo->commit();
                redirect_to('issues.php?success=returned');
            } catch (Throwable $ex) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $error = 'Could not return the book. Please try again.';
            }
        }
    }
}
$books = $pdo->query('SELECT id, title, available FROM books WHERE available > 0 ORDER BY title')->fetchAll();
$students = $pdo->query('SELECT id, name, email FROM students ORDER BY name')->fetchAll();
$issues = $pdo->query("SELECT i.id, i.status, i.issued_at, i.returned_at, b.title, s.name, s.email FROM issues i JOIN books b ON b.id = i.book_id JOIN students s ON s.id = i.student_id ORDER BY i.id DESC")->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Issue / Return Books</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'nav.php'; ?><main class="container page"><div class="page-heading"><div><p class="eyebrow">CIRCULATION</p><h1>Issue / Return Books</h1><p>Issue an available book to a registered student and record returns.</p></div></div>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><?php if (isset($_GET['success'])): ?><div class="alert success"><?= $_GET['success'] === 'issued' ? 'Book issued successfully.' : 'Book returned successfully.' ?></div><?php endif; ?>
<section class="panel"><h2>Issue a book</h2><?php if (!$books || !$students): ?><p class="empty">Add at least one available book and register at least one student before issuing.</p><?php else: ?><form method="post" class="form-grid"><input type="hidden" name="action" value="issue"><label>Book<select name="book_id" required><option value="">Select a book</option><?php foreach ($books as $book): ?><option value="<?= (int)$book['id'] ?>"><?= e($book['title']) ?> (<?= (int)$book['available'] ?> available)</option><?php endforeach; ?></select></label><label>Student<select name="student_id" required><option value="">Select a student</option><?php foreach ($students as $student): ?><option value="<?= (int)$student['id'] ?>"><?= e($student['name']) ?> — <?= e($student['email']) ?></option><?php endforeach; ?></select></label><div class="form-actions"><button class="button" type="submit">Issue Book</button></div></form><?php endif; ?></section>
<section class="panel"><div class="section-heading"><div><h2>Issue history</h2><p><?= count($issues) ?> record(s)</p></div></div>
<?php if (!$issues): ?><p class="empty">No issue records yet.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Book</th><th>Student</th><th>Issued date</th><th>Status</th><th>Action</th></tr></thead><tbody><?php foreach ($issues as $issue): ?><tr><td><?= e($issue['title']) ?></td><td><?= e($issue['name']) ?></td><td><?= e(date('d M Y', strtotime($issue['issued_at']))) ?></td><td><span class="status <?= $issue['status'] === 'Issued' ? 'status-issued' : 'status-returned' ?>"><?= e($issue['status']) ?></span></td><td><?php if ($issue['status'] === 'Issued'): ?><form method="post" data-confirm="Mark this book as returned?"><input type="hidden" name="action" value="return"><input type="hidden" name="issue_id" value="<?= (int)$issue['id'] ?>"><button class="button secondary small" type="submit">Return</button></form><?php else: ?><span class="muted"><?= $issue['returned_at'] ? e(date('d M Y', strtotime($issue['returned_at']))) : '—' ?></span><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
</section></main><script src="script.js"></script></body></html>
