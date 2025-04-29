<?php
// Start session
session_start();

// Load configuration
require_once 'config/config.php';
require_once 'config/database.php';

// Load utility functions
require_once 'utils/helpers.php';
require_once 'utils/validation.php';
require_once 'utils/notification.php';

// Load models
require_once 'models/User.php';
require_once 'models/Item.php';
require_once 'models/Notification.php';

// Load controllers
require_once 'controllers/UserController.php';
require_once 'controllers/ItemController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/NotificationController.php';

// Simple router
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && ($_SESSION['user_role'] ?? '') === 'admin';

// Routes that don't require authentication
$publicRoutes = [
    'home' => true,
    'user' => [
        'login' => true,
        'register' => true,
        'doLogin' => true,
        'doRegister' => true
    ],
    'item' => [
        'index' => true,
        'search' => true,
        'view' => true
    ]
];

// Check if the current route requires authentication
$requiresAuth = true;
if (isset($publicRoutes[$page]) && (is_array($publicRoutes[$page]) && isset($publicRoutes[$page][$action]) || $publicRoutes[$page] === true)) {
    $requiresAuth = false;
}

// Redirect to login if authentication is required but user is not logged in
if ($requiresAuth && !$isLoggedIn) {
    $_SESSION['error'] = 'Please log in to access this page.';
    header('Location: index.php?page=user&action=login');
    exit;
}

// Admin routes
$adminRoutes = [
    'admin' => true
];

// Check if the current route requires admin privileges
$requiresAdmin = false;
if (isset($adminRoutes[$page]) && (is_array($adminRoutes[$page]) && isset($adminRoutes[$page][$action]) || $adminRoutes[$page] === true)) {
    $requiresAdmin = true;
}

// Redirect if admin privileges are required but user is not an admin
if ($requiresAdmin && !$isAdmin) {
    $_SESSION['error'] = 'You do not have permission to access this page.';
    header('Location: index.php');
    exit;
}

// Route the request to the appropriate controller and action
switch ($page) {
    case 'home':
        // Инициализируем нужные модели
        $itemModel = new Item();
        $userModel = new User(); // Создаем экземпляр модели User
        $categories = $itemModel->getCategories();
        
        // Получаем статистику
        $stats = [
            'success_rate' => '85%',
            'total_users' => $userModel->countAllUsers(),
            'items_resolved' => $itemModel->countItemsByStatus('resolved')
        ];
        
        // Получаем последние добавленные предметы
        $recentItems = $itemModel->searchItems(['status' => 'verified'], 1, 4);
        
        include 'views/includes/header.php';
        include 'views/home.php';  // Используем новое представление
        include 'views/includes/footer.php';
        break;
        
    case 'user':
        $controller = new UserController();
        switch ($action) {
            case 'login':
                $controller->login();
                break;
            case 'register':
                $controller->register();
                break;
            case 'doLogin':
                $controller->doLogin();
                break;
            case 'doRegister':
                $controller->doRegister();
                break;
            case 'profile':
                $controller->profile();
                break;
            case 'updateProfile':
                $controller->updateProfile();
                break;
            case 'logout':
                $controller->logout();
                break;
            default:
                http_response_code(404);
                include 'views/404.php';
                break;
        }
        break;
        
    case 'item':
        $controller = new ItemController();
        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'search':
                $controller->search();
                break;
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'view':
                $controller->view();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'update':
                $controller->update();
                break;
            case 'delete':
                $controller->delete();
                break;
            case 'contact':
                $controller->contact();
                break;
            default:
                http_response_code(404);
                include 'views/404.php';
                break;
        }
        break;
        
    case 'admin':
        $controller = new AdminController();
        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'items':
                $controller->items();
                break;
            case 'users':
                $controller->users();
                break;
            case 'verifyItem':
                $controller->verifyItem();
                break;
            case 'rejectItem':
                $controller->rejectItem();
                break;
            case 'resolveItem':
                $controller->resolveItem();
                break;
            case 'editUser':
                $controller->editUser();
                break;
            case 'updateUser':
                $controller->updateUser();
                break;
            case 'deleteUser':
                $controller->deleteUser();
                break;
            default:
                http_response_code(404);
                include 'views/404.php';
                break;
        }
        break;
        
    case 'notification':
        $controller = new NotificationController();
        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'markAsRead':
                $controller->markAsRead();
                break;
            case 'markAllAsRead':
                $controller->markAllAsRead();
                break;
            default:
                http_response_code(404);
                include 'views/404.php';
                break;
        }
        break;
        
    default:
        http_response_code(404);
        include 'views/404.php';
        break;
}