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
        // Build a resilient config from localized data with sensible fallbacks
        window.__grpAdminConfig = (function() {
            var cfg = window.grp_admin || window.grp_admin_ajax || {};
            cfg.ajax_url = cfg.ajax_url || (typeof window.ajaxurl !== 'undefined' ? window.ajaxurl : '');
            cfg.nonce = cfg.nonce || '';
            cfg.strings = cfg.strings || {
                testing_connection: 'Testing connection...',
                connection_success: 'Connection successful!',
                connection_failed: 'Connection failed',
                syncing_reviews: 'Syncing reviews...',
                sync_success: 'Reviews synced successfully!',
                sync_failed: 'Failed to sync reviews.',
                confirm_disconnect: 'Are you sure you want to disconnect?'
            };
            return cfg;
        })();

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
            
            $button.prop('disabled', true).text(window.__grpAdminConfig.strings.testing_connection);
            
            $.post(window.__grpAdminConfig.ajax_url, {
                action: 'grp_test_connection',
                nonce: window.__grpAdminConfig.nonce
            }, function(response) {
                if (response.success) {
                    showNotice('success', window.__grpAdminConfig.strings.connection_success);
                } else {
                    // Surface a cleaner message for common 429 zero-quota errors
                    var msg = (response && response.data) ? String(response.data) : '';
                    if (/quota exceeded|rate[_\- ]?limit|RESOURCE_EXHAUSTED|DefaultRequestsPerMinutePerProject|quota_limit_value.*0/i.test(msg)) {
                        msg = 'Quota is 0 QPM for your project on a required Business Profile API. Please complete GBP API prerequisites and request access approval, then retry.';
                    }
                    showNotice('error', window.__grpAdminConfig.strings.connection_failed + ': ' + msg);
                }
            }).always(function() {
                $button.prop('disabled', false).text(originalText);
            });
        });
        
        // Sync reviews button
        $('#grp-sync-reviews').on('click', function() {
            var $button = $(this);
            var originalText = $button.text();
            
            $button.prop('disabled', true).text(window.__grpAdminConfig.strings.syncing_reviews);
            
            $.post(window.__grpAdminConfig.ajax_url, {
                action: 'grp_sync_reviews',
                nonce: window.__grpAdminConfig.nonce
            }, function(response) {
                if (response.success) {
                    showNotice('success', window.__grpAdminConfig.strings.sync_success);
                    // Reload page to show updated reviews
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showNotice('error', window.__grpAdminConfig.strings.sync_failed + ': ' + response.data);
                }
            }).always(function() {
                $button.prop('disabled', false).text(originalText);
            });
        });
        
        // Disconnect button
        $('#grp-disconnect').on('click', function() {
            if (confirm(window.__grpAdminConfig.strings.confirm_disconnect)) {
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
            
            $.post(grp_admin.ajax_url, {
                action: 'grp_clear_cache',
                nonce: grp_admin.nonce
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

        // Populate accounts when connected
        if (window.__grpAdminConfig.is_connected) {
            populateAccounts();
        }

        // Refresh accounts list
        $('#grp-refresh-accounts').on('click', function() {
            populateAccounts(true);
        });

        // Load locations when account changes
        $(document).on('change', '#grp-account-select', function() {
            var accountId = $(this).val();
            populateLocations(accountId, true);
        });
    }
    
    /**
     * Initialize forms
     */
    function initForms() {
        // Pro toggle enables/disables Client ID/Secret fields
        function applyProToggle() {
            var enabled = $('input[name="grp_enable_pro_features"]').is(':checked');
            var $clientId = $('input[name="grp_google_client_id"]');
            var $clientSecret = $('input[name="grp_google_client_secret"]');
            $clientId.prop('disabled', !enabled);
            $clientSecret.prop('disabled', !enabled);
        }
        $(document).on('change', 'input[name="grp_enable_pro_features"]', function() {
            applyProToggle();
        });
        // Initialize on load
        applyProToggle();

        // Auto-save form data
        $('.grp-auto-save').on('change', function() {
            var $form = $(this).closest('form');
            var formData = $form.serialize();
            
            $.post(grp_admin.ajax_url, {
                action: 'grp_save_settings',
                nonce: grp_admin.nonce,
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

    // Fetch accounts via AJAX and populate the select
    function populateAccounts(force) {
        var $select = $('#grp-account-select');
        if (!$select.length) return;
        $select.prop('disabled', true).empty().append(
            $('<option>').val('').text(window.__grpAdminConfig.strings.loading)
        );
        $.post(window.__grpAdminConfig.ajax_url, {
            action: 'grp_list_accounts',
            nonce: window.__grpAdminConfig.nonce
        }, function(response) {
            $select.empty();
            if (response && response.success && response.data && response.data.accounts && response.data.accounts.length) {
                $select.append($('<option>').val('').text(window.__grpAdminConfig.strings.select_account));
                response.data.accounts.forEach(function(acc) {
                    var opt = $('<option>').val(acc.id).text(acc.label || acc.id);
                    if (!force && window.__grpAdminConfig.saved_account_id === acc.id) {
                        opt.attr('selected', 'selected');
                    }
                    $select.append(opt);
                });
                // Auto-populate locations if we have a saved account id
                var accountId = $select.val() || window.__grpAdminConfig.saved_account_id;
                if (accountId) {
                    populateLocations(accountId, false);
                } else {
                    $('#grp-location-select').empty().append(
                        $('<option>').val('').text(window.__grpAdminConfig.strings.select_location)
                    ).prop('disabled', true);
                }
            } else {
                var err = (response && response.data) ? String(response.data) : '';
                if (/quota exceeded|rate[_\- ]?limit|RESOURCE_EXHAUSTED|quota_limit_value.*0/i.test(err)) {
                    err = 'No accounts found. Your Google project may have 0 QPM for Account Management. See Troubleshooting below.';
                } else if (!err) {
                    err = 'No accounts found';
                }
                $select.append($('<option>').val('').text(err));
            }
        }).always(function() {
            $select.prop('disabled', false);
        });
    }

    // Fetch locations for an account and populate the select
    function populateLocations(accountId, force) {
        var $select = $('#grp-location-select');
        if (!$select.length) return;
        if (!accountId) {
            $select.empty().append($('<option>').val('').text('Select an account first')).prop('disabled', true);
            return;
        }
        $select.prop('disabled', true).empty().append(
            $('<option>').val('').text(window.__grpAdminConfig.strings.loading)
        );
        $.post(window.__grpAdminConfig.ajax_url, {
            action: 'grp_list_locations',
            nonce: window.__grpAdminConfig.nonce,
            account_id: accountId
        }, function(response) {
            $select.empty();
            if (response && response.success && response.data && response.data.locations && response.data.locations.length) {
                $select.append($('<option>').val('').text(window.__grpAdminConfig.strings.select_location));
                response.data.locations.forEach(function(loc) {
                    var opt = $('<option>').val(loc.id).text(loc.label || loc.id);
                    if (!force && window.__grpAdminConfig.saved_location_id === loc.id) {
                        opt.attr('selected', 'selected');
                    }
                    $select.append(opt);
                });
                $select.prop('disabled', false);
            } else {
                var err = (response && response.data) ? String(response.data) : '';
                if (/quota exceeded|rate[_\- ]?limit|RESOURCE_EXHAUSTED|quota_limit_value.*0/i.test(err)) {
                    err = 'No locations found. Your Google project may have 0 QPM or insufficient scopes. See Troubleshooting below.';
                } else if (!err) {
                    err = 'No locations found';
                }
                $select.append($('<option>').val('').text(err));
                $select.prop('disabled', false);
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
        
        // Add new notice near a visible page header
        var $header = $('.grp-settings h1, .grp-dashboard h1, .wrap h1').first();
        if ($header.length) {
            $header.after($notice);
        } else {
            // Fallback: prepend to body
            $('body').prepend($notice);
        }
        
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