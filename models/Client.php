<?php
/**
 * Client Model
 */

require_once __DIR__ . '/BaseModel.php';

class Client extends BaseModel {
    protected $table = 'clients';

    /**
     * Get clients with pagination and search
     */
    public function getPaginated($page = 1, $perPage = 10, $search = '') {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?";
            $searchTerm = "%{$search}%";
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Count clients with search
     */
    public function countWithSearch($search = '') {
        if (empty($search)) {
            return $this->count();
        }
        
        $searchTerm = "%{$search}%";
        return $this->count(
            "name LIKE ? OR email LIKE ? OR phone LIKE ?",
            [$searchTerm, $searchTerm, $searchTerm]
        );
    }

    /**
     * Export clients to CSV
     */
    public function exportToCSV() {
        $clients = $this->all('name ASC');
        
        $filename = 'clients_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Address', 'GST/VAT', 'Notes', 'Created At']);
        
        // Data
        foreach ($clients as $client) {
            fputcsv($output, [
                $client['id'],
                $client['name'],
                $client['email'],
                $client['phone'],
                $client['address'],
                $client['gst_vat'],
                $client['notes'],
                $client['created_at']
            ]);
        }
        
        fclose($output);
        exit;
    }
}
