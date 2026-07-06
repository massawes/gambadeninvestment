<?php if (!defined('NEXOR_APP')) { http_response_code(403); exit; }

$totalRevenue = array_sum(array_column($data['bundles'], 'revenue_month'));
$bundlesByRevenue = $data['bundles'];
usort($bundlesByRevenue, fn($a, $b) => $b['revenue_month'] <=> $a['revenue_month']);

$recentSales = db()->query(
    "SELECT s.*, b.name AS bundle_name
       FROM sales s
       JOIN bundles b ON b.id = s.bundle_id
      ORDER BY s.sold_at DESC
      LIMIT 50"
)->fetchAll();
?>

<div class="nx-card nx-card-body text-center mb-4" style="background:linear-gradient(135deg,var(--nx-primary),var(--nx-accent));color:#fff;">
  <div class="small opacity-75 text-uppercase">Jumla ya Mapato (Mwezi Huu)</div>
  <div class="display-6 fw-bold">TZS <?= money($totalRevenue) ?></div>
</div>

<div class="nx-card mb-4">
  <div class="nx-card-body pb-0">
    <h6 class="fw-bold"><i class="bi bi-bar-chart"></i> Mapato kwa Bundle (Mwezi Huu)</h6>
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr class="text-secondary small text-uppercase">
          <th class="ps-4">Bundle</th>
          <th>Mauzo</th>
          <th>Bei Moja</th>
          <th class="pe-4">Mapato</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($bundlesByRevenue as $b): ?>
        <tr>
          <td class="ps-4 fw-semibold"><?= htmlspecialchars($b['name']) ?></td>
          <td><?= $b['sales_month'] ?></td>
          <td>TZS <?= money($b['price']) ?></td>
          <td class="pe-4 fw-bold">TZS <?= money($b['revenue_month']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($bundlesByRevenue)): ?>
        <tr><td colspan="4" class="text-center text-secondary py-4">Hakuna bundle bado.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="nx-card">
  <div class="nx-card-body pb-0">
    <h6 class="fw-bold"><i class="bi bi-receipt"></i> Mauzo ya Hivi Karibuni</h6>
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr class="text-secondary small text-uppercase">
          <th class="ps-4">#</th>
          <th>Bundle</th>
          <th>Idadi</th>
          <th>Kiasi</th>
          <th class="pe-4">Wakati</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recentSales as $i => $s): ?>
        <tr>
          <td class="ps-4"><?= $i + 1 ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($s['bundle_name']) ?></td>
          <td><?= (int) $s['quantity'] ?></td>
          <td>TZS <?= money($s['amount']) ?></td>
          <td class="pe-4" style="font-size:12px;"><?= date('d M Y H:i', strtotime($s['sold_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($recentSales)): ?>
        <tr><td colspan="5" class="text-center text-secondary py-4">
          Bado hakuna mauzo yaliyorekodiwa. Nenda <a href="admin.php?page=bundles">Bundles</a> na bonyeza "Record Sale".
        </td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
