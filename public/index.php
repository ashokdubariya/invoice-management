<?php
/**
 * Application Entry Point
 */

require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/../controllers/BaseController.php';

// Load all controllers
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../controllers/ClientController.php';
require_once __DIR__ . '/../controllers/CurrencyController.php';
require_once __DIR__ . '/../controllers/InvoiceController.php';
require_once __DIR__ . '/../controllers/SettingsController.php';
require_once __DIR__ . '/../controllers/PDFController.php';
require_once __DIR__ . '/../controllers/BankingController.php';

// Initialize router
$router = new Router();

// Authentication routes
$router->get('/', [AuthController::class, 'showLogin']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// Dashboard
$router->get('/dashboard', [DashboardController::class, 'index']);

// Clients
$router->get('/clients', [ClientController::class, 'index']);
$router->get('/clients/create', [ClientController::class, 'create']);
$router->post('/clients/store', [ClientController::class, 'store']);
$router->get('/clients/edit', [ClientController::class, 'edit']);
$router->post('/clients/update', [ClientController::class, 'update']);
$router->post('/clients/delete', [ClientController::class, 'delete']);
$router->get('/clients/export', [ClientController::class, 'exportCSV']);

// Currencies
$router->get('/currencies', [CurrencyController::class, 'index']);
$router->get('/currencies/create', [CurrencyController::class, 'create']);
$router->post('/currencies/store', [CurrencyController::class, 'store']);
$router->get('/currencies/edit', [CurrencyController::class, 'edit']);
$router->post('/currencies/update', [CurrencyController::class, 'update']);
$router->post('/currencies/delete', [CurrencyController::class, 'delete']);
$router->post('/currencies/set-default', [CurrencyController::class, 'setDefault']);

// Invoices
$router->get('/invoices', [InvoiceController::class, 'index']);
$router->get('/invoices/create', [InvoiceController::class, 'create']);
$router->post('/invoices/store', [InvoiceController::class, 'store']);
$router->get('/invoices/edit', [InvoiceController::class, 'edit']);
$router->post('/invoices/update', [InvoiceController::class, 'update']);
$router->post('/invoices/delete', [InvoiceController::class, 'delete']);
$router->get('/invoices/view', [InvoiceController::class, 'viewInvoice']);

// PDF Export
$router->get('/invoices/pdf', [PDFController::class, 'generate']);

// Settings
$router->get('/settings', [SettingsController::class, 'index']);
$router->post('/settings/update', [SettingsController::class, 'update']);

// Banking Settings
$router->get('/settings/banking', [BankingController::class, 'index']);
$router->post('/settings/banking/update', [BankingController::class, 'update']);

// Dispatch the request
$router->dispatch();
