<?php
// ================================================================
// GAMBADEN HOTSPOT — Captive Portal
// Lipa Namba: 140197316
// Configure EAC200 to redirect to: http://172.16.0.50/portal/
// ================================================================

$clientIp  = $_GET['clientIp']  ?? $_SERVER['REMOTE_ADDR'] ?? '';
$clientMac = $_GET['clientMac'] ?? '';
$gatewayIp = $_GET['gatewayIp'] ?? '172.16.0.1';

// ---- Pull branding + live pricing from the Nexor Digital dashboard DB ----
// Falls back to sensible defaults if that DB isn't reachable yet, so this
// public customer page never breaks even before MySQL is fully set up.
require_once __DIR__ . '/includes/db_config.php';

$portal = [
    'business_name' => 'GAMBADEN HOTSPOT',
    'welcome_text'  => 'Internet ya Uhakika · Arusha, Tanzania',
    'primary_color' => '#4f46e5',
    'lipa_number'   => '140197316',
    'contact_phone' => '+255745325531',
];
$bundles = [];
$bundleEmojis = ['⚡', '🌟', '🔥', '💎', '👑', '🚀'];

try {
    $nexorDb = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 3]
    );

    $settingsRow = $nexorDb->query('SELECT * FROM portal_settings WHERE id = 1')->fetch(PDO::FETCH_ASSOC);
    if ($settingsRow) {
        $portal = array_merge($portal, $settingsRow);
    }

    $bundles = $nexorDb->query(
        "SELECT b.*, COALESCE(m.qty, 0) AS sales_month
           FROM bundles b
           LEFT JOIN (
                SELECT bundle_id, SUM(quantity) AS qty
                  FROM sales
                 WHERE YEAR(sold_at) = YEAR(CURDATE()) AND MONTH(sold_at) = MONTH(CURDATE())
                 GROUP BY bundle_id
           ) m ON m.bundle_id = b.id
          WHERE b.status = 'active'
          ORDER BY b.price ASC"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    // Dashboard DB not reachable — page still renders with the defaults above.
}

$error   = '';
$success = false;
$username_used = '';

// ---- Handle login form submission ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim(strtolower($_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Tafadhali ingiza username na password.';
    } else {
        // Connect to RADIUS MySQL database
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=radius;charset=utf8mb4',
                'radius',
                'VincentRadius2026!',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Check user exists and password matches
            $stmt = $pdo->prepare(
                "SELECT value FROM radcheck 
                 WHERE username = ? AND attribute = 'User-Password' LIMIT 1"
            );
            $stmt->execute([$username]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                $error = '❌ Username haipatikani. Angalia code yako vizuri.';
            } elseif ($row['value'] !== $password) {
                $error = '❌ Password (PIN) si sahihi. Jaribu tena.';
            } else {
                // Check expiry
                $stmt2 = $pdo->prepare(
                    "SELECT value FROM radcheck 
                     WHERE username = ? AND attribute = 'Expiration' LIMIT 1"
                );
                $stmt2->execute([$username]);
                $exp = $stmt2->fetch(PDO::FETCH_ASSOC);

                if ($exp && strtotime($exp['value']) < time()) {
                    $error = '⏰ Voucher yako imeisha muda wake. Nunua mpya.';
                } else {
                    // Log successful auth
                    $stmt3 = $pdo->prepare(
                        "INSERT INTO radpostauth (username, pass, reply, authdate) 
                         VALUES (?, ?, 'Access-Accept', NOW())"
                    );
                    $stmt3->execute([$username, $password]);

                    $success = true;
                    $username_used = $username;

                    // Redirect to success or let EAC200 handle it
                    $redirect = "http://{$gatewayIp}:2011/portal/success?username={$username}";
                }
            }
        } catch (PDOException $e) {
            $error = '⚠️ Tatizo la mfumo. Jaribu tena baadaye.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title><?= htmlspecialchars($portal['business_name']) ?> — Karibu!</title>
<meta name="theme-color" content="<?= htmlspecialchars($portal['primary_color']) ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="assets/css/style.css">
<style>
  body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 16px;
    background: radial-gradient(circle at top left, #4338ca 0%, #0f172a 55%, #0b1220 100%);
  }
  .portal-card { width: 100%; max-width: 440px; border-radius: 20px; overflow: hidden; }
  .portal-header { color: #fff; text-align: center; margin-bottom: 18px; }
  .wifi-pulse { font-size: 48px; display: block; animation: pulse 2.5s ease-in-out infinite; }
  @keyframes pulse { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.08); opacity: .85; } }
  .pkg-option { border: 2px solid var(--nx-border); border-radius: 14px; padding: 12px 8px; text-align: center; position: relative; height: 100%; }
  .pkg-option.popular { border-color: var(--nx-primary); background: linear-gradient(135deg, #eef2ff, var(--nx-surface)); }
  .pkg-option.popular::before {
    content: 'POPULAR'; position: absolute; top: -10px; left: 50%; transform: translateX(-50%);
    background: var(--nx-primary); color: #fff; font-size: 9px; font-weight: 700;
    padding: 3px 10px; border-radius: 20px; letter-spacing: .5px;
  }
  .pkg-option.vip { grid-column: 1 / -1; background: linear-gradient(135deg,#0f172a,#312e81); color: #fff; border-color: #0f172a; }
  .step-num {
    background: var(--nx-primary); color: #fff; width: 24px; height: 24px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0;
  }
  .footer-note { text-align: center; margin-top: 16px; color: rgba(255,255,255,.5); font-size: 11.5px; letter-spacing: .5px; }
  .btn-admin-link {
    display: flex; align-items: center; gap: 6px;
    margin: 10px auto 0; width: fit-content;
    padding: 7px 18px;
    border-radius: 20px;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.18);
    color: rgba(255,255,255,.8);
    font-size: 12.5px; font-weight: 600;
    text-decoration: none;
    transition: background .2s, color .2s;
  }
  .btn-admin-link:hover { background: rgba(255,255,255,.16); color: #fff; }
</style>
</head>
<body>

<div class="portal-header">
  <img src="assets/img/logo.png" alt="Gambaden Investment" style="max-width:200px;width:100%;height:auto;background:#fff;border-radius:12px;padding:8px 14px;">
  <h1 class="fw-bold fs-3 mt-3 mb-1"><?= htmlspecialchars($portal['business_name']) ?></h1>
  <p class="small opacity-75 mb-0"><?= htmlspecialchars($portal['welcome_text']) ?></p>
</div>

<div class="nx-card portal-card">

<?php if ($success): ?>
  <!-- SUCCESS PAGE -->
  <div class="nx-card-body text-center py-5">
    <div class="fs-1 text-success mb-3"><i class="bi bi-check-circle-fill"></i></div>
    <h2 class="h4 fw-bold text-success mb-2">Umefanikiwa!</h2>
    <p class="text-secondary">Karibu <strong><?= htmlspecialchars($username_used) ?></strong>!<br>
       Uko mtandaoni sasa. Furahia internet!</p>
    <a href="https://www.google.com" class="btn btn-success btn-lg rounded-3 mt-2">
      <i class="bi bi-globe"></i> Anza Kuvinjari
    </a>
  </div>

<?php else: ?>

  <!-- TABS -->
  <ul class="nav nav-pills nx-card-body pb-0 gap-1" id="portalTabs" role="tablist">
    <li class="nav-item flex-fill" role="presentation">
      <button class="nav-link active w-100" id="voucher-tab" data-bs-toggle="pill" data-bs-target="#tab-voucher" type="button">
        <i class="bi bi-ticket-perforated"></i> Nina Voucher
      </button>
    </li>
    <li class="nav-item flex-fill" role="presentation">
      <button class="nav-link w-100" id="pay-tab" data-bs-toggle="pill" data-bs-target="#tab-pay" type="button">
        <i class="bi bi-phone"></i> Kulipa
      </button>
    </li>
    <li class="nav-item flex-fill" role="presentation">
      <button class="nav-link w-100" id="bei-tab" data-bs-toggle="pill" data-bs-target="#tab-bei" type="button">
        <i class="bi bi-tags"></i> Bei Zetu
      </button>
    </li>
  </ul>

  <div class="tab-content nx-card-body pt-3">

    <!-- TAB 1: VOUCHER LOGIN -->
    <div class="tab-pane fade show active" id="tab-voucher">

      <?php if ($error): ?>
      <div class="alert alert-danger small"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="nx-form-label text-uppercase"><i class="bi bi-key"></i> Ingiza Voucher Yako</div>

      <form method="POST" action="">
        <input type="hidden" name="action"    value="login">
        <input type="hidden" name="clientIp"  value="<?= htmlspecialchars($clientIp) ?>">
        <input type="hidden" name="clientMac" value="<?= htmlspecialchars($clientMac) ?>">

        <div class="mb-3">
          <label class="nx-form-label">Username / Code</label>
          <input class="form-control" type="text" name="username"
                 placeholder="Mfano: siku1-A3F2B1"
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                 autocomplete="off" autocapitalize="none" spellcheck="false" required>
        </div>

        <div class="mb-3">
          <label class="nx-form-label">Password / PIN</label>
          <input class="form-control" type="password" name="password"
                 placeholder="Namba 4 za PIN yako"
                 inputmode="numeric" maxlength="10" required>
        </div>

        <button type="submit" class="btn btn-nx-primary w-100 py-2">
          <i class="bi bi-rocket-takeoff"></i> INGIA — ANZA KUTUMIA MTANDAO
        </button>
      </form>
    </div>

    <!-- TAB 2: JINSI YA KULIPA -->
    <div class="tab-pane fade" id="tab-pay">
      <div class="nx-tip-banner">
        <div class="fw-semibold mb-3"><i class="bi bi-phone"></i> Jinsi ya Kupata Voucher</div>

        <div class="d-flex align-items-start gap-2 mb-3">
          <span class="step-num">1</span>
          <span class="small">Chagua package unayoitaka kutoka kwenye orodha ya Bei Zetu</span>
        </div>

        <div class="d-flex align-items-start gap-2 mb-3">
          <span class="step-num">2</span>
          <div class="small">
            <div>Lipa kwa Lipa Namba:</div>
            <div class="badge btn-nx-primary fs-6 my-1"><?= htmlspecialchars($portal['lipa_number']) ?></div>
            <div class="text-secondary" style="font-size:11.5px;"><?= htmlspecialchars($portal['business_name']) ?></div>
          </div>
        </div>

        <?php $waNumber = preg_replace('/[^0-9]/', '', $portal['contact_phone'] ?? ''); ?>
        <div class="d-flex align-items-start gap-2 mb-3">
          <span class="step-num">3</span>
          <div class="small">
            <div>Tuma <strong>SMS au WhatsApp</strong> kuthibitisha malipo yako kwenye:</div>
            <div class="d-flex flex-wrap gap-2 mt-2">
              <a class="btn btn-success btn-sm" href="https://wa.me/<?= htmlspecialchars($waNumber) ?>" target="_blank" rel="noopener">
                <i class="bi bi-whatsapp"></i> WhatsApp <?= htmlspecialchars($portal['contact_phone'] ?? '') ?>
              </a>
              <a class="btn btn-outline-secondary btn-sm" href="tel:<?= htmlspecialchars($waNumber) ?>">
                <i class="bi bi-telephone"></i> Piga Simu
              </a>
            </div>
          </div>
        </div>

        <div class="d-flex align-items-start gap-2 mb-3">
          <span class="step-num">4</span>
          <span class="small">Utapewa <strong>Username na PIN</strong> yako haraka haraka</span>
        </div>

        <div class="d-flex align-items-start gap-2 mb-3">
          <span class="step-num">5</span>
          <span class="small">Rudi hapa → bonyeza <strong>"Nina Voucher"</strong> → ingiza → uanze!</span>
        </div>

        <div class="d-flex flex-wrap gap-2">
          <span class="badge rounded-pill" style="background:#ef4444;">Vodacom M-Pesa</span>
          <span class="badge rounded-pill" style="background:#f97316;">Airtel Money</span>
          <span class="badge rounded-pill" style="background:#3b82f6;">Mixx by Yas</span>
          <span class="badge rounded-pill" style="background:#8b5cf6;">HaloPesa</span>
        </div>
      </div>
    </div>

    <!-- TAB 3: BEI ZETU -->
    <div class="tab-pane fade" id="tab-bei">
      <div class="nx-form-label text-uppercase"><i class="bi bi-tags"></i> Chagua Package Inayokufaa</div>
      <div class="row g-2">
        <?php
        $topSales = max(array_merge([0], array_column($bundles, 'sales_month')));
        foreach ($bundles as $i => $b):
            $isPopular = $topSales > 0 && (int) $b['sales_month'] === (int) $topSales;
            $unitLabel = (int) $b['duration_value'] === 1 ? rtrim($b['duration_unit'], 's') : $b['duration_unit'];
        ?>
        <div class="col-6">
          <div class="pkg-option <?= $isPopular ? 'popular' : '' ?>">
            <div class="fs-4"><?= $bundleEmojis[$i % count($bundleEmojis)] ?></div>
            <div class="small text-secondary">
              <?= htmlspecialchars($b['name']) ?><br>
              <span style="font-size:10.5px;"><?= $b['duration_value'] . ' ' . $unitLabel ?></span>
            </div>
            <div class="fw-bold fs-5" style="color:var(--nx-primary);">
              TZS <?= number_format($b['price']) ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($bundles)): ?>
        <div class="col-12 text-center text-secondary small py-3">
          Bei za packages hazijawekwa bado. Tafadhali wasiliana nasi moja kwa moja.
        </div>
        <?php endif; ?>
      </div>

      <div class="nx-tip-banner mt-3 text-center small">
        <i class="bi bi-credit-card"></i> Lipa kwa Lipa Namba <strong><?= htmlspecialchars($portal['lipa_number']) ?></strong><br>
        Kisha wasiliana nasi upate voucher yako!
      </div>
    </div>

  </div>

<?php endif; ?>

</div><!-- .portal-card -->

<div class="footer-note">
  <?= htmlspecialchars($portal['business_name']) ?> © <?= date('Y') ?> · Tanzania
</div>
<a href="login.php" class="btn-admin-link">
  <i class="bi bi-shield-lock"></i> Admin
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
