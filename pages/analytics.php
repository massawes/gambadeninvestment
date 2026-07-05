<?php if (!defined('NEXOR_APP')) { http_response_code(403); exit; }

// Mock weekly usage — TODO(mysql): pull from radacct/radpostauth once wired up.
$weekly = [
    ['label' => 'Mon', 'value' => 42],
    ['label' => 'Tue', 'value' => 58],
    ['label' => 'Wed', 'value' => 37],
    ['label' => 'Thu', 'value' => 71],
    ['label' => 'Fri', 'value' => 90],
    ['label' => 'Sat', 'value' => 65],
    ['label' => 'Sun', 'value' => 48],
];
$max = max(array_column($weekly, 'value'));
$totalUsersToday = 24;
$avgSession = '38 min';
$dataUsedToday = '6.2 GB';
?>

<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="nx-card nx-card-body">
      <div class="text-secondary small mb-1">Watumiaji Leo</div>
      <div class="fs-4 fw-bold"><?= $totalUsersToday ?></div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="nx-card nx-card-body">
      <div class="text-secondary small mb-1">Muda wa Wastani</div>
      <div class="fs-4 fw-bold"><?= $avgSession ?></div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="nx-card nx-card-body">
      <div class="text-secondary small mb-1">Data Iliyotumika</div>
      <div class="fs-4 fw-bold"><?= $dataUsedToday ?></div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="nx-card nx-card-body">
      <div class="text-secondary small mb-1">Sites Zilizounganika</div>
      <div class="fs-4 fw-bold"><?= count(array_filter($data['sites'], fn($s) => $s['status'] === 'online')) ?></div>
    </div>
  </div>
</div>

<div class="nx-card nx-card-body">
  <h6 class="fw-bold mb-4"><i class="bi bi-bar-chart-line"></i> Matumizi ya Wiki Hii</h6>
  <div class="d-flex align-items-end gap-3" style="height:180px;">
    <?php foreach ($weekly as $day): ?>
    <div class="d-flex flex-column align-items-center flex-fill">
      <div style="width:100%;max-width:36px;height:<?= round($day['value'] / $max * 140) ?>px;
                  background:linear-gradient(180deg,var(--nx-accent),var(--nx-primary));
                  border-radius:8px 8px 0 0;"></div>
      <div class="text-secondary small mt-2"><?= $day['label'] ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
