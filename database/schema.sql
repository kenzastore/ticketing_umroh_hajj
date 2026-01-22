-- =========================================================
-- Ticketing Umroh/Haji - Consolidated Schema (MariaDB)
-- =========================================================
SET NAMES utf8mb4;
SET time_zone = "+00:00";

-- -----------------------------
-- 1) SYSTEM / AUTHENTICATION (Retained from existing)
-- -----------------------------
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100),
    entity_type VARCHAR(50),
    entity_id INT,
    old_value TEXT,
    new_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Seed roles (if not exists)
INSERT IGNORE INTO roles (name, description) VALUES 
('admin', 'Full access to all modules'),
('operational', 'Ticketing and Movement management'),
('finance', 'Access to invoicing and payment modules'),
('monitor', 'View-only access to dashboards');


-- -----------------------------
-- 2) MASTER DATA (Updated to match Worksheet)
-- -----------------------------
-- Drop old agents table if it exists and differs significantly, or rely on CREATE IF NOT EXISTS if clean.
-- Since this is a re-alignment, we prioritize the new structure.
DROP TABLE IF EXISTS agents;

CREATE TABLE IF NOT EXISTS agents (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(150) NOT NULL,
  skyagent_id VARCHAR(50) NULL,
  phone VARCHAR(50) NULL,
  email VARCHAR(150) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_agents_name (name),
  KEY idx_agents_skyagent (skyagent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS corporates (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(200) NOT NULL,
  address TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_corporates_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- -----------------------------
-- 3) BOOKING REQUEST (Replaces 'requests')
-- -----------------------------
DROP TABLE IF EXISTS requests; 
-- We drop 'requests' to avoid confusion, assuming migration isn't needed for this exercise.

CREATE TABLE IF NOT EXISTS booking_requests (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  request_no INT NULL,                         -- Worksheet: NO
  corporate_id BIGINT UNSIGNED NULL,
  corporate_name VARCHAR(200) NULL,            -- keep original text for easy import
  agent_id BIGINT UNSIGNED NULL,
  agent_name VARCHAR(150) NULL,                -- keep original text for easy import
  skyagent_id VARCHAR(50) NULL,                -- Worksheet: Skyagent ID

  group_size INT NULL,                         -- Group Size
  tcp DECIMAL(18,2) NULL,                      -- TCP
  gp_approved_fare DECIMAL(18,2) NULL,         -- GP approved fares
  selling_fare DECIMAL(18,2) NULL,             -- FARES
  nett_fare DECIMAL(18,2) NULL,                -- NETT FARE

  duration_days INT NULL,                      -- DURATION
  add1_days INT NULL,                          -- ADD 1
  ttl_days INT NULL,                           -- TTL DAYS

  notes TEXT NULL,
  is_converted TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_br_request_no (request_no),
  KEY idx_br_agent_name (agent_name),
  KEY idx_br_corporate_name (corporate_name),
  CONSTRAINT fk_br_agent_id FOREIGN KEY (agent_id) REFERENCES agents(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_br_corporate_id FOREIGN KEY (corporate_id) REFERENCES corporates(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Normalized legs from FLT_DATE1..4 / FLT_NO1..4 / SECTOR1..4
CREATE TABLE IF NOT EXISTS booking_request_legs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_request_id BIGINT UNSIGNED NOT NULL,
  leg_no TINYINT UNSIGNED NOT NULL,            -- 1..4
  flight_date DATE NULL,
  flight_no VARCHAR(20) NULL,                  -- e.g. TR596
  sector VARCHAR(50) NULL,                     -- e.g. SIN-JED or SUB-SIN
  origin_iata CHAR(3) NULL,
  dest_iata CHAR(3) NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_br_leg (booking_request_id, leg_no),
  KEY idx_br_leg_flight (flight_no),
  KEY idx_br_leg_date (flight_date),
  CONSTRAINT fk_br_leg_req FOREIGN KEY (booking_request_id) REFERENCES booking_requests(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- -----------------------------
-- 4) MOVEMENT (Replaces 'bookings')
-- -----------------------------
DROP TABLE IF EXISTS bookings;

CREATE TABLE IF NOT EXISTS movements (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  category VARCHAR(50) DEFAULT 'UMRAH',
  movement_no INT NULL,                         -- Worksheet: NO
  agent_id BIGINT UNSIGNED NULL,
  agent_name VARCHAR(150) NULL,                 -- Travel Agent
  created_date DATE NULL,                       -- Creation

  pnr VARCHAR(20) NULL,                         -- PNR
  tour_code VARCHAR(80) NULL,                   -- TOIR CODE (fixed)
  carrier VARCHAR(20) NULL,                     -- CARRIER

  dp1_status VARCHAR(30) NULL,                  -- DP1 (can be "PAID"/etc)
  dp2_status VARCHAR(30) NULL,                  -- DP2
  fp_status  VARCHAR(30) NULL,                  -- FP

  -- Outbound / inbound compressed columns (from sheet)
  flight_no_out VARCHAR(20) NULL,
  sector_out VARCHAR(50) NULL,
  dep_seg1_date DATE NULL,
  dep_seg2_date DATE NULL,
  arr_seg3_date DATE NULL,
  arr_seg4_date DATE NULL,
  sector_in VARCHAR(50) NULL,
  flight_no_in VARCHAR(20) NULL,

  pattern_code VARCHAR(50) NULL,                -- PATTERN
  passenger_count INT NULL,                     -- Passenger

  approved_fare DECIMAL(18,2) NULL,             -- Approved Fare
  selling_fare  DECIMAL(18,2) NULL,             -- Selling
  nett_selling  DECIMAL(18,2) NULL,             -- NETT SELLING
  total_selling DECIMAL(18,2) NULL,             -- TOTAL SELLING

  deposit1_airlines_amount DECIMAL(18,2) NULL,  -- 1ST DEPOSIT-AIRLINES
  deposit1_airlines_date DATE NULL,
  deposit1_eemw_date DATE NULL,

  deposit2_airlines_amount DECIMAL(18,2) NULL,  -- 2ND DEPOSIT-AIRLINES
  deposit2_airlines_date DATE NULL,
  deposit2_eemw_date DATE NULL,

  fullpay_airlines_date DATE NULL,
  fullpay_eemw_date DATE NULL,

  ticketing_deadline DATE NULL,                 -- time limit manifest & ticketing
  nett_balance_amount DECIMAL(18,2) NULL,
  sell_balance_amount DECIMAL(18,2) NULL,

  ticketing_done TINYINT(1) NOT NULL DEFAULT 0,
  belonging_to VARCHAR(120) NULL,

  duration_days INT NULL,
  add1_days INT NULL,
  ttl_days INT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_mv_pnr (pnr),
  KEY idx_mv_tour_code (tour_code),
  KEY idx_mv_agent_name (agent_name),
  KEY idx_mv_created_date (created_date),
  CONSTRAINT fk_mv_agent_id FOREIGN KEY (agent_id) REFERENCES agents(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Normalized flight legs for dashboard + status tracking
CREATE TABLE IF NOT EXISTS flight_legs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  movement_id BIGINT UNSIGNED NOT NULL,

  leg_no TINYINT UNSIGNED NOT NULL,             -- 1..n
  direction ENUM('OUT','IN') NOT NULL DEFAULT 'OUT',

  carrier VARCHAR(20) NULL,                     -- airline code if available
  flight_no VARCHAR(20) NULL,                   -- e.g. TR596
  sector VARCHAR(50) NULL,                      -- e.g. SUB-SIN
  origin_iata CHAR(3) NULL,
  dest_iata CHAR(3) NULL,

  scheduled_departure DATE NULL,                -- worksheet mostly uses DATE
  scheduled_arrival DATE NULL,
  time_range VARCHAR(30) NULL,                  -- if you have "0945-1315" etc

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_flight_leg (movement_id, leg_no, direction),
  KEY idx_flight_leg_flight (flight_no),
  KEY idx_flight_leg_date (scheduled_departure),
  CONSTRAINT fk_flight_leg_mv FOREIGN KEY (movement_id) REFERENCES movements(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Real-time snapshots (store last known status)
CREATE TABLE IF NOT EXISTS flight_status_snapshots (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  flight_leg_id BIGINT UNSIGNED NOT NULL,

  status VARCHAR(40) NOT NULL,                  -- scheduled/delayed/airborne/arrived/canceled
  delay_minutes INT NULL,

  last_seen_at DATETIME NOT NULL,               -- when we fetched it
  raw_payload JSON NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_snap_leg_time (flight_leg_id, last_seen_at),
  CONSTRAINT fk_snap_leg FOREIGN KEY (flight_leg_id) REFERENCES flight_legs(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Status events (timeline changes)
CREATE TABLE IF NOT EXISTS flight_status_events (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  flight_leg_id BIGINT UNSIGNED NOT NULL,

  event_type VARCHAR(40) NOT NULL,              -- status_change/time_change/gate_change/etc
  old_value VARCHAR(255) NULL,
  new_value VARCHAR(255) NULL,

  occurred_at DATETIME NOT NULL,
  source VARCHAR(40) NULL,                      -- api/manual

  raw_payload JSON NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_evt_leg_time (flight_leg_id, occurred_at),
  CONSTRAINT fk_evt_leg FOREIGN KEY (flight_leg_id) REFERENCES flight_legs(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- -----------------------------
-- 5) PAYMENT REPORT (Worksheet Mirror)
-- -----------------------------
CREATE TABLE IF NOT EXISTS payment_report_lines (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  line_no INT NULL,                             -- NO
  dep_arr_date DATE NULL,                       -- DATE DEP/ARR
  flight_no VARCHAR(20) NULL,                   -- FLIGHT NO
  city VARCHAR(50) NULL,                        -- CITY
  time_range VARCHAR(30) NULL,                  -- TIME
  reference_id VARCHAR(80) NULL,                -- REFERENCE ID
  payment_date DATE NULL,                       -- DATE OF PAYMENT
  total_pax INT NULL,                           -- TTL PAX
  remarks VARCHAR(255) NULL,                    -- REMARKS
  fare_per_pax DECIMAL(18,2) NULL,              -- FARE PER-PAX
  debit_amount DECIMAL(18,2) NULL,              -- DEBET

  bank_from VARCHAR(80) NULL,                   -- FROM
  bank_from_name VARCHAR(120) NULL,             -- BANK ACCOUNT NAME (from)
  bank_to VARCHAR(80) NULL,                     -- TO
  bank_to_name VARCHAR(120) NULL,               -- BANK ACCOUNT NAME (to)

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_pr_payment_date (payment_date),
  KEY idx_pr_reference (reference_id),
  KEY idx_pr_flight_no (flight_no)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- -----------------------------
-- 6) INVOICE (Generated)
-- -----------------------------
CREATE TABLE IF NOT EXISTS invoices (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  invoice_no VARCHAR(50) NULL,
  invoice_date DATE NULL,

  corporate_id BIGINT UNSIGNED NULL,
  corporate_name VARCHAR(200) NULL,
  attention_to VARCHAR(200) NULL,
  address TEXT NULL,

  ref_text VARCHAR(120) NULL,                   -- e.g. REF : 11MAY-45S-26D-TR
  pnr VARCHAR(20) NULL,
  tour_code VARCHAR(80) NULL,

  total_pax INT NULL,
  fare_per_pax DECIMAL(18,2) NULL,
  amount_idr DECIMAL(18,2) NULL,
  status ENUM('UNPAID', 'PARTIALLY_PAID', 'PAID') DEFAULT 'UNPAID',

  pdf_path VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_inv_pnr (pnr),
  KEY idx_inv_tour_code (tour_code),
  CONSTRAINT fk_inv_corp FOREIGN KEY (corporate_id) REFERENCES corporates(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS invoice_flight_lines (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  invoice_id BIGINT UNSIGNED NOT NULL,
  line_no INT NOT NULL,
  flight_date DATE NULL,
  flight_no VARCHAR(20) NULL,
  sector VARCHAR(50) NULL,
  time_range VARCHAR(30) NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_inv_flight_line (invoice_id, line_no),
  CONSTRAINT fk_inv_flight FOREIGN KEY (invoice_id) REFERENCES invoices(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS invoice_fare_lines (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  invoice_id BIGINT UNSIGNED NOT NULL,
  line_no INT NOT NULL,
  dot_date DATE NULL,                           -- DOT column
  description VARCHAR(255) NULL,
  total_pax INT NULL,
  fare_amount DECIMAL(18,2) NULL,
  amount_idr DECIMAL(18,2) NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_inv_fare_line (invoice_id, line_no),
  CONSTRAINT fk_inv_fare FOREIGN KEY (invoice_id) REFERENCES invoices(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- -----------------------------
-- 7) DOCUMENTS & ATTACHMENTS
-- -----------------------------
CREATE TABLE IF NOT EXISTS documents (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  entity_type VARCHAR(40) NOT NULL,             -- 'movement','invoice','payment_report'
  entity_id BIGINT UNSIGNED NOT NULL,
  doc_type VARCHAR(40) NOT NULL,                -- 'invoice_pdf','resi_pdf','payment_advise','attachment'
  file_path VARCHAR(255) NOT NULL,
  token_hash CHAR(64) NULL,                     -- for secure public link
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_docs_entity (entity_type, entity_id),
  KEY idx_docs_token (token_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------
-- 8) PAYMENTS (Transaction Log)
-- -----------------------------
CREATE TABLE IF NOT EXISTS payments (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  invoice_id BIGINT UNSIGNED NOT NULL,
  amount_paid DECIMAL(18,2) NOT NULL,
  payment_date DATE NOT NULL,
  payment_method VARCHAR(50) NOT NULL,
  reference_number VARCHAR(100) NULL,
  notes TEXT NULL,
  receipt_hash CHAR(64) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_payments_invoice (invoice_id),
  CONSTRAINT fk_payments_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------
-- 9) NOTIFICATIONS (Alerts)
-- -----------------------------
CREATE TABLE IF NOT EXISTS notifications (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  entity_type VARCHAR(50) NULL,
  entity_id BIGINT UNSIGNED NULL,
  message TEXT NOT NULL,
  alert_type ENUM('DEADLINE', 'PAYMENT', 'SYSTEM') DEFAULT 'SYSTEM',
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_notif_status (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;CREATE TABLE IF NOT EXISTS payment_advises (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    movement_id BIGINT UNSIGNED NULL,
    agent_name VARCHAR(255) NULL,
    tour_code VARCHAR(100) NULL,
    pnr VARCHAR(50) NULL,
    
    date_created DATE NULL,
    date_email_to_airline DATE NULL,
    email_confirmation_from_airline VARCHAR(255) NULL,
    
    grp_depart_date DATE NULL,
    total_seats_confirmed INT NULL,
    total_seats_used_percent DECIMAL(5,2) NULL,
    
    approved_fare DECIMAL(18,2) NULL,
    total_amount DECIMAL(18,2) NULL,
    deposit_amount DECIMAL(18,2) NULL, -- 20%
    balance_payment_amount DECIMAL(18,2) NULL, -- 80%
    top_up_amount DECIMAL(18,2) NULL,
    transfer_amount DECIMAL(18,2) NULL,
    reference_number VARCHAR(100) NULL,
    
    -- Recipient Bank Info
    company_name VARCHAR(255) NULL,
    company_account_no VARCHAR(100) NULL,
    company_bank_name VARCHAR(255) NULL,
    company_address TEXT NULL,
    
    -- Remitter Bank Info
    remitter_name VARCHAR(255) NULL,
    remitter_account_no VARCHAR(100) NULL,
    remitter_bank_name VARCHAR(255) NULL,
    
    -- Transaction Info
    date_top_up_created DATE NULL,
    remarks_top_up TEXT NULL,
    date_bank_transferred DATE NULL,
    remarks_bank_transfer TEXT NULL,
    
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    CONSTRAINT fk_pa_movement FOREIGN KEY (movement_id) REFERENCES movements(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
