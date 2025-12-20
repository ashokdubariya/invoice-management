<?php
/**
 * Dashboard Controller
 */

require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Client.php';

class DashboardController extends BaseController {
    private $invoiceModel;
    private $clientModel;

    public function __construct() {
        $this->invoiceModel = new Invoice();
        $this->clientModel = new Client();
    }

    /**
     * Show dashboard
     */
    public function index() {
        $this->requireAuth();
        
        // Get statistics
        $stats = $this->invoiceModel->getStatistics();
        $stats['total_clients'] = $this->clientModel->count();
        
        // Get recent invoices
        $recentInvoices = $this->invoiceModel->getRecent(10);
        
        $flash = $this->getFlashMessage();
        
        $this->view('dashboard/index', compact('stats', 'recentInvoices', 'flash'));
    }
}
