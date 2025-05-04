</main>
    
    <!-- This file defines the main structure of the webpage, including a responsive footer with site info, 
     navigation links, user account options, and JavaScript integrations for functionality. -->


    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><?= h(APP_NAME) ?></h5>
                    <p>A platform to help people find their lost items and report found ones.</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Home</a></li>
                        <li><a href="index.php?page=item&action=index&type=lost" class="text-white">Lost Items</a></li>
                        <li><a href="index.php?page=item&action=index&type=found" class="text-white">Found Items</a></li>
                        <li><a href="index.php?page=item&action=search" class="text-white">Search</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Account</h5>
                    <ul class="list-unstyled">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="index.php?page=user&action=profile" class="text-white">Profile</a></li>
                            <li><a href="index.php?page=item&action=create" class="text-white">Post Item</a></li>
                            <li><a href="index.php?page=notification&action=index" class="text-white">Notifications</a></li>
                            <li><a href="index.php?page=user&action=logout" class="text-white">Logout</a></li>
                        <?php else: ?>
                            <li><a href="index.php?page=user&action=login" class="text-white">Login</a></li>
                            <li><a href="index.php?page=user&action=register" class="text-white">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <hr class="bg-light">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?= date('Y') ?> <?= h(APP_NAME) ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-right">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-facebook"></i></a></li>
                        <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-twitter"></i></a></li>
                        <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-instagram"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="assets/js/main.js"></script>
    
    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
    <!-- Admin JavaScript -->
    <script src="assets/js/admin.js"></script>
    <?php endif; ?>
    
</body>
</html>