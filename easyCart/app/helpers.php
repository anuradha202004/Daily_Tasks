<?php
/**
 * Helper Functions
 * Global utility functions used across the application
 */



/**
 * Redirect to a URL
 * @param string $url
 */
function redirect($url) {
    // If url starts with /, assume it is internal
    if (strpos($url, '/') === 0) {
        $url = ltrim($url, '/');
    }
    header("Location: $url");
    exit;
}

/**
 * Get the base URL of the application (root of project)
 * @param string $path
 * @return string
 */
function url($path = '') {
    // If URL_ROOT is defined (points to /public), we go one level up for the project root
    // Fix: dirname on URL is unreliable. We assume standard structure where we split by /public
    $root = str_replace('/public', '', URL_ROOT);
    return $root . '/' . ltrim($path, '/');
}

/**
 * Get the base URL for public assets (points directly to /public)
 * @param string $path
 * @return string
 */
function baseUrl($path = '') {
    return URL_ROOT . '/' . ltrim($path, '/');
}

/**
 * Load a view file
 * @param string $view
 * @param array $data
 */
function view($view, $data = []) {
    extract($data);
    $viewPath = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
    
    if (file_exists($viewPath)) {
        require $viewPath;
    } else {
        die("View not found: $view");
    }
}

/**
 * Sanitize output
 * @param string $string
 * @return string
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Get or Create a Guest Session ID
 * Uses a long-lived cookie to track guests
 */
function getGuestSessionId() {
    // First check session (for current request consistency)
    if (isset($_SESSION['guest_session_id'])) {
        return $_SESSION['guest_session_id'];
    }
    
    // Then check cookie (for persistence across requests)
    if (isset($_COOKIE['guest_session_id'])) {
        $_SESSION['guest_session_id'] = $_COOKIE['guest_session_id'];
        return $_COOKIE['guest_session_id'];
    }
    
    // Generate new unique ID
    $guestId = bin2hex(random_bytes(16));
    
    // Store in session for this request
    $_SESSION['guest_session_id'] = $guestId;
    
    // Set cookie for 30 days (will be available on next request)
    setcookie('guest_session_id', $guestId, time() + (86400 * 30), "/");
    
    return $guestId;
}
