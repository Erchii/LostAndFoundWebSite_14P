<?php $pageTitle = 'Register'; ?>


<!-- This code displays the "Register" page, 
 where users can create an account by providing essential details such as username,
  email, password, and personal information. 
  It also includes fields for confirming the password and optionally adding a phone number.
   The form validates that the user agrees to the Terms of Service and Privacy Policy, which are displayed in modals.
    After submission, form data is preserved in the session for re-population in case of validation errors. 
    The page also includes a link to the login page for existing users.-->


<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Register</h4>
            </div>
            <div class="card-body">
                <form action="index.php?page=user&action=doRegister" method="post" class="password-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username">Username *</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                    value="<?= isset($_SESSION['form_data']['username']) ? h($_SESSION['form_data']['username']) : '' ?>" required>
                                <small class="form-text text-muted">Choose a unique username.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                    value="<?= isset($_SESSION['form_data']['email']) ? h($_SESSION['form_data']['email']) : '' ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Minimum 6 characters required.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirm">Confirm Password *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password_confirm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                    value="<?= isset($_SESSION['form_data']['first_name']) ? h($_SESSION['form_data']['first_name']) : '' ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                    value="<?= isset($_SESSION['form_data']['last_name']) ? h($_SESSION['form_data']['last_name']) : '' ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number (Optional)</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                            value="<?= isset($_SESSION['form_data']['phone']) ? h($_SESSION['form_data']['phone']) : '' ?>">
                        <small class="form-text text-muted">This will help people contact you about found/lost items.</small>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="terms" name="terms" required>
                            <label class="custom-control-label" for="terms">
                                I agree to the <a href="#" data-toggle="modal" data-target="#termsModal">Terms of Service</a> and <a href="#" data-toggle="modal" data-target="#privacyModal">Privacy Policy</a>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Already have an account? <a href="index.php?page=user&action=login">Login</a></p>
            </div>
        </div>
    </div>
</div>

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms of Service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>1. Acceptance of Terms</h5>
                <p>By accessing and using this Lost and Found Portal, you accept and agree to be bound by the terms and provisions of this agreement.</p>
                
                <h5>2. Description of Service</h5>
                <p>The Lost and Found Portal provides a platform for users to report lost items and found items to help connect owners with their lost belongings.</p>
                
                <h5>3. User Conduct</h5>
                <p>Users agree to:</p>
                <ul>
                    <li>Provide accurate information about lost or found items</li>
                    <li>Not misrepresent items or their ownership</li>
                    <li>Not use the service for any illegal purpose</li>
                    <li>Respect the privacy of other users</li>
                </ul>
                
                <h5>4. Content Submission</h5>
                <p>Users are solely responsible for the content they post on the platform. The portal reserves the right to remove any content deemed inappropriate or misleading.</p>
                
                <h5>5. Liability Limitations</h5>
                <p>The portal is not responsible for any transactions between users and does not guarantee the return of lost items or the accuracy of listings.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Privacy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1" role="dialog" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">Privacy Policy</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>1. Information Collection</h5>
                <p>We collect personal information such as name, email, and phone number to facilitate the lost and found process. This information is only shared with other users when necessary for item recovery.</p>
                
                <h5>2. Information Usage</h5>
                <p>Personal information is used to:</p>
                <ul>
                    <li>Create and manage user accounts</li>
                    <li>Enable communication between users regarding lost/found items</li>
                    <li>Improve the service and user experience</li>
                </ul>
                
                <h5>3. Information Protection</h5>
                <p>We implement security measures to protect personal information from unauthorized access or disclosure.</p>
                
                <h5>4. Information Sharing</h5>
                <p>Personal contact information is only shared between users when a match between a lost item and a found item is established and both parties consent to the sharing.</p>
                
                <h5>5. Data Retention</h5>
                <p>User information is retained as long as the account is active. Users may request deletion of their account and associated data at any time.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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