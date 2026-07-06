<?php
require __DIR__ . '/includes/config.php';

$sent = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (empty($email)) {
        $error = 'Tafadhali ingiza email yako.';
    } else {
        $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = 'Hakuna account yenye email hiyo.';
        } else {
            // TODO(mailer): send a real email with this link instead of
            // showing it on screen. The token itself is now persisted in
            // the database (password_resets), not the session.
            $token = bin2hex(random_bytes(16));

            // Expiry is computed by MySQL's own clock (NOW()), not PHP's,
            // so the two servers' clocks can never drift apart on this check.
            db()->prepare(
                'INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))'
            )->execute([$user['id'], $token]);

            $_SESSION['reset_token'] = $token;
            $_SESSION['reset_email'] = $email;
            $sent = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Umesahau Password — Gambaden Investment</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <div class="text-center mb-4">
      <div class="mx-auto mb-3" style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,var(--nx-primary),var(--nx-accent));display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
        <i class="bi bi-key"></i>
      </div>
      <h1 class="h5 fw-bold mb-1">Umesahau Password?</h1>
      <p class="text-secondary small mb-0">Ingiza email yako, tutakutumia link ya kubadilisha password.</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($sent): ?>
    <div class="alert alert-success py-2 small">
      ✅ Maelekezo yametumwa kwa <strong><?= htmlspecialchars($_SESSION['reset_email']) ?></strong>.
    </div>
    <div class="alert alert-warning py-2 small">
      Mfumo bado haujaunganishwa na huduma ya email (itaongezwa pamoja na MySQL).
      Kwa sasa, tumia link hii moja kwa moja kuendelea:
      <a href="reset-password.php?token=<?= urlencode($_SESSION['reset_token']) ?>">Weka Password Mpya</a>
    </div>
    <?php else: ?>
    <form method="POST" novalidate>
      <div class="mb-3">
        <label class="nx-form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="jina@mfano.com" required autofocus>
      </div>
      <button type="submit" class="btn btn-nx-primary w-100 py-2">
        <i class="bi bi-send"></i> Tuma Maelekezo
      </button>
    </form>
    <?php endif; ?>

    <p class="text-center small mt-4 mb-0">
      <a href="login.php" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Rudi kuingia</a>
    </p>
  </div>
</div>
</body>
</html>
