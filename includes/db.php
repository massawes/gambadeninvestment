<?php
// ================================================================
// NEXOR DIGITAL — PDO connection (single shared instance per request)
// ================================================================

require_once __DIR__ . '/db_config.php';

function db(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            die(
                '<div style="font-family:sans-serif;max-width:600px;margin:60px auto;padding:24px;'
                . 'border:1px solid #fecaca;background:#fef2f2;color:#991b1b;border-radius:12px;">'
                . '<h2 style="margin-top:0;">Database connection failed</h2>'
                . '<p>Check that <code>includes/db_config.php</code> has the correct DB_PASS, '
                . 'and that <code>includes/schema.sql</code> has been imported.</p>'
                . '<p style="font-size:13px;color:#7f1d1d;">' . htmlspecialchars($e->getMessage()) . '</p>'
                . '</div>'
            );
        }
    }

    return $pdo;
}
