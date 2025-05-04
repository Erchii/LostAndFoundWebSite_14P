<?php $pageTitle = 'Welcome to Lost and Found Portal'; ?>


<!--
// This page displays the main Lost and Found portal. It includes:
// 1. A hero section with options to report lost or found items.
// 2. Platform statistics such as success rate, total users, and items resolved.
// 3. A quick search form to filter items by keyword, type, and category.
// 4. A display of recently added items, with links to view more or add an item.
// 5. A "How It Works" section explaining the process of reporting and recovering lost/found items.
// 6. Success stories from users sharing their positive experiences with the platform.
-->


<!-- Hero Section с большим баннером -->
<div class="hero text-center py-5">
    <div class="container">
        <h1>Lost Something? Found Something?</h1>
        <p class="lead mt-3">Our platform helps connect people with their lost items.</p>
        <div class="d-flex justify-content-center mt-4">
            <a href="index.php?page=item&action=create&type=lost" class="btn btn-light btn-lg mr-3">
                <i class="fas fa-search mr-2"></i> Report Lost Item
            </a>
            <a href="index.php?page=item&action=create&type=found" class="btn btn-outline-light btn-lg">
                <i class="fas fa-hand-holding mr-2"></i> Report Found Item
            </a>
        </div>
    </div>
</div>

<!-- Статистика или преимущества -->
<div class="container mt-5">
    <div class="row text-center">
        <div class="col-md-4 mb-4">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-award"></i>
                </div>
                <div class="stats-number"><?= $stats['success_rate'] ?? '90%' ?></div>
                <div class="stats-title">Success Rate</div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-number"><?= $stats['total_users'] ?? '500+' ?></div>
                <div class="stats-title">Happy Users</div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number"><?= $stats['items_resolved'] ?? '1,000+' ?></div>
                <div class="stats-title">Items Resolved</div>
            </div>
        </div>
    </div>
</div>

<!-- Поисковая форма (сокращенная версия) -->
<div class="container mt-5">
    <div class="card search-form">
        <div class="card-body">
            <h4 class="mb-3">Quick Search</h4>
            <form action="index.php" method="get">
                <input type="hidden" name="page" value="item">
                <input type="hidden" name="action" value="search">
                
                <div class="form-row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <input type="text" class="form-control" name="keyword" 
                                placeholder="Search by keyword...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control" name="type">
                                <option value="">All Types</option>
                                <option value="lost">Lost Items</option>
                                <option value="found">Found Items</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control" name="category_id">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= h($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <div class="text-center mt-2">
                    <a href="index.php?page=item&action=search" class="small">
                        <i class="fas fa-sliders-h mr-1"></i> Advanced Search
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Последние добавленные предметы -->
<div class="container mt-5">
    <h4>Recently Added Items</h4>
    
    <div class="row items-grid">
        <?php foreach ($recentItems as $item): ?>
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
                        <h5 class="card-title mt-2"><?= h($item['title']) ?></h5>
                        <p class="card-text small">
                            <?= h(truncateText($item['description'], 50)) ?>
                        </p>
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
    
    <?php if (empty($recentItems)): ?>
        <div class="empty-state py-4">
            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
            <h5>No items found</h5>
            <p>Be the first to add an item!</p>
            <a href="index.php?page=item&action=create" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-2"></i> Add Item
            </a>
        </div>
    <?php else: ?>
        <div class="text-center mt-3">
            <a href="index.php?page=item&action=search" class="btn btn-outline-primary">
                View All Items
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Как это работает секция -->
<div class="container my-5">
    <h4 class="text-center mb-4">How It Works</h4>
    
    <div class="row">
        <div class="col-md-3 text-center">
            <div class="p-3">
                <div class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-user-plus fa-2x"></i>
                </div>
                <h5>1. Create an Account</h5>
                <p class="text-muted">Sign up for a free account to access all features.</p>
            </div>
        </div>
        
        <div class="col-md-3 text-center">
            <div class="p-3">
                <div class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-plus-circle fa-2x"></i>
                </div>
                <h5>2. Report an Item</h5>
                <p class="text-muted">Report your lost item or an item you found.</p>
            </div>
        </div>
        
        <div class="col-md-3 text-center">
            <div class="p-3">
                <div class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-search fa-2x"></i>
                </div>
                <h5>3. Search & Match</h5>
                <p class="text-muted">Search for matching items or wait for a match.</p>
            </div>
        </div>
        
        <div class="col-md-3 text-center">
            <div class="p-3">
                <div class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-handshake fa-2x"></i>
                </div>
                <h5>4. Connect & Recover</h5>
                <p class="text-muted">Connect with the finder/owner to recover the item.</p>
            </div>
        </div>
    </div>
</div>

<!-- Testimonials или отзывы (опционально) -->
<div class="container my-5">
    <h4 class="text-center mb-4">Success Stories</h4>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center mr-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">John Doe</h5>
                            <small class="text-muted">Lost a wallet</small>
                        </div>
                    </div>
                    <p class="card-text">
                        "I lost my wallet at the central park and thought it was gone forever. 
                        Thanks to this platform, someone found it and contacted me within 24 hours!"
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center mr-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Jane Smith</h5>
                            <small class="text-muted">Found a phone</small>
                        </div>
                    </div>
                    <p class="card-text">
                        "I found a phone on the bus and wanted to return it to its owner. 
                        I posted it here and was able to find the owner the same day!"
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center mr-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Mike Johnson</h5>
                            <small class="text-muted">Lost a laptop</small>
                        </div>
                    </div>
                    <p class="card-text">
                        "I accidentally left my laptop at a coffee shop. 
                        Posted it here and someone contacted me who had found it. Forever grateful!"
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>