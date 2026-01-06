-- Add category column
ALTER TABLE movements ADD COLUMN IF NOT EXISTS category ENUM('UMRAH', 'HAJJI') DEFAULT 'UMRAH' AFTER movement_no;

-- Ensure all worksheet columns exist (checking based on movement.pdf)
ALTER TABLE movements 
  ADD COLUMN IF NOT EXISTS approved_fare_currency VARCHAR(10) DEFAULT 'IDR' AFTER pattern_code,
  MODIFY COLUMN approved_fare DECIMAL(18,2) NULL,
  MODIFY COLUMN selling_fare DECIMAL(18,2) NULL,
  ADD COLUMN IF NOT EXISTS nett_selling_total DECIMAL(18,2) NULL AFTER nett_selling,
  ADD COLUMN IF NOT EXISTS total_selling_all DECIMAL(18,2) NULL AFTER total_selling,
  ADD COLUMN IF NOT EXISTS first_deposit_airlines_date DATE NULL AFTER deposit1_airlines_amount,
  ADD COLUMN IF NOT EXISTS first_deposit_eemw_date DATE NULL AFTER first_deposit_airlines_date,
  ADD COLUMN IF NOT EXISTS second_deposit_airlines_date DATE NULL AFTER deposit2_airlines_amount,
  ADD COLUMN IF NOT EXISTS second_deposit_eemw_date DATE NULL AFTER second_deposit_airlines_date,
  ADD COLUMN IF NOT EXISTS time_limit_manifest_ticketing DATE NULL AFTER fullpay_eemw_date;

-- Index for category
CREATE INDEX IF NOT EXISTS idx_mv_category ON movements(category);
