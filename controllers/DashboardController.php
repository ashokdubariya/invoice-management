<?php
/**
 * Dashboard Controller
 */

require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Payment.php';

class DashboardController extends BaseController {
    private $invoiceModel;
    private $clientModel;
    private $paymentModel;

    public function __construct() {
        $this->invoiceModel = new Invoice();
        $this->clientModel = new Client();
        $this->paymentModel = new Payment();
    }

    /**
     * Show dashboard
     */
    public function index() {
        $this->requireAuth();
        
        // Get statistics
        $stats = $this->invoiceModel->getStatistics();
        $stats['total_clients'] = $this->clientModel->count();
        
        // Get payment statistics
        $paymentStats = $this->paymentModel->getStatistics();
        $stats = array_merge($stats, $paymentStats);
        
        // Get recent invoices
        $recentInvoices = $this->invoiceModel->getRecent(10);
        
        // Get recent payments
        $recentPayments = $this->paymentModel->getRecent(5);
        
        $flash = $this->getFlashMessage();
        
        $this->view('dashboard/index', compact('stats', 'recentInvoices', 'recentPayments', 'flash'));
    }
}
