<?php
class NotificationController {
    private $notificationModel;
    
    public function __construct() {
        $this->notificationModel = new Notification();
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'You must be logged in to view notifications.';
            header('Location: index.php?page=user&action=login');
            exit;
        }
    }
    
    public function index() {
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $userId = $_SESSION['user_id'];
        
        $notifications = $this->notificationModel->getAllNotifications($userId, $page);
        $unreadCount = $this->notificationModel->getUnreadNotificationsCount($userId);

        // Определяем общее количество уведомлений
        $totalNotifications = $this->notificationModel->countAllNotifications($userId);
        // Вычисляем количество страниц
        $totalPages = ceil($totalNotifications / ITEMS_PER_PAGE);
        
        include 'views/includes/header.php';
        include 'views/notifications/index.php';
        include 'views/includes/footer.php';
    }
    
    public function markAsRead() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $userId = $_SESSION['user_id'];
        
        if (!$id) {
            $_SESSION['error'] = 'Invalid notification ID.';
            header('Location: index.php?page=notification&action=index');
            exit;
        }
        
        $result = $this->notificationModel->markAsRead($id, $userId);
        
        if (!$result) {
            $_SESSION['error'] = 'Failed to mark notification as read.';
            header('Location: index.php?page=notification&action=index');
            exit;
        }
        
        // If redirect parameter is set, redirect to that page
        if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
            header('Location: ' . $_GET['redirect']);
            exit;
        }
        
        header('Location: index.php?page=notification&action=index');
        exit;
    }
    
    public function markAllAsRead() {
        $userId = $_SESSION['user_id'];
        
        $result = $this->notificationModel->markAllAsRead($userId);
        
        if (!$result) {
            $_SESSION['error'] = 'Failed to mark all notifications as read.';
            header('Location: index.php?page=notification&action=index');
            exit;
        }
        
        $_SESSION['success'] = 'All notifications marked as read.';
        header('Location: index.php?page=notification&action=index');
        exit;
    }
}