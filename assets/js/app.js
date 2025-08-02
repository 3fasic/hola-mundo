// Task Manager JavaScript Application

$(document).ready(function() {
    // Initialize the application
    initializeApp();
    
    // Auto-hide alerts after 5 seconds
    $('.alert').each(function() {
        const alert = $(this);
        setTimeout(function() {
            alert.fadeOut('slow');
        }, 5000);
    });
    
    // Add fade-in animation to cards
    $('.card').addClass('fade-in');
    
    // Initialize tooltips
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

function initializeApp() {
    // Password confirmation validation
    setupPasswordConfirmation();
    
    // Form enhancements
    setupFormEnhancements();
    
    // Task management features
    setupTaskManagement();
    
    // Search functionality
    setupSearch();
    
    // Theme handling
    setupTheme();
}

// Password confirmation validation
function setupPasswordConfirmation() {
    const password = $('#password');
    const confirmPassword = $('#confirm_password');
    
    if (password.length && confirmPassword.length) {
        function validatePassword() {
            if (password.val() !== confirmPassword.val()) {
                confirmPassword[0].setCustomValidity('Passwords do not match');
                confirmPassword.addClass('is-invalid');
                if (!$('#password-error').length) {
                    confirmPassword.after('<div id="password-error" class="invalid-feedback">Passwords do not match</div>');
                }
            } else {
                confirmPassword[0].setCustomValidity('');
                confirmPassword.removeClass('is-invalid');
                $('#password-error').remove();
            }
        }
        
        password.on('input', validatePassword);
        confirmPassword.on('input', validatePassword);
    }
}

// Form enhancements
function setupFormEnhancements() {
    // Add floating labels effect
    $('.form-control, .form-select').on('focus blur', function() {
        $(this).toggleClass('focused');
    });
    
    // Character counter for text areas
    $('textarea[name="description"]').each(function() {
        const textarea = $(this);
        const maxLength = 500;
        
        // Add character counter
        textarea.after(`<div class="form-text text-end" id="char-counter">0 / ${maxLength} characters</div>`);
        
        textarea.on('input', function() {
            const currentLength = $(this).val().length;
            const counter = $('#char-counter');
            counter.text(`${currentLength} / ${maxLength} characters`);
            
            if (currentLength > maxLength * 0.9) {
                counter.addClass('text-warning');
            } else {
                counter.removeClass('text-warning');
            }
            
            if (currentLength > maxLength) {
                counter.addClass('text-danger').removeClass('text-warning');
            } else {
                counter.removeClass('text-danger');
            }
        });
    });
    
    // Auto-save draft functionality (simplified)
    const forms = $('form[method="POST"]');
    forms.each(function() {
        const form = $(this);
        const formId = form.attr('action') || window.location.pathname;
        
        // Load saved draft
        loadDraft(form, formId);
        
        // Save draft on input
        form.find('input, textarea, select').on('input change', function() {
            saveDraft(form, formId);
        });
    });
}

// Task management features
function setupTaskManagement() {
    // Quick status update with AJAX
    $('.quick-status-update').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        updateTaskStatus(form);
    });
    
    // Confirm task deletion
    $('button[type="submit"]').filter(function() {
        return $(this).text().includes('Delete') || $(this).find('.fa-trash').length > 0;
    }).on('click', function(e) {
        if (!confirm('Are you sure you want to delete this task? This action cannot be undone.')) {
            e.preventDefault();
        }
    });
    
    // Task priority styling
    $('.task-card').each(function() {
        const card = $(this);
        const priority = card.data('priority');
        const status = card.data('status');
        
        if (priority) {
            card.addClass(`priority-${priority}`);
        }
        
        if (status) {
            card.addClass(`status-${status}`);
        }
    });
    
    // Due date highlighting
    highlightDueDates();
}

// Search functionality
function setupSearch() {
    const searchInput = $('input[name="search"]');
    
    if (searchInput.length) {
        // Live search with debouncing
        let searchTimeout;
        searchInput.on('input', function() {
            clearTimeout(searchTimeout);
            const query = $(this).val();
            
            searchTimeout = setTimeout(function() {
                if (query.length >= 2) {
                    performLiveSearch(query);
                } else {
                    clearSearchResults();
                }
            }, 300);
        });
        
        // Clear search
        const clearBtn = $('<button type="button" class="btn btn-outline-secondary btn-sm ms-2">Clear</button>');
        searchInput.parent().append(clearBtn);
        
        clearBtn.on('click', function() {
            searchInput.val('').trigger('input');
            clearSearchResults();
        });
    }
}

// Theme handling
function setupTheme() {
    // Check for saved theme preference
    const savedTheme = localStorage.getItem('taskManagerTheme');
    if (savedTheme) {
        document.documentElement.setAttribute('data-theme', savedTheme);
    }
    
    // Theme toggle (if implemented in future)
    $('.theme-toggle').on('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('taskManagerTheme', newTheme);
    });
}

// AJAX Functions
function updateTaskStatus(form) {
    const formData = form.serialize();
    const submitBtn = form.find('button[type="submit"]');
    const originalText = submitBtn.html();
    
    // Show loading state
    submitBtn.html('<span class="loading"></span> Updating...');
    submitBtn.prop('disabled', true);
    
    $.ajax({
        url: form.attr('action') || window.location.href,
        method: 'POST',
        data: formData,
        success: function(response) {
            // Refresh the page or update the UI
            location.reload();
        },
        error: function() {
            showNotification('Failed to update task status', 'error');
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);
        }
    });
}

function performLiveSearch(query) {
    // This would be implemented with an AJAX search endpoint
    // For now, we'll use client-side filtering
    const tasks = $('.task-card');
    
    tasks.each(function() {
        const task = $(this);
        const title = task.find('.card-title').text().toLowerCase();
        const description = task.find('.card-text').text().toLowerCase();
        
        if (title.includes(query.toLowerCase()) || description.includes(query.toLowerCase())) {
            task.parent().show().addClass('slide-in');
        } else {
            task.parent().hide().removeClass('slide-in');
        }
    });
}

function clearSearchResults() {
    $('.task-card').parent().show().addClass('slide-in');
}

// Draft management
function saveDraft(form, formId) {
    const formData = {};
    
    form.find('input, textarea, select').each(function() {
        const field = $(this);
        const name = field.attr('name');
        const value = field.val();
        
        if (name && value) {
            formData[name] = value;
        }
    });
    
    localStorage.setItem(`draft_${formId}`, JSON.stringify(formData));
}

function loadDraft(form, formId) {
    const draftData = localStorage.getItem(`draft_${formId}`);
    
    if (draftData) {
        try {
            const data = JSON.parse(draftData);
            
            // Only load if form is empty
            let isEmpty = true;
            form.find('input, textarea, select').each(function() {
                if ($(this).val() && $(this).attr('type') !== 'hidden') {
                    isEmpty = false;
                    return false;
                }
            });
            
            if (isEmpty) {
                Object.keys(data).forEach(name => {
                    const field = form.find(`[name="${name}"]`);
                    if (field.length) {
                        field.val(data[name]);
                    }
                });
                
                showNotification('Draft restored', 'info');
            }
        } catch (e) {
            console.error('Failed to load draft:', e);
        }
    }
}

function clearDraft(formId) {
    localStorage.removeItem(`draft_${formId}`);
}

// Notification system
function showNotification(message, type = 'info') {
    const alertClass = `alert-${type === 'error' ? 'danger' : type}`;
    const iconClass = type === 'error' ? 'exclamation-circle' : 
                      type === 'success' ? 'check-circle' : 'info-circle';
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 1050; min-width: 300px;">
            <i class="fas fa-${iconClass} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    // Auto-hide after 3 seconds
    setTimeout(function() {
        notification.fadeOut('slow', function() {
            $(this).remove();
        });
    }, 3000);
}

// Utility functions
function highlightDueDates() {
    $('.task-card').each(function() {
        const card = $(this);
        const dueDateElement = card.find('[data-due-date]');
        
        if (dueDateElement.length) {
            const dueDate = new Date(dueDateElement.data('due-date'));
            const today = new Date();
            const diffTime = dueDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            // Add visual indicators based on due date
            if (diffDays < 0) {
                card.addClass('border-danger');
                dueDateElement.addClass('text-danger fw-bold');
            } else if (diffDays <= 3) {
                card.addClass('border-warning');
                dueDateElement.addClass('text-warning fw-bold');
            } else if (diffDays <= 7) {
                dueDateElement.addClass('text-info');
            }
        }
    });
}

// Keyboard shortcuts
$(document).on('keydown', function(e) {
    // Ctrl/Cmd + N: New task
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        window.location.href = '?page=add-task';
    }
    
    // Ctrl/Cmd + /: Focus search
    if ((e.ctrlKey || e.metaKey) && e.key === '/') {
        e.preventDefault();
        const searchInput = $('input[name="search"]');
        if (searchInput.length) {
            searchInput.focus();
        }
    }
    
    // Escape: Clear modals/dropdowns
    if (e.key === 'Escape') {
        $('.dropdown-menu').removeClass('show');
        $('.modal').modal('hide');
    }
});

// Progressive Web App features (for future enhancement)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js').then(function(registration) {
            console.log('ServiceWorker registration successful');
        }, function(err) {
            console.log('ServiceWorker registration failed');
        });
    });
}

// Export functions for testing
window.TaskManager = {
    updateTaskStatus,
    showNotification,
    saveDraft,
    loadDraft,
    clearDraft
};