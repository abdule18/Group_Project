<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../lib/helpers.php';
require __DIR__ . '/../lib/validation.php';
require __DIR__ . '/../config/csrf.php';
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_text($_POST['name'] ?? '');
    $email = sanitize_text($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    if (!$name || !$email || !$pass || !validate_email($email)) {
        $error = 'Please fill all fields with a valid email.';
    } else {
        try {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO users (name,email,password_hash) VALUES (?,?,?)');
            $stmt->execute([$name,$email,$hash]);
            $_SESSION['flash'] = 'Account created, please login.';
            redirect('/login.php');
        } catch (Throwable $e) {
            $error = 'Email already registered.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Register</title><link rel="stylesheet" href="/assets/styles.css"></head>
<body>
<header><div class="wrap"><a href="/">Remote Tech Support</a></div></header>
<div class="container">
  <div class="card">
    <h2>Create account</h2>
    <?php if (!empty($error)): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <label>Name</label>
      <input name="name" required>
      <label>Email</label>
      <input type="email" name="email" required>
      <label>Password</label>
      <input type="password" name="password" required>
      <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="/login.php">Login</a></p>
  </div>
</div>
</body>
</html>
