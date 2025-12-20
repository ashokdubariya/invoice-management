<?php
/**
 * Client Controller
 */

require_once __DIR__ . '/../models/Client.php';

class ClientController extends BaseController {
    private $clientModel;

    public function __construct() {
        $this->clientModel = new Client();
    }

    /**
     * List all clients
     */
    public function index() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? $this->sanitize($_GET['search']) : '';
        $perPage = 10;
        
        $clients = $this->clientModel->getPaginated($page, $perPage, $search);
        $totalClients = $this->clientModel->countWithSearch($search);
        $totalPages = ceil($totalClients / $perPage);
        
        $flash = $this->getFlashMessage();
        
        $this->view('clients/list', compact('clients', 'page', 'totalPages', 'search', 'flash'));
    }

    /**
     * Show create form
     */
    public function create() {
        $this->requireAuth();
        
        $flash = $this->getFlashMessage();
        $csrf_token = $this->generateCSRF();
        
        $this->view('clients/form', compact('flash', 'csrf_token'));
    }

    /**
     * Store new client
     */
    public function store() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/clients');
        }
        
        $this->validateCSRF($_POST['csrf_token'] ?? '');
        
        $data = [
            'name' => $this->sanitize($_POST['name'] ?? ''),
            'email' => $this->sanitize($_POST['email'] ?? ''),
            'phone' => $this->sanitize($_POST['phone'] ?? ''),
            'address' => $this->sanitize($_POST['address'] ?? ''),
            'gst_vat' => $this->sanitize($_POST['gst_vat'] ?? ''),
            'notes' => $this->sanitize($_POST['notes'] ?? '')
        ];
        
        if (empty($data['name'])) {
            $this->redirect('/clients/create', 'Client name is required', 'error');
        }
        
        $this->clientModel->create($data);
        $this->redirect('/clients', 'Client created successfully');
    }

    /**
     * Show edit form
     */
    public function edit() {
        $this->requireAuth();
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $client = $this->clientModel->find($id);
        
        if (!$client) {
            $this->redirect('/clients', 'Client not found', 'error');
        }
        
        $flash = $this->getFlashMessage();
        $csrf_token = $this->generateCSRF();
        
        $this->view('clients/form', compact('client', 'flash', 'csrf_token'));
    }

    /**
     * Update client
     */
    public function update() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/clients');
        }
        
        $this->validateCSRF($_POST['csrf_token'] ?? '');
        
        $id = (int)$_POST['id'];
        
        $data = [
            'name' => $this->sanitize($_POST['name'] ?? ''),
            'email' => $this->sanitize($_POST['email'] ?? ''),
            'phone' => $this->sanitize($_POST['phone'] ?? ''),
            'address' => $this->sanitize($_POST['address'] ?? ''),
            'gst_vat' => $this->sanitize($_POST['gst_vat'] ?? ''),
            'notes' => $this->sanitize($_POST['notes'] ?? '')
        ];
        
        if (empty($data['name'])) {
            $this->redirect('/clients/edit?id=' . $id, 'Client name is required', 'error');
        }
        
        $this->clientModel->update($id, $data);
        $this->redirect('/clients', 'Client updated successfully');
    }

    /**
     * Delete client
     */
    public function delete() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/clients');
        }
        
        $id = (int)$_POST['id'];
        $this->clientModel->delete($id);
        
        $this->redirect('/clients', 'Client deleted successfully');
    }

    /**
     * Export clients to CSV
     */
    public function exportCSV() {
        $this->requireAuth();
        $this->clientModel->exportToCSV();
    }
}
