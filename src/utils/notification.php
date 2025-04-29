<?php
/**
 * Send email notification
 */
function sendEmailNotification($to, $subject, $message) {
    // Skip if email settings are not configured
    if (empty(MAIL_HOST) || empty(MAIL_USERNAME) || empty(MAIL_PASSWORD)) {
        return false;
    }
    
    // Headers
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM_ADDRESS . '>' . "\r\n";
    
    // Send email
    return mail($to, $subject, $message, $headers);
}

/**
 * Format notification message as HTML
 */
function formatNotificationEmail($title, $message, $actionUrl = null, $actionText = null) {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>' . h($title) . '</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
            }
            .header {
                background-color: #4a6fdc;
                color: white;
                padding: 20px;
                text-align: center;
            }
            .content {
                padding: 20px;
                background-color: #f9f9f9;
            }
            .footer {
                text-align: center;
                padding: 20px;
                font-size: 12px;
                color: #666;
            }
            .button {
                display: inline-block;
                background-color: #4a6fdc;
                color: white;
                text-decoration: none;
                padding: 10px 20px;
                border-radius: 5px;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>' . h($title) . '</h1>
            </div>
            <div class="content">
                <p>' . nl2br(h($message)) . '</p>';
    
    // Add action button if URL is provided
    if ($actionUrl && $actionText) {
        $html .= '<p><a href="' . h($actionUrl) . '" class="button">' . h($actionText) . '</a></p>';
    }
    
    $html .= '
            </div>
            <div class="footer">
                <p>This is an automated message from ' . h(APP_NAME) . '. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
}

/**
 * Send notification for new item
 */
function sendNewItemNotification($item, $user) {
    $subject = 'New ' . ucfirst($item['type']) . ' Item Posted: ' . $item['title'];
    $message = 'A new ' . $item['type'] . ' item has been posted by ' . $user['username'] . '.' . "\n\n";
    $message .= 'Title: ' . $item['title'] . "\n";
    $message .= 'Category: ' . $item['category_name'] . "\n";
    $message .= 'Location: ' . $item['location'] . "\n";
    $message .= 'Date: ' . formatDate($item['date_lost_found']);
    
    $actionUrl = APP_URL . '/index.php?page=item&action=view&id=' . $item['id'];
    $actionText = 'View Item';
    
    $emailContent = formatNotificationEmail($subject, $message, $actionUrl, $actionText);
    
    // Send to admins
    $db = Database::getInstance();
    $admins = $db->select("SELECT email FROM users WHERE role = 'admin'");
    
    foreach ($admins as $admin) {
        sendEmailNotification($admin['email'], $subject, $emailContent);
    }
}

/**
 * Send notification for item status change
 */
function sendItemStatusNotification($item, $status) {
    $subject = 'Item Status Updated: ' . $item['title'];
    $message = 'The status of your item "' . $item['title'] . '" has been updated to ' . ucfirst($status) . '.' . "\n\n";
    
    switch ($status) {
        case 'verified':
            $message .= 'Your item is now visible to other users and will appear in search results.';
            break;
        case 'rejected':
            $message .= 'Your item has been rejected. Reason: ' . ($item['admin_notes'] ?: 'No reason provided.');
            break;
        case 'resolved':
            $message .= 'Your item has been marked as resolved. If this is incorrect, please contact an administrator.';
            break;
    }
    
    $actionUrl = APP_URL . '/index.php?page=item&action=view&id=' . $item['id'];
    $actionText = 'View Item';
    
    $emailContent = formatNotificationEmail($subject, $message, $actionUrl, $actionText);
    
    // Get user email
    $db = Database::getInstance();
    $user = $db->selectOne("SELECT email FROM users WHERE id = ?", [$item['user_id']]);
    
    if ($user) {
        sendEmailNotification($user['email'], $subject, $emailContent);
    }
}

/**
 * Send notification for contact request
 */
function sendContactNotification($contact, $item, $user) {
    $subject = 'New Contact Request for: ' . $item['title'];
    $message = 'You have received a new contact request for your item "' . $item['title'] . '" from ' . $user['username'] . '.' . "\n\n";
    $message .= 'Message: ' . $contact['message'] . "\n\n";
    $message .= 'You can view the contact details and respond from your dashboard.';
    
    $actionUrl = APP_URL . '/index.php?page=item&action=view&id=' . $item['id'];
    $actionText = 'View Contact Request';
    
    $emailContent = formatNotificationEmail($subject, $message, $actionUrl, $actionText);
    
    // Get item owner email
    $db = Database::getInstance();
    $itemOwner = $db->selectOne("SELECT email FROM users WHERE id = ?", [$item['user_id']]);
    
    if ($itemOwner) {
        sendEmailNotification($itemOwner['email'], $subject, $emailContent);
    }
}

/**
 * Send notification for contact request status
 */
function sendContactStatusNotification($contact, $item, $status) {
    $subject = 'Contact Request ' . ucfirst($status) . ': ' . $item['title'];
    
    if ($status === 'approved') {
        $message = 'Your contact request for the item "' . $item['title'] . '" has been approved.' . "\n\n";
        $message .= 'The item owner will contact you directly using your registered contact information.';
    } else {
        $message = 'Your contact request for the item "' . $item['title'] . '" has been rejected.' . "\n\n";
        $message .= 'The item owner has decided not to share their contact information for this request.';
    }
    
    $actionUrl = APP_URL . '/index.php?page=item&action=view&id=' . $item['id'];
    $actionText = 'View Item';
    
    $emailContent = formatNotificationEmail($subject, $message, $actionUrl, $actionText);
    
    // Get requester email
    $db = Database::getInstance();
    $requester = $db->selectOne("SELECT email FROM users WHERE id = ?", [$contact['user_id']]);
    
    if ($requester) {
        sendEmailNotification($requester['email'], $subject, $emailContent);
    }
}

/**
 * Display flash messages
 */
function displayFlashMessages() {
    $output = '';
    
    // Display success message
    if (isset($_SESSION['success'])) {
        $output .= '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        $output .= h($_SESSION['success']);
        $output .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
        $output .= '<span aria-hidden="true">&times;</span>';
        $output .= '</button>';
        $output .= '</div>';
        
        unset($_SESSION['success']);
    }
    
    // Display error message
    if (isset($_SESSION['error'])) {
        $output .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        $output .= h($_SESSION['error']);
        $output .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
        $output .= '<span aria-hidden="true">&times;</span>';
        $output .= '</button>';
        $output .= '</div>';
        
        unset($_SESSION['error']);
    }
    
    // Display error messages array
    if (isset($_SESSION['errors']) && is_array($_SESSION['errors']) && !empty($_SESSION['errors'])) {
        $output .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        $output .= '<ul class="mb-0">';
        
        foreach ($_SESSION['errors'] as $error) {
            $output .= '<li>' . h($error) . '</li>';
        }
        
        $output .= '</ul>';
        $output .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
        $output .= '<span aria-hidden="true">&times;</span>';
        $output .= '</button>';
        $output .= '</div>';
        
        unset($_SESSION['errors']);
    }
    
    return $output;
}