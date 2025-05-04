<?php 
$pageTitle = isset($_GET['type']) && $_GET['type'] === 'found' ? 'Found Items' : 'Lost Items'; 
$itemType = isset($_GET['type']) ? $_GET['type'] : 'lost';
?>

<!-- This script displays a list of either "Lost" or "Found" items, with filtering options based on category, status, and type. 
 Users can report new items, and if no items match the filter, an "empty state" message is shown. 
 The page also supports pagination to navigate through multiple pages of results. 
 Each item includes basic details, with an option to view more details about the item. -->



<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><?= $pageTitle ?></h2>
            <a href="index.php?page=item&action=create" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Report an Item
            </a>
        </div>
        
        <!-- Filter options -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="index.php" method="get" class="row">
                    <input type="hidden" name="page" value="item">
                    <input type="hidden" name="action" value="index">
                    <input type="hidden" name="type" value="<?= $itemType ?>">
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="form-control" id="category_id" name="category_id">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= (isset($_GET['category_id']) && $_GET['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                        <?= h($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="verified" <?= (!isset($_GET['status']) || $_GET['status'] === 'verified') ? 'selected' : '' ?>>Verified Items</option>
                                <option value="resolved" <?= (isset($_GET['status']) && $_GET['status'] === 'resolved') ? 'selected' : '' ?>>Resolved Items</option>
                                <option value="all" <?= (isset($_GET['status']) && $_GET['status'] === 'all') ? 'selected' : '' ?>>All Items</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-filter mr-1"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if (empty($items)): ?>
            <div class="empty-state">
                <i class="fas fa-<?= $itemType === 'lost' ? 'search' : 'hand-holding' ?>"></i>
                <h3>No <?= strtolower($pageTitle) ?> found</h3>
                <p>There are no items to display with the current filters.</p>
                <div>
                    <a href="index.php?page=item&action=create" class="btn btn-primary">
                        <i class="fas fa-plus-circle mr-1"></i> Report an Item
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row items-grid">
                <?php foreach ($items as $item): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <?php if (!empty($item['image_path'])): ?>
                                <img src="uploads/<?= h($item['image_path']) ?>" class="card-img-top" alt="<?= h($item['title']) ?>">
                            <?php else: ?>
                                <div class="card-img-top d-flex align-items-center justify-content-center bg-light">
                                    <i class="fas <?= $item['type'] === 'lost' ? 'fa-search' : 'fa-hand-holding' ?> fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <span class="type-badge <?= $item['type'] === 'lost' ? 'type-lost' : 'type-found' ?>">
                                    <?= ucfirst($item['type']) ?>
                                </span>
                                <span class="category-badge">
                                    <?= h($item['category_name']) ?>
                                </span>
                                <h5 class="card-title mt-2"><?= h($item['title']) ?></h5>
                                <p class="card-text">
                                    <?= h(truncateText($item['description'], 100)) ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i> <?= h($item['location']) ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="far fa-calendar-alt"></i> <?= formatDate($item['date_lost_found']) ?>
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <a href="index.php?page=item&action=view&id=<?= $item['id'] ?>" class="btn btn-sm btn-primary btn-block">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="d-flex justify-content-center">
                    <?= generatePagination($page, $totalPages, preg_replace('/&p=\d+/', '', $_SERVER['REQUEST_URI'])) ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>