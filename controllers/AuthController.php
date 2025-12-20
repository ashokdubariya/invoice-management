<?php
/**
 * Authentication Controller
 */

require_once __DIR__ . '/../models/User.php';

class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Show login page
     */
    public function showLogin() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }

        $flash = $this->getFlashMessage();
        $csrf_token = $this->generateCSRF();
        
        $this->view('auth/login', compact('flash', 'csrf_token'));
    }

    /**
     * Handle login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        $username = $this->sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $csrf_token = $_POST['csrf_token'] ?? '';

        // Validate CSRF
        $this->validateCSRF($csrf_token);

        // Validate input
        if (empty($username) || empty($password)) {
            $this->redirect('/login', 'Please enter username and password', 'error');
        }

        // Authenticate
        $user = $this->userModel->authenticate($username, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            $this->redirect('/dashboard', 'Welcome back, ' . $user['username'] . '!');
        } else {
            $this->redirect('/login', 'Invalid username or password', 'error');
        }
    }

    /**
     * Handle logout
     */
    public function logout() {
        session_destroy();
        $this->redirect('/login', 'You have been logged out successfully');
    }
}
