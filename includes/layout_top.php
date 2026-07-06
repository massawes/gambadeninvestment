<?php if (!defined('NEXOR_APP')) { http_response_code(403); exit; } ?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> — Gambaden Investment</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="nx-shell">
<?php require __DIR__ . '/sidebar.php'; ?>

  <div class="nx-main">
    <header class="nx-topbar">
      <div class="d-flex align-items-center gap-3">
        <button class="nx-icon-btn d-md-none" id="nxSidebarToggle"><i class="bi bi-list"></i></button>
        <div>
          <h1><?= htmlspecialchars($pageTitle ?? '') ?></h1>
          <?php if (!empty($pageSubtitle)): ?><div class="sub"><?= htmlspecialchars($pageSubtitle) ?></div><?php endif; ?>
        </div>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button class="nx-icon-btn" id="nxThemeToggle" title="Badilisha mwonekano"><i class="bi bi-moon-stars"></i></button>
        <button class="nx-icon-btn" title="Arifa">
          <i class="bi bi-bell"></i>
          <span class="nx-badge-dot">3</span>
        </button>
      </div>
    </header>

    <main class="nx-content">
