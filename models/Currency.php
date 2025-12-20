<?php
/**
 * Currency Model
 */

require_once __DIR__ . '/BaseModel.php';

class Currency extends BaseModel {
    protected $table = 'currencies';

    /**
     * Get default currency
     */
    public function getDefault() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE is_default = 1 LIMIT 1");
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Set default currency
     */
    public function setDefault($id) {
        // Remove default from all
        $this->db->exec("UPDATE {$this->table} SET is_default = 0");
        
        // Set new default
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_default = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Check if currency code exists
     */
    public function codeExists($code, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE code = ?";
        $params = [$code];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
}
