-- Add booking_request_id and tcp to movements table
ALTER TABLE movements 
ADD COLUMN booking_request_id BIGINT UNSIGNED NULL AFTER id,
ADD COLUMN tcp INT NULL AFTER passenger_count,
ADD INDEX idx_mv_booking_request (booking_request_id),
ADD CONSTRAINT fk_mv_booking_request FOREIGN KEY (booking_request_id) REFERENCES booking_requests(id) ON UPDATE CASCADE ON DELETE SET NULL;
