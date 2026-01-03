<?php
/**
 * Payment Model
 * Handles payment tracking for invoices
 */

require_once __DIR__ . '/BaseModel.php';

class Payment extends BaseModel {
    protected $table = 'payments';

    /**
     * Get all payments for a specific invoice
     */
    public function getByInvoice($invoiceId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE invoice_id = ? 
                ORDER BY payment_date DESC, created_at DESC";
        
        $stmt = $this->query($sql, [$invoiceId]);
        return $stmt->fetchAll();
    }

    /**
     * Get total amount paid for an invoice
     */
    public function getTotalPaid($invoiceId) {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total_paid 
                FROM {$this->table} 
                WHERE invoice_id = ?";
        
        $stmt = $this->query($sql, [$invoiceId]);
        $result = $stmt->fetch();
        
        return $result['total_paid'] ?? 0;
    }

    /**
     * Get payment statistics for dashboard
     */
    public function getStatistics() {
        $stats = [];
        
        // Total collected today
        $sql = "SELECT COALESCE(SUM(amount), 0) as today_total 
                FROM {$this->table} 
                WHERE payment_date = CURDATE()";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        $stats['today_payments'] = $result['today_total'] ?? 0;
        
        // Total collected this month
        $sql = "SELECT COALESCE(SUM(amount), 0) as month_total 
                FROM {$this->table} 
                WHERE YEAR(payment_date) = YEAR(CURDATE()) 
                AND MONTH(payment_date) = MONTH(CURDATE())";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        $stats['month_payments'] = $result['month_total'] ?? 0;
        
        // Total collected all time
        $sql = "SELECT COALESCE(SUM(amount), 0) as total 
                FROM {$this->table}";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        $stats['total_collected'] = $result['total'] ?? 0;
        
        // Count of payments today
        $sql = "SELECT COUNT(*) as count 
                FROM {$this->table} 
                WHERE payment_date = CURDATE()";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        $stats['today_count'] = $result['count'] ?? 0;
        
        return $stats;
    }

    /**
     * Get recent payments
     */
    public function getRecent($limit = 10) {
        $sql = "SELECT p.*, i.invoice_number, c.name as client_name, cur.symbol as currency_symbol
                FROM {$this->table} p
                LEFT JOIN invoices i ON p.invoice_id = i.id
                LEFT JOIN clients c ON i.client_id = c.id
                LEFT JOIN currencies cur ON i.currency_id = cur.id
                ORDER BY p.payment_date DESC, p.created_at DESC
                LIMIT ?";
        
        $stmt = $this->query($sql, [$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get available payment methods
     */
    public static function getPaymentMethods() {
        return [
            'Cash' => 'Cash',
            'Check' => 'Check',
            'Bank Transfer' => 'Bank Transfer',
            'Credit Card' => 'Credit Card',
            'Debit Card' => 'Debit Card',
            'PayPal' => 'PayPal',
            'Stripe' => 'Stripe',
            'Other' => 'Other'
        ];
    }

    /**
     * Validate payment data
     */
    public function validate($data) {
        $errors = [];

        if (empty($data['invoice_id'])) {
            $errors[] = 'Invoice ID is required';
        }

        if (empty($data['payment_date'])) {
            $errors[] = 'Payment date is required';
        } elseif (strtotime($data['payment_date']) > time()) {
            $errors[] = 'Payment date cannot be in the future';
        }

        if (empty($data['amount']) || $data['amount'] <= 0) {
            $errors[] = 'Payment amount must be greater than zero';
        }

        if (empty($data['payment_method'])) {
            $errors[] = 'Payment method is required';
        }

        return $errors;
    }

    /**
     * Get payment summary for an invoice
     */
    public function getPaymentSummary($invoiceId) {
        $payments = $this->getByInvoice($invoiceId);
        $totalPaid = $this->getTotalPaid($invoiceId);
        
        return [
            'payments' => $payments,
            'total_paid' => $totalPaid,
            'payment_count' => count($payments)
        ];
    }

    /**
     * Get payments by date range
     */
    public function getByDateRange($startDate, $endDate) {
        $sql = "SELECT p.*, i.invoice_number, c.name as client_name, cur.symbol as currency_symbol
                FROM {$this->table} p
                LEFT JOIN invoices i ON p.invoice_id = i.id
                LEFT JOIN clients c ON i.client_id = c.id
                LEFT JOIN currencies cur ON i.currency_id = cur.id
                WHERE p.payment_date BETWEEN ? AND ?
                ORDER BY p.payment_date DESC";
        
        $stmt = $this->query($sql, [$startDate, $endDate]);
        return $stmt->fetchAll();
    }

    /**
     * Get payments by method
     */
    public function getByMethod($method, $limit = null) {
        $sql = "SELECT p.*, i.invoice_number, c.name as client_name
                FROM {$this->table} p
                LEFT JOIN invoices i ON p.invoice_id = i.id
                LEFT JOIN clients c ON i.client_id = c.id
                WHERE p.payment_method = ?
                ORDER BY p.payment_date DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $stmt = $this->query($sql, [$method, $limit]);
        } else {
            $stmt = $this->query($sql, [$method]);
        }
        
        return $stmt->fetchAll();
    }

    /**
     * Get all payments with filters and pagination
     */
    public function getAllWithFilters($page = 1, $perPage = 20, $filters = []) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT p.*, i.invoice_number, c.name as client_name, cur.symbol as currency_symbol
                FROM {$this->table} p
                LEFT JOIN invoices i ON p.invoice_id = i.id
                LEFT JOIN clients c ON i.client_id = c.id
                LEFT JOIN currencies cur ON i.currency_id = cur.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (i.invoice_number LIKE ? OR c.name LIKE ? OR p.reference_number LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND p.payment_date >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND p.payment_date <= ?";
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['payment_method'])) {
            $sql .= " AND p.payment_method = ?";
            $params[] = $filters['payment_method'];
        }
        
        $sql .= " ORDER BY p.payment_date DESC, p.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Count payments with filters
     */
    public function countWithFilters($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} p
                LEFT JOIN invoices i ON p.invoice_id = i.id
                LEFT JOIN clients c ON i.client_id = c.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (i.invoice_number LIKE ? OR c.name LIKE ? OR p.reference_number LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND p.payment_date >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND p.payment_date <= ?";
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['payment_method'])) {
            $sql .= " AND p.payment_method = ?";
            $params[] = $filters['payment_method'];
        }
        
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        
        return $result['total'];
    }
}
