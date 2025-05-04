<?php

// UserController handles user-related operations such as login, registration, profile management, and logout.
// It validates input data, manages sessions, updates user profiles, and interacts with models for users, items, and notifications.
// Includes access control to restrict unauthorized access to certain features.


class UserController {
    private $userModel;
    private $itemModel;
    private $notificationModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->itemModel = new Item();
        $this->notificationModel = new Notification();
    }
    
    public function login() {
        // Display login form
        include 'views/includes/header.php';
        include 'views/users/login.php';
        include 'views/includes/footer.php';
    }
    
    public function register() {
        // Display registration form
        include 'views/includes/header.php';
        include 'views/users/register.php';
        include 'views/includes/footer.php';
    }
    
    public function doLogin() {
        // Process login form
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=user&action=login');
            exit;
        }
        
        // Validate input
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Please enter both username and password.';
            header('Location: index.php?page=user&action=login');
            exit;
        }
        
        // Check if user exists
        $user = $this->userModel->getUserByUsername($username);
        
        if (!$user || !$this->userModel->verifyPassword($user, $password)) {
            $_SESSION['error'] = 'Invalid username or password.';
            header('Location: index.php?page=user&action=login');
            exit;
        }
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['success'] = 'You are now logged in.';
        
        // Redirect to appropriate page
        if ($user['role'] === 'admin') {
            header('Location: index.php?page=admin');
        } else {
            header('Location: index.php');
        }
        exit;
    }
    
    public function doRegister() {
        // Process registration form
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=user&action=register');
            exit;
        }
        
        // Validate input
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        $errors = [];
        
        if (empty($username)) {
            $errors[] = 'Username is required.';
        } elseif ($this->userModel->getUserByUsername($username)) {
            $errors[] = 'Username already exists.';
        }
        
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        } elseif ($this->userModel->getUserByEmail($email)) {
            $errors[] = 'Email already exists.';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        } elseif ($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match.';
        }
        
        if (empty($firstName)) {
            $errors[] = 'First name is required.';
        }
        
        if (empty($lastName)) {
            $errors[] = 'Last name is required.';
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = [
                'username' => $username,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone
            ];
            header('Location: index.php?page=user&action=register');
            exit;
        }
        
        // Create user
        $userData = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'role' => 'user'
        ];
        
        $userId = $this->userModel->createUser($userData);
        
        if (!$userId) {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            header('Location: index.php?page=user&action=register');
            exit;
        }
        
        $_SESSION['success'] = 'Registration successful. You can now log in.';
        header('Location: index.php?page=user&action=login');
        exit;
    }
    
    public function profile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=user&action=login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);
        $items = $this->itemModel->getItemsByUser($userId);
        
        include 'views/includes/header.php';
        include 'views/users/profile.php';
        include 'views/includes/footer.php';
    }
    
    public function updateProfile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=user&action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=user&action=profile');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Validate input
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        $errors = [];
        
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        } else {
            $existingUser = $this->userModel->getUserByEmail($email);
            if ($existingUser && $existingUser['id'] !== $userId) {
                $errors[] = 'Email already exists.';
            }
        }
        
        if (!empty($password) && strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }
        
        if (!empty($password) && $password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match.';
        }
        
        if (empty($firstName)) {
            $errors[] = 'First name is required.';
        }
        
        if (empty($lastName)) {
            $errors[] = 'Last name is required.';
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?page=user&action=profile');
            exit;
        }
        
        // Update user
        $userData = [
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone
        ];
        
        if (!empty($password)) {
            $userData['password'] = $password;
        }
        
        $result = $this->userModel->updateUser($userId, $userData);
        
        if (!$result) {
            $_SESSION['error'] = 'Update failed. Please try again.';
            header('Location: index.php?page=user&action=profile');
            exit;
        }
        
        $_SESSION['success'] = 'Profile updated successfully.';
        header('Location: index.php?page=user&action=profile');
        exit;
    }
    
    public function logout() {
        // Clear session variables
        session_unset();
        session_destroy();
        
        // Redirect to login page
        header('Location: index.php?page=user&action=login');
        exit;
    }
}