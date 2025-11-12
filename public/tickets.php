<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../lib/auth.php';
require __DIR__ . '/../lib/helpers.php';
require_login();
if (!is_admin()) { http_response_code(403); die('Forbidden'); }

// All tickets with JOINs
$stmt = $pdo->query('
SELECT t.id, t.title, t.created_at, s.name AS status, p.name AS priority, c.name AS category, u.name AS reporter
FROM tickets t
JOIN statuses s ON s.id = t.status_id
JOIN priorities p ON p.id = t.priority_id
JOIN categories c ON c.id = t.category_id
JOIN users u ON u.id = t.user_id
ORDER BY t.created_at DESC
');
$tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>All Tickets</title><link rel="stylesheet" href="/assets/styles.css"></head>
<body>
<header><div class="wrap">
  <a href="/">Remote Tech Support</a>
  <nav class="flex right">
    <a href="/dashboard.php">Dashboard</a>
    <a href="/logout.php">Logout</a>
  </nav>
</div></header>
<div class="container">
  <div class="card">
    <h2>All Tickets</h2>
    <table class="table">
      <thead><tr><th>ID</th><th>Title</th><th>Category</th><th>Priority</th><th>Status</th><th>Reporter</th><th>Created</th></tr></thead>
      <tbody>
        <?php foreach($tickets as $t): ?>
          <tr>
            <td>#<?= $t['id'] ?></td>
            <td><a href="/ticket.php?id=<?= $t['id'] ?>"><?= e($t['title']) ?></a></td>
            <td><?= e($t['category']) ?></td>
            <td><?= e($t['priority']) ?></td>
            <td><?= e($t['status']) ?></td>
            <td><?= e($t['reporter']) ?></td>
            <td><?= e($t['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
