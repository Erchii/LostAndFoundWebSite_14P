<?php $pageTitle = h($item['title']); ?>

<!-- This script displays detailed information about a specific lost or found item. 
 It includes options for admins and users to interact with the item, such as contacting the poster, editing, or deleting.
  Additionally, it shows similar items based on category and type, and includes functionality for admin approval,
 rejection, and resolving the item status. -->



<div class="row">
    <div class="col-lg-8">
        <div class="card item-details">
            <?php if ($item['status'] === 'resolved'): ?>
                <div class="status-badge bg-primary text-white">
                    <i class="fas fa-check-circle mr-1"></i> Resolved
                </div>
            <?php endif; ?>
            
            <?php if (!empty($item['image_path'])): ?>
                <img src="uploads/<?= h($item['image_path']) ?>" class="item-image" alt="<?= h($item['title']) ?>">
            <?php else: ?>
                <div class="item-image d-flex align-items-center justify-content-center bg-light">
                    <i class="fas <?= $item['type'] === 'lost' ? 'fa-search' : 'fa-hand-holding' ?> fa-5x text-muted"></i>
                </div>
            <?php endif; ?>
            
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="type-badge <?= $item['type'] === 'lost' ? 'type-lost' : 'type-found' ?>">
                            <?= ucfirst($item['type']) ?>
                        </span>
                        <span class="category-badge">
                            <?= h($item['category_name']) ?>
                        </span>
                    </div>
                    <div>
                        <?= getStatusBadge($item['status']) ?>
                    </div>
                </div>
                
                <h2 class="item-title"><?= h($item['title']) ?></h2>
                
                <div class="item-meta row">
                    <div class="col-md-6">
                        <p>
                            <i class="fas fa-map-marker-alt text-danger mr-2"></i> 
                            <strong>Location:</strong> <?= h($item['location']) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <i class="far fa-calendar-alt text-primary mr-2"></i> 
                            <strong>Date:</strong> <?= formatDate($item['date_lost_found']) ?>
                            <?= !empty($item['time_lost_found']) ? ' at ' . formatTime($item['time_lost_found']) : '' ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <i class="far fa-user text-info mr-2"></i> 
                            <strong>Posted by:</strong> <?= h($item['username']) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <i class="far fa-clock text-warning mr-2"></i> 
                            <strong>Posted:</strong> <?= timeElapsed($item['created_at']) ?>
                        </p>
                    </div>
                </div>
                
                <div class="item-description">
                    <h5>Description</h5>
                    <p><?= nl2br(h($item['description'])) ?></p>
                </div>
                
                <?php if ($item['status'] === 'rejected' && ($_SESSION['user_id'] == $item['user_id'] || $_SESSION['user_role'] === 'admin')): ?>
                    <div class="alert alert-danger mt-3">
                        <h5><i class="fas fa-exclamation-circle mr-2"></i> This item has been rejected</h5>
                        <p><strong>Reason:</strong> <?= nl2br(h($item['admin_notes'])) ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if ($item['status'] === 'pending' && ($_SESSION['user_id'] == $item['user_id'] || $_SESSION['user_role'] === 'admin')): ?>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-clock mr-2"></i> This item is pending verification and is not publicly visible yet.
                    </div>
                <?php endif; ?>
                
                <div class="item-actions mt-4">
                    <?php if ($_SESSION['user_id'] == $item['user_id'] || $_SESSION['user_role'] === 'admin'): ?>
                        <a href="index.php?page=item&action=edit&id=<?= $item['id'] ?>" class="btn btn-warning">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        <a href="index.php?page=item&action=delete&id=<?= $item['id'] ?>" class="btn btn-danger confirm-delete">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['user_role'] === 'admin' && $item['status'] === 'pending'): ?>
                        <a href="index.php?page=admin&action=verifyItem&id=<?= $item['id'] ?>" class="btn btn-success">
                            <i class="fas fa-check mr-1"></i> Verify
                        </a>
                        <button type="button" class="btn btn-danger reject-item-btn" data-id="<?= $item['id'] ?>">
                            <i class="fas fa-times mr-1"></i> Reject
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['user_role'] === 'admin' && $item['status'] === 'verified'): ?>
                        <a href="index.php?page=admin&action=resolveItem&id=<?= $item['id'] ?>" class="btn btn-primary confirm-action">
                            <i class="fas fa-check-double mr-1"></i> Mark as Resolved
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $item['user_id'] && in_array($item['status'], ['verified', 'resolved'])): ?>
            <div class="card contact-form mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Contact About This Item</h5>
                </div>
                <div class="card-body">
                    <form action="index.php?page=item&action=contact" method="post" id="contact-form">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                        
                        <div class="form-group">
                            <label for="message">Your Message *</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                            <small class="form-text text-muted">Explain why you believe this item is yours or provide additional details.</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle mr-1"></i> 
                                Your contact request will be sent to the item poster for approval before they can see your contact information.
                            </small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane mr-1"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($_SESSION['user_id'] == $item['user_id'] && !empty($contacts)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Contact Requests</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($contacts)): ?>
                        <div class="list-group-item text-center py-4">
                            <i class="far fa-envelope fa-2x text-muted mb-2"></i>
                            <p class="mb-0">No contact requests yet</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($contacts as $contact): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong><?= h($contact['username']) ?></strong>
                                    <small><?= timeElapsed($contact['created_at']) ?></small>
                                </div>
                                <p class="mt-2 mb-3"><?= nl2br(h($contact['message'])) ?></p>
                                
                                <?php if ($contact['status'] === 'pending'): ?>
                                    <div class="d-flex">
                                        <a href="index.php?page=item&action=approveContact&id=<?= $contact['id'] ?>" class="btn btn-sm btn-success mr-2">
                                            <i class="fas fa-check mr-1"></i> Approve
                                        </a>
                                        <a href="index.php?page=item&action=rejectContact&id=<?= $contact['id'] ?>" class="btn btn-sm btn-danger">
                                            <i class="fas fa-times mr-1"></i> Reject
                                        </a>
                                    </div>
                                <?php elseif ($contact['status'] === 'approved'): ?>
                                    <div class="alert alert-success mb-0">
                                        <small>
                                            <i class="fas fa-check-circle mr-1"></i> 
                                            <strong>Approved</strong> - Contact Information:
                                        </small>
                                        <div class="mt-2">
                                            <p class="mb-1"><strong>Email:</strong> <?= h($contact['email']) ?></p>
                                            <?php if (!empty($contact['phone'])): ?>
                                                <p class="mb-0"><strong>Phone:</strong> <?= h($contact['phone']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php elseif ($contact['status'] === 'rejected'): ?>
                                    <div class="alert alert-danger mb-0">
                                        <small>
                                            <i class="fas fa-times-circle mr-1"></i> 
                                            <strong>Rejected</strong> - You have declined this contact request
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Similar Items</h5>
            </div>
            <div class="list-group list-group-flush">
                <?php 
                // Get similar items based on category
                $similarItems = $itemModel->searchItems([
                    'category_id' => $item['category_id'],
                    'type' => $item['type'] === 'lost' ? 'found' : 'lost', // Show opposite type
                    'status' => 'verified'
                ], 1, 5);
                
                if (empty($similarItems)):
                ?>
                    <div class="list-group-item text-center py-4">
                        <i class="fas fa-search fa-2x text-muted mb-2"></i>
                        <p class="mb-0">No similar items found</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($similarItems as $similarItem): ?>
                        <?php if ($similarItem['id'] != $item['id']): ?>
                            <a href="index.php?page=item&action=view&id=<?= $similarItem['id'] ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1"><?= h($similarItem['title']) ?></h6>
                                    <span class="type-badge <?= $similarItem['type'] === 'lost' ? 'type-lost' : 'type-found' ?>">
                                        <?= ucfirst($similarItem['type']) ?>
                                    </span>
                                </div>
                                <small class="text-muted">
                                    <i class="far fa-calendar-alt mr-1"></i> <?= formatDate($similarItem['date_lost_found']) ?>
                                </small>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Reject Item Modal (for admin) -->
<?php if ($_SESSION['user_role'] === 'admin'): ?>
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
<?php endif; ?>