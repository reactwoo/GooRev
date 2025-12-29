/**
 * Onboarding wizard JavaScript
 */

(function($) {
    'use strict';
    
    const OnboardingWizard = {
        currentStep: 0,
        steps: ['welcome', 'google_connect', 'place_id'],
        
        init: function() {
            // Check if there's a step parameter in the URL (e.g., after OAuth redirect)
            const urlParams = new URLSearchParams(window.location.search);
            const stepParam = urlParams.get('onboarding_step');
            if (stepParam) {
                const stepIndex = this.steps.indexOf(stepParam);
                if (stepIndex !== -1) {
                    this.currentStep = stepIndex;
                }
            }
            
            this.bindEvents();
            this.updateStepDisplay();
            
            // Clean up URL by removing the onboarding_step parameter
            if (stepParam) {
                const newUrl = window.location.pathname + window.location.search.replace(/[?&]onboarding_step=[^&]*/, '').replace(/^&/, '?');
                if (newUrl !== window.location.pathname + window.location.search) {
                    window.history.replaceState({}, '', newUrl || window.location.pathname);
                }
            }
        },
        
        bindEvents: function() {
            const self = this;
            
            // Next button
            $(document).on('click', '.grp-onboarding-next', function(e) {
                e.preventDefault();
                self.handleNext();
            });
            
            // Back button
            $(document).on('click', '.grp-onboarding-back', function(e) {
                e.preventDefault();
                self.handleBack();
            });
            
            // Skip button
            $(document).on('click', '.grp-onboarding-skip', function(e) {
                e.preventDefault();
                self.handleSkip();
            });
            
            // Toggle license key field
            $(document).on('change', '#grp-onboarding-has-license', function() {
                $('.grp-onboarding-license-field').toggle($(this).is(':checked'));
            });
            
            // Check if Google is connected (polling for step 2)
            if ($('.grp-onboarding-step[data-step="google_connect"]').length) {
                this.checkGoogleConnection();
            }
        },
        
        handleNext: function() {
            const currentStepName = this.steps[this.currentStep];
            const $currentStep = $('.grp-onboarding-step[data-step="' + currentStepName + '"]');
            
            // Validate current step
            if (!this.validateStep(currentStepName)) {
                return;
            }
            
            // Get step data
            const stepData = this.getStepData(currentStepName);
            
            // Submit step
            this.submitStep(currentStepName, stepData);
        },
        
        handleBack: function() {
            if (this.currentStep > 0) {
                this.currentStep--;
                this.updateStepDisplay();
            }
        },
        
        handleSkip: function() {
            if (!confirm(grpOnboarding.strings.skip_confirm || 'Are you sure you want to skip the setup? You can complete it later in settings.')) {
                return;
            }
            
            $.ajax({
                url: grpOnboarding.ajax_url,
                type: 'POST',
                data: {
                    action: 'grp_skip_onboarding',
                    nonce: grpOnboarding.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#grp-onboarding-modal').fadeOut(300, function() {
                            $(this).remove();
                        });
                    }
                }
            });
        },
        
        validateStep: function(stepName) {
            switch(stepName) {
                case 'welcome':
                    // No validation needed - all fields optional
                    return true;
                    
                case 'google_connect':
                    // Check if Google is connected
                    const $successBox = $('.grp-onboarding-success');
                    if ($successBox.length === 0) {
                        alert('Please connect your Google account first. Click the "Connect Google Account" button above.');
                        return false;
                    }
                    return true;
                    
                case 'place_id':
                    // No validation needed - Place ID is optional
                    return true;
                    
                default:
                    return true;
            }
        },
        
        getStepData: function(stepName) {
            switch(stepName) {
                case 'welcome':
                    return {
                        name: $('#grp-onboarding-name').val(),
                        email: $('#grp-onboarding-email').val(),
                        has_license: $('#grp-onboarding-has-license').is(':checked'),
                        license_key: $('#grp-onboarding-license-key').val()
                    };
                    
                case 'google_connect':
                    return {};
                    
                case 'place_id':
                    return {
                        place_id: $('#grp-onboarding-place-id').val()
                    };
                    
                default:
                    return {};
            }
        },
        
        submitStep: function(stepName, data) {
            const $nextBtn = $('.grp-onboarding-next');
            const originalText = $nextBtn.text();
            
            $nextBtn.prop('disabled', true).text(grpOnboarding.strings.loading);
            
            $.ajax({
                url: grpOnboarding.ajax_url,
                type: 'POST',
                data: {
                    action: 'grp_onboarding_step',
                    nonce: grpOnboarding.nonce,
                    step: stepName,
                    data: data
                },
                success: (response) => {
                    if (response.success) {
                        // Move to next step
                        if (response.data && response.data.next_step) {
                            const nextStepIndex = this.steps.indexOf(response.data.next_step);
                            if (nextStepIndex !== -1) {
                                this.currentStep = nextStepIndex;
                                this.updateStepDisplay();
                            }
                        } else if (response.data && response.data.redirect) {
                            // Onboarding complete - redirect
                            window.location.href = response.data.redirect;
                        }
                    } else {
                        const errorMessage = response.data && response.data.message ? response.data.message : 'An error occurred. Please try again.';
                        alert(errorMessage);
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                        errorMessage = xhr.responseJSON.data.message;
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.data && response.data.message) {
                                errorMessage = response.data.message;
                            }
                        } catch (e) {
                            // If parsing fails, use default message
                        }
                    }
                    alert(errorMessage);
                },
                complete: function() {
                    $nextBtn.prop('disabled', false).text(originalText);
                }
            });
        },
        
        updateStepDisplay: function() {
            // Hide all steps
            $('.grp-onboarding-step').removeClass('active').hide();
            
            // Show current step
            const currentStepName = this.steps[this.currentStep];
            $('.grp-onboarding-step[data-step="' + currentStepName + '"]').addClass('active').show();
            
            // Update buttons
            $('.grp-onboarding-back').toggle(this.currentStep > 0);
            
            const isLastStep = this.currentStep === this.steps.length - 1;
            $('.grp-onboarding-next').text(isLastStep ? grpOnboarding.strings.finish : grpOnboarding.strings.next);
            
            // Special handling for Google connect step
            if (currentStepName === 'google_connect') {
                this.checkGoogleConnection();
            }
        },
        
        checkGoogleConnection: function() {
            // Poll to check if Google connection status changed
            const self = this;
            let checkCount = 0;
            const maxChecks = 60; // Check for up to 60 seconds
            
            const checkInterval = setInterval(function() {
                checkCount++;
                
                // Check if success box appeared (user connected in another tab)
                if ($('.grp-onboarding-success').length > 0) {
                    clearInterval(checkInterval);
                    // User can now proceed
                }
                
                if (checkCount >= maxChecks) {
                    clearInterval(checkInterval);
                }
            }, 1000);
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        if ($('#grp-onboarding-modal').length) {
            OnboardingWizard.init();
            $('#grp-onboarding-modal').fadeIn(300);
        }
    });
    
})(jQuery);

