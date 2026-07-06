-- ================================================================
-- Run this ONCE via phpMyAdmin's SQL tab, against your LIVE database,
-- if you already imported an earlier version of includes/schema.sql.
-- It adds real sales-tracking and removes the fake demo numbers
-- (static sales_month, fake billing invoices) so every figure in the
-- dashboard becomes real, live data instead of seeded placeholders.
-- Safe to re-run.
-- ================================================================

CREATE TABLE IF NOT EXISTS sales (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  bundle_id  INT NOT NULL,
  quantity   INT NOT NULL DEFAULT 1,
  amount     INT NOT NULL,
  sold_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sales_bundle FOREIGN KEY (bundle_id) REFERENCES bundles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE bundles DROP COLUMN IF EXISTS sales_month;
ALTER TABLE sites DROP COLUMN IF EXISTS bundles_sold;
ALTER TABLE portal_settings ADD COLUMN IF NOT EXISTS contact_phone VARCHAR(30) DEFAULT '+255745325531';

UPDATE portal_settings
   SET contact_phone = '+255745325531'
 WHERE id = 1 AND (contact_phone IS NULL OR contact_phone = '');

UPDATE users SET plan = 'Free', plan_price = 0, next_renewal = NULL WHERE id = 1;

DELETE FROM billing_invoices;
