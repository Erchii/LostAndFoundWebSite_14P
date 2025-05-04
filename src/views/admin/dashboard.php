<?php $pageTitle = 'Admin Dashboard'; ?>


 <!--// The Admin Dashboard provides an interface for administrators to manage and monitor key metrics, 
 user activities, and item statuses. It includes features such as stats overview, 
 quick actions, lists of pending items and recently registered users,
  and the ability to perform actions like verification, rejection, and user management. 
It also contains a modal for rejecting items with an explanation.-->

<div class="admin-dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Admin Dashboard</h2>
        <button id="refresh-stats" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-sync-alt mr-1"></i> Refresh Stats
        </button>
    </div>
    
    <!-- Stats Overview -->
    <div class="row admin-stats" id="admin-stats">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['total_items'] ?></h3>
                    <p>Total Items</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-search"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['lost_items'] ?></h3>
                    <p>Lost Items</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-hand-holding"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['found_items'] ?></h3>
                    <p>Found Items</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['total_users'] ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['pending_items'] ?></h3>
                    <p>Pending Items</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['verified_items'] ?></h3>
                    <p>Verified Items</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['resolved_items'] ?></h3>
                    <p>Resolved Items</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['rejected_items'] ?></h3>
                    <p>Rejected Items</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Quick Actions</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-3">
                    <a href="index.php?page=admin&action=items&status=pending" class="btn btn-warning btn-block">
                        <i class="fas fa-clipboard-check mr-1"></i> Pending Verifications
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <a href="index.php?page=admin&action=items" class="btn btn-primary btn-block">
                        <i class="fas fa-list mr-1"></i> All Items
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <a href="index.php?page=admin&action=users" class="btn btn-info btn-block">
                        <i class="fas fa-users-cog mr-1"></i> Manage Users
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <a href="index.php?page=item&action=create" class="btn btn-success btn-block">
                        <i class="fas fa-plus-circle mr-1"></i> Create New Item
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Latest Pending Items -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Latest Pending Items</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>User</th>
                            <th>Date Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!isset($itemModel)) {
                            $itemModel = new Item();
                        }
                        // Get latest pending items
                        $pendingItems = $itemModel->searchItems(['status' => 'pending'], 1, 5);
                        
                        if (empty($pendingItems)): 
                        ?>
                        <tr>
                            <td colspan="6" class="text-center py-3">No pending items found.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($pendingItems as $item): ?>
                            <tr>
                                <td><?= $item['id'] ?></td>
                                <td><?= h($item['title']) ?></td>
                                <td><?= getTypeBadge($item['type']) ?></td>
                                <td><?= h($item['username']) ?></td>
                                <td><?= formatDate($item['created_at']) ?></td>
                                <td>
                                    <div class="admin-actions">
                                        <a href="index.php?page=item&action=view&id=<?= $item['id'] ?>" class="btn btn-sm btn-info" data-toggle="tooltip" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="index.php?page=admin&action=verifyItem&id=<?= $item['id'] ?>" class="btn btn-sm btn-success" data-toggle="tooltip" title="Verify">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger reject-item-btn" data-id="<?= $item['id'] ?>" data-toggle="tooltip" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-right">
            <a href="index.php?page=admin&action=items&status=pending" class="btn btn-sm btn-outline-primary">View All Pending Items</a>
        </div>
    </div>
    
    <!-- Recently Registered Users -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Recently Registered Users</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Date Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!isset($userModel)) {
                            $userModel = new User();
                        }
                        // Get recent users
                        $recentUsers = $userModel->getAllUsers(1, 5);
                        
                        if (empty($recentUsers)): 
                        ?>
                        <tr>
                            <td colspan="6" class="text-center py-3">No users found.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($recentUsers as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= h($user['username']) ?></td>
                                <td><?= h($user['email']) ?></td>
                                <td>
                                    <span class="badge <?= $user['role'] === 'admin' ? 'badge-primary' : 'badge-secondary' ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td><?= formatDate($user['created_at']) ?></td>
                                <td>
                                    <div class="admin-actions">
                                        <a href="index.php?page=admin&action=editUser&id=<?= $user['id'] ?>" class="btn btn-sm btn-warning" data-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="index.php?page=admin&action=deleteUser&id=<?= $user['id'] ?>" class="btn btn-sm btn-danger confirm-action" data-toggle="tooltip" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-right">
            <a href="index.php?page=admin&action=users" class="btn btn-sm btn-outline-primary">View All Users</a>
        </div>
    </div>
</div>

<!-- Reject Item Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="reject-form" action="index.php?page=admin&action=rejectItem" method="post">
                <div class="modal-body">
                    <input type="hidden" id="reject_item_id" name="id">
                    <div class="form-group">
                        <label for="admin_notes">Reason for Rejection</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="4" required></textarea>
                        <small class="form-text text-muted">This reason will be shown to the user who submitted the item.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Item</button>
                </div>
            </form>
        </div>
    </div>
</div>