<?php
/**
 * PDF Controller
 */

require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/InvoiceItem.php';
require_once __DIR__ . '/../models/Setting.php';

class PDFController extends BaseController {
    private $invoiceModel;
    private $invoiceItemModel;
    private $settingModel;

    public function __construct() {
        $this->invoiceModel = new Invoice();
        $this->invoiceItemModel = new InvoiceItem();
        $this->settingModel = new Setting();
    }

    /**
     * Generate PDF
     */
    public function generate() {
        $this->requireAuth();
        
        // Check if DOMPDF is installed
        if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
            die('Please run "composer install" to install DOMPDF');
        }
        
        require_once __DIR__ . '/../vendor/autoload.php';
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $invoice = $this->invoiceModel->getFullInvoice($id);
        
        if (!$invoice) {
            $this->redirect('/invoices', 'Invoice not found', 'error');
        }
        
        $items = $this->invoiceItemModel->getByInvoice($id);
        $settings = $this->settingModel->getAllSettings();
        
        // Generate HTML
        ob_start();
        include __DIR__ . '/../views/pdf/invoice-template.php';
        $html = ob_get_clean();
        
        // Create PDF
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Output PDF
        $dompdf->stream('invoice_' . $invoice['invoice_number'] . '.pdf', ['Attachment' => true]);
    }
}
