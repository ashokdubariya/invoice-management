<?php
/**
 * Helper Functions
 */

/**
 * Format currency
 */
function formatCurrency($amount, $symbol = '$') {
    return $symbol . number_format($amount, 2);
}

/**
 * Format date
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Get status badge HTML
 */
function getStatusBadge($status) {
    $badges = [
        'Draft' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Draft</span>',
        'Sent' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Sent</span>',
        'Paid' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Paid</span>',
        'Overdue' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Overdue</span>',
    ];
    
    return $badges[$status] ?? $status;
}

/**
 * Generate unique invoice number
 */
function generateInvoiceNumber() {
    return 'INV-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

/**
 * Check if invoice is overdue
 */
function isOverdue($dueDate, $status) {
    if ($status === 'Paid') {
        return false;
    }
    return strtotime($dueDate) < strtotime('today');
}

/**
 * Escape HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Get base path for URLs
 */
function getBasePath() {
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    return $scriptName === '/' ? '' : $scriptName;
}

/**
 * Get asset URL
 */
function asset($path) {
    return getBasePath() . '/assets/' . ltrim($path, '/');
}

/**
 * Generate URL with base path
 */
function url($path = '') {
    return getBasePath() . '/' . ltrim($path, '/');
}

/**
 * Check if current route matches
 */
function isActive($route) {
    $currentRoute = $_SERVER['REQUEST_URI'];
    return strpos($currentRoute, $route) !== false ? 'active' : '';
}

/**
 * Truncate text
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Calculate line total
 */
function calculateLineTotal($quantity, $unitPrice, $taxPercent = 0) {
    $subtotal = $quantity * $unitPrice;
    $tax = ($subtotal * $taxPercent) / 100;
    return $subtotal + $tax;
}

/**
 * Get pagination HTML
 */
function getPagination($currentPage, $totalPages, $baseUrl) {
    if ($totalPages <= 1) {
        return '';
    }
    
    $html = '<div class="flex items-center justify-center space-x-2 mt-6">';
    
    // Previous button
    if ($currentPage > 1) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($currentPage - 1) . '" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">Previous</a>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $currentPage) {
            $html .= '<span class="px-3 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md">' . $i . '</span>';
        } else {
            $html .= '<a href="' . $baseUrl . '?page=' . $i . '" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">' . $i . '</a>';
        }
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($currentPage + 1) . '" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">Next</a>';
    }
    
    $html .= '</div>';
    
    return $html;
}
