/**
 * Review Widgets Frontend JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Track button clicks (if tracking is enabled)
        $('.grp-review-button').on('click', function() {
            var $button = $(this);
            var widgetId = $button.data('widget-id') || 'button';
            
            // Send tracking event (non-blocking)
            if (typeof grpReviewWidgets !== 'undefined' && grpReviewWidgets.tracking_enabled) {
                $.ajax({
                    url: grpReviewWidgets.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'grp_track_widget_click',
                        nonce: grpReviewWidgets.nonce,
                        widget_type: 'button',
                        widget_id: widgetId
                    },
                    async: true // Don't block navigation
                });
            }
        });
    });
    
})(jQuery);

