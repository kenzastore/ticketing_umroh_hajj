CREATE TABLE IF NOT EXISTS payment_advises (
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
