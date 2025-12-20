<?php
/**
 * Setting Model
 */

require_once __DIR__ . '/BaseModel.php';

class Setting extends BaseModel {
    protected $table = 'settings';
    protected $primaryKey = 'id';

    /**
     * Get setting value by key
     */
    public function get($key, $default = null) {
        $stmt = $this->db->prepare("SELECT setting_value FROM {$this->table} WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        
        return $result ? $result['setting_value'] : $default;
    }

    /**
     * Set setting value
     */
    public function set($key, $value) {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (setting_key, setting_value) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE setting_value = ?
        ");
        return $stmt->execute([$key, $value, $value]);
    }

    /**
     * Get all settings as key-value array
     */
    public function getAllSettings() {
        $stmt = $this->db->query("SELECT setting_key, setting_value FROM {$this->table}");
        $results = $stmt->fetchAll();
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        return $settings;
    }
}
