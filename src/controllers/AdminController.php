<?php

// Controller for admin panel: manages dashboard, 
// Item moderation (verify/reject/resolve), and user management (list/edit/update/delete).


class AdminController {
    private $itemModel;
    private $userModel;
    private $notificationModel;
    
    public function __construct() {
        $this->itemModel = new Item();
        $this->userModel = new User();
        $this->notificationModel = new Notification();
        
        // Check if user is admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = 'You do not have permission to access the admin area.';
            header('Location: index.php');
            exit;
        }
    }
    
    public function index() {
        // Get statistics for dashboard
        $stats = [
            'total_items' => $this->itemModel->countAllItems(),
            'lost_items' => $this->itemModel->countItemsByType('lost'),
            'found_items' => $this->itemModel->countItemsByType('found'),
            'pending_items' => $this->itemModel->countItemsByStatus('pending'),
            'verified_items' => $this->itemModel->countItemsByStatus('verified'),
            'resolved_items' => $this->itemModel->countItemsByStatus('resolved'),
            'rejected_items' => $this->itemModel->countItemsByStatus('rejected'),
            'total_users' => $this->userModel->countAllUsers(),
            'regular_users' => $this->userModel->countUsersByRole('user'),
            'admin_users' => $this->userModel->countUsersByRole('admin')
        ];
        
        include 'views/includes/header.php';
        include 'views/admin/dashboard.php';
        include 'views/includes/footer.php';
    }
    
    public function items() {
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $status = isset($_GET['status']) ? $_GET['status'] : 'all';
        $type = isset($_GET['type']) ? $_GET['type'] : 'all';
        
        $filters = [
            'status' => $status !== 'all' ? $status : '',
            'type' => $type !== 'all' ? $type : '',
            'keyword' => isset($_GET['keyword']) ? trim($_GET['keyword']) : ''
        ];
        
        $items = $this->itemModel->searchItems($filters, $page);
        $totalItems = $this->itemModel->countSearchResults($filters);
        $totalPages = ceil($totalItems / ITEMS_PER_PAGE);
        
        include 'views/includes/header.php';
        include 'views/admin/items.php';
        include 'views/includes/footer.php';
    }
    
    public function users() {
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        
        if (!empty($keyword)) {
            $users = $this->userModel->searchUsers($keyword, $page);
            $totalUsers = $this->userModel->countSearchResults($keyword);
        } else {
            $users = $this->userModel->getAllUsers($page);
            $totalUsers = $this->userModel->countAllUsers();
        }
        
        $totalPages = ceil($totalUsers / ITEMS_PER_PAGE);
        
        include 'views/includes/header.php';
        include 'views/admin/users.php';
        include 'views/includes/footer.php';
    }
    
    public function verifyItem() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            $_SESSION['error'] = 'Invalid item ID.';
            header('Location: index.php?page=admin&action=items');
            exit;
        }
        
        $item = $this->itemModel->getItemById($id);
        
        if (!$item) {
            $_SESSION['error'] = 'Item not found.';
            header('Location: index.php?page=admin&action=items');
            exit;
        }
        
        $result = $this->itemModel->updateItem($id, ['status' => 'verified']);
        
        if (!$result) {
            $_SESSION['error'] = 'Failed to verify item. Please try again.';
            header('Location: index.php?page=admin&action=items');
            exit;
        }
        
        // Create notification for item owner
        $this->notificationModel->createItemStatusNotification($id, 'verified');
        
        $_SESSION['success'] = 'Item verified successfully.';
        header('Location: index.php?page=admin&action=items');
        exit;
    }
    
    public function rejectItem() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=items');
            exit;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $adminNotes = trim($_POST['admin_notes'] ?? '');
        
        if (!$id) {
            $_SESSION['error'] = 'Invalid item ID.';
            header('Location: index.php?page=admin&action=items');
            exit;
        }
        
        if (empty($adminNotes)) {
            $_SESSION['error'] = 'Please provide a reason for rejection.';
            header('Location: index.php?page=admin&action=items');
            exit;
        }
        
        $item = $this->itemModel->getItemById($id);
        
        if (!$item) {
            $_SESSION['error'] = 'Item not found.';
            header('Location: index.php?page=admin&action=items');
            exit;
        }
        
        $result = $this->itemModel->updateItem($id, [
            'status' => 'rejected',
            'admin_notes' => $adminNotes
        ]);
        
        if (!$result) {
            $_SESSION['error'] = 'Failed to reject item. Please try again.';
            header('Location: index.php?page=admin&action=items');
            exit;
        }
        
        // Create notification for item owner
        $this->notificationModel->createItemStatusNotification($id, 'rejected');
        
        $_SESSION['success'] = 'Item rejected successfully.';
        header('Location: index.php?page=admin&action=items');
        exit;
    }
    
    public function resolveItem() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            $_SESSION['error'] = 'Invalid item ID.';
            header('Location: index.php?page=admin&action=items');
            exit;
        }
        
        $item = $this->itemModel->getItemById($id);
        
        if (!$item) {
            $_SESSION['error'] = 'Item not found.';
            header('Location: index.php?page=admin&action=items');
            exit;
        }
        
        $result = $this->itemModel->updateItem($id, ['status' => 'resolved']);
        
        if (!$result) {
            $_SESSION['error'] = 'Failed to resolve item. Please try again.';
            header('Location: index.php?page=admin&action=items');
            exit;
        }
        
        // Create notification for item owner
        $this->notificationModel->createItemStatusNotification($id, 'resolved');
        
        $_SESSION['success'] = 'Item resolved successfully.';
        header('Location: index.php?page=admin&action=items');
        exit;
    }
    
    public function editUser() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            $_SESSION['error'] = 'Invalid user ID.';
            header('Location: index.php?page=admin&action=users');
            exit;
        }
        
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'User not found.';
            header('Location: index.php?page=admin&action=users');
            exit;
        }
        
        include 'views/includes/header.php';
        include 'views/admin/edit_user.php';
        include 'views/includes/footer.php';
    }
    
    public function updateUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin&action=users');
            exit;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            $_SESSION['error'] = 'Invalid user ID.';
            header('Location: index.php?page=admin&action=users');
            exit;
        }
        
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'User not found.';
            header('Location: index.php?page=admin&action=users');
            exit;
        }
        
        // Validate input
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $role = isset($_POST['role']) && in_array($_POST['role'], ['user', 'admin']) ? $_POST['role'] : 'user';
        
        $errors = [];
        
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        } else {
            $existingUser = $this->userModel->getUserByEmail($email);
            if ($existingUser && $existingUser['id'] !== $id) {
                $errors[] = 'Email already exists.';
            }
        }
        
        if (!empty($password) && strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }
        
        if (empty($firstName)) {
            $errors[] = 'First name is required.';
        }
        
        if (empty($lastName)) {
            $errors[] = 'Last name is required.';
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?page=admin&action=editUser&id=' . $id);
            exit;
        }
        
        // Update user
        $userData = [
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'role' => $role
        ];
        
        if (!empty($password)) {
            $userData['password'] = $password;
        }
        
        $result = $this->userModel->updateUser($id, $userData);
        
        if (!$result) {
            $_SESSION['error'] = 'Failed to update user. Please try again.';
            header('Location: index.php?page=admin&action=editUser&id=' . $id);
            exit;
        }
        
        $_SESSION['success'] = 'User updated successfully.';
        header('Location: index.php?page=admin&action=users');
        exit;
    }
    
    public function deleteUser() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            $_SESSION['error'] = 'Invalid user ID.';
            header('Location: index.php?page=admin&action=users');
            exit;
        }
        
        // Don't allow deleting yourself
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'You cannot delete your own account.';
            header('Location: index.php?page=admin&action=users');
            exit;
        }
        
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'User not found.';
            header('Location: index.php?page=admin&action=users');
            exit;
        }
        
        $result = $this->userModel->deleteUser($id);
        
        if (!$result) {
            $_SESSION['error'] = 'Failed to delete user. Please try again.';
            header('Location: index.php?page=admin&action=users');
            exit;
        }
        
        $_SESSION['success'] = 'User deleted successfully.';
        header('Location: index.php?page=admin&action=users');
        exit;
    }
}