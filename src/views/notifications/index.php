<?php $pageTitle = 'My Notifications'; ?>


<!-- This code displays the "My Notifications" page where users can view their notifications. 
 It features a list of notifications, with an option to mark all as read if there are unread notifications. 
 Each notification includes a title, message, and timestamp, with a button to mark individual notifications as read. 
 If the notification is related to an item, a "View Item" button is displayed.
  Additionally, pagination is provided if there are multiple pages of notifications. -->


<div class="container">
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>My Notifications</h2>
                <?php if ($unreadCount > 0): ?>
                <a href="index.php?page=notification&action=markAllAsRead" class="btn btn-outline-primary">
                    <i class="fas fa-check-double mr-1"></i> Mark All as Read
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="notification-list">
                        <?php if (empty($notifications)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-bell fa-3x text-muted mb-3"></i>
                                <h4>No notifications</h4>
                                <p class="text-muted">You don't have any notifications yet.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($notifications as $notification): ?>
                                <div class="notification-item <?= $notification['is_read'] ? '' : 'unread' ?>">
                                    <div class="d-flex justify-content-between">
                                        <h5 class="notification-title">
                                            <?php if (!$notification['is_read']): ?>
                                                <span class="badge badge-primary mr-2">New</span>
                                            <?php endif; ?>
                                            <?= h($notification['title']) ?>
                                        </h5>
                                        <small class="notification-time">
                                            <?= timeElapsed($notification['created_at']) ?>
                                        </small>
                                    </div>
                                    <p class="notification-message"><?= nl2br(h($notification['message'])) ?></p>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <?php if (!empty($notification['item_id']) && !empty($notification['item_title'])): ?>
                                            <a href="index.php?page=item&action=view&id=<?= $notification['item_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye mr-1"></i> View Item
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if (!$notification['is_read']): ?>
                                            <a href="index.php?page=notification&action=markAsRead&id=<?= $notification['id'] ?>&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-sm btn-light">
                                                <i class="fas fa-check mr-1"></i> Mark as Read
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($notifications) && $totalPages > 1): ?>
                <div class="mt-4">
                    <?= generatePagination($page, $totalPages, 'index.php?page=notification&action=index') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>