<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Remote Tech Support Service</title>
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
<header>
  <div class="wrap">
    <div><strong>Remote Tech Support</strong></div>
    <nav class="flex">
      <?php if ($user): ?>
        <a href="/dashboard.php">Dashboard</a>
        <?php if (($user['role'] ?? 'user') === 'admin'): ?>
          <a href="/tickets.php">All Tickets</a>
        <?php endif; ?>
        <span>Hello, <?= htmlspecialchars($user['name']) ?></span>
        <a href="/logout.php">Logout</a>
      <?php else: ?>
        <a href="/login.php">Login</a>
        <a href="/register.php">Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<div class="container">
  <div class="card">
    <h2>Welcome to Remote Tech Support Service</h2>
    <p>Create, track, and resolve tech support tickets.</p>
    <?php if (!$user): ?>
      <div class="flex">
        <a href="/register.php"><button>Create an account</button></a>
        <a href="/login.php"><button class="secondary">Login</button></a>
      </div>
    <?php else: ?>
      <a href="/dashboard.php"><button>Go to Dashboard</button></a>
    <?php endif; ?>
  </div>
  <div class="card">
    <h3>How it works</h3>
    <ol>
      <li>Sign up and log in.</li>
      <li>Submit a support ticket with category and priority.</li>
      <li>View status updates and comment with technicians.</li>
    </ol>
  </div>
</div>
<script src="/assets/app.js"></script>
</body>
</html>
