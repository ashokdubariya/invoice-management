<?php
/**
 * Invoice Item Model
 */

require_once __DIR__ . '/BaseModel.php';

class InvoiceItem extends BaseModel {
    protected $table = 'invoice_items';

    /**
     * Get items for an invoice
     */
    public function getByInvoice($invoiceId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE invoice_id = ? ORDER BY id ASC");
        $stmt->execute([$invoiceId]);
        return $stmt->fetchAll();
    }

    /**
     * Delete all items for an invoice
     */
    public function deleteByInvoice($invoiceId) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE invoice_id = ?");
        return $stmt->execute([$invoiceId]);
    }

    /**
     * Create multiple items
     */
    public function createMultiple($items) {
        foreach ($items as $item) {
            $this->create($item);
        }
    }
}
