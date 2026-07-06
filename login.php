<?php
require __DIR__ . '/includes/config.php';

if (!empty($_SESSION['logged_in'])) {
    header('Location: admin.php');
    exit;
}

$error = flash('login_error');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT id, password_hash FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = (int) $user['id'];
        header('Location: admin.php');
        exit;
    }

    $error = 'Username au password si sahihi. Jaribu tena.';
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ingia — Nexor Digital</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <div class="text-center mb-4">
      <div class="mx-auto mb-3" style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,var(--nx-primary),var(--nx-accent));display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;"><i class="bi bi-reception-4"></i></div>
      <h1 class="h4 fw-bold mb-1">NEXOR DIGITAL</h1>
      <p class="text-secondary small mb-0">Ingia kusimamia mfumo wako wa hotspot</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-3">
        <label class="nx-form-label">Username</label>
        <input type="text" name="username" class="form-control" placeholder="admin" required autofocus>
      </div>
      <div class="mb-2">
        <label class="nx-form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>
      <div class="text-end mb-3">
        <a href="forgot-password.php" class="small text-decoration-none">Umesahau password?</a>
      </div>
      <button type="submit" class="btn btn-nx-primary w-100 py-2">
        <i class="bi bi-box-arrow-in-right"></i> Ingia
      </button>
    </form>

    <p class="text-center text-secondary small mt-4 mb-0">© <?= date('Y') ?> Nexor Digital · Tanzania</p>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
