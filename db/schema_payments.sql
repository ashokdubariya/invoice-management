-- Payment Tracking Migration
-- Created: 2025-12-24
-- Description: Add payments table to track invoice payments

USE invoice_management;

-- Create payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    payment_date DATE NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('Cash', 'Check', 'Bank Transfer', 'Credit Card', 'Debit Card', 'PayPal', 'Stripe', 'Other') DEFAULT 'Cash',
    reference_number VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_invoice_id (invoice_id),
    INDEX idx_payment_date (payment_date),
    INDEX idx_payment_method (payment_method)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes for better query performance
CREATE INDEX idx_invoice_payment_date ON payments(invoice_id, payment_date);

-- Add amount_paid column to invoices table for caching (optional but improves performance)
ALTER TABLE invoices 
ADD COLUMN amount_paid DECIMAL(10, 2) DEFAULT 0.00 AFTER total_amount,
ADD COLUMN outstanding_balance DECIMAL(10, 2) DEFAULT 0.00 AFTER amount_paid;

-- Update existing invoices to set outstanding_balance = total_amount
UPDATE invoices SET outstanding_balance = total_amount WHERE outstanding_balance = 0;

-- Create trigger to auto-update invoice payment totals when payment is added
DELIMITER $$

CREATE TRIGGER after_payment_insert
AFTER INSERT ON payments
FOR EACH ROW
BEGIN
    DECLARE total_paid DECIMAL(10, 2);
    DECLARE invoice_total DECIMAL(10, 2);
    DECLARE new_status VARCHAR(20);
    
    -- Calculate total paid for this invoice
    SELECT COALESCE(SUM(amount), 0) INTO total_paid
    FROM payments
    WHERE invoice_id = NEW.invoice_id;
    
    -- Get invoice total
    SELECT total_amount INTO invoice_total
    FROM invoices
    WHERE id = NEW.invoice_id;
    
    -- Determine new status
    IF total_paid >= invoice_total THEN
        SET new_status = 'Paid';
    ELSEIF total_paid > 0 THEN
        SET new_status = 'Partially Paid';
    ELSE
        SET new_status = 'Sent';
    END IF;
    
    -- Update invoice
    UPDATE invoices
    SET amount_paid = total_paid,
        outstanding_balance = invoice_total - total_paid,
        status = new_status
    WHERE id = NEW.invoice_id;
END$$

-- Create trigger to auto-update invoice payment totals when payment is updated
CREATE TRIGGER after_payment_update
AFTER UPDATE ON payments
FOR EACH ROW
BEGIN
    DECLARE total_paid DECIMAL(10, 2);
    DECLARE invoice_total DECIMAL(10, 2);
    DECLARE new_status VARCHAR(20);
    
    -- Calculate total paid for this invoice
    SELECT COALESCE(SUM(amount), 0) INTO total_paid
    FROM payments
    WHERE invoice_id = NEW.invoice_id;
    
    -- Get invoice total
    SELECT total_amount INTO invoice_total
    FROM invoices
    WHERE id = NEW.invoice_id;
    
    -- Determine new status
    IF total_paid >= invoice_total THEN
        SET new_status = 'Paid';
    ELSEIF total_paid > 0 THEN
        SET new_status = 'Partially Paid';
    ELSE
        SET new_status = 'Sent';
    END IF;
    
    -- Update invoice
    UPDATE invoices
    SET amount_paid = total_paid,
        outstanding_balance = invoice_total - total_paid,
        status = new_status
    WHERE id = NEW.invoice_id;
END$$

-- Create trigger to auto-update invoice payment totals when payment is deleted
CREATE TRIGGER after_payment_delete
AFTER DELETE ON payments
FOR EACH ROW
BEGIN
    DECLARE total_paid DECIMAL(10, 2);
    DECLARE invoice_total DECIMAL(10, 2);
    DECLARE new_status VARCHAR(20);
    
    -- Calculate total paid for this invoice
    SELECT COALESCE(SUM(amount), 0) INTO total_paid
    FROM payments
    WHERE invoice_id = OLD.invoice_id;
    
    -- Get invoice total
    SELECT total_amount INTO invoice_total
    FROM invoices
    WHERE id = OLD.invoice_id;
    
    -- Determine new status
    IF total_paid >= invoice_total THEN
        SET new_status = 'Paid';
    ELSEIF total_paid > 0 THEN
        SET new_status = 'Partially Paid';
    ELSE
        SET new_status = 'Sent';
    END IF;
    
    -- Update invoice
    UPDATE invoices
    SET amount_paid = total_paid,
        outstanding_balance = invoice_total - total_paid,
        status = new_status
    WHERE id = OLD.invoice_id;
END$$

DELIMITER ;

-- Update invoice status enum to include 'Partially Paid'
ALTER TABLE invoices 
MODIFY COLUMN status ENUM('Draft', 'Sent', 'Partially Paid', 'Paid', 'Overdue') DEFAULT 'Draft';
