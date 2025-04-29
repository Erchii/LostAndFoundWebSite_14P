<?php $pageTitle = 'Search Items'; ?>

<!-- Hero Section -->
<div class="hero text-center">
    <div class="container">
        <h1>Lost Something? Found Something?</h1>
        <p class="lead">Our platform helps connect people with their lost items.</p>
        <div class="d-flex justify-content-center">
            <a href="index.php?page=item&action=create" class="btn btn-light mr-2">
                <i class="fas fa-plus-circle mr-1"></i> Report Lost Item
            </a>
            <a href="index.php?page=item&action=create" class="btn btn-outline-light">
                <i class="fas fa-hand-holding mr-1"></i> Report Found Item
            </a>
        </div>
    </div>
</div>

<!-- Search Form -->
<div class="row">
    <div class="col-lg-12">
        <div class="search-form">
            <h4 class="mb-3">Search Lost & Found Items</h4>
            <form id="search-form" action="index.php" method="get">
                <input type="hidden" name="page" value="item">
                <input type="hidden" name="action" value="search">
                
                <div class="form-row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="keyword">Keyword</label>
                            <input type="text" class="form-control" id="keyword" name="keyword" 
                                value="<?= isset($_GET['keyword']) ? h($_GET['keyword']) : '' ?>" 
                                placeholder="Search by title, description...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="type">Item Type</label>
                            <select class="form-control" id="type" name="type" style="width: 100%; text-overflow: clip; white-space: normal; padding-right: 30px;">
                                <option value="">All Types</option>
                                <option value="lost" <?= (isset($_GET['type']) && $_GET['type'] === 'lost') ? 'selected' : '' ?>>Lost Items</option>
                                <option value="found" <?= (isset($_GET['type']) && $_GET['type'] === 'found') ? 'selected' : '' ?>>Found Items</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="form-control" id="category_id" name="category_id" style="width: 100%; text-overflow: clip; white-space: normal; padding-right: 30px;">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= (isset($_GET['category_id']) && $_GET['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                        <?= h($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search mr-1"></i> Search
                            </button>
                        </div>
                    </div>
                </div>
                
                <a href="#" id="advanced-search-toggle" class="small">
                    <i class="fas fa-chevron-down"></i> Show Advanced Filters
                </a>
                
                <div id="advanced-search-filters" class="search-filters" style="display: none;">
                    <div class="form-row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                    value="<?= isset($_GET['location']) ? h($_GET['location']) : '' ?>" 
                                    placeholder="City, Area, Building...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_from">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                    value="<?= isset($_GET['date_from']) ? h($_GET['date_from']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_to">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                    value="<?= isset($_GET['date_to']) ? h($_GET['date_to']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" style="width: 100%; text-overflow: clip; white-space: normal; padding-right: 30px;">
                                    <option value="verified" <?= (!isset($_GET['status']) || $_GET['status'] === 'verified') ? 'selected' : '' ?>>Verified Items</option>
                                    <option value="resolved" <?= (isset($_GET['status']) && $_GET['status'] === 'resolved') ? 'selected' : '' ?>>Resolved Items</option>
                                    <option value="all" <?= (isset($_GET['status']) && $_GET['status'] === 'all') ? 'selected' : '' ?>>All Items</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Search Results -->
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>
                Search Results 
                <small class="text-muted">(<?= $totalItems ?> <?= $totalItems === 1 ? 'item' : 'items' ?> found)</small>
            </h4>
            <div class="btn-group">
                <a href="<?= $_SERVER['REQUEST_URI'] ?>&sort=newest" class="btn btn-sm btn-outline-secondary <?= (!isset($_GET['sort']) || $_GET['sort'] === 'newest') ? 'active' : '' ?>">
                    Newest First
                </a>
                <a href="<?= $_SERVER['REQUEST_URI'] ?>&sort=oldest" class="btn btn-sm btn-outline-secondary <?= (isset($_GET['sort']) && $_GET['sort'] === 'oldest') ? 'active' : '' ?>">
                    Oldest First
                </a>
            </div>
        </div>
        
        <?php if (empty($items)): ?>
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h3>No items found</h3>
                <p>Try adjusting your search criteria or browse all items.</p>
                <div>
                    <a href="index.php?page=item&action=index&type=lost" class="btn btn-primary mr-2">
                        Browse Lost Items
                    </a>
                    <a href="index.php?page=item&action=index&type=found" class="btn btn-info">
                        Browse Found Items
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

<!-- Featured Categories -->
<div class="row mt-5">
    <div class="col-lg-12">
        <h4 class="mb-4">Browse by Category</h4>
    </div>
    
    <?php foreach ($categories as $category): ?>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
        <a href="index.php?page=item&action=search&category_id=<?= $category['id'] ?>" class="card text-center h-100 text-decoration-none">
            <div class="card-body">
                <i class="fas 
                    <?php 
                    switch($category['name']) {
                        case 'Electronics': echo 'fa-laptop';
                            break;
                        case 'Personal Items': echo 'fa-wallet';
                            break;
                        case 'Documents': echo 'fa-file-alt';
                            break;
                        case 'Clothing': echo 'fa-tshirt';
                            break;
                        case 'Jewelry': echo 'fa-gem';
                            break;
                        default: echo 'fa-box';
                    }
                    ?> 
                    fa-2x text-primary mb-3"></i>
                <h5 class="card-title"><?= h($category['name']) ?></h5>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<!-- How It Works -->
<div class="row mt-5">
    <div class="col-lg-12">
        <h4 class="mb-4">How It Works</h4>
    </div>
    
    <div class="col-md-3 text-center">
        <div class="p-3">
            <div class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px;">
                <i class="fas fa-user-plus fa-2x"></i>
            </div>
            <h5>1. Create an Account</h5>
            <p>Sign up for a free account to access all features.</p>
        </div>
    </div>
    
    <div class="col-md-3 text-center">
        <div class="p-3">
            <div class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px;">
                <i class="fas fa-plus-circle fa-2x"></i>
            </div>
            <h5>2. Report an Item</h5>
            <p>Report your lost item or an item you found.</p>
        </div>
    </div>
    
    <div class="col-md-3 text-center">
        <div class="p-3">
            <div class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px;">
                <i class="fas fa-search fa-2x"></i>
            </div>
            <h5>3. Search & Match</h5>
            <p>Search for matching items or wait for a match.</p>
        </div>
    </div>
    
    <div class="col-md-3 text-center">
        <div class="p-3">
            <div class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px;">
                <i class="fas fa-handshake fa-2x"></i>
            </div>
            <h5>4. Connect & Recover</h5>
            <p>Connect with the finder/owner to recover the item.</p>
        </div>
    </div>
</div>