<?php if (!defined('NEXOR_APP')) { http_response_code(403); exit; }
$profile = $data['profile'];
$profileMsg = flash('profile_msg');
$passwordError = flash('password_error');
?>

<?php if ($profileMsg): ?>
<div class="alert alert-success py-2 small"><?= htmlspecialchars($profileMsg) ?></div>
<?php endif; ?>

<div class="nx-card mb-4" style="background:linear-gradient(135deg,var(--nx-primary),var(--nx-accent));color:#fff;">
  <div class="nx-card-body d-flex align-items-center gap-3 flex-wrap">
    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width:64px;height:64px;font-size:26px;">
      <i class="bi bi-person"></i>
    </div>
    <div>
      <div class="fs-5 fw-bold"><?= htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) ?></div>
      <div class="small opacity-75 d-flex flex-wrap gap-3 mt-1">
        <span><i class="bi bi-envelope"></i> <?= htmlspecialchars($profile['email']) ?></span>
        <span><i class="bi bi-telephone"></i> <?= htmlspecialchars($profile['phone']) ?></span>
        <span><i class="bi bi-shield-check"></i> <?= htmlspecialchars($profile['account_type']) ?></span>
        <span><i class="bi bi-calendar3"></i> Member since <?= htmlspecialchars($profile['member_since']) ?></span>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="nx-card nx-card-body">
      <h6 class="fw-bold mb-3"><i class="bi bi-person"></i> Profile Information</h6>
      <form method="POST">
        <input type="hidden" name="action" value="save_profile">
        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="nx-form-label">First Name</label>
            <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($profile['first_name']) ?>" required>
          </div>
          <div class="col-6">
            <label class="nx-form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($profile['last_name']) ?>" required>
          </div>
        </div>
        <div class="mb-3">
          <label class="nx-form-label">Email</label>
          <input type="email" class="form-control" value="<?= htmlspecialchars($profile['email']) ?>" disabled>
          <div class="form-text">Email cannot be changed</div>
        </div>
        <div class="mb-3">
          <label class="nx-form-label">Phone Number</label>
          <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($profile['phone']) ?>">
        </div>

        <hr>
        <h6 class="fw-bold mb-2"><i class="bi bi-bank"></i> Withdrawal Account</h6>
        <div class="alert alert-secondary small">
          Active account: <strong><?= htmlspecialchars($profile['bank_name']) ?></strong> ·
          ****<?= htmlspecialchars(substr($profile['account_number'], -4)) ?><br>
          Changes to an existing withdrawal account require admin approval.
        </div>
        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="nx-form-label">Bank Name</label>
            <select name="bank_name" class="form-select">
              <?php foreach (['CRDB', 'NMB', 'NBC', 'Equity', 'Exim'] as $bank): ?>
              <option <?= $bank === $profile['bank_name'] ? 'selected' : '' ?>><?= $bank ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-6">
            <label class="nx-form-label">Account Number</label>
            <input type="text" name="account_number" class="form-control" value="<?= htmlspecialchars($profile['account_number']) ?>">
          </div>
        </div>

        <button type="submit" class="btn btn-nx-primary"><i class="bi bi-save"></i> Save Profile</button>
      </form>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="nx-card nx-card-body">
      <h6 class="fw-bold mb-3"><i class="bi bi-lock"></i> Change Password</h6>

      <?php if ($passwordError): ?>
      <div class="alert alert-danger py-2 small"><?= htmlspecialchars($passwordError) ?></div>
      <?php endif; ?>

      <form method="POST">
        <input type="hidden" name="action" value="change_password">
        <div class="mb-3">
          <label class="nx-form-label">Current Password</label>
          <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="nx-form-label">New Password</label>
          <input type="password" name="new_password" class="form-control" required minlength="6">
        </div>
        <div class="mb-3">
          <label class="nx-form-label">Confirm New Password</label>
          <input type="password" name="confirm_password" class="form-control" required minlength="6">
        </div>
        <button type="submit" class="btn btn-nx-primary w-100"><i class="bi bi-lock"></i> Change Password</button>
      </form>
    </div>
  </div>
</div>
