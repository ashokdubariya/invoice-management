<?php
/**
 * Banking Settings Controller
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Setting.php';

class BankingController extends BaseController {
    private $settingModel;

    public function __construct() {
        $this->settingModel = new Setting();
    }

    /**
     * Show banking settings form
     */
    public function index() {
        $this->requireAuth();
        
        // Get all banking settings
        $settings = $this->settingModel->getAllSettings();
        
        $this->view('settings/banking-settings', [
            'settings' => $settings,
            'pageTitle' => 'Banking Details'
        ]);
    }

    /**
     * Update banking settings
     */
    public function update() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/settings/banking');
        }

        // Define all banking fields
        $bankingFields = [
            // USD fields
            'bank_usd_account_holder',
            'bank_usd_account_number',
            'bank_usd_routing_ach',
            'bank_usd_wire_routing',
            'bank_usd_swift_bic',
            'bank_usd_bank_name',
            'bank_usd_bank_address',
            // GBP fields
            'bank_gbp_account_holder',
            'bank_gbp_account_number',
            'bank_gbp_iban',
            'bank_gbp_sort_code',
            'bank_gbp_swift_bic',
            'bank_gbp_bank_name',
            'bank_gbp_bank_address',
            // INR fields
            'bank_inr_bank_name',
            'bank_inr_account_name',
            'bank_inr_account_number',
            'bank_inr_ifsc_code'
        ];

        // Update each field
        foreach ($bankingFields as $field) {
            $value = $_POST[$field] ?? '';
            $this->settingModel->set($field, $value);
        }

        $this->redirect('/settings/banking', 'Banking details updated successfully');
    }
}
