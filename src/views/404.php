<?php $pageTitle = 'Page Not Found'; ?>

<div class="text-center py-5">
    <div class="display-1 text-danger">404</div>
    <h1 class="mt-4">Page Not Found</h1>
    <p class="lead text-muted">The page you are looking for does not exist or has been moved.</p>
    
    <div class="mt-5">
        <a href="index.php" class="btn btn-primary btn-lg">
            <i class="fas fa-home mr-2"></i> Go to Homepage
        </a>
    </div>
    
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5>Looking for something?</h5>
                    <p>You might want to try:</p>
                    <ul class="text-left">
                        <li><a href="index.php?page=item&action=search">Search for lost and found items</a></li>
                        <li><a href="index.php?page=item&action=create">Report a lost or found item</a></li>
                        <li><a href="index.php?page=user&action=login">Login to your account</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>