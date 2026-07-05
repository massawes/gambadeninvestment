<?php if (!defined('NEXOR_APP')) { http_response_code(403); exit; }

$totalSites    = count($data['sites']);
$onlineSites   = count(array_filter($data['sites'], fn($s) => $s['status'] === 'online'));
$totalDevices  = count($data['devices']);
$activeBundles = count(array_filter($data['bundles'], fn($b) => $b['status'] === 'active'));
$monthRevenue  = array_sum(array_map(fn($b) => $b['price'] * $b['sales_month'], $data['bundles']));
$monthSales    = array_sum(array_column($data['bundles'], 'sales_month'));
?>

<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="nx-card nx-card-body d-flex align-items-center justify-content-between">
      <div>
        <div class="fs-4 fw-bold"><?= $onlineSites ?>/<?= $totalSites ?></div>
        <div class="text-secondary small">Sites Online</div>
      </div>
      <div class="nx-stat-icon" style="background:#eef2ff;color:var(--nx-primary);"><i class="bi bi-broadcast"></i></div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="nx-card nx-card-body d-flex align-items-center justify-content-between">
      <div>
        <div class="fs-4 fw-bold"><?= $totalDevices ?></div>
        <div class="text-secondary small">Devices</div>
      </div>
      <div class="nx-stat-icon" style="background:#e0f2fe;color:#0284c7;"><i class="bi bi-hdd-network"></i></div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="nx-card nx-card-body d-flex align-items-center justify-content-between">
      <div>
        <div class="fs-4 fw-bold"><?= $activeBundles ?></div>
        <div class="text-secondary small">Active Bundles</div>
      </div>
      <div class="nx-stat-icon" style="background:#dcfce7;color:var(--nx-success);"><i class="bi bi-box-seam"></i></div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="nx-card nx-card-body d-flex align-items-center justify-content-between">
      <div>
        <div class="fs-4 fw-bold">TZS <?= money($monthRevenue) ?></div>
        <div class="text-secondary small">Revenue (mwezi huu)</div>
      </div>
      <div class="nx-stat-icon" style="background:#fef3c7;color:var(--nx-warning);"><i class="bi bi-cash-coin"></i></div>
    </div>
  </div>
</div>

<div class="nx-tip-banner mb-4 d-flex align-items-start gap-3">
  <i class="bi bi-lightning-charge-fill fs-4" style="color:var(--nx-primary);"></i>
  <div>
    <div class="fw-semibold">Karibu tena, <?= htmlspecialchars($data['profile']['first_name']) ?>!</div>
    <div class="text-secondary small">Umefanya mauzo <?= $monthSales ?> mwezi huu kutoka bundles zako <?= $activeBundles ?> zilizo active.</div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="nx-card nx-card-body h-100">
      <h6 class="fw-bold mb-3"><i class="bi bi-lightning-charge"></i> Vitendo vya Haraka</h6>
      <div class="row g-2">
        <div class="col-6 col-md-3">
          <a href="admin.php?page=bundles" class="nx-card nx-card-body text-center text-decoration-none d-block py-3">
            <i class="bi bi-box-seam fs-4 d-block mb-2" style="color:var(--nx-primary);"></i>
            <span class="small fw-semibold" style="color:var(--nx-text);">Bundles</span>
          </a>
        </div>
        <div class="col-6 col-md-3">
          <a href="admin.php?page=sites" class="nx-card nx-card-body text-center text-decoration-none d-block py-3">
            <i class="bi bi-broadcast fs-4 d-block mb-2" style="color:var(--nx-primary);"></i>
            <span class="small fw-semibold" style="color:var(--nx-text);">Sites</span>
          </a>
        </div>
        <div class="col-6 col-md-3">
          <a href="admin.php?page=analytics" class="nx-card nx-card-body text-center text-decoration-none d-block py-3">
            <i class="bi bi-bar-chart-line fs-4 d-block mb-2" style="color:var(--nx-primary);"></i>
            <span class="small fw-semibold" style="color:var(--nx-text);">Analytics</span>
          </a>
        </div>
        <div class="col-6 col-md-3">
          <a href="admin.php?page=revenue" class="nx-card nx-card-body text-center text-decoration-none d-block py-3">
            <i class="bi bi-cash-coin fs-4 d-block mb-2" style="color:var(--nx-primary);"></i>
            <span class="small fw-semibold" style="color:var(--nx-text);">Revenue</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="nx-card nx-card-body h-100">
      <h6 class="fw-bold mb-3"><i class="bi bi-box-seam"></i> Bundles Zinazouzwa Zaidi</h6>
      <?php
      $top = $data['bundles'];
      usort($top, fn($a, $b) => $b['sales_month'] <=> $a['sales_month']);
      foreach (array_slice($top, 0, 4) as $b):
      ?>
      <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
        <div>
          <div class="fw-semibold small"><?= htmlspecialchars($b['name']) ?></div>
          <div class="text-secondary" style="font-size:11.5px;">TZS <?= money($b['price']) ?></div>
        </div>
        <span class="nx-badge nx-badge-active"><?= $b['sales_month'] ?> mauzo</span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
