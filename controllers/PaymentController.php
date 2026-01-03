<?php
/**
 * Payment Controller
 * Handles payment recording and management
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Invoice.php';

class PaymentController extends BaseController {
    private $paymentModel;
    private $invoiceModel;

    public function __construct() {
        $this->paymentModel = new Payment();
        $this->invoiceModel = new Invoice();
    }

    /**
     * List all payments
     */
    public function index() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        
        // Build filters
        $filters = [
            'search' => $_GET['search'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? '',
            'payment_method' => $_GET['payment_method'] ?? ''
        ];
        
        // Get payments with filters
        $payments = $this->paymentModel->getAllWithFilters($page, $perPage, $filters);
        $totalPayments = $this->paymentModel->countWithFilters($filters);
        $totalPages = ceil($totalPayments / $perPage);
        
        // Get statistics
        $stats = $this->paymentModel->getStatistics();
        
        // Get payment methods for filter
        $paymentMethods = array_values(Payment::getPaymentMethods());
        
        $flash = $this->getFlashMessage();
        
        $this->view('payments/list', compact('payments', 'page', 'totalPages', 'filters', 'stats', 'totalPayments', 'paymentMethods', 'flash'));
    }

    /**
     * Store a new payment
     */
    public function store() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        // Get POST data
        $data = [
            'invoice_id' => $_POST['invoice_id'] ?? null,
            'payment_date' => $_POST['payment_date'] ?? date('Y-m-d'),
            'amount' => $_POST['amount'] ?? 0,
            'payment_method' => $_POST['payment_method'] ?? 'Cash',
            'reference_number' => $_POST['reference_number'] ?? null,
            'notes' => $_POST['notes'] ?? null
        ];

        // Validate
        $errors = $this->paymentModel->validate($data);
        
        if (!empty($errors)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 400);
            return;
        }

        // Check if invoice exists
        $invoice = $this->invoiceModel->findById($data['invoice_id']);
        if (!$invoice) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
            return;
        }

        // Get current outstanding balance
        $outstandingBalance = $invoice['outstanding_balance'] ?? $invoice['total_amount'];
        
        // Warning if payment exceeds outstanding balance
        $warning = null;
        if ($data['amount'] > $outstandingBalance) {
            $warning = "Payment amount exceeds outstanding balance. Overpayment: " . 
                       formatCurrency($data['amount'] - $outstandingBalance, $invoice['currency_symbol'] ?? '$');
        }

        try {
            // Create payment
            $paymentId = $this->paymentModel->create($data);

            if ($paymentId) {
                // Get updated invoice details
                $updatedInvoice = $this->invoiceModel->findById($data['invoice_id']);
                
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Payment recorded successfully',
                    'warning' => $warning,
                    'payment_id' => $paymentId,
                    'invoice' => [
                        'status' => $updatedInvoice['status'],
                        'amount_paid' => $updatedInvoice['amount_paid'],
                        'outstanding_balance' => $updatedInvoice['outstanding_balance']
                    ]
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Failed to record payment'
                ], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing payment
     */
    public function update() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        $paymentId = $_POST['payment_id'] ?? null;
        
        if (!$paymentId) {
            $this->jsonResponse(['success' => false, 'message' => 'Payment ID is required'], 400);
            return;
        }

        // Check if payment exists
        $existingPayment = $this->paymentModel->findById($paymentId);
        if (!$existingPayment) {
            $this->jsonResponse(['success' => false, 'message' => 'Payment not found'], 404);
            return;
        }

        // Get update data
        $data = [
            'payment_date' => $_POST['payment_date'] ?? $existingPayment['payment_date'],
            'amount' => $_POST['amount'] ?? $existingPayment['amount'],
            'payment_method' => $_POST['payment_method'] ?? $existingPayment['payment_method'],
            'reference_number' => $_POST['reference_number'] ?? $existingPayment['reference_number'],
            'notes' => $_POST['notes'] ?? $existingPayment['notes']
        ];

        // Add invoice_id for validation
        $data['invoice_id'] = $existingPayment['invoice_id'];

        // Validate
        $errors = $this->paymentModel->validate($data);
        
        if (!empty($errors)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 400);
            return;
        }

        try {
            $success = $this->paymentModel->update($paymentId, $data);

            if ($success) {
                // Get updated invoice details
                $updatedInvoice = $this->invoiceModel->findById($existingPayment['invoice_id']);
                
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Payment updated successfully',
                    'invoice' => [
                        'status' => $updatedInvoice['status'],
                        'amount_paid' => $updatedInvoice['amount_paid'],
                        'outstanding_balance' => $updatedInvoice['outstanding_balance']
                    ]
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Failed to update payment'
                ], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a payment
     */
    public function delete() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        $paymentId = $_POST['payment_id'] ?? null;
        
        if (!$paymentId) {
            $this->jsonResponse(['success' => false, 'message' => 'Payment ID is required'], 400);
            return;
        }

        // Check if payment exists
        $payment = $this->paymentModel->findById($paymentId);
        if (!$payment) {
            $this->jsonResponse(['success' => false, 'message' => 'Payment not found'], 404);
            return;
        }

        try {
            $success = $this->paymentModel->delete($paymentId);

            if ($success) {
                // Get updated invoice details
                $updatedInvoice = $this->invoiceModel->findById($payment['invoice_id']);
                
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Payment deleted successfully',
                    'invoice' => [
                        'status' => $updatedInvoice['status'],
                        'amount_paid' => $updatedInvoice['amount_paid'],
                        'outstanding_balance' => $updatedInvoice['outstanding_balance']
                    ]
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Failed to delete payment'
                ], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payments for an invoice (AJAX)
     */
    public function getByInvoice() {
        $this->requireAuth();
        
        $invoiceId = $_GET['invoice_id'] ?? null;
        
        if (!$invoiceId) {
            $this->jsonResponse(['success' => false, 'message' => 'Invoice ID is required'], 400);
            return;
        }

        try {
            $summary = $this->paymentModel->getPaymentSummary($invoiceId);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $summary
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to send JSON response
     */
    private function jsonResponse($data, $statusCode = 200) {
        // Clean any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
