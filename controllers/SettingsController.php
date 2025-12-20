<?php
/**
 * Settings Controller
 */

require_once __DIR__ . '/../models/Setting.php';

class SettingsController extends BaseController {
    private $settingModel;

    public function __construct() {
        $this->settingModel = new Setting();
    }

    /**
     * Show settings page
     */
    public function index() {
        $this->requireAuth();
        
        $settings = $this->settingModel->getAllSettings();
        $flash = $this->getFlashMessage();
        $csrf_token = $this->generateCSRF();
        
        $this->view('settings/company', compact('settings', 'flash', 'csrf_token'));
    }

    /**
     * Update settings
     */
    public function update() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/settings');
        }
        
        $this->validateCSRF($_POST['csrf_token'] ?? '');
        
        // Update settings
        $settingKeys = ['company_name', 'company_address', 'company_phone', 'company_email', 'company_website', 'company_tax_number'];
        
        foreach ($settingKeys as $key) {
            if (isset($_POST[$key])) {
                $this->settingModel->set($key, $this->sanitize($_POST[$key]));
            }
        }
        
        // Handle logo upload
        if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = LOGO_PATH;
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION);
            $fileName = 'logo_' . time() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['company_logo']['tmp_name'], $uploadPath)) {
                $this->settingModel->set('company_logo', $fileName);
            }
        }
        
        $this->redirect('/settings', 'Settings updated successfully');
    }
}
