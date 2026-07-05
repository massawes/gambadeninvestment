<?php if (!defined('NEXOR_APP')) { http_response_code(403); exit; }
$msg = flash('device_msg');
$sitesById = [];
foreach ($data['sites'] as $s) { $sitesById[$s['id']] = $s['name']; }
?>

<?php if ($msg): ?>
<div class="alert alert-success py-2 small"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="text-secondary small">Routers na access points zilizounganishwa kwenye sites zako.</div>
  <button class="btn btn-nx-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
    <i class="bi bi-plus-lg"></i> Add Device
  </button>
</div>

<div class="nx-card">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr class="text-secondary small text-uppercase">
          <th class="ps-4">Device</th>
          <th>Aina</th>
          <th>Site</th>
          <th>IP Address</th>
          <th>Hali</th>
          <th class="text-end pe-4">Futa</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data['devices'] as $d): ?>
        <tr>
          <td class="ps-4 fw-semibold"><i class="bi bi-hdd-network text-secondary me-1"></i> <?= htmlspecialchars($d['name']) ?></td>
          <td><?= htmlspecialchars($d['type']) ?></td>
          <td><?= htmlspecialchars($sitesById[$d['site_id']] ?? '—') ?></td>
          <td><?= htmlspecialchars($d['ip']) ?></td>
          <td>
            <span class="nx-badge <?= $d['status'] === 'online' ? 'nx-badge-active' : 'nx-badge-inactive' ?>">
              <?= $d['status'] === 'online' ? 'Online' : 'Offline' ?>
            </span>
          </td>
          <td class="text-end pe-4">
            <form method="POST" data-confirm="Futa kifaa hiki?">
              <input type="hidden" name="action" value="delete_device">
              <input type="hidden" name="id" value="<?= $d['id'] ?>">
              <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($data['devices'])): ?>
        <tr><td colspan="6" class="text-center text-secondary py-4">Hakuna kifaa bado.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Device Modal -->
<div class="modal fade" id="addDeviceModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <input type="hidden" name="action" value="create_device">
      <div class="modal-header">
        <h5 class="modal-title">Ongeza Kifaa Kipya</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="nx-form-label">Jina la Kifaa</label>
          <input type="text" name="name" class="form-control" placeholder="Mfano: MikroTik hAP" required>
        </div>
        <div class="mb-3">
          <label class="nx-form-label">Aina</label>
          <select name="type" class="form-select">
            <option>Router</option>
            <option>Access Point</option>
            <option>Switch</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="nx-form-label">Site</label>
          <select name="site_id" class="form-select">
            <?php foreach ($data['sites'] as $s): ?>
            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-1">
          <label class="nx-form-label">IP Address</label>
          <input type="text" name="ip" class="form-control" placeholder="172.16.0.1" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Ghairi</button>
        <button type="submit" class="btn btn-nx-primary">Hifadhi Kifaa</button>
      </div>
    </form>
  </div>
</div>
