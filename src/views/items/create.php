<?php $pageTitle = 'Post an Item'; ?>

<!-- This script generates a form for users to post lost or found items. 
 It dynamically validates input fields, supports image uploads, 
 and stores session data for better user experience. -->



<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Post a Lost or Found Item</h4>
            </div>
            <div class="card-body">
                <form action="index.php?page=item&action=store" method="post" enctype="multipart/form-data" class="create-form">
                    <div class="form-group">
                        <label for="type">Item Type *</label>
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <label class="btn btn-outline-danger <?= (!isset($_SESSION['form_data']['type']) || $_SESSION['form_data']['type'] === 'lost') ? 'active' : '' ?>">
                                <input type="radio" name="type" id="type_lost" value="lost" <?= (!isset($_SESSION['form_data']['type']) || $_SESSION['form_data']['type'] === 'lost') ? 'checked' : '' ?> required> 
                                <i class="fas fa-search mr-1"></i> I Lost an Item
                            </label>
                            <label class="btn btn-outline-info <?= (isset($_SESSION['form_data']['type']) && $_SESSION['form_data']['type'] === 'found') ? 'active' : '' ?>">
                                <input type="radio" name="type" id="type_found" value="found" <?= (isset($_SESSION['form_data']['type']) && $_SESSION['form_data']['type'] === 'found') ? 'checked' : '' ?> required> 
                                <i class="fas fa-hand-holding mr-1"></i> I Found an Item
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="title">Item Title *</label>
                        <input type="text" class="form-control" id="title" name="title" 
                            value="<?= isset($_SESSION['form_data']['title']) ? h($_SESSION['form_data']['title']) : '' ?>" 
                            placeholder="e.g., Blue Wallet, Gold Ring, Black iPhone..." required>
                        <small class="form-text text-muted">Keep it short and descriptive.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Category *</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= (isset($_SESSION['form_data']['category_id']) && $_SESSION['form_data']['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                    <?= h($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea class="form-control has-character-counter" id="description" name="description" 
                            rows="5" maxlength="1000" required><?= isset($_SESSION['form_data']['description']) ? h($_SESSION['form_data']['description']) : '' ?></textarea>
                        <small class="form-text text-muted">Provide a detailed description including color, brand, distinguishing features, etc.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location *</label>
                        <input type="text" class="form-control" id="location" name="location" 
                            value="<?= isset($_SESSION['form_data']['location']) ? h($_SESSION['form_data']['location']) : '' ?>" 
                            placeholder="e.g., Central Park, Main Street, Building A..." required>
                        <small class="form-text text-muted">Where the item was lost or found.</small>
                    </div>
                    
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_lost_found">Date *</label>
                                <input type="date" class="form-control" id="date_lost_found" name="date_lost_found" 
                                    value="<?= isset($_SESSION['form_data']['date_lost_found']) ? h($_SESSION['form_data']['date_lost_found']) : '' ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="time_lost_found">Time (Optional)</label>
                                <input type="time" class="form-control" id="time_lost_found" name="time_lost_found" 
                                    value="<?= isset($_SESSION['form_data']['time_lost_found']) ? h($_SESSION['form_data']['time_lost_found']) : '' ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Image (Optional)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                            <label class="custom-file-label" for="image">Choose file</label>
                        </div>
                        <small class="form-text text-muted">Upload an image of the item. Max size: 5MB. Allowed types: JPG, PNG, GIF.</small>
                    </div>
                    
                    <div class="form-group">
                        <img id="image-preview" src="#" alt="Image Preview" class="img-fluid mt-2" style="display: none; max-height: 200px;">
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i> Your post will be reviewed by an administrator before being published.
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane mr-1"></i> Submit Item
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary btn-lg ml-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Clear form data after displaying
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>