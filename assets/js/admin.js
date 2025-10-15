/**
 * Google Reviews Plugin - Admin JavaScript
 */

(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        initAdmin();
    });
    
    /**
     * Initialize admin functionality
     */
    function initAdmin() {
        initTabs();
        initButtons();
        initForms();
        initModals();
    }
    
    /**
     * Initialize tabs
     */
    function initTabs() {
        $('.grp-tab').on('click', function(e) {
            e.preventDefault();
            
            var $tab = $(this);
            var target = $tab.attr('href');
            
            // Update active tab
            $('.grp-tab').removeClass('active');
            $tab.addClass('active');
            
            // Show target content
            $('.grp-tab-content').hide();
            $(target).show();
        });
    }
    
    /**
     * Initialize buttons
     */
    function initButtons() {
        // Test connection button
        $('#grp-test-connection').on('click', function() {
            var $button = $(this);
            var originalText = $button.text();
            
            $button.prop('disabled', true).text(grp_admin_ajax.strings.testing_connection);
            
            $.post(grp_admin_ajax.ajax_url, {
                action: 'grp_test_connection',
                nonce: grp_admin_ajax.nonce
            }, function(response) {
                if (response.success) {
                    showNotice('success', grp_admin_ajax.strings.connection_success);
                } else {
                    showNotice('error', grp_admin_ajax.strings.connection_failed + ': ' + response.data);
                }
            }).always(function() {
                $button.prop('disabled', false).text(originalText);
            });
        });
        
        // Sync reviews button
        $('#grp-sync-reviews').on('click', function() {
            var $button = $(this);
            var originalText = $button.text();
            
            $button.prop('disabled', true).text(grp_admin_ajax.strings.syncing_reviews);
            
            $.post(grp_admin_ajax.ajax_url, {
                action: 'grp_sync_reviews',
                nonce: grp_admin_ajax.nonce
            }, function(response) {
                if (response.success) {
                    showNotice('success', grp_admin_ajax.strings.sync_success);
                    // Reload page to show updated reviews
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showNotice('error', grp_admin_ajax.strings.sync_failed + ': ' + response.data);
                }
            }).always(function() {
                $button.prop('disabled', false).text(originalText);
            });
        });
        
        // Disconnect button
        $('#grp-disconnect').on('click', function() {
            if (confirm(grp_admin_ajax.strings.confirm_disconnect)) {
                // Add disconnect functionality here
                showNotice('info', 'Disconnecting...');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            }
        });
        
        // Clear cache button
        $('#grp-clear-cache').on('click', function() {
            var $button = $(this);
            var originalText = $button.text();
            
            $button.prop('disabled', true).text('Clearing...');
            
            $.post(grp_admin_ajax.ajax_url, {
                action: 'grp_clear_cache',
                nonce: grp_admin_ajax.nonce
            }, function(response) {
                if (response.success) {
                    showNotice('success', 'Cache cleared successfully!');
                } else {
                    showNotice('error', 'Failed to clear cache: ' + response.data);
                }
            }).always(function() {
                $button.prop('disabled', false).text(originalText);
            });
        });
    }
    
    /**
     * Initialize forms
     */
    function initForms() {
        // Auto-save form data
        $('.grp-auto-save').on('change', function() {
            var $form = $(this).closest('form');
            var formData = $form.serialize();
            
            $.post(grp_admin_ajax.ajax_url, {
                action: 'grp_save_settings',
                nonce: grp_admin_ajax.nonce,
                data: formData
            }, function(response) {
                if (response.success) {
                    showNotice('success', 'Settings saved!', 2000);
                } else {
                    showNotice('error', 'Failed to save settings: ' + response.data);
                }
            });
        });
        
        // Form validation
        $('.grp-form').on('submit', function(e) {
            var $form = $(this);
            var isValid = true;
            
            // Clear previous errors
            $form.find('.grp-error').remove();
            $form.find('.grp-form-control').removeClass('error');
            
            // Validate required fields
            $form.find('[required]').each(function() {
                var $field = $(this);
                if (!$field.val()) {
                    isValid = false;
                    $field.addClass('error');
                    $field.after('<div class="grp-error">This field is required.</div>');
                }
            });
            
            // Validate email fields
            $form.find('input[type="email"]').each(function() {
                var $field = $(this);
                var email = $field.val();
                if (email && !isValidEmail(email)) {
                    isValid = false;
                    $field.addClass('error');
                    $field.after('<div class="grp-error">Please enter a valid email address.</div>');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotice('error', 'Please fix the errors below.');
            }
        });
    }
    
    /**
     * Initialize modals
     */
    function initModals() {
        // Modal triggers
        $('[data-modal]').on('click', function(e) {
            e.preventDefault();
            var modalId = $(this).data('modal');
            $('#' + modalId).show();
        });
        
        // Modal close buttons
        $('.grp-modal-close').on('click', function() {
            $(this).closest('.grp-modal').hide();
        });
        
        // Close modal on backdrop click
        $('.grp-modal').on('click', function(e) {
            if (e.target === this) {
                $(this).hide();
            }
        });
        
        // Close modal on escape key
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27) { // Escape key
                $('.grp-modal:visible').hide();
            }
        });
    }
    
    /**
     * Show notice
     */
    function showNotice(type, message, duration) {
        var $notice = $('<div class="grp-notice grp-notice-' + type + '">' + message + '</div>');
        
        // Remove existing notices
        $('.grp-notice').remove();
        
        // Add new notice
        $('.grp-admin h1').after($notice);
        
        // Auto-hide after duration
        if (duration) {
            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, duration);
        }
        
        // Scroll to notice
        $('html, body').animate({
            scrollTop: $notice.offset().top - 100
        }, 500);
    }
    
    /**
     * Validate email address
     */
    function isValidEmail(email) {
        var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
    
    /**
     * Format file size
     */
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    /**
     * Format date
     */
    function formatDate(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    }
    
    /**
     * Debounce function
     */
    function debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }
    
    /**
     * Throttle function
     */
    function throttle(func, limit) {
        var inThrottle;
        return function() {
            var args = arguments;
            var context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(function() {
                    inThrottle = false;
                }, limit);
            }
        };
    }
    
    // Expose functions globally
    window.GRPAdmin = {
        showNotice: showNotice,
        formatFileSize: formatFileSize,
        formatDate: formatDate,
        debounce: debounce,
        throttle: throttle
    };
    
})(jQuery);