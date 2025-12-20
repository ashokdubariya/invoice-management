<?php
/**
 * Currency Controller
 */

require_once __DIR__ . '/../models/Currency.php';

class CurrencyController extends BaseController {
    private $currencyModel;

    public function __construct() {
        $this->currencyModel = new Currency();
    }

    /**
     * List all currencies
     */
    public function index() {
        $this->requireAuth();
        
        $currencies = $this->currencyModel->all('code ASC');
        $flash = $this->getFlashMessage();
        
        $this->view('currencies/list', compact('currencies', 'flash'));
    }

    /**
     * Show create form
     */
    public function create() {
        $this->requireAuth();
        
        $flash = $this->getFlashMessage();
        $csrf_token = $this->generateCSRF();
        
        $this->view('currencies/form', compact('flash', 'csrf_token'));
    }

    /**
     * Store new currency
     */
    public function store() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/currencies');
        }
        
        $this->validateCSRF($_POST['csrf_token'] ?? '');
        
        $data = [
            'code' => strtoupper($this->sanitize($_POST['code'] ?? '')),
            'symbol' => $this->sanitize($_POST['symbol'] ?? ''),
            'exchange_rate' => (float)($_POST['exchange_rate'] ?? 1.0),
            'is_default' => 0
        ];
        
        if (empty($data['code']) || empty($data['symbol'])) {
            $this->redirect('/currencies/create', 'Currency code and symbol are required', 'error');
        }
        
        if ($this->currencyModel->codeExists($data['code'])) {
            $this->redirect('/currencies/create', 'Currency code already exists', 'error');
        }
        
        $this->currencyModel->create($data);
        $this->redirect('/currencies', 'Currency created successfully');
    }

    /**
     * Show edit form
     */
    public function edit() {
        $this->requireAuth();
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $currency = $this->currencyModel->find($id);
        
        if (!$currency) {
            $this->redirect('/currencies', 'Currency not found', 'error');
        }
        
        $flash = $this->getFlashMessage();
        $csrf_token = $this->generateCSRF();
        
        $this->view('currencies/form', compact('currency', 'flash', 'csrf_token'));
    }

    /**
     * Update currency
     */
    public function update() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/currencies');
        }
        
        $this->validateCSRF($_POST['csrf_token'] ?? '');
        
        $id = (int)$_POST['id'];
        
        $data = [
            'code' => strtoupper($this->sanitize($_POST['code'] ?? '')),
            'symbol' => $this->sanitize($_POST['symbol'] ?? ''),
            'exchange_rate' => (float)($_POST['exchange_rate'] ?? 1.0)
        ];
        
        if (empty($data['code']) || empty($data['symbol'])) {
            $this->redirect('/currencies/edit?id=' . $id, 'Currency code and symbol are required', 'error');
        }
        
        if ($this->currencyModel->codeExists($data['code'], $id)) {
            $this->redirect('/currencies/edit?id=' . $id, 'Currency code already exists', 'error');
        }
        
        $this->currencyModel->update($id, $data);
        $this->redirect('/currencies', 'Currency updated successfully');
    }

    /**
     * Delete currency
     */
    public function delete() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/currencies');
        }
        
        $id = (int)$_POST['id'];
        $currency = $this->currencyModel->find($id);
        
        if ($currency && $currency['is_default']) {
            $this->redirect('/currencies', 'Cannot delete default currency', 'error');
        }
        
        $this->currencyModel->delete($id);
        $this->redirect('/currencies', 'Currency deleted successfully');
    }

    /**
     * Set default currency
     */
    public function setDefault() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/currencies');
        }
        
        $id = (int)$_POST['id'];
        $this->currencyModel->setDefault($id);
        
        $this->redirect('/currencies', 'Default currency updated successfully');
    }
}
