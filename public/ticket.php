<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../lib/auth.php';
require __DIR__ . '/../lib/helpers.php';
require __DIR__ . '/../config/csrf.php';
verify_csrf();
require_login();

$id = (int)($_GET['id'] ?? 0);

// Handle updates (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_admin()) {
    if (isset($_POST['status_id'])) {
        $status_id = (int)$_POST['status_id'];
        $assignee = empty($_POST['assigned_to']) ? null : (int)$_POST['assigned_to'];
        $stmt = $pdo->prepare('UPDATE tickets SET status_id=?, assigned_to=? WHERE id=?');
        $stmt->execute([$status_id, $assignee, $id]);
    }
}

// Add comment (any logged user involved)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && $_POST['comment'] !== '') {
    $stmt = $pdo->prepare('INSERT INTO ticket_comments (ticket_id, user_id, comment) VALUES (?,?,?)');
    $stmt->execute([$id, $_SESSION['user']['id'], trim($_POST['comment'])]);
}

$stmt = $pdo->prepare('
SELECT t.*, u.name AS reporter, s.name AS status, p.name AS priority, c.name AS category, a.name AS assignee
FROM tickets t
JOIN users u ON u.id = t.user_id
JOIN statuses s ON s.id = t.status_id
JOIN priorities p ON p.id = t.priority_id
JOIN categories c ON c.id = t.category_id
LEFT JOIN users a ON a.id = t.assigned_to
WHERE t.id = ?');
$stmt->execute([$id]);
$ticket = $stmt->fetch();
if (!$ticket) { http_response_code(404); die('Ticket not found'); }

$comments = $pdo->prepare('
SELECT tc.comment, tc.created_at, u.name
FROM ticket_comments tc
JOIN users u ON u.id = tc.user_id
WHERE tc.ticket_id = ?
ORDER BY tc.created_at ASC');
$comments->execute([$id]);
$comments = $comments->fetchAll();

$statuses = $pdo->query('SELECT id,name FROM statuses')->fetchAll();
$users = $pdo->query('SELECT id,name FROM users ORDER BY name')->fetchAll();

$can_view = is_admin() || $_SESSION['user']['id'] == $ticket['user_id'] || $_SESSION['user']['id'] == $ticket['assigned_to'];
if (!$can_view) { http_response_code(403); die('Forbidden'); }
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Ticket #<?= $ticket['id'] ?></title><link rel="stylesheet" href="/assets/styles.css"></head>
<body>
<header><div class="wrap">
  <a href="/">Remote Tech Support</a>
  <nav class="flex right">
    <a href="/dashboard.php">Dashboard</a>
    <?php if (is_admin()): ?><a href="/tickets.php">All Tickets</a><?php endif; ?>
    <a href="/logout.php">Logout</a>
  </nav>
</div></header>
<div class="container">
  <div class="card">
    <h2>Ticket #<?= $ticket['id'] ?> — <?= e($ticket['title']) ?></h2>
    <p><strong>Status:</strong> <span class="badge"><?= e($ticket['status']) ?></span> ·
       <strong>Priority:</strong> <span class="badge"><?= e($ticket['priority']) ?></span> ·
       <strong>Category:</strong> <span class="badge"><?= e($ticket['category']) ?></span></p>
    <p><strong>Reporter:</strong> <?= e($ticket['reporter']) ?> ·
       <strong>Assignee:</strong> <?= e($ticket['assignee'] ?? 'Unassigned') ?></p>
    <p><?= nl2br(e($ticket['description'])) ?></p>
  </div>

  <?php if (is_admin()): ?>
  <div class="card">
    <h3>Admin Controls</h3>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <div class="flex">
        <div style="flex:1">
          <label>Status</label>
          <select name="status_id">
            <?php foreach($statuses as $s): ?>
              <option value="<?= $s['id'] ?>" <?= $s['id']==$ticket['status_id']?'selected':'' ?>><?= e($s['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="flex:1">
          <label>Assign to</label>
          <select name="assigned_to">
            <option value="">-- Unassigned --</option>
            <?php foreach($users as $u): ?>
              <option value="<?= $u['id'] ?>" <?= $ticket['assigned_to']==$u['id']?'selected':'' ?>><?= e($u['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <button type="submit">Update</button>
    </form>
  </div>
  <?php endif; ?>

  <div class="card">
    <h3>Conversation</h3>
    <?php foreach($comments as $c): ?>
      <div class="card" style="margin:8px 0;padding:12px">
        <strong><?= e($c['name']) ?></strong> <span class="badge"><?= e($c['created_at']) ?></span>
        <p><?= nl2br(e($c['comment'])) ?></p>
      </div>
    <?php endforeach; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <textarea name="comment" rows="3" placeholder="Add a comment..."></textarea>
      <button type="submit">Post Comment</button>
    </form>
  </div>
</div>
</body>
</html>
