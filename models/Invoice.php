<?php
/**
 * Invoice Model
 */

require_once __DIR__ . '/BaseModel.php';

class Invoice extends BaseModel {
    protected $table = 'invoices';

    /**
     * Get invoices with client and currency info
     */
    public function getWithDetails($page = 1, $perPage = 10, $filters = []) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT i.*, c.name as client_name, cur.code as currency_code, cur.symbol as currency_symbol 
                FROM {$this->table} i
                LEFT JOIN clients c ON i.client_id = c.id
                LEFT JOIN currencies cur ON i.currency_id = cur.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND i.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['client_id'])) {
            $sql .= " AND i.client_id = ?";
            $params[] = $filters['client_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (i.invoice_number LIKE ? OR c.name LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY i.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Count invoices with filters
     */
    public function countWithFilters($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} i
                LEFT JOIN clients c ON i.client_id = c.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND i.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['client_id'])) {
            $sql .= " AND i.client_id = ?";
            $params[] = $filters['client_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (i.invoice_number LIKE ? OR c.name LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        
        return $result['total'];
    }

    /**
     * Generate unique invoice number
     */
    public function generateInvoiceNumber() {
        do {
            $number = 'INV-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE invoice_number = ?");
            $stmt->execute([$number]);
            $result = $stmt->fetch();
        } while ($result['count'] > 0);
        
        return $number;
    }

    /**
     * Get invoice with all details
     */
    public function getFullInvoice($id) {
        $sql = "SELECT i.*, c.*, cur.code as currency_code, cur.symbol as currency_symbol,
                c.name as client_name, c.email as client_email, c.phone as client_phone,
                c.address as client_address, c.gst_vat as client_gst_vat
                FROM {$this->table} i
                LEFT JOIN clients c ON i.client_id = c.id
                LEFT JOIN currencies cur ON i.currency_id = cur.id
                WHERE i.id = ?";
        
        $stmt = $this->query($sql, [$id]);
        return $stmt->fetch();
    }

    /**
     * Get dashboard statistics
     */
    public function getStatistics() {
        $stats = [];
        
        // Today's revenue
        $stmt = $this->db->query("SELECT SUM(total_amount) as revenue FROM {$this->table} WHERE invoice_date = CURDATE() AND status = 'Paid'");
        $result = $stmt->fetch();
        $stats['today_revenue'] = $result['revenue'] ?? 0;
        
        // Total invoices
        $stats['total_invoices'] = $this->count();
        
        // Pending invoices
        $stats['pending_invoices'] = $this->count("status IN ('Draft', 'Sent')");
        
        // Overdue invoices
        $stats['overdue_invoices'] = $this->count("status != 'Paid' AND due_date < CURDATE()");
        
        return $stats;
    }

    /**
     * Get recent invoices
     */
    public function getRecent($limit = 5) {
        $sql = "SELECT i.*, c.name as client_name, cur.symbol as currency_symbol
                FROM {$this->table} i
                LEFT JOIN clients c ON i.client_id = c.id
                LEFT JOIN currencies cur ON i.currency_id = cur.id
                ORDER BY i.created_at DESC
                LIMIT ?";
        
        $stmt = $this->query($sql, [$limit]);
        return $stmt->fetchAll();
    }
}
