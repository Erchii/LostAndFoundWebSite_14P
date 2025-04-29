<?php
/**
 * Display formatted date
 */
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

/**
 * Display formatted time
 */
function formatTime($time) {
    return date('g:i A', strtotime($time));
}

/**
 * Escape HTML output
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate pagination links
 */
function generatePagination($currentPage, $totalPages, $url) {
    if ($totalPages <= 1) {
        return '';
    }
    
    $output = '<nav aria-label="Page navigation"><ul class="pagination">';
    
    // Previous button
    if ($currentPage > 1) {
        $output .= '<li class="page-item"><a class="page-link" href="' . $url . '&p=' . ($currentPage - 1) . '">&laquo; Previous</a></li>';
    } else {
        $output .= '<li class="page-item disabled"><span class="page-link">&laquo; Previous</span></li>';
    }
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    if ($startPage > 1) {
        $output .= '<li class="page-item"><a class="page-link" href="' . $url . '&p=1">1</a></li>';
        if ($startPage > 2) {
            $output .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $output .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $output .= '<li class="page-item"><a class="page-link" href="' . $url . '&p=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $output .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $output .= '<li class="page-item"><a class="page-link" href="' . $url . '&p=' . $totalPages . '">' . $totalPages . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $output .= '<li class="page-item"><a class="page-link" href="' . $url . '&p=' . ($currentPage + 1) . '">Next &raquo;</a></li>';
    } else {
        $output .= '<li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>';
    }
    
    $output .= '</ul></nav>';
    
    return $output;
}

/**
 * Truncate text to a specified length
 */
function truncateText($text, $length = 100, $append = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $text = substr($text, 0, strrpos($text, ' '));
    
    return $text . $append;
}

/**
 * Format file size for display
 */
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

/**
 * Generate a random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $randomString;
}

/**
 * Convert a date to MySQL format
 */
function toMySQLDate($date) {
    return date('Y-m-d', strtotime($date));
}

/**
 * Get item status badge HTML
 */
function getStatusBadge($status) {
    $badgeClass = '';
    
    switch ($status) {
        case 'pending':
            $badgeClass = 'badge-warning';
            break;
        case 'verified':
            $badgeClass = 'badge-success';
            break;
        case 'resolved':
            $badgeClass = 'badge-primary';
            break;
        case 'rejected':
            $badgeClass = 'badge-danger';
            break;
        default:
            $badgeClass = 'badge-secondary';
    }
    
    return '<span class="badge ' . $badgeClass . '">' . ucfirst($status) . '</span>';
}

/**
 * Get item type badge HTML
 */
function getTypeBadge($type) {
    $badgeClass = $type === 'lost' ? 'badge-danger' : 'badge-info';
    
    return '<span class="badge ' . $badgeClass . '">' . ucfirst($type) . '</span>';
}

/**
 * Upload an image file
 */
function uploadImage($file, $uploadPath) {
    // Check if upload directory exists, create if not
    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        
        return ['error' => 'Upload error: ' . ($errorMessages[$file['error']] ?? 'Unknown error')];
    }
    
    // Check file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['error' => 'File size exceeds the maximum allowed size (' . formatFileSize(MAX_UPLOAD_SIZE) . ')'];
    }
    
    // Check file type
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $fileType = finfo_file($fileInfo, $file['tmp_name']);
    finfo_close($fileInfo);
    
    if (!in_array($fileType, ALLOWED_IMAGE_TYPES)) {
        return ['error' => 'Invalid file type. Allowed types: ' . implode(', ', array_map(function($type) {
            return str_replace('image/', '', $type);
        }, ALLOWED_IMAGE_TYPES))];
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = generateRandomString(16) . '.' . $fileExtension;
    $filePath = $fileName;
    
    // Move the uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath . $filePath)) {
        return ['error' => 'Failed to move uploaded file'];
    }
    
    return ['path' => $filePath];
}

/**
 * Get time elapsed string
 */
function timeElapsed($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    // Исправленная версия - вычисляем недели без создания динамического свойства
    $weeks = floor($diff->d / 7);
    $days = $diff->d % 7;
    
    $string = [
        'y' => 'year',
        'm' => 'month',
        // Используем переменную вместо свойства
        'w' => 'week',
        // Используем переменную вместо измененного свойства
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    ];
    
    $parts = [];
    
    if ($diff->y) {
        $parts[] = $diff->y . ' ' . $string['y'] . ($diff->y > 1 ? 's' : '');
    }
    
    if ($diff->m) {
        $parts[] = $diff->m . ' ' . $string['m'] . ($diff->m > 1 ? 's' : '');
    }
    
    if ($weeks) {
        $parts[] = $weeks . ' ' . $string['w'] . ($weeks > 1 ? 's' : '');
    }
    
    if ($days) {
        $parts[] = $days . ' ' . $string['d'] . ($days > 1 ? 's' : '');
    }
    
    if ($diff->h) {
        $parts[] = $diff->h . ' ' . $string['h'] . ($diff->h > 1 ? 's' : '');
    }
    
    if ($diff->i) {
        $parts[] = $diff->i . ' ' . $string['i'] . ($diff->i > 1 ? 's' : '');
    }
    
    if ($diff->s) {
        $parts[] = $diff->s . ' ' . $string['s'] . ($diff->s > 1 ? 's' : '');
    }
    
    if (empty($parts)) {
        return 'just now';
    }
    
    // Возвращаем только первую часть (как в оригинальной функции)
    return $parts[0] . ' ago';
}

/**
 * Get first paragraph of text
 */
function getFirstParagraph($text) {
    $pos = strpos($text, "\n");
    
    if ($pos === false) {
        return truncateText($text, 200);
    }
    
    return substr($text, 0, $pos);
}

/**
 * Check if current page matches given page
 */
function isCurrentPage($page, $action = null) {
    $currentPage = $_GET['page'] ?? 'home';
    $currentAction = $_GET['action'] ?? 'index';
    
    if ($action === null) {
        return $currentPage === $page;
    }
    
    return $currentPage === $page && $currentAction === $action;
}

/**
 * Generate active class for navigation
 */
function getActiveClass($page, $action = null) {
    return isCurrentPage($page, $action) ? 'active' : '';
}