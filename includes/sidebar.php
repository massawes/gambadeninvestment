<?php if (!defined('NEXOR_APP')) { http_response_code(403); exit; } ?>
<div class="nx-sidebar-backdrop"></div>
<aside class="nx-sidebar">
  <div class="nx-sidebar-brand">
    <div class="logo-mark"><i class="bi bi-reception-4"></i></div>
    <div>
      <div class="brand-name">GAMBADEN</div>
      <div class="brand-sub">INVESTEMENT</div>
    </div>
  </div>

  <nav class="nx-nav">
    <?php
    $nx_links = [
      'dashboard' => ['bi-grid-1x2-fill',   'Dashboard'],
      'sites'     => ['bi-broadcast',       'Sites'],
      'devices'   => ['bi-hdd-network',     'Devices'],
      'bundles'   => ['bi-box-seam',        'Bundles'],
      'portal'    => ['bi-palette',         'Portal'],
      'profile'   => ['bi-person',          'Profile'],
    ];
    foreach ($nx_links as $key => [$icon, $label]):
    ?>
    <a href="admin.php?page=<?= $key ?>" class="<?= $page === $key ? 'active' : '' ?>">
      <i class="bi <?= $icon ?>"></i> <?= $label ?>
    </a>
    <?php endforeach; ?>
  </nav>

  <div class="px-3 pb-2"> 
    <a href="#" class="d-flex align-items-center gap-2 px-2 py-2 text-decoration-none" style="color:var(--nx-muted);font-size:13px;">
      <i class="bi bi-question-circle"></i> Help &amp; Support
    </a>
  </div>

  <div class="nx-sidebar-footer">
    <div class="nx-avatar"><?= strtoupper(substr($data['profile']['first_name'], 0, 1)) ?></div>
    <div class="flex-grow-1 overflow-hidden">
      <div class="who text-truncate"><?= htmlspecialchars($data['profile']['first_name'] . ' ' . $data['profile']['last_name']) ?></div>
      <div class="email text-truncate"><?= htmlspecialchars($data['profile']['email']) ?></div>
    </div>
    <a href="logout.php" class="nx-icon-btn" title="Logout" style="width:32px;height:32px;">
      <i class="bi bi-box-arrow-right"></i>
    </a>
  </div>
</aside>
