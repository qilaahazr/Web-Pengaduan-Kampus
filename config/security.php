<?php
/**
 * Security Utilities
 * Provides functions for input sanitization and output escaping
 */

// Escape output for XSS prevention
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Sanitize input
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
}

// Generate CSRF token
function csrf_token() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verify_csrf($token) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Secure file upload helper
function secure_upload($file, $allowed_types = [], $max_size = 5242880) {
    $result = ['success' => false, 'filename' => '', 'error' => ''];

    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = 'No file uploaded';
        return $result;
    }

    if ($file['size'] > $max_size) {
        $result['error'] = 'File too large';
        return $result;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!empty($allowed_types) && !in_array($mime, $allowed_types)) {
        $result['error'] = 'Invalid file type';
        return $result;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $safe_name = time() . '_' . preg_replace("/[^a-z0-9]/i", "_", $ext);

    if (move_uploaded_file($file['tmp_name'], "../uploads/" . $safe_name)) {
        $result['success'] = true;
        $result['filename'] = $safe_name;
    } else {
        $result['error'] = 'Failed to move file';
    }

    return $result;
}
?>