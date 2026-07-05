<?php if (!defined('NEXOR_APP')) { http_response_code(403); exit; }

$totalRevenue = array_sum(array_map(fn($b) => $b['price'] * $b['sales_month'], $data['bundles']));
usort($data['bundles'], fn($a, $b) => ($b['price'] * $b['sales_month']) <=> ($a['price'] * $a['sales_month']));
?>

<div class="nx-card nx-card-body text-center mb-4" style="background:linear-gradient(135deg,var(--nx-primary),var(--nx-accent));color:#fff;">
  <div class="small opacity-75 text-uppercase">Jumla ya Mapato (Mwezi Huu)</div>
  <div class="display-6 fw-bold">TZS <?= money($totalRevenue) ?></div>
</div>

<div class="nx-card">
  <div class="nx-card-body pb-0">
    <h6 class="fw-bold"><i class="bi bi-bar-chart"></i> Mapato kwa Bundle</h6>
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
        <?php foreach ($data['bundles'] as $b): ?>
        <tr>
          <td class="ps-4 fw-semibold"><?= htmlspecialchars($b['name']) ?></td>
          <td><?= $b['sales_month'] ?></td>
          <td>TZS <?= money($b['price']) ?></td>
          <td class="pe-4 fw-bold">TZS <?= money($b['price'] * $b['sales_month']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
