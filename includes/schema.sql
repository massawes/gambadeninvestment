-- ================================================================
-- NEXOR DIGITAL — Database schema + seed data
-- Import this once via phpMyAdmin against if0_42271545_dambaden_db.
-- Safe to re-run: tables use IF NOT EXISTS, seeds use INSERT IGNORE.
-- Default login after import: admin / Nexor@2026
--
-- If you already imported an earlier version of this file, run
-- includes/migration_001_real_sales.sql instead (it upgrades the
-- existing live database without losing anything real).
-- ================================================================

CREATE TABLE IF NOT EXISTS users (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  username       VARCHAR(50)  NOT NULL UNIQUE,
  password_hash  VARCHAR(255) NOT NULL,
  first_name     VARCHAR(100) NOT NULL DEFAULT '',
  last_name      VARCHAR(100) NOT NULL DEFAULT '',
  email          VARCHAR(150) NOT NULL,
  phone          VARCHAR(30)  DEFAULT '',
  account_type   VARCHAR(50)  DEFAULT 'Customer Account',
  member_since   DATE         DEFAULT NULL,
  bank_name      VARCHAR(50)  DEFAULT '',
  account_number VARCHAR(50)  DEFAULT '',
  plan           VARCHAR(50)  DEFAULT 'Free',
  plan_price     INT          DEFAULT 0,
  billing_cycle  VARCHAR(20)  DEFAULT 'Monthly',
  next_renewal   DATE         DEFAULT NULL,
  created_at     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS sites (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(150) NOT NULL,
  location     VARCHAR(200) DEFAULT '',
  status       ENUM('online','offline') DEFAULT 'online',
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS devices (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  site_id    INT NOT NULL,
  name       VARCHAR(150) NOT NULL,
  type       VARCHAR(50) DEFAULT 'Router',
  ip         VARCHAR(45) DEFAULT '',
  status     ENUM('online','offline') DEFAULT 'online',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_devices_site FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bundles (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  name           VARCHAR(100) NOT NULL,
  duration_value INT NOT NULL DEFAULT 1,
  duration_unit  ENUM('hours','days','weeks') DEFAULT 'days',
  price          INT NOT NULL DEFAULT 0,
  speed          VARCHAR(50) DEFAULT 'Unlimited',
  data_limit     VARCHAR(50) DEFAULT 'Unlimited',
  status         ENUM('active','inactive') DEFAULT 'active',
  created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Real, admin-recorded sales. Every "revenue"/"analytics" number in the
-- dashboard is computed live from this table — nothing is hardcoded.
CREATE TABLE IF NOT EXISTS sales (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  bundle_id  INT NOT NULL,
  quantity   INT NOT NULL DEFAULT 1,
  amount     INT NOT NULL,
  sold_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sales_bundle FOREIGN KEY (bundle_id) REFERENCES bundles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Real client voucher codes the admin generates for customers. The public
-- captive portal (index.php) checks logins against this table directly.
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

CREATE TABLE IF NOT EXISTS billing_invoices (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  invoice_no   VARCHAR(30) NOT NULL,
  invoice_date DATE NOT NULL,
  amount       INT NOT NULL,
  status       VARCHAR(20) DEFAULT 'paid',
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS portal_settings (
  id            INT PRIMARY KEY DEFAULT 1,
  business_name VARCHAR(150) DEFAULT 'GAMBADEN HOTSPOT',
  welcome_text  VARCHAR(255) DEFAULT 'Internet ya Uhakika kwa Bei Nafuu',
  primary_color VARCHAR(10)  DEFAULT '#4f46e5',
  lipa_number   VARCHAR(30)  DEFAULT '140197316',
  contact_phone VARCHAR(30)  DEFAULT '+255745325531'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS password_resets (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL,
  token      VARCHAR(64) NOT NULL,
  expires_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_resets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================================
-- Seed data — a real business starts at zero. No fake sales/invoices.
-- Password hash below is bcrypt for: Nexor@2026
-- ================================================================

INSERT IGNORE INTO users
  (id, username, password_hash, first_name, last_name, email, phone, account_type, member_since, bank_name, account_number, plan, plan_price, billing_cycle, next_renewal)
VALUES
  (1, 'admin', '$2y$10$/4vsRl5j06/x.5feXLvMFePamhJtOVqFOk7KoDcUn63e1u61bOA52',
   'Vicent', 'Massawe', 'massawes269@gmail.com', '0695389537', 'Customer Account',
   '2026-06-23', 'CRDB', '0152815947400', 'Free', 0, 'Monthly', NULL);

INSERT IGNORE INTO sites (id, name, location, status) VALUES
  (1, 'Nexor Main Site', 'Arusha CBD', 'online');

INSERT IGNORE INTO devices (id, site_id, name, type, ip, status) VALUES
  (1, 1, 'EAC200 Router', 'Router', '172.16.0.1', 'online');

INSERT IGNORE INTO bundles (id, name, duration_value, duration_unit, price, speed, data_limit, status) VALUES
  (1, '12 Hours', 12, 'hours', 500,  'Unlimited', 'Unlimited', 'active'),
  (2, '1 Day',    1,  'days',  1000, 'Unlimited', 'Unlimited', 'active'),
  (3, '3 Days',   3,  'days',  3000, 'Unlimited', 'Unlimited', 'active'),
  (4, '7 Days',   7,  'days',  5000, 'Unlimited', 'Unlimited', 'active');

INSERT IGNORE INTO portal_settings (id, business_name, welcome_text, primary_color, lipa_number, contact_phone) VALUES
  (1, 'GAMBADEN HOTSPOT', 'Internet ya Uhakika kwa Bei Nafuu', '#4f46e5', '140197316', '+255745325531');
