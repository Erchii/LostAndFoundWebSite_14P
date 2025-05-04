<!DOCTYPE html>

<!-- This file provides the structure for the webpage's head, navigation bar,
  and dynamic main content. It includes stylesheets, session-based user role handling, 
  and navigation links for different user actions. -->


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? h($pageTitle) . ' - ' : '' ?><?= h(APP_NAME) ?></title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    
    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
    <!-- Admin CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">
    <?php endif; ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-search-location mr-2"></i><?= h(APP_NAME) ?>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item <?= getActiveClass('home') ?>">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item <?= (getActiveClass('item', 'index') && isset($_GET['type']) && $_GET['type'] === 'lost') ? 'active' : '' ?>">
                        <a class="nav-link" href="index.php?page=item&action=index&type=lost">Lost Items</a>
                    </li>
                    <li class="nav-item <?= (getActiveClass('item', 'index') && isset($_GET['type']) && $_GET['type'] === 'found') ? 'active' : '' ?>">
                        <a class="nav-link" href="index.php?page=item&action=index&type=found">Found Items</a>
                    </li>
                    <li class="nav-item <?= getActiveClass('item', 'search') ?>">
                        <a class="nav-link" href="index.php?page=item&action=search">Search</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav ml-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php 
                        // Get unread notifications count
                        $notificationModel = new Notification();
                        $unreadCount = $notificationModel->getUnreadNotificationsCount($_SESSION['user_id']);
                        ?>
                        <li class="nav-item <?= getActiveClass('notification') ?>">
                            <a class="nav-link" href="index.php?page=notification&action=index">
                                <i class="fas fa-bell"></i>
                                <?php if ($unreadCount > 0): ?>
                                    <span class="badge badge-danger"><?= $unreadCount ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item <?= getActiveClass('item', 'create') ?>">
                            <a class="nav-link" href="index.php?page=item&action=create">
                                <i class="fas fa-plus-circle"></i> Post Item
                            </a>
                        </li>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <li class="nav-item <?= getActiveClass('admin') ?>">
                                <a class="nav-link" href="index.php?page=admin">Admin</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> <?= h($_SESSION['username']) ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="index.php?page=user&action=profile">
                                    <i class="fas fa-user mr-2"></i> Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="index.php?page=user&action=logout">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="nav-item <?= getActiveClass('user', 'login') ?>">
                            <a class="nav-link" href="index.php?page=user&action=login">Login</a>
                        </li>
                        <li class="nav-item <?= getActiveClass('user', 'register') ?>">
                            <a class="nav-link" href="index.php?page=user&action=register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="container py-4">
        <?= displayFlashMessages() ?>