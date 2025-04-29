/**
 * Main JavaScript for Lost and Found Portal
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize Bootstrap popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Image preview for uploads
    var imageUpload = document.getElementById('image');
    var imagePreview = document.getElementById('image-preview');
    
    if (imageUpload && imagePreview) {
        imageUpload.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.style.display = 'block';
                    imagePreview.src = e.target.result;
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Toggle advanced search filters
    var advancedSearchToggle = document.getElementById('advanced-search-toggle');
    var advancedSearchFilters = document.getElementById('advanced-search-filters');
    
    if (advancedSearchToggle && advancedSearchFilters) {
        advancedSearchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (advancedSearchFilters.style.display === 'none' || advancedSearchFilters.style.display === '') {
                advancedSearchFilters.style.display = 'block';
                this.innerHTML = '<i class="fas fa-chevron-up"></i> Hide Advanced Filters';
            } else {
                advancedSearchFilters.style.display = 'none';
                this.innerHTML = '<i class="fas fa-chevron-down"></i> Show Advanced Filters';
            }
        });
    }

    // Date picker initialization
    var dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(function(input) {
        // Default to today's date for new items
        if (input.value === '' && input.id === 'date_lost_found' && document.querySelector('form.create-form')) {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();
            
            input.value = yyyy + '-' + mm + '-' + dd;
        }
    });

    // Contact form validation
    var contactForm = document.getElementById('contact-form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            var messageInput = document.getElementById('message');
            
            if (!messageInput.value.trim()) {
                e.preventDefault();
                alert('Please enter a message.');
            }
        });
    }

    // Password confirmation validation
    var passwordForm = document.querySelector('form.password-form');
    
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            var password = document.getElementById('password');
            var passwordConfirm = document.getElementById('password_confirm');
            
            if (password.value !== passwordConfirm.value) {
                e.preventDefault();
                alert('Passwords do not match.');
            }
        });
    }

    // Confirmation dialogs
    var confirmLinks = document.querySelectorAll('.confirm-action');
    
    confirmLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to perform this action?')) {
                e.preventDefault();
            }
        });
    });

    // Specific confirmation messages
    var deleteLinks = document.querySelectorAll('.confirm-delete');
    
    deleteLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Custom file input label update
    var fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            var fileName = this.files[0] ? this.files[0].name : 'Choose file';
            var label = this.nextElementSibling;
            
            if (label && label.classList.contains('custom-file-label')) {
                label.textContent = fileName;
            }
        });
    });

    // Toggle password visibility
    var togglePasswordButtons = document.querySelectorAll('.toggle-password');
    
    togglePasswordButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var passwordInput = document.getElementById(this.getAttribute('data-target'));
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });

    // Category filter in search
    var categorySelect = document.getElementById('category_id');
    var typeSelect = document.getElementById('type');
    
    if (categorySelect && typeSelect) {
        categorySelect.addEventListener('change', function() {
            document.getElementById('search-form').submit();
        });
        
        typeSelect.addEventListener('change', function() {
            document.getElementById('search-form').submit();
        });
    }

    // Input character counters
    var textareaWithCounter = document.querySelectorAll('.has-character-counter');
    
    textareaWithCounter.forEach(function(textarea) {
        var counter = document.createElement('small');
        counter.className = 'character-counter text-muted ml-2';
        counter.textContent = textarea.value.length + ' / ' + textarea.getAttribute('maxlength') + ' characters';
        
        textarea.parentNode.appendChild(counter);
        
        textarea.addEventListener('input', function() {
            counter.textContent = this.value.length + ' / ' + this.getAttribute('maxlength') + ' characters';
        });
    });

    // Responsive tables
    var tables = document.querySelectorAll('table.table');
    
    tables.forEach(function(table) {
        var wrapper = document.createElement('div');
        wrapper.className = 'table-responsive';
        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);
    });
});