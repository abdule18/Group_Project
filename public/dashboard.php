<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../lib/auth.php';
require __DIR__ . '/../lib/helpers.php';
require __DIR__ . '/../config/csrf.php';
require_login();

// Create ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $priority_id = (int)($_POST['priority_id'] ?? 0);
    if ($title && $description && $category_id && $priority_id) {
        $status_id = 1; // Open
        $stmt = $pdo->prepare('INSERT INTO tickets (user_id, category_id, priority_id, status_id, title, description) VALUES (?,?,?,?,?,?)');
        $stmt->execute([$_SESSION['user']['id'], $category_id, $priority_id, $status_id, $title, $description]);
        $_SESSION['flash'] = 'Ticket submitted.';
        redirect('/dashboard.php');
    } else {
        $error = 'Please fill in all fields.';
    }
}

// fetch lists
$cats = $pdo->query('SELECT id,name FROM categories ORDER BY name')->fetchAll();
$pris = $pdo->query('SELECT id,name FROM priorities ORDER BY id')->fetchAll();

// fetch user's tickets (JOINs)
$stmt = $pdo->prepare('
SELECT t.id, t.title, t.created_at, s.name AS status, p.name AS priority, c.name AS category
FROM tickets t
JOIN statuses s ON s.id = t.status_id
JOIN priorities p ON p.id = t.priority_id
JOIN categories c ON c.id = t.category_id
WHERE t.user_id = ?
ORDER BY t.created_at DESC
');
$stmt->execute([$_SESSION['user']['id']]);
$mytickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Dashboard</title><link rel="stylesheet" href="/assets/styles.css"></head>
<body>
<header><div class="wrap">
  <a href="/">Remote Tech Support</a>
  <nav class="flex right">
    <a href="/logout.php">Logout</a>
  </nav>
</div></header>
<div class="container">
  <div class="card">
    <h2>Create Support Ticket</h2>
    <?php if (!empty($error)): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <label>Title</label>
      <input name="title" required maxlength="150">
      <label>Description</label>
      <textarea name="description" rows="4" required></textarea>
      <div class="flex">
        <div style="flex:1">
          <label>Category</label>
          <select name="category_id" required>
            <option value="">-- select --</option>
            <?php foreach($cats as $c): ?>
              <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="flex:1">
          <label>Priority</label>
          <select name="priority_id" required>
            <option value="">-- select --</option>
            <?php foreach($pris as $p): ?>
              <option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <button type="submit">Submit Ticket</button>
    </form>
  </div>

  <div class="card">
    <h2>My Tickets</h2>
    <table class="table">
      <thead><tr><th>ID</th><th>Title</th><th>Category</th><th>Priority</th><th>Status</th><th>Created</th></tr></thead>
      <tbody>
        <?php foreach($mytickets as $t): ?>
          <tr>
            <td>#<?= $t['id'] ?></td>
            <td><a href="/ticket.php?id=<?= $t['id'] ?>"><?= e($t['title']) ?></a></td>
            <td><span class="badge"><?= e($t['category']) ?></span></td>
            <td><span class="badge"><?= e($t['priority']) ?></span></td>
            <td><span class="badge"><?= e($t['status']) ?></span></td>
            <td><?= e($t['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
