<?php
/**
 * Invoice Controller
 */

require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/InvoiceItem.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Currency.php';

class InvoiceController extends BaseController {
    private $invoiceModel;
    private $invoiceItemModel;
    private $clientModel;
    private $currencyModel;

    public function __construct() {
        $this->invoiceModel = new Invoice();
        $this->invoiceItemModel = new InvoiceItem();
        $this->clientModel = new Client();
        $this->currencyModel = new Currency();
    }

    /**
     * List all invoices
     */
    public function index() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $filters = [
            'status' => $_GET['status'] ?? '',
            'client_id' => $_GET['client_id'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        
        $perPage = 10;
        $invoices = $this->invoiceModel->getWithPaymentDetails($page, $perPage, $filters);
        $totalInvoices = $this->invoiceModel->countWithFilters($filters);
        $totalPages = ceil($totalInvoices / $perPage);
        
        $clients = $this->clientModel->all('name ASC');
        $flash = $this->getFlashMessage();
        
        $this->view('invoices/list', compact('invoices', 'page', 'totalPages', 'filters', 'clients', 'flash'));
    }

    /**
     * Show create form
     */
    public function create() {
        $this->requireAuth();
        
        $clients = $this->clientModel->all('name ASC');
        $currencies = $this->currencyModel->all('code ASC');
        $defaultCurrency = $this->currencyModel->getDefault();
        $invoiceNumber = $this->invoiceModel->generateInvoiceNumber();
        
        $flash = $this->getFlashMessage();
        $csrf_token = $this->generateCSRF();
        
        $this->view('invoices/form', compact('clients', 'currencies', 'defaultCurrency', 'invoiceNumber', 'flash', 'csrf_token'));
    }

    /**
     * Store new invoice
     */
    public function store() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/invoices');
        }
        
        $this->validateCSRF($_POST['csrf_token'] ?? '');
        
        // Calculate totals
        $subtotal = 0;
        $taxAmount = 0;
        
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                $qty = (float)$item['quantity'];
                $price = (float)$item['unit_price'];
                $tax = (float)$item['tax_percent'];
                
                $lineSubtotal = $qty * $price;
                $lineTax = ($lineSubtotal * $tax) / 100;
                
                $subtotal += $lineSubtotal;
                $taxAmount += $lineTax;
            }
        }
        
        $discountAmount = (float)($_POST['discount_amount'] ?? 0);
        $totalAmount = $subtotal + $taxAmount - $discountAmount;
        
        // Create invoice
        $status = $_POST['status'] ?? 'Draft';
        
        // Initialize payment fields based on status
        $amountPaid = 0;
        $outstandingBalance = $totalAmount;
        
        if ($status === 'Paid') {
            $amountPaid = $totalAmount;
            $outstandingBalance = 0;
        }
        
        $invoiceData = [
            'invoice_number' => $this->sanitize($_POST['invoice_number']),
            'client_id' => (int)$_POST['client_id'],
            'currency_id' => (int)$_POST['currency_id'],
            'invoice_date' => $_POST['invoice_date'],
            'due_date' => $_POST['due_date'],
            'status' => $status,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'amount_paid' => $amountPaid,
            'outstanding_balance' => $outstandingBalance,
            'notes' => $this->sanitize($_POST['notes'] ?? '')
        ];
        
        $invoiceId = $this->invoiceModel->create($invoiceData);
        
        // Create invoice items
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                $qty = (float)$item['quantity'];
                $price = (float)$item['unit_price'];
                $tax = (float)$item['tax_percent'];
                $lineTotal = calculateLineTotal($qty, $price, $tax);
                
                $itemData = [
                    'invoice_id' => $invoiceId,
                    'item_name' => $this->sanitize($item['item_name']),
                    'description' => $this->sanitize($item['description'] ?? ''),
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'tax_percent' => $tax,
                    'line_total' => $lineTotal
                ];
                
                $this->invoiceItemModel->create($itemData);
            }
        }
        
        $this->redirect('/invoices', 'Invoice created successfully');
    }

    /**
     * Show edit form
     */
    public function edit() {
        $this->requireAuth();
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $invoice = $this->invoiceModel->find($id);
        
        if (!$invoice) {
            $this->redirect('/invoices', 'Invoice not found', 'error');
        }
        
        $items = $this->invoiceItemModel->getByInvoice($id);
        $clients = $this->clientModel->all('name ASC');
        $currencies = $this->currencyModel->all('code ASC');
        
        $flash = $this->getFlashMessage();
        $csrf_token = $this->generateCSRF();
        
        $this->view('invoices/form', compact('invoice', 'items', 'clients', 'currencies', 'flash', 'csrf_token'));
    }

    /**
     * Update invoice
     */
    public function update() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/invoices');
        }
        
        $this->validateCSRF($_POST['csrf_token'] ?? '');
        
        $id = (int)$_POST['id'];
        
        // Calculate totals
        $subtotal = 0;
        $taxAmount = 0;
        
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                $qty = (float)$item['quantity'];
                $price = (float)$item['unit_price'];
                $tax = (float)$item['tax_percent'];
                
                $lineSubtotal = $qty * $price;
                $lineTax = ($lineSubtotal * $tax) / 100;
                
                $subtotal += $lineSubtotal;
                $taxAmount += $lineTax;
            }
        }
        
        $discountAmount = (float)($_POST['discount_amount'] ?? 0);
        $totalAmount = $subtotal + $taxAmount - $discountAmount;
        
        // Update invoice
        $status = $_POST['status'] ?? 'Draft';
        
        // Get current invoice to preserve payment data from payment records
        $currentInvoice = $this->invoiceModel->find($id);
        $amountPaid = $currentInvoice['amount_paid'] ?? 0;
        $outstandingBalance = $totalAmount - $amountPaid;
        
        // If manually changing status to Paid and no payments exist, set amounts accordingly
        if ($status === 'Paid' && $amountPaid == 0) {
            $amountPaid = $totalAmount;
            $outstandingBalance = 0;
        }
        // If status changed from Paid to something else and amount_paid equals old total, reset it
        elseif ($status !== 'Paid' && $amountPaid == $currentInvoice['total_amount'] && $outstandingBalance == 0) {
            // Check if there are actual payment records
            require_once __DIR__ . '/../models/Payment.php';
            $paymentModel = new Payment();
            $actualPaid = $paymentModel->getTotalPaid($id);
            
            if ($actualPaid == 0) {
                // No actual payments, reset the amounts
                $amountPaid = 0;
                $outstandingBalance = $totalAmount;
                if ($status === 'Draft' || $status === 'Sent') {
                    $status = $status; // Keep the selected status
                }
            } else {
                // Has actual payments, recalculate
                $amountPaid = $actualPaid;
                $outstandingBalance = $totalAmount - $actualPaid;
            }
        }
        // Recalculate outstanding balance if total changed
        else {
            $outstandingBalance = $totalAmount - $amountPaid;
        }
        
        $invoiceData = [
            'invoice_number' => $this->sanitize($_POST['invoice_number']),
            'client_id' => (int)$_POST['client_id'],
            'currency_id' => (int)$_POST['currency_id'],
            'invoice_date' => $_POST['invoice_date'],
            'due_date' => $_POST['due_date'],
            'status' => $status,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'amount_paid' => $amountPaid,
            'outstanding_balance' => $outstandingBalance,
            'notes' => $this->sanitize($_POST['notes'] ?? '')
        ];
        
        $this->invoiceModel->update($id, $invoiceData);
        
        // Delete old items and create new ones
        $this->invoiceItemModel->deleteByInvoice($id);
        
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                $qty = (float)$item['quantity'];
                $price = (float)$item['unit_price'];
                $tax = (float)$item['tax_percent'];
                $lineTotal = calculateLineTotal($qty, $price, $tax);
                
                $itemData = [
                    'invoice_id' => $id,
                    'item_name' => $this->sanitize($item['item_name']),
                    'description' => $this->sanitize($item['description'] ?? ''),
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'tax_percent' => $tax,
                    'line_total' => $lineTotal
                ];
                
                $this->invoiceItemModel->create($itemData);
            }
        }
        
        $this->redirect('/invoices', 'Invoice updated successfully');
    }

    /**
     * Delete invoice
     */
    public function delete() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/invoices');
        }
        
        $id = (int)$_POST['id'];
        $this->invoiceModel->delete($id);
        
        $this->redirect('/invoices', 'Invoice deleted successfully');
    }

    /**
     * View invoice details
     */
    public function viewInvoice() {
        $this->requireAuth();
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $invoice = $this->invoiceModel->getFullInvoice($id);
        
        if (!$invoice) {
            $this->redirect('/invoices', 'Invoice not found', 'error');
        }
        
        $items = $this->invoiceItemModel->getByInvoice($id);
        
        // Load payment data
        require_once __DIR__ . '/../models/Payment.php';
        $paymentModel = new Payment();
        $payments = $paymentModel->getByInvoice($id);
        $paymentMethods = Payment::getPaymentMethods();
        
        $this->view('invoices/view', compact('invoice', 'items', 'payments', 'paymentMethods'));
    }
}
