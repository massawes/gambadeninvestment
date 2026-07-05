<?php if (!defined('NEXOR_APP')) { http_response_code(403); exit; }
$msg = flash('bundle_msg');

function bundle_duration_label(array $b): string {
    $unit = $b['duration_value'] == 1 ? rtrim($b['duration_unit'], 's') : $b['duration_unit'];
    return $b['duration_value'] . ' ' . $unit;
}
?>

<?php if ($msg): ?>
<div class="alert alert-success py-2 small"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
  <div class="nx-tip-banner flex-grow-1">
    <div class="d-flex align-items-start gap-3">
      <div class="nx-stat-icon" style="background:#0ea5e9;color:#fff;"><i class="bi bi-lightning-charge-fill"></i></div>
      <div>
        <div class="fw-semibold">Bundle Configuration Tips</div>
        <div class="text-secondary small">Create packages that match your customer needs. Popular options: 1 Hour, 1 Day, 1 Week</div>
        <div class="text-secondary small mt-1">
          <i class="bi bi-tag"></i> Set competitive prices &nbsp;·&nbsp;
          <i class="bi bi-speedometer2"></i> Define speed limits &nbsp;·&nbsp;
          <i class="bi bi-bar-chart"></i> Track sales
        </div>
      </div>
    </div>
  </div>
  <button class="btn btn-nx-primary px-3" data-bs-toggle="modal" data-bs-target="#createBundleModal">
    <i class="bi bi-plus-lg"></i> Create Bundle
  </button>
</div>

<div class="row g-3">
  <?php foreach ($data['bundles'] as $b): ?>
  <div class="col-md-6 col-lg-4">
    <div class="nx-card nx-card-body h-100 d-flex flex-column">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <div class="nx-stat-icon" style="background:linear-gradient(135deg,var(--nx-primary),var(--nx-accent));color:#fff;">
          <i class="bi bi-box-seam"></i>
        </div>
        <span class="nx-badge <?= $b['status'] === 'active' ? 'nx-badge-active' : 'nx-badge-inactive' ?>">
          <?= $b['status'] === 'active' ? 'Active' : 'Inactive' ?>
        </span>
      </div>

      <div class="fw-bold fs-6"><?= htmlspecialchars($b['name']) ?></div>
      <div class="text-secondary small mb-2"><i class="bi bi-clock"></i> <?= bundle_duration_label($b) ?></div>

      <div class="mb-3">
        <span class="fs-4 fw-bold"><?= money($b['price']) ?></span>
        <span class="text-secondary small">TZS</span>
      </div>

      <div class="small mb-3">
        <div class="d-flex justify-content-between py-1 border-bottom">
          <span class="text-secondary">Download Speed:</span>
          <span class="fw-semibold"><?= htmlspecialchars($b['speed']) ?></span>
        </div>
        <div class="d-flex justify-content-between py-1">
          <span class="text-secondary">Data Limit:</span>
          <span class="fw-semibold"><?= htmlspecialchars($b['data_limit']) ?></span>
        </div>
      </div>

      <div class="text-success small mb-3"><i class="bi bi-graph-up-arrow"></i> <?= $b['sales_month'] ?> sales this month</div>

      <div class="mt-auto d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#editBundle<?= $b['id'] ?>">
          <i class="bi bi-pencil"></i>
        </button>
        <form method="POST" class="flex-fill">
          <input type="hidden" name="action" value="toggle_bundle">
          <input type="hidden" name="id" value="<?= $b['id'] ?>">
          <button type="submit" class="btn btn-sm w-100 <?= $b['status'] === 'active' ? 'btn-outline-warning' : 'btn-outline-success' ?>">
            <?= $b['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
          </button>
        </form>
        <form method="POST" data-confirm="Una uhakika unataka kufuta bundle hii?">
          <input type="hidden" name="action" value="delete_bundle">
          <input type="hidden" name="id" value="<?= $b['id'] ?>">
          <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Bundle Modal -->
  <div class="modal fade" id="editBundle<?= $b['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Hariri Bundle — <?= htmlspecialchars($b['name']) ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-secondary small">
            Uhariri kamili (jina, muda, bei) utawezekana baada ya mfumo kuunganishwa na database.
            Kwa sasa tumia <strong>Deactivate</strong> au <strong>Delete</strong>.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Funga</button>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Create Bundle Modal -->
<div class="modal fade" id="createBundleModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <input type="hidden" name="action" value="create_bundle">
      <div class="modal-header">
        <h5 class="modal-title">Create New Bundle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="nx-form-label">Jina la Bundle</label>
          <input type="text" name="name" class="form-control" placeholder="Mfano: 1 Week" required>
        </div>
        <div class="row g-2 mb-3">
          <div class="col-6">
            <label class="nx-form-label">Muda</label>
            <input type="number" name="duration_value" class="form-control" value="1" min="1" required>
          </div>
          <div class="col-6">
            <label class="nx-form-label">Kipimo</label>
            <select name="duration_unit" class="form-select">
              <option value="hours">Hours</option>
              <option value="days" selected>Days</option>
              <option value="weeks">Weeks</option>
            </select>
          </div>
        </div>
        <div class="mb-3">
          <label class="nx-form-label">Bei (TZS)</label>
          <input type="number" name="price" class="form-control" placeholder="1000" min="0" required>
        </div>
        <div class="row g-2">
          <div class="col-6">
            <label class="nx-form-label">Download Speed</label>
            <input type="text" name="speed" class="form-control" placeholder="Unlimited">
          </div>
          <div class="col-6">
            <label class="nx-form-label">Data Limit</label>
            <input type="text" name="data_limit" class="form-control" placeholder="Unlimited">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Ghairi</button>
        <button type="submit" class="btn btn-nx-primary">Create Bundle</button>
      </div>
    </form>
  </div>
</div>
