<?php if (!defined('NEXOR_APP')) { http_response_code(403); exit; }
$msg = flash('site_msg');
?>

<?php if ($msg): ?>
<div class="alert alert-success py-2 small"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="text-secondary small">Simamia maeneo yote unayotoa huduma ya hotspot.</div>
  <button class="btn btn-nx-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addSiteModal">
    <i class="bi bi-plus-lg"></i> Add Site
  </button>
</div>

<div class="row g-3">
  <?php foreach ($data['sites'] as $site): ?>
  <div class="col-md-6 col-lg-4">
    <div class="nx-card nx-card-body h-100">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <div class="nx-stat-icon" style="background:#eef2ff;color:var(--nx-primary);"><i class="bi bi-broadcast"></i></div>
        <span class="nx-badge <?= $site['status'] === 'online' ? 'nx-badge-active' : 'nx-badge-inactive' ?>">
          <?= $site['status'] === 'online' ? 'Online' : 'Offline' ?>
        </span>
      </div>
      <div class="fw-bold"><?= htmlspecialchars($site['name']) ?></div>
      <div class="text-secondary small mb-3"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($site['location']) ?></div>
      <div class="d-flex justify-content-between align-items-center border-top pt-2">
        <span class="text-secondary small"><i class="bi bi-hdd-network"></i> <?= $site['device_count'] ?> vifaa</span>
        <form method="POST" data-confirm="Una uhakika unataka kufuta site hii?">
          <input type="hidden" name="action" value="delete_site">
          <input type="hidden" name="id" value="<?= $site['id'] ?>">
          <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
        </form>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

  <?php if (empty($data['sites'])): ?>
  <div class="col-12">
    <div class="nx-card nx-card-body text-center text-secondary py-5">Hakuna site bado. Bonyeza "Add Site" kuanza.</div>
  </div>
  <?php endif; ?>
</div>

<!-- Add Site Modal -->
<div class="modal fade" id="addSiteModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <input type="hidden" name="action" value="create_site">
      <div class="modal-header">
        <h5 class="modal-title">Ongeza Site Mpya</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="nx-form-label">Jina la Site</label>
          <input type="text" name="name" class="form-control" placeholder="Mfano: Kijenge Branch" required>
        </div>
        <div class="mb-1">
          <label class="nx-form-label">Location</label>
          <input type="text" name="location" class="form-control" placeholder="Mfano: Kijenge, Arusha" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Ghairi</button>
        <button type="submit" class="btn btn-nx-primary">Hifadhi Site</button>
      </div>
    </form>
  </div>
</div>
