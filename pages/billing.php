<?php if (!defined('NEXOR_APP')) { http_response_code(403); exit; }
$billing = $data['billing'];
$isFree = $billing['price'] <= 0;
?>

<div class="row g-3 mb-4">
  <div class="col-lg-4">
    <div class="nx-card nx-card-body h-100">
      <div class="text-secondary small mb-1">Mpango wa Sasa</div>
      <div class="fs-3 fw-bold"><?= htmlspecialchars($billing['plan']) ?></div>
      <div class="text-secondary small">
        <?= $isFree ? 'Hakuna malipo kwa sasa' : 'TZS ' . money($billing['price']) . ' / ' . htmlspecialchars($billing['cycle']) ?>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="nx-card nx-card-body h-100">
      <div class="text-secondary small mb-1">Kulipa Tena</div>
      <div class="fs-4 fw-bold"><?= $billing['next_renewal'] ? htmlspecialchars($billing['next_renewal']) : '—' ?></div>
      <span class="nx-badge <?= $isFree ? 'nx-badge-warning' : 'nx-badge-active' ?>"><?= $isFree ? 'Free tier' : 'Active' ?></span>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="nx-card nx-card-body h-100 d-flex flex-column justify-content-center">
      <button class="btn btn-nx-primary" disabled title="Malipo ya kweli bado hayajaunganishwa">
        <i class="bi bi-arrow-up-circle"></i> Boresha Mpango
      </button>
    </div>
  </div>
</div>

<div class="nx-card">
  <div class="nx-card-body pb-0">
    <h6 class="fw-bold"><i class="bi bi-receipt"></i> Historia ya Malipo</h6>
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr class="text-secondary small text-uppercase">
          <th class="ps-4">Invoice</th>
          <th>Tarehe</th>
          <th>Kiasi</th>
          <th class="pe-4">Hali</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($billing['invoices'] as $inv): ?>
        <tr>
          <td class="ps-4 fw-semibold"><?= htmlspecialchars($inv['id']) ?></td>
          <td><?= htmlspecialchars($inv['date']) ?></td>
          <td>TZS <?= money($inv['amount']) ?></td>
          <td class="pe-4"><span class="nx-badge nx-badge-active">Paid</span></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($billing['invoices'])): ?>
        <tr><td colspan="4" class="text-center text-secondary py-4">Hakuna malipo bado kwenye akaunti hii.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
