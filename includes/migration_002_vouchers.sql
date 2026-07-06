-- ================================================================
-- Run this ONCE via phpMyAdmin's SQL tab, against your LIVE database.
-- Adds the vouchers table used to give real client login codes and
-- checked directly by the public captive portal (index.php) — no more
-- dependency on a separate RADIUS server, which InfinityFree can't run.
-- Safe to re-run.
-- ================================================================

CREATE TABLE IF NOT EXISTS vouchers (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  bundle_id  INT NOT NULL,
  code       VARCHAR(40) NOT NULL UNIQUE,
  pin        VARCHAR(10) NOT NULL,
  status     ENUM('unused','active','expired') DEFAULT 'unused',
  expires_at DATETIME NULL,
  used_at    DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_vouchers_bundle FOREIGN KEY (bundle_id) REFERENCES bundles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
