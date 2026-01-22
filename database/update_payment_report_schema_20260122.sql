-- Update payment_report_lines table for dual-table support and bank details
ALTER TABLE payment_report_lines 
ADD COLUMN table_type ENUM('SALES', 'COST') DEFAULT 'SALES' AFTER debit_amount,
ADD COLUMN time_limit_date DATE NULL AFTER table_type,
ADD COLUMN bank_from_number VARCHAR(100) NULL AFTER bank_from_name,
ADD COLUMN bank_to_number VARCHAR(100) NULL AFTER bank_to_name;

-- Update movements table for summary calculations
ALTER TABLE movements 
ADD COLUMN incentive_amount DECIMAL(18,2) DEFAULT 0 AFTER sell_balance_amount,
ADD COLUMN discount_amount DECIMAL(18,2) DEFAULT 0 AFTER incentive_amount;
