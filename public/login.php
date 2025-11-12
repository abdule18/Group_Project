<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../lib/helpers.php';
require __DIR__ . '/../lib/validation.php';
require __DIR__ . '/../config/csrf.php';
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_text($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    if ($u && password_verify($pass, $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']];
        redirect('/dashboard.php');
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Login</title><link rel="stylesheet" href="/assets/styles.css"></head>
<body>
<header><div class="wrap"><a href="/">Remote Tech Support</a></div></header>
<div class="container">
  <div class="card">
    <h2>Login</h2>
    <?php if (!empty($error)): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <label>Email</label>
      <input type="email" name="email" required>
      <label>Password</label>
      <input type="password" name="password" required>
      <button type="submit">Login</button>
    </form>
    <p>No account? <a href="/register.php">Register</a></p>
  </div>
</div>
</body>
</html>
