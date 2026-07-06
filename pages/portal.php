<?php if (!defined('NEXOR_APP')) { http_response_code(403); exit; }
$msg = flash('portal_msg');
$portal = $data['portal'];
?>

<?php if ($msg): ?>
<div class="alert alert-success py-2 small"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="nx-card nx-card-body">
      <h6 class="fw-bold mb-3"><i class="bi bi-palette"></i> Portal Branding</h6>
      <form method="POST">
        <input type="hidden" name="action" value="save_portal">
        <div class="mb-3">
          <label class="nx-form-label">Jina la Biashara</label>
          <input type="text" name="business_name" class="form-control" value="<?= htmlspecialchars($portal['business_name']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="nx-form-label">Ujumbe wa Karibu</label>
          <input type="text" name="welcome_text" class="form-control" value="<?= htmlspecialchars($portal['welcome_text']) ?>">
        </div>
        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="nx-form-label">Rangi Kuu</label>
            <input type="color" name="primary_color" class="form-control form-control-color w-100" value="<?= htmlspecialchars($portal['primary_color']) ?>">
          </div>
          <div class="col-6">
            <label class="nx-form-label">Lipa Namba</label>
            <input type="text" name="lipa_number" class="form-control" value="<?= htmlspecialchars($portal['lipa_number']) ?>">
          </div>
        </div>
        <div class="mb-3">
          <label class="nx-form-label">Namba ya SMS/WhatsApp (uthibitisho wa malipo)</label>
          <input type="text" name="contact_phone" class="form-control" placeholder="+255745325531" value="<?= htmlspecialchars($portal['contact_phone'] ?? '') ?>">
        </div>
        <button type="submit" class="btn btn-nx-primary"><i class="bi bi-save"></i> Save Portal Settings</button>
      </form>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="nx-card nx-card-body">
      <h6 class="fw-bold mb-3"><i class="bi bi-eye"></i> Live Preview</h6>
      <div class="rounded-4 p-4 text-center text-white" style="background:linear-gradient(135deg,<?= htmlspecialchars($portal['primary_color']) ?>,#0f172a);">
        <div class="fs-3 mb-1"><i class="bi bi-wifi"></i></div>
        <div class="fw-bold"><?= htmlspecialchars($portal['business_name']) ?></div>
        <div class="small opacity-75 mb-3"><?= htmlspecialchars($portal['welcome_text']) ?></div>
        <div class="bg-white bg-opacity-10 rounded-3 p-3 text-start small">
          <div class="mb-2 fw-semibold">Ingiza Voucher Yako</div>
          <div class="bg-white bg-opacity-25 rounded-2 p-2 mb-2">Username</div>
          <div class="bg-white bg-opacity-25 rounded-2 p-2">Password</div>
        </div>
      </div>
      <div class="text-secondary small mt-3">
        Hii ndiyo mwonekano ambao wateja wako wataona wanapo-connect kwenye hotspot yako
        (ukurasa <code>index.php</code>).
      </div>
    </div>
  </div>
</div>
