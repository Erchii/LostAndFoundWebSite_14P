<?php

// ItemController class handles all operations related to lost and found items.
// It includes methods for displaying, searching, creating, updating, viewing, and deleting items.
// Also manages notifications for admins and user access control for each action.
// Constructor loads models for item, user, and notification operations.
// Access to certain actions is restricted to authenticated users or admins.


class ItemController {
    private $itemModel;
    private $userModel;
    private $notificationModel;
    
    public function __construct() {
        $this->itemModel = new Item();
        $this->userModel = new User();
        $this->notificationModel = new Notification();
    }
    
    public function index() {
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $type = isset($_GET['type']) && in_array($_GET['type'], ['lost', 'found']) ? $_GET['type'] : null;
        
        if ($type) {
            $items = $this->itemModel->getItemsByType($type, $page);
            $totalItems = $this->itemModel->countItemsByType($type);
        } else {
            $items = $this->itemModel->getAllItems($page);
            $totalItems = $this->itemModel->countAllItems();
        }
        
        $totalPages = ceil($totalItems / ITEMS_PER_PAGE);
        $categories = $this->itemModel->getCategories();
        
        // Определяем относительный путь от корня проекта 
        include __DIR__ . '/../views/includes/header.php';
        include __DIR__ . '/../views/items/index.php';
        include __DIR__ . '/../views/includes/footer.php';
    }
    
    public function search() {
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        
        // Get filter parameters
        $filters = [
            'type' => isset($_GET['type']) && in_array($_GET['type'], ['lost', 'found']) ? $_GET['type'] : '',
            'category_id' => isset($_GET['category_id']) ? (int)$_GET['category_id'] : '',
            'date_from' => isset($_GET['date_from']) ? $_GET['date_from'] : '',
            'date_to' => isset($_GET['date_to']) ? $_GET['date_to'] : '',
            'location' => isset($_GET['location']) ? trim($_GET['location']) : '',
            'keyword' => isset($_GET['keyword']) ? trim($_GET['keyword']) : '',
            'status' => isset($_GET['status']) ? $_GET['status'] : 'verified'
        ];
        
        $items = $this->itemModel->searchItems($filters, $page);
        $totalItems = $this->itemModel->countSearchResults($filters);
        $totalPages = ceil($totalItems / ITEMS_PER_PAGE);
        $categories = $this->itemModel->getCategories();
        
        include 'views/includes/header.php';
        include 'views/items/search.php';
        include 'views/includes/footer.php';
    }
    
    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=user&action=login');
            exit;
        }
        
        $categories = $this->itemModel->getCategories();
        
        include 'views/includes/header.php';
        include 'views/items/create.php';
        include 'views/includes/footer.php';
    }
    
    public function store() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=user&action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=item&action=create');
            exit;
        }
        
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $type = isset($_POST['type']) && in_array($_POST['type'], ['lost', 'found']) ? $_POST['type'] : '';
        $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : '';
        $location = trim($_POST['location'] ?? '');
        $dateLostFound = $_POST['date_lost_found'] ?? '';
        $timeLostFound = $_POST['time_lost_found'] ?? '';
        
        $errors = [];
        
        if (empty($title)) {
            $errors[] = 'Title is required.';
        }
        
        if (empty($description)) {
            $errors[] = 'Description is required.';
        }
        
        if (empty($type)) {
            $errors[] = 'Type is required.';
        }
        
        if (empty($categoryId)) {
            $errors[] = 'Category is required.';
        }
        
        if (empty($location)) {
            $errors[] = 'Location is required.';
        }
        
        if (empty($dateLostFound)) {
            $errors[] = 'Date is required.';
        } elseif (!validateDate($dateLostFound)) {
            $errors[] = 'Invalid date format.';
        }
        
        if (!empty($timeLostFound) && !validateTime($timeLostFound)) {
            $errors[] = 'Invalid time format.';
        }
        
        // Handle image upload
        $imagePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadImage($_FILES['image'], UPLOAD_PATH);
            
            if (isset($uploadResult['error'])) {
                $errors[] = $uploadResult['error'];
            } else {
                $imagePath = $uploadResult['path'];
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = [
                'title' => $title,
                'description' => $description,
                'type' => $type,
                'category_id' => $categoryId,
                'location' => $location,
                'date_lost_found' => $dateLostFound,
                'time_lost_found' => $timeLostFound
            ];
            header('Location: index.php?page=item&action=create');
            exit;
        }
        
        // Create item
        $itemData = [
            'title' => $title,
            'description' => $description,
            'type' => $type,
            'status' => 'pending', // New items are pending until verified by admin
            'category_id' => $categoryId,
            'location' => $location,
            'date_lost_found' => $dateLostFound,
            'time_lost_found' => $timeLostFound ?: null,
            'image_path' => $imagePath,
            'user_id' => $_SESSION['user_id']
        ];
        
        $itemId = $this->itemModel->createItem($itemData);
        
        if (!$itemId) {
            $_SESSION['error'] = 'Failed to create item. Please try again.';
            header('Location: index.php?page=item&action=create');
            exit;
        }
        
        // Create notification for admins about new item
        $admins = $this->userModel->getUsersByRole('admin');
        foreach ($admins as $admin) {
            $notificationData = [
                'user_id' => $admin['id'],
                'item_id' => $itemId,
                'title' => 'New Item Submitted',
                'message' => 'A new ' . $type . ' item has been submitted and requires verification: "' . $title . '"',
                'is_read' => 0
            ];
            $this->notificationModel->createNotification($notificationData);
        }
        
        $_SESSION['success'] = 'Item created successfully. It will be visible after admin verification.';
        header('Location: index.php?page=user&action=profile');
        exit;
    }
    
    public function view() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            header('Location: index.php?page=item&action=index');
            exit;
        }
        
        $item = $this->itemModel->getItemById($id);
        
        if (!$item) {
            $_SESSION['error'] = 'Item not found.';
            header('Location: index.php?page=item&action=index');
            exit;
        }
        
        // Check if item is pending and user is not the owner or admin
        if ($item['status'] === 'pending' && (!isset($_SESSION['user_id']) || ($_SESSION['user_id'] != $item['user_id'] && $_SESSION['user_role'] !== 'admin'))) {
            $_SESSION['error'] = 'This item is pending verification and not yet visible.';
            header('Location: index.php?page=item&action=index');
            exit;
        }
        
        $contacts = [];
        if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $item['user_id'] || $_SESSION['user_role'] === 'admin')) {
            $contacts = $this->itemModel->getContactsByItem($id);
        }

        $itemModel = $this->itemModel;
        
        include 'views/includes/header.php';
        include 'views/items/details.php';
        include 'views/includes/footer.php';
    }
    
    public function edit() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=user&action=login');
            exit;
        }
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            header('Location: index.php?page=user&action=profile');
            exit;
        }
        
        $item = $this->itemModel->getItemById($id);
        
        if (!$item) {
            $_SESSION['error'] = 'Item not found.';
            header('Location: index.php?page=user&action=profile');
            exit;
        }
        
        // Check if user is the owner or admin
        if ($_SESSION['user_id'] != $item['user_id'] && $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = 'You do not have permission to edit this item.';
            header('Location: index.php?page=item&action=view&id=' . $id);
            exit;
        }
        
        $categories = $this->itemModel->getCategories();
        
        include 'views/includes/header.php';
        include 'views/items/edit.php';
        include 'views/includes/footer.php';
    }
    
    public function update() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=user&action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=user&action=profile');
            exit;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            header('Location: index.php?page=user&action=profile');
            exit;
        }
        
        $item = $this->itemModel->getItemById($id);
        
        if (!$item) {
            $_SESSION['error'] = 'Item not found.';
            header('Location: index.php?page=user&action=profile');
            exit;
        }
        
        // Check if user is the owner or admin
        if ($_SESSION['user_id'] != $item['user_id'] && $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = 'You do not have permission to edit this item.';
            header('Location: index.php?page=item&action=view&id=' . $id);
            exit;
        }
        
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : '';
        $location = trim($_POST['location'] ?? '');
        $dateLostFound = $_POST['date_lost_found'] ?? '';
        $timeLostFound = $_POST['time_lost_found'] ?? '';
        
        $errors = [];
        
        if (empty($title)) {
            $errors[] = 'Title is required.';
        }
        
        if (empty($description)) {
            $errors[] = 'Description is required.';
        }
        
        if (empty($categoryId)) {
            $errors[] = 'Category is required.';
        }
        
        if (empty($location)) {
            $errors[] = 'Location is required.';
        }
        
        if (empty($dateLostFound)) {
            $errors[] = 'Date is required.';
        } elseif (!validateDate($dateLostFound)) {
            $errors[] = 'Invalid date format.';
        }
        
        if (!empty($timeLostFound) && !validateTime($timeLostFound)) {
            $errors[] = 'Invalid time format.';
        }
        
        // Handle image upload
        $imagePath = $item['image_path'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadImage($_FILES['image'], UPLOAD_PATH);
            
            if (isset($uploadResult['error'])) {
                $errors[] = $uploadResult['error'];
            } else {
                // Delete old image if exists
                if (!empty($imagePath) && file_exists(UPLOAD_PATH . $imagePath)) {
                    unlink(UPLOAD_PATH . $imagePath);
                }
                
                $imagePath = $uploadResult['path'];
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?page=item&action=edit&id=' . $id);
            exit;
        }
        
        // Update item
        $itemData = [
            'title' => $title,
            'description' => $description,
            'category_id' => $categoryId,
            'location' => $location,
            'date_lost_found' => $dateLostFound,
            'time_lost_found' => $timeLostFound ?: null,
            'image_path' => $imagePath
        ];
        
        // If admin is updating, check if status is changed
        if ($_SESSION['user_role'] === 'admin' && isset($_POST['status'])) {
            $status = $_POST['status'];
            if (in_array($status, ['pending', 'verified', 'resolved', 'rejected'])) {
                $itemData['status'] = $status;
                
                // If status is rejected, add admin notes
                if ($status === 'rejected' && isset($_POST['admin_notes'])) {
                    $itemData['admin_notes'] = trim($_POST['admin_notes']);
                }
                
                // Create notification for status change
                $this->notificationModel->createItemStatusNotification($id, $status);
            }
        } else {
            // If not admin, reset to pending status when updated
            $itemData['status'] = 'pending';
            
            // Create notification for admins about updated item
            $admins = $this->userModel->getUsersByRole('admin');
            foreach ($admins as $admin) {
                $notificationData = [
                    'user_id' => $admin['id'],
                    'item_id' => $id,
                    'title' => 'Item Updated',
                    'message' => 'An item has been updated and requires re-verification: "' . $title . '"',
                    'is_read' => 0
                ];
                $this->notificationModel->createNotification($notificationData);
            }
        }
        
        $result = $this->itemModel->updateItem($id, $itemData);
        
        if (!$result) {
            $_SESSION['error'] = 'Failed to update item. Please try again.';
            header('Location: index.php?page=item&action=edit&id=' . $id);
            exit;
        }
        
        $_SESSION['success'] = 'Item updated successfully.';
        
        if ($_SESSION['user_role'] === 'admin') {
            header('Location: index.php?page=admin&action=items');
        } else {
            header('Location: index.php?page=item&action=view&id=' . $id);
        }
        exit;
    }
    
    public function delete() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=user&action=login');
            exit;
        }
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            header('Location: index.php?page=user&action=profile');
            exit;
        }
        
        $item = $this->itemModel->getItemById($id);
        
        if (!$item) {
            $_SESSION['error'] = 'Item not found.';
            header('Location: index.php?page=user&action=profile');
            exit;
        }
        
        // Check if user is the owner or admin
        if ($_SESSION['user_id'] != $item['user_id'] && $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = 'You do not have permission to delete this item.';
            header('Location: index.php?page=item&action=view&id=' . $id);
            exit;
        }
        
        // Delete image if exists
        if (!empty($item['image_path']) && file_exists(UPLOAD_PATH . $item['image_path'])) {
            unlink(UPLOAD_PATH . $item['image_path']);
        }
        
        $result = $this->itemModel->deleteItem($id);
        
        if (!$result) {
            $_SESSION['error'] = 'Failed to delete item. Please try again.';
            
            if ($_SESSION['user_role'] === 'admin') {
                header('Location: index.php?page=admin&action=items');
            } else {
                header('Location: index.php?page=user&action=profile');
            }
            exit;
        }
        
        $_SESSION['success'] = 'Item deleted successfully.';
        
        if ($_SESSION['user_role'] === 'admin') {
            header('Location: index.php?page=admin&action=items');
        } else {
            header('Location: index.php?page=user&action=profile');
        }
        exit;
    }
    
    public function contact() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=user&action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }
        
        $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $message = trim($_POST['message'] ?? '');
        
        if (!$itemId || empty($message)) {
            $_SESSION['error'] = 'Invalid request.';
            header('Location: index.php');
            exit;
        }
        
        $item = $this->itemModel->getItemById($itemId);
        
        if (!$item) {
            $_SESSION['error'] = 'Item not found.';
            header('Location: index.php');
            exit;
        }
        
        // Check if item is verified or resolved
        if (!in_array($item['status'], ['verified', 'resolved'])) {
            $_SESSION['error'] = 'This item is not available for contact requests.';
            header('Location: index.php?page=item&action=view&id=' . $itemId);
            exit;
        }
        
        // Check if user is not the owner
        if ($_SESSION['user_id'] == $item['user_id']) {
            $_SESSION['error'] = 'You cannot contact yourself about your own item.';
            header('Location: index.php?page=item&action=view&id=' . $itemId);
            exit;
        }
        
        // Create contact request
        $contactData = [
            'item_id' => $itemId,
            'user_id' => $_SESSION['user_id'],
            'message' => $message,
            'status' => 'pending'
        ];
        
        $contactId = $this->itemModel->createContact($contactData);
        
        if (!$contactId) {
            $_SESSION['error'] = 'Failed to send contact request. Please try again.';
            header('Location: index.php?page=item&action=view&id=' . $itemId);
            exit;
        }
        
        // Create notification for item owner
        $this->notificationModel->createContactNotification($contactId);
        
        $_SESSION['success'] = 'Contact request sent successfully. The item owner will be notified.';
        header('Location: index.php?page=item&action=view&id=' . $itemId);
        exit;
    }
}