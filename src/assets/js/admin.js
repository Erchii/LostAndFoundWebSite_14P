/**
 * Admin JavaScript for Lost and Found Portal
 */

document.addEventListener('DOMContentLoaded', function() {
    // Admin dashboard item filter functionality
    var statusFilter = document.getElementById('status-filter');
    var typeFilter = document.getElementById('type-filter');
    var keywordSearch = document.getElementById('keyword-search');
    var filterForm = document.getElementById('admin-filter-form');
    
    if (statusFilter && typeFilter && filterForm) {
        // Submit form when filters change
        statusFilter.addEventListener('change', function() {
            filterForm.submit();
        });
        
        typeFilter.addEventListener('change', function() {
            filterForm.submit();
        });
        
        // Handle search with debounce
        var searchTimeout;
        if (keywordSearch) {
            keywordSearch.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    filterForm.submit();
                }, 500);
            });
        }
    }
    
    // Bulk actions
    var bulkActionSelect = document.getElementById('bulk-action');
    var bulkActionForm = document.getElementById('bulk-action-form');
    var bulkCheckboxes = document.querySelectorAll('.bulk-checkbox');
    var selectAllCheckbox = document.getElementById('select-all');
    
    if (bulkActionSelect && bulkActionForm && bulkCheckboxes.length > 0 && selectAllCheckbox) {
        // Toggle all checkboxes
        selectAllCheckbox.addEventListener('change', function() {
            bulkCheckboxes.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
        
        // Update "select all" state based on individual checkboxes
        bulkCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                var allChecked = true;
                bulkCheckboxes.forEach(function(cb) {
                    if (!cb.checked) {
                        allChecked = false;
                    }
                });
                selectAllCheckbox.checked = allChecked;
            });
        });
        
        // Submit bulk action form
        var bulkActionButton = document.getElementById('apply-bulk-action');
        if (bulkActionButton) {
            bulkActionButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                var selectedAction = bulkActionSelect.value;
                if (!selectedAction) {
                    alert('Please select an action.');
                    return;
                }
                
                var checked = false;
                bulkCheckboxes.forEach(function(checkbox) {
                    if (checkbox.checked) {
                        checked = true;
                    }
                });
                
                if (!checked) {
                    alert('Please select at least one item.');
                    return;
                }
                
                var confirmMessage = 'Are you sure you want to apply this action to all selected items?';
                if (selectedAction === 'delete') {
                    confirmMessage = 'Are you sure you want to delete all selected items? This action cannot be undone.';
                }
                
                if (confirm(confirmMessage)) {
                    bulkActionForm.submit();
                }
            });
        }
    }
    
    // Admin item rejection modal
    var rejectButtons = document.querySelectorAll('.reject-item-btn');
    var rejectForm = document.getElementById('reject-form');
    var rejectItemId = document.getElementById('reject_item_id');
    var rejectModal = document.getElementById('rejectModal');
    
    if (rejectButtons.length > 0 && rejectForm && rejectItemId && rejectModal) {
        rejectButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var itemId = this.getAttribute('data-id');
                rejectItemId.value = itemId;
                
                // Clear previous admin notes
                document.getElementById('admin_notes').value = '';
                
                // Show modal (using Bootstrap JS)
                var modal = new bootstrap.Modal(rejectModal);
                modal.show();
            });
        });
        
        // Validate admin notes before submitting
        rejectForm.addEventListener('submit', function(e) {
            var adminNotes = document.getElementById('admin_notes').value.trim();
            if (!adminNotes) {
                e.preventDefault();
                alert('Please provide a reason for rejecting this item.');
            }
        });
    }
    
    // Admin user edit functionality
    var roleSelect = document.getElementById('role');
    var adminWarning = document.getElementById('admin-role-warning');
    
    if (roleSelect && adminWarning) {
        roleSelect.addEventListener('change', function() {
            if (this.value === 'admin') {
                adminWarning.style.display = 'block';
            } else {
                adminWarning.style.display = 'none';
            }
        });
    }
    
    // Admin dashboard quick stats
    var refreshStatsButton = document.getElementById('refresh-stats');
    
    if (refreshStatsButton) {
        refreshStatsButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Show loading indicator
            var statsContainer = document.getElementById('admin-stats');
            statsContainer.innerHTML = '<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-3">Refreshing stats...</p></div>';
            
            // Fetch updated stats via AJAX
            fetch('index.php?page=admin&action=getStats', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                // Update stats in UI
                statsContainer.innerHTML = '';
                
                // Re-render stats cards with new data
                for (var key in data) {
                    var statCard = document.createElement('div');
                    statCard.className = 'col-md-3 col-sm-6';
                    
                    var iconClass = 'fas fa-question';
                    var bgClass = 'bg-primary';
                    
                    // Assign icon and color based on stat type
                    switch (key) {
                        case 'total_items':
                            iconClass = 'fas fa-boxes';
                            break;
                        case 'lost_items':
                            iconClass = 'fas fa-search';
                            bgClass = 'bg-danger';
                            break;
                        case 'found_items':
                            iconClass = 'fas fa-hand-holding';
                            bgClass = 'bg-info';
                            break;
                        case 'pending_items':
                            iconClass = 'fas fa-clock';
                            bgClass = 'bg-warning';
                            break;
                        case 'verified_items':
                            iconClass = 'fas fa-check-circle';
                            bgClass = 'bg-success';
                            break;
                        case 'resolved_items':
                            iconClass = 'fas fa-check-double';
                            bgClass = 'bg-primary';
                            break;
                        case 'total_users':
                            iconClass = 'fas fa-users';
                            break;
                    }
                    
                    statCard.innerHTML = `
                        <div class="stat-card">
                            <div class="stat-icon ${bgClass}">
                                <i class="${iconClass}"></i>
                            </div>
                            <div class="stat-info">
                                <h3>${data[key]}</h3>
                                <p>${key.replace('_', ' ').replace(/(?:^|\s)\S/g, function(a) { return a.toUpperCase(); })}</p>
                            </div>
                        </div>
                    `;
                    
                    statsContainer.appendChild(statCard);
                }
            })
            .catch(function(error) {
                statsContainer.innerHTML = '<div class="alert alert-danger">Failed to refresh stats. Please try again.</div>';
                console.error('Error:', error);
            });
        });
    }
    
    // Admin contact approval/rejection
    var approveContactButtons = document.querySelectorAll('.approve-contact');
    var rejectContactButtons = document.querySelectorAll('.reject-contact');
    
    if (approveContactButtons.length > 0 || rejectContactButtons.length > 0) {
        // Approve contact
        approveContactButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                var contactId = this.getAttribute('data-id');
                if (confirm('Are you sure you want to approve this contact request?')) {
                    window.location.href = `index.php?page=admin&action=approveContact&id=${contactId}`;
                }
            });
        });
        
        // Reject contact
        rejectContactButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                var contactId = this.getAttribute('data-id');
                if (confirm('Are you sure you want to reject this contact request?')) {
                    window.location.href = `index.php?page=admin&action=rejectContact&id=${contactId}`;
                }
            });
        });
    }
    
    // Admin data export
    var exportButton = document.getElementById('export-data');
    
    if (exportButton) {
        exportButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            var exportType = document.getElementById('export-type').value;
            var exportFormat = document.getElementById('export-format').value;
            
            if (!exportType || !exportFormat) {
                alert('Please select what to export and the format.');
                return;
            }
            
            window.location.href = `index.php?page=admin&action=export&type=${exportType}&format=${exportFormat}`;
        });
    }
});