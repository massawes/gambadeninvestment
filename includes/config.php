<?php
// ================================================================
// NEXOR DIGITAL — Shared bootstrap: session, DB-backed data, helpers
// ================================================================

require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('NEXOR_APP', true);

function require_login(): void {
    if (empty($_SESSION['logged_in'])) {
        header('Location: login.php');
        exit;
    }
}

function flash(string $key, ?string $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function money(float $amount): string {
    return number_format($amount, 0);
}

function current_user_id(): int {
    return (int) ($_SESSION['user_id'] ?? 1);
}

function load_app_data(): array {
    $pdo = db();
    $userId = current_user_id();

    $profile = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $profile->execute([$userId]);
    $user = $profile->fetch() ?: [];

    // Devices per site — real, computed from the devices table (no static counter).
    $sites = $pdo->query(
        'SELECT s.*, COUNT(d.id) AS device_count
           FROM sites s
           LEFT JOIN devices d ON d.site_id = s.id
          GROUP BY s.id
          ORDER BY s.id'
    )->fetchAll();

    $devices = $pdo->query('SELECT * FROM devices ORDER BY id')->fetchAll();

    // Bundles + this month's real sales/revenue, computed live from the
    // sales table (nothing here is a stored/static counter).
    $bundles = $pdo->query(
        "SELECT b.*,
                COALESCE(m.qty, 0) AS sales_month,
                COALESCE(m.revenue, 0) AS revenue_month
           FROM bundles b
           LEFT JOIN (
                SELECT bundle_id, SUM(quantity) AS qty, SUM(amount) AS revenue
                  FROM sales
                 WHERE YEAR(sold_at) = YEAR(CURDATE()) AND MONTH(sold_at) = MONTH(CURDATE())
                 GROUP BY bundle_id
           ) m ON m.bundle_id = b.id
          ORDER BY b.id"
    )->fetchAll();

    $invoices = $pdo->query('SELECT * FROM billing_invoices ORDER BY invoice_date DESC')->fetchAll();

    $portalStmt = $pdo->query('SELECT * FROM portal_settings WHERE id = 1');
    $portal = $portalStmt->fetch() ?: [
        'business_name' => 'GAMBADEN HOTSPOT',
        'welcome_text'  => 'Internet ya Uhakika kwa Bei Nafuu',
        'primary_color' => '#4f46e5',
        'lipa_number'   => '140197316',
        'contact_phone' => '+255745325531',
    ];

    return [
        'sites' => $sites,
        'devices' => $devices,
        'bundles' => $bundles,
        'portal' => $portal,
        'profile' => [
            'first_name' => $user['first_name'] ?? '',
            'last_name'  => $user['last_name'] ?? '',
            'email'      => $user['email'] ?? '',
            'phone'      => $user['phone'] ?? '',
            'account_type' => $user['account_type'] ?? 'Customer Account',
            'member_since' => !empty($user['member_since']) ? date('d M Y', strtotime($user['member_since'])) : '',
            'bank_name'    => $user['bank_name'] ?? '',
            'account_number' => $user['account_number'] ?? '',
        ],
        'billing' => [
            'plan'         => $user['plan'] ?? 'Free',
            'price'        => (int) ($user['plan_price'] ?? 0),
            'cycle'        => $user['billing_cycle'] ?? 'Monthly',
            'next_renewal' => !empty($user['next_renewal']) ? date('d M Y', strtotime($user['next_renewal'])) : '',
            'invoices'     => array_map(fn($inv) => [
                'id' => $inv['invoice_no'],
                'date' => date('d M Y', strtotime($inv['invoice_date'])),
                'amount' => (int) $inv['amount'],
                'status' => $inv['status'],
            ], $invoices),
        ],
    ];
}

// Real sales for the last 7 days (today included), grouped by day.
// Days with no recorded sales come back as zero — nothing here is mocked.
function get_weekly_sales(): array {
    $rows = db()->query(
        "SELECT DATE(sold_at) AS day, SUM(quantity) AS qty, SUM(amount) AS revenue
           FROM sales
          WHERE sold_at >= (CURDATE() - INTERVAL 6 DAY)
          GROUP BY DATE(sold_at)"
    )->fetchAll();

    $byDay = [];
    foreach ($rows as $r) {
        $byDay[$r['day']] = ['qty' => (int) $r['qty'], 'revenue' => (int) $r['revenue']];
    }

    $result = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-{$i} days"));
        $result[] = [
            'label'   => date('D', strtotime($date)),
            'qty'     => $byDay[$date]['qty'] ?? 0,
            'revenue' => $byDay[$date]['revenue'] ?? 0,
        ];
    }
    return $result;
}
