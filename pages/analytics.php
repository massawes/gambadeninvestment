<?php if (!defined('NEXOR_APP')) { http_response_code(403); exit; }

$weekly = get_weekly_sales();
$max = max(1, max(array_column($weekly, 'qty')));

$todayRow = end($weekly);
$todaySales   = $todayRow['qty'];
$todayRevenue = $todayRow['revenue'];
$weekSales    = array_sum(array_column($weekly, 'qty'));
$weekRevenue  = array_sum(array_column($weekly, 'revenue'));
$avgPerDay    = round($weekSales / 7, 1);
?>

<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="nx-card nx-card-body">
      <div class="text-secondary small mb-1">Mauzo Leo</div>
      <div class="fs-4 fw-bold"><?= $todaySales ?></div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="nx-card nx-card-body">
      <div class="text-secondary small mb-1">Mapato Leo</div>
      <div class="fs-4 fw-bold">TZS <?= money($todayRevenue) ?></div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="nx-card nx-card-body">
      <div class="text-secondary small mb-1">Wastani/Siku (Wiki hii)</div>
      <div class="fs-4 fw-bold"><?= $avgPerDay ?></div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="nx-card nx-card-body">
      <div class="text-secondary small mb-1">Mapato Wiki Hii</div>
      <div class="fs-4 fw-bold">TZS <?= money($weekRevenue) ?></div>
    </div>
  </div>
</div>

<div class="nx-card nx-card-body">
  <h6 class="fw-bold mb-4"><i class="bi bi-bar-chart-line"></i> Mauzo ya Wiki Hii (Real-time)</h6>
  <div class="d-flex align-items-end gap-3" style="height:180px;">
    <?php foreach ($weekly as $day): ?>
    <div class="d-flex flex-column align-items-center flex-fill">
      <div class="text-secondary small mb-1"><?= $day['qty'] ?></div>
      <div style="width:100%;max-width:36px;height:<?= round(($day['qty'] / $max) * 130) + 4 ?>px;
                  background:linear-gradient(180deg,var(--nx-accent),var(--nx-primary));
                  border-radius:8px 8px 0 0;"></div>
      <div class="text-secondary small mt-2"><?= $day['label'] ?></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php if ($weekSales === 0): ?>
  <div class="text-center text-secondary small mt-3">
    Hakuna mauzo yaliyorekodiwa wiki hii bado. Nenda <a href="admin.php?page=bundles">Bundles</a> na bonyeza "Record Sale" baada ya kila mteja kulipa.
  </div>
  <?php endif; ?>
</div>
