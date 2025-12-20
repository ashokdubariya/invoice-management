<?php
/**
 * Base Controller Class
 * Provides common methods for all controllers
 */

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

abstract class BaseController {
    
    /**
     * Render a view
     */
    protected function view($viewPath, $data = []) {
        extract($data);
        
        $viewFile = __DIR__ . '/../views/' . $viewPath . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: {$viewPath}");
        }
    }

    /**
     * Redirect to another page
     */
    protected function redirect($url, $message = null, $type = 'success') {
        if ($message) {
            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_type'] = $type;
        }
        
        // Use url() helper to add base path if URL starts with /
        if (!empty($url) && $url[0] === '/') {
            $url = url($url);
        }
        
        header("Location: {$url}");
        exit;
    }

    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Check if user is authenticated
     */
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login', 'Please login to continue', 'error');
        }
    }

    /**
     * Get current user
     */
    protected function getCurrentUser() {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get flash message
     */
    protected function getFlashMessage() {
        $message = $_SESSION['flash_message'] ?? null;
        $type = $_SESSION['flash_type'] ?? 'success';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return ['message' => $message, 'type' => $type];
    }

    /**
     * Validate CSRF token
     */
    protected function validateCSRF($token) {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            die('CSRF token validation failed');
        }
    }

    /**
     * Generate CSRF token
     */
    protected function generateCSRF() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Sanitize input
     */
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
}
