<?php
// ================================================================
// NEXOR DIGITAL — Business Dashboard (router)
// Data lives in MariaDB now (see includes/db.php + includes/schema.sql).
// ================================================================

require __DIR__ . '/includes/config.php';
require_login();

$page = $_GET['page'] ?? 'dashboard';
$allowedPages = ['dashboard', 'sites', 'devices', 'bundles', 'portal', 'profile'];
if (!in_array($page, $allowedPages, true)) {
    $page = 'dashboard';
}

$userId = current_user_id();

// ---- POST handlers (Post/Redirect/Get) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $pdo = db();

    switch ($action) {

        case 'create_bundle':
            $value = max(1, (int) ($_POST['duration_value'] ?? 1));
            $unit  = in_array($_POST['duration_unit'] ?? 'days', ['hours', 'days', 'weeks'], true)
                ? $_POST['duration_unit'] : 'days';
            $name  = trim($_POST['name'] ?? '') ?: 'Bundle';

            $pdo->prepare(
                'INSERT INTO bundles (name, duration_value, duration_unit, price, speed, data_limit, status)
                 VALUES (?, ?, ?, ?, ?, ?, "active")'
            )->execute([
                $name,
                $value,
                $unit,
                max(0, (int) ($_POST['price'] ?? 0)),
                trim($_POST['speed'] ?? '') ?: 'Unlimited',
                trim($_POST['data_limit'] ?? '') ?: 'Unlimited',
            ]);
            flash('bundle_msg', 'Bundle mpya imeongezwa kikamilifu.');
            break;

        case 'toggle_bundle':
            $pdo->prepare(
                "UPDATE bundles SET status = IF(status = 'active', 'inactive', 'active') WHERE id = ?"
            )->execute([(int) ($_POST['id'] ?? 0)]);
            flash('bundle_msg', 'Hali ya bundle imebadilishwa.');
            break;

        case 'delete_bundle':
            $pdo->prepare('DELETE FROM bundles WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
            flash('bundle_msg', 'Bundle imefutwa.');
            break;

        case 'record_sale':
            $bundleId = (int) ($_POST['bundle_id'] ?? 0);
            $qty      = max(1, (int) ($_POST['quantity'] ?? 1));

            $stmt = $pdo->prepare('SELECT price FROM bundles WHERE id = ?');
            $stmt->execute([$bundleId]);
            $bundle = $stmt->fetch();

            if (!$bundle) {
                flash('bundle_msg', 'Bundle haipatikani.');
            } else {
                $pdo->prepare('INSERT INTO sales (bundle_id, quantity, amount, sold_at) VALUES (?, ?, ?, NOW())')
                    ->execute([$bundleId, $qty, $bundle['price'] * $qty]);
                flash('bundle_msg', 'Mauzo yamerekodiwa kikamilifu.');
            }
            break;

        case 'create_site':
            $pdo->prepare(
                'INSERT INTO sites (name, location, status) VALUES (?, ?, "online")'
            )->execute([
                trim($_POST['name'] ?? '') ?: 'Site',
                trim($_POST['location'] ?? ''),
            ]);
            flash('site_msg', 'Site mpya imeongezwa.');
            break;

        case 'delete_site':
            $pdo->prepare('DELETE FROM sites WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
            flash('site_msg', 'Site imefutwa.');
            break;

        case 'create_device':
            $pdo->prepare(
                'INSERT INTO devices (site_id, name, type, ip, status) VALUES (?, ?, ?, ?, "online")'
            )->execute([
                (int) ($_POST['site_id'] ?? 0),
                trim($_POST['name'] ?? '') ?: 'Device',
                trim($_POST['type'] ?? 'Router'),
                trim($_POST['ip'] ?? ''),
            ]);
            flash('device_msg', 'Kifaa kipya kimeongezwa.');
            break;

        case 'delete_device':
            $pdo->prepare('DELETE FROM devices WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
            flash('device_msg', 'Kifaa kimefutwa.');
            break;

        case 'save_profile':
            $email = trim($_POST['email'] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash('profile_msg_error', 'Email si sahihi.');
            } else {
                $pdo->prepare(
                    'UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?'
                )->execute([
                    trim($_POST['first_name'] ?? ''),
                    trim($_POST['last_name'] ?? ''),
                    $email,
                    trim($_POST['phone'] ?? ''),
                    $userId,
                ]);
                flash('profile_msg', 'Wasifu wako umehifadhiwa.');
            }
            break;

        case 'change_password':
            $current = $_POST['current_password'] ?? '';
            $new     = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $row = $stmt->fetch();

            if (!$row || !password_verify($current, $row['password_hash'])) {
                flash('password_error', 'Password ya sasa si sahihi.');
            } elseif (strlen($new) < 6) {
                flash('password_error', 'Password mpya lazima iwe na angalau herufi 6.');
            } elseif ($new !== $confirm) {
                flash('password_error', 'Password mpya hazifanani.');
            } else {
                $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
                    ->execute([password_hash($new, PASSWORD_DEFAULT), $userId]);
                flash('profile_msg', 'Password yako imebadilishwa kikamilifu.');
            }
            break;

        case 'save_portal':
            $pdo->prepare(
                'INSERT INTO portal_settings (id, business_name, welcome_text, primary_color, lipa_number, contact_phone)
                 VALUES (1, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE business_name = VALUES(business_name),
                                         welcome_text = VALUES(welcome_text),
                                         primary_color = VALUES(primary_color),
                                         lipa_number = VALUES(lipa_number),
                                         contact_phone = VALUES(contact_phone)'
            )->execute([
                trim($_POST['business_name'] ?? ''),
                trim($_POST['welcome_text'] ?? ''),
                trim($_POST['primary_color'] ?? '#4f46e5'),
                trim($_POST['lipa_number'] ?? ''),
                trim($_POST['contact_phone'] ?? ''),
            ]);
            flash('portal_msg', 'Mipangilio ya Portal imehifadhiwa.');
            break;
    }

    header('Location: admin.php?page=' . $page);
    exit;
}

$data = load_app_data();

$pageTitles = [
    'dashboard' => ['Dashboard', 'Muhtasari wa biashara yako ya hotspot'],
    'sites'     => ['Sites', 'Simamia maeneo yako ya hotspot'],
    'devices'   => ['Devices', 'Simamia routers na access points'],
    'bundles'   => ['Internet Bundles', 'Configure your hotspot packages and pricing'],
    'portal'    => ['Portal', 'Customize the captive portal your customers see'],
    'profile'   => ['Profile Settings', 'Manage your account information and security'],
];
[$pageTitle, $pageSubtitle] = $pageTitles[$page];

require __DIR__ . '/includes/layout_top.php';
require __DIR__ . "/pages/{$page}.php";
require __DIR__ . '/includes/layout_bottom.php';
