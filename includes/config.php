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

    $sites = $pdo->query('SELECT * FROM sites ORDER BY id')->fetchAll();
    $devices = $pdo->query('SELECT * FROM devices ORDER BY id')->fetchAll();
    $bundles = $pdo->query('SELECT * FROM bundles ORDER BY id')->fetchAll();
    $invoices = $pdo->query('SELECT * FROM billing_invoices ORDER BY invoice_date DESC')->fetchAll();

    $portalStmt = $pdo->query('SELECT * FROM portal_settings WHERE id = 1');
    $portal = $portalStmt->fetch() ?: [
        'business_name' => 'GAMBADEN HOTSPOT',
        'welcome_text'  => 'Internet ya Uhakika kwa Bei Nafuu',
        'primary_color' => '#4f46e5',
        'lipa_number'   => '140197316',
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
            'plan'         => $user['plan'] ?? 'Pro',
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
