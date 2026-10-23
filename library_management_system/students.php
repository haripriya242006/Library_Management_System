<?php
require_once 'config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $department = trim($_POST['department'] ?? '');
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $department === '') {
        $error = 'Enter a name, valid email address, and department.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO students (name, email, department) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, $department]);
            redirect_to('students.php?success=added');
        } catch (PDOException $ex) {
            $error = $ex->getCode() === '23000' ? 'That email address is already registered.' : 'Could not save student. Please check your database.';
        }
    }
}
$students = $pdo->query('SELECT * FROM students ORDER BY name')->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Manage Students</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'nav.php'; ?><main class="container page"><div class="page-heading"><div><p class="eyebrow">MEMBERS</p><h1>Manage Students</h1><p>Register library members and view their details.</p></div></div>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><?php if (isset($_GET['success'])): ?><div class="alert success">Student registered successfully.</div><?php endif; ?>
<section class="panel"><h2>Register a student</h2><form method="post" class="form-grid" data-validate>
<label>Full name<input name="name" required maxlength="120" placeholder="Student name"></label><label>Email address<input type="email" name="email" required maxlength="190" placeholder="student@example.com"></label><label>Department / Class<input name="department" required maxlength="100" placeholder="e.g. B.Sc. Computer Science"></label><div class="form-actions"><button class="button" type="submit">Register Student</button></div>
</form></section>
<section class="panel"><div class="section-heading"><div><h2>Registered students</h2><p><?= count($students) ?> student(s)</p></div></div>
<?php if (!$students): ?><p class="empty">No students registered yet.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Department</th><th>Registered</th></tr></thead><tbody><?php foreach ($students as $student): ?><tr><td><?= e($student['name']) ?></td><td><?= e($student['email']) ?></td><td><?= e($student['department']) ?></td><td><?= e(date('d M Y', strtotime($student['created_at']))) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
</section></main><script src="script.js"></script></body></html>
