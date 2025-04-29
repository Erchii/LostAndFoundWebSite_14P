<?php
/**
 * Validate email address
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate date format (YYYY-MM-DD)
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Validate time format (HH:MM)
 */
function validateTime($time, $format = 'H:i') {
    $t = DateTime::createFromFormat($format, $time);
    return $t && $t->format($format) === $time;
}

/**
 * Validate phone number
 */
function validatePhone($phone) {
    // Remove common separators
    $phone = str_replace(['-', '.', ' ', '(', ')', '+'], '', $phone);
    
    // Check if it contains only digits
    return ctype_digit($phone) && strlen($phone) >= 10 && strlen($phone) <= 15;
}

/**
 * Validate password strength
 */
function validatePasswordStrength($password) {
    // At least 6 characters
    if (strlen($password) < 6) {
        return false;
    }
    
    // At least one letter
    if (!preg_match('/[a-zA-Z]/', $password)) {
        return false;
    }
    
    // At least one number
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    
    return true;
}

/**
 * Validate username format
 */
function validateUsername($username) {
    // Alphanumeric, underscores, and hyphens only
    return preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $username);
}

/**
 * Validate URL format
 */
function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Validate image file type
 */
function validateImageType($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $fileType = finfo_file($fileInfo, $file['tmp_name']);
    finfo_close($fileInfo);
    
    return in_array($fileType, $allowedTypes);
}

/**
 * Validate file size
 */
function validateFileSize($file, $maxSize) {
    return $file['size'] <= $maxSize;
}

/**
 * Validate required fields
 */
function validateRequired($data, $fields) {
    $errors = [];
    
    foreach ($fields as $field => $label) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            $errors[] = $label . ' is required.';
        }
    }
    
    return $errors;
}