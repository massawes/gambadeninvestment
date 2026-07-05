<?php
require __DIR__ . '/includes/config.php';

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$error = '';
$done = false;
$reset = null;

if (!empty($token)) {
    $stmt = db()->prepare(
        'SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() ORDER BY id DESC LIMIT 1'
    );
    $stmt->execute([$token]);
    $reset = $stmt->fetch();
}

$validToken = (bool) $reset;

if (!$validToken) {
    $error = 'Link hii ya kubadilisha password si sahihi au imeisha muda wake.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (strlen($password) < 6) {
        $error = 'Password lazima iwe na angalau herufi 6.';
    } elseif ($password !== $confirm) {
        $error = 'Password hazifanani. Jaribu tena.';
    } else {
        $pdo = db();
        $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
            ->execute([password_hash($password, PASSWORD_DEFAULT), $reset['user_id']]);
        $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?')
            ->execute([$reset['user_id']]);
        unset($_SESSION['reset_token'], $_SESSION['reset_email']);
        $done = true;
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Weka Password Mpya — Nexor Digital</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <div class="text-center mb-4">
      <div class="mx-auto mb-3" style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,var(--nx-primary),var(--nx-accent));display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
        <i class="bi bi-shield-lock"></i>
      </div>
      <h1 class="h5 fw-bold mb-1">Weka Password Mpya</h1>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($done): ?>
    <div class="alert alert-success py-2 small">✅ Password yako imebadilishwa. Sasa unaweza kuingia.</div>
    <a href="login.php" class="btn btn-nx-primary w-100 py-2">Ingia Sasa</a>
    <?php elseif ($validToken): ?>
    <form method="POST" novalidate>
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <div class="mb-3">
        <label class="nx-form-label">Password Mpya</label>
        <input type="password" name="password" class="form-control" required minlength="6" autofocus>
      </div>
      <div class="mb-3">
        <label class="nx-form-label">Rudia Password Mpya</label>
        <input type="password" name="confirm" class="form-control" required minlength="6">
      </div>
      <button type="submit" class="btn btn-nx-primary w-100 py-2">
        <i class="bi bi-check2-circle"></i> Badilisha Password
      </button>
    </form>
    <?php else: ?>
    <a href="forgot-password.php" class="btn btn-nx-primary w-100 py-2">Omba Link Mpya</a>
    <?php endif; ?>

    <p class="text-center small mt-4 mb-0">
      <a href="login.php" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Rudi kuingia</a>
    </p>
  </div>
</div>
</body>
</html>
