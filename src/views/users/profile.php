<?php $pageTitle = 'My Profile'; ?>


<!-- This code displays the "My Profile" page, which includes a user's personal information, profile stats,
  and a list of their posted items. It also provides quick actions for the user such as posting a new item, 
  viewing notifications, and logging out. If the user is an admin,
   an additional link to the admin dashboard is displayed. -->


<div class="container">
    <!-- Profile Header -->
    <div class="profile-header mb-4">
        <div class="row">
            <div class="col-md-8">
                <div class="profile-info">
                    <h2><?= h($user['username']) ?></h2>
                    <div class="d-flex profile-stats">
                        <div class="mr-4">
                            <i class="far fa-user mr-1"></i> Member since <?= formatDate($user['created_at']) ?>
                        </div>
                        <div>
                            <i class="fas fa-box-open mr-1"></i> <?= count($items) ?> items posted
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-right">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editProfileModal">
                    <i class="fas fa-edit mr-1"></i> Edit Profile
                </button>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- User Information -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted d-block">Full Name</label>
                        <div class="font-weight-bold"><?= h($user['first_name'] . ' ' . $user['last_name']) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted d-block">Email Address</label>
                        <div class="font-weight-bold"><?= h($user['email']) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted d-block">Phone Number</label>
                        <div class="font-weight-bold"><?= !empty($user['phone']) ? h($user['phone']) : '<span class="text-muted">Not provided</span>' ?></div>
                    </div>
                    <div>
                        <label class="text-muted d-block">Account Type</label>
                        <div>
                            <span class="badge <?= $user['role'] === 'admin' ? 'badge-primary' : 'badge-secondary' ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="index.php?page=item&action=create" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus-circle text-success mr-2"></i> Post a New Item
                    </a>
                    <a href="index.php?page=notification&action=index" class="list-group-item list-group-item-action">
                        <i class="fas fa-bell text-warning mr-2"></i> View Notifications
                        <?php if (isset($unreadCount) && $unreadCount > 0): ?>
                            <span class="badge badge-danger float-right"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </a>
                    <?php if ($user['role'] === 'admin'): ?>
                    <a href="index.php?page=admin" class="list-group-item list-group-item-action">
                        <i class="fas fa-tachometer-alt text-primary mr-2"></i> Admin Dashboard
                    </a>
                    <?php endif; ?>
                    <a href="index.php?page=user&action=logout" class="list-group-item list-group-item-action text-danger">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
        
        <!-- User Items -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">My Items</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($items)): ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5>No items posted yet</h5>
                            <p class="text-muted">You haven't posted any lost or found items yet.</p>
                            <a href="index.php?page=item&action=create" class="btn btn-primary mt-2">
                                <i class="fas fa-plus-circle mr-1"></i> Post Your First Item
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Date Posted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td>
                                                <a href="index.php?page=item&action=view&id=<?= $item['id'] ?>">
                                                    <?= h($item['title']) ?>
                                                </a>
                                            </td>
                                            <td><?= getTypeBadge($item['type']) ?></td>
                                            <td><?= getStatusBadge($item['status']) ?></td>
                                            <td><?= formatDate($item['created_at']) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="index.php?page=item&action=view&id=<?= $item['id'] ?>" class="btn btn-info" data-toggle="tooltip" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="index.php?page=item&action=edit&id=<?= $item['id'] ?>" class="btn btn-warning" data-toggle="tooltip" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="index.php?page=item&action=delete&id=<?= $item['id'] ?>" class="btn btn-danger confirm-delete" data-toggle="tooltip" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="index.php?page=user&action=updateProfile" method="post" class="password-form">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= h($user['email']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?= h($user['phone']) ?>">
                                <small class="form-text text-muted">This helps people contact you about found/lost items.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= h($user['first_name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= h($user['last_name']) ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h5>Change Password</h5>
                    <p class="text-muted small">Leave blank if you don't want to change your password.</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Minimum 6 characters.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirm">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password_confirm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>