/**
 * Google Reviews Plugin - Frontend JavaScript
 */

(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        initCarousels();
        initLazyLoading();
    });
    
    /**
     * Initialize carousels
     */
    function initCarousels() {
        $('.grp-layout-carousel').each(function() {
            var $carousel = $(this);
            var $wrapper = $carousel.find('.grp-carousel-wrapper');
            var $items = $carousel.find('.grp-review-item');
            var $prev = $carousel.find('.grp-carousel-prev');
            var $next = $carousel.find('.grp-carousel-next');
            var $dots = $carousel.find('.grp-dot');
            
            var currentIndex = 0;
            var totalItems = $items.length;
            var options = $carousel.data('options') || {};
            
            // Set up carousel
            if (totalItems > 1) {
                setupCarousel();
                
                // Auto-play if enabled
                if (options.autoplay) {
                    startAutoplay();
                }
            } else {
                // Hide controls if only one item
                $prev.hide();
                $next.hide();
                $dots.hide();
            }
        });
    }
    
    /**
     * Setup carousel functionality
     */
    function setupCarousel() {
        var $carousel = $(this);
        var $wrapper = $carousel.find('.grp-carousel-wrapper');
        var $items = $carousel.find('.grp-review-item');
        var $prev = $carousel.find('.grp-carousel-prev');
        var $next = $carousel.find('.grp-carousel-next');
        var $dots = $carousel.find('.grp-dot');
        
        var currentIndex = 0;
        var totalItems = $items.length;
        var options = $carousel.data('options') || {};
        
        // Update carousel position
        function updateCarousel() {
            var translateX = -currentIndex * 100;
            $wrapper.css('transform', 'translateX(' + translateX + '%)');
            
            // Update dots
            $dots.removeClass('active');
            $dots.eq(currentIndex).addClass('active');
            
            // Update arrows
            $prev.prop('disabled', currentIndex === 0);
            $next.prop('disabled', currentIndex === totalItems - 1);
        }
        
        // Go to specific slide
        function goToSlide(index) {
            if (index >= 0 && index < totalItems) {
                currentIndex = index;
                updateCarousel();
            }
        }
        
        // Next slide
        function nextSlide() {
            if (currentIndex < totalItems - 1) {
                currentIndex++;
            } else {
                currentIndex = 0; // Loop to first
            }
            updateCarousel();
        }
        
        // Previous slide
        function prevSlide() {
            if (currentIndex > 0) {
                currentIndex--;
            } else {
                currentIndex = totalItems - 1; // Loop to last
            }
            updateCarousel();
        }
        
        // Start autoplay
        function startAutoplay() {
            var speed = options.speed || 5000;
            $carousel.data('autoplay-interval', setInterval(nextSlide, speed));
        }
        
        // Stop autoplay
        function stopAutoplay() {
            var interval = $carousel.data('autoplay-interval');
            if (interval) {
                clearInterval(interval);
                $carousel.removeData('autoplay-interval');
            }
        }
        
        // Event handlers
        $next.on('click', function(e) {
            e.preventDefault();
            nextSlide();
            if (options.autoplay) {
                stopAutoplay();
                startAutoplay();
            }
        });
        
        $prev.on('click', function(e) {
            e.preventDefault();
            prevSlide();
            if (options.autoplay) {
                stopAutoplay();
                startAutoplay();
            }
        });
        
        $dots.on('click', function(e) {
            e.preventDefault();
            var index = $(this).data('index');
            goToSlide(index);
            if (options.autoplay) {
                stopAutoplay();
                startAutoplay();
            }
        });
        
        // Pause on hover
        if (options.autoplay) {
            $carousel.on('mouseenter', stopAutoplay);
            $carousel.on('mouseleave', startAutoplay);
        }
        
        // Touch/swipe support
        var startX = 0;
        var startY = 0;
        var distX = 0;
        var distY = 0;
        var threshold = 50;
        
        $carousel.on('touchstart', function(e) {
            var touch = e.originalEvent.touches[0];
            startX = touch.clientX;
            startY = touch.clientY;
        });
        
        $carousel.on('touchend', function(e) {
            var touch = e.originalEvent.changedTouches[0];
            distX = touch.clientX - startX;
            distY = touch.clientY - startY;
            
            // Check if horizontal swipe
            if (Math.abs(distX) > Math.abs(distY) && Math.abs(distX) > threshold) {
                if (distX > 0) {
                    prevSlide();
                } else {
                    nextSlide();
                }
                
                if (options.autoplay) {
                    stopAutoplay();
                    startAutoplay();
                }
            }
        });
        
        // Keyboard navigation
        $carousel.on('keydown', function(e) {
            switch(e.which) {
                case 37: // Left arrow
                    e.preventDefault();
                    prevSlide();
                    break;
                case 39: // Right arrow
                    e.preventDefault();
                    nextSlide();
                    break;
            }
        });
        
        // Initialize
        updateCarousel();
    }
    
    /**
     * Initialize lazy loading for images
     */
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            var imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            $('.grp-review-avatar img[data-src]').each(function() {
                imageObserver.observe(this);
            });
        }
    }
    
    /**
     * AJAX function to load more reviews
     */
    function loadMoreReviews(container, args) {
        var $container = $(container);
        var $loading = $('<div class="grp-loading">Loading more reviews...</div>');
        
        $container.append($loading);
        
        $.post(grp_ajax.ajax_url, {
            action: 'grp_get_reviews',
            nonce: grp_ajax.nonce,
            args: args
        }, function(response) {
            $loading.remove();
            
            if (response.success) {
                // Process and display new reviews
                // This would be implemented based on specific needs
            } else {
                $container.append('<div class="grp-error">Error loading reviews: ' + response.data + '</div>');
            }
        }).fail(function() {
            $loading.remove();
            $container.append('<div class="grp-error">Failed to load reviews. Please try again.</div>');
        });
    }
    
    /**
     * Utility function to format dates
     */
    function formatDate(dateString) {
        var date = new Date(dateString);
        var options = { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        };
        return date.toLocaleDateString('en-US', options);
    }
    
    /**
     * Utility function to truncate text
     */
    function truncateText(text, maxLength) {
        if (text.length <= maxLength) {
            return text;
        }
        return text.substr(0, maxLength) + '...';
    }
    
    // Expose functions globally if needed
    window.GRP = {
        loadMoreReviews: loadMoreReviews,
        formatDate: formatDate,
        truncateText: truncateText
    };
    
})(jQuery);