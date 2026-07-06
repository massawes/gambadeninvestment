<?php if (!defined('NEXOR_APP')) { http_response_code(403); exit; }
$msg = flash('voucher_msg');
$generated = $_SESSION['generated_vouchers'] ?? null;
unset($_SESSION['generated_vouchers']);

$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT v.*, b.name AS bundle_name, b.price
          FROM vouchers v
          JOIN bundles b ON b.id = v.bundle_id
         WHERE 1=1";
$params = [];
if ($search !== '') {
    $sql .= ' AND v.code LIKE ?';
    $params[] = "%{$search}%";
}
if (in_array($statusFilter, ['unused', 'active', 'expired'], true)) {
    $sql .= ' AND v.status = ?';
    $params[] = $statusFilter;
}
$sql .= ' ORDER BY v.id DESC LIMIT 200';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$vouchers = $stmt->fetchAll();
?>

<?php if ($msg): ?>
<div class="alert alert-success py-2 small"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if ($generated): ?>
<div class="nx-card mb-4">
  <div class="nx-card-body">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
      <h6 class="fw-bold mb-0"><i class="bi bi-ticket-perforated"></i> Vocha Mpya Zilizotengenezwa &mdash; Chapisha!</h6>
      <button class="btn btn-outline-secondary btn-sm no-print" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
    </div>
    <div class="row g-2">
      <?php foreach ($generated as $v): ?>
      <div class="col-6 col-md-4 col-lg-3">
        <div class="border rounded-3 p-3 text-center" style="border-style:dashed !important;border-color:var(--nx-primary) !important;">
          <div class="fw-bold small"><?= htmlspecialchars($v['code']) ?></div>
          <div class="fs-4 fw-bold" style="letter-spacing:3px;"><?= htmlspecialchars($v['pin']) ?></div>
          <div class="text-secondary" style="font-size:11px;"><?= htmlspecialchars($v['bundle_name']) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="row g-3 mb-4">
  <div class="col-lg-5">
    <div class="nx-card nx-card-body h-100">
      <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle"></i> Tengeneza Vocha Mpya</h6>
      <form method="POST">
        <input type="hidden" name="action" value="generate_vouchers">
        <div class="mb-3">
          <label class="nx-form-label">Bundle</label>
          <select name="bundle_id" class="form-select" required>
            <?php foreach ($data['bundles'] as $b): ?>
              <?php if ($b['status'] === 'active'): ?>
              <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?> &mdash; TZS <?= money($b['price']) ?></option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="nx-form-label">Idadi ya Vocha</label>
          <input type="number" name="quantity" class="form-control" value="1" min="1" max="100" required>
          <div class="form-text">Hii pia itarekodi mauzo (Revenue) kiotomatiki.</div>
        </div>
        <button type="submit" class="btn btn-nx-primary w-100"><i class="bi bi-ticket-perforated"></i> Tengeneza Vocha</button>
      </form>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="nx-card nx-card-body h-100">
      <h6 class="fw-bold mb-3"><i class="bi bi-info-circle"></i> Jinsi Inavyofanya Kazi</h6>
      <ol class="small text-secondary ps-3 mb-0">
        <li class="mb-2">Mteja analipa (M-Pesa/Airtel/SMS/WhatsApp uthibitisho).</li>
        <li class="mb-2">Chagua bundle aliyolipia, tengeneza vocha 1 (au zaidi).</li>
        <li class="mb-2">Mpe mteja Code na PIN zilizotengenezwa (andika au piga picha).</li>
        <li>Ataingiza kwenye ukurasa wa mbele (<code>index.php</code>) &mdash; muda wa vocha huanza kuhesabu tangu aingie mara ya kwanza.</li>
      </ol>
    </div>
  </div>
</div>

<div class="nx-card">
  <div class="nx-card-body pb-0">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h6 class="fw-bold">
        <i class="bi bi-ticket-perforated"></i> Vocha Zote (<?= count($vouchers) ?>)
      </h6>
      <form method="GET" class="d-flex gap-2 flex-wrap">
        <input type="hidden" name="page" value="vouchers">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control form-control-sm" placeholder="Tafuta code..." style="width:160px;">
        <select name="status" class="form-select form-select-sm" style="width:130px;" onchange="this.form.submit()">
          <option value="">Zote</option>
          <option value="unused" <?= $statusFilter === 'unused' ? 'selected' : '' ?>>Hazijatumika</option>
          <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="expired" <?= $statusFilter === 'expired' ? 'selected' : '' ?>>Zimeisha</option>
        </select>
        <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
      </form>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr class="text-secondary small text-uppercase">
          <th class="ps-4">Code</th>
          <th>PIN</th>
          <th>Bundle</th>
          <th>Hali</th>
          <th>Inaisha</th>
          <th class="pe-4 text-end">Futa</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vouchers as $v): ?>
        <tr>
          <td class="ps-4 fw-semibold"><?= htmlspecialchars($v['code']) ?></td>
          <td style="letter-spacing:2px;"><?= htmlspecialchars($v['pin']) ?></td>
          <td><?= htmlspecialchars($v['bundle_name']) ?></td>
          <td>
            <?php if ($v['status'] === 'unused'): ?>
              <span class="nx-badge nx-badge-warning">Haijatumika</span>
            <?php elseif ($v['status'] === 'active'): ?>
              <span class="nx-badge nx-badge-active">Active</span>
            <?php else: ?>
              <span class="nx-badge nx-badge-inactive">Imeisha</span>
            <?php endif; ?>
          </td>
          <td style="font-size:12px;"><?= $v['expires_at'] ? date('d M Y H:i', strtotime($v['expires_at'])) : '&mdash;' ?></td>
          <td class="pe-4 text-end">
            <form method="POST" data-confirm="Futa vocha hii?">
              <input type="hidden" name="action" value="delete_voucher">
              <input type="hidden" name="id" value="<?= $v['id'] ?>">
              <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($vouchers)): ?>
        <tr><td colspan="6" class="text-center text-secondary py-4">Hakuna vocha bado. Tengeneza hapo juu.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
