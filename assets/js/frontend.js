/**
 * Google Reviews Plugin - Frontend JavaScript
 */

(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        initCarousels();
        initGridCarousels();
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

        // Get current columns per view based on screen size
        function getColumnsPerView() {
            var width = window.innerWidth;
            if (width <= 640) return 1; // Mobile
            if (width <= 1024) return 2; // Tablet
            return 3; // Desktop
        }

        // Update carousel position
        function updateCarousel() {
            var columnsPerView = getColumnsPerView();
            var translateX = -currentIndex * (100 / columnsPerView);
            $wrapper.css('transform', 'translateX(' + translateX + '%)');

            // Update dots - calculate which group is active
            var activeGroup = Math.floor(currentIndex / columnsPerView);
            $dots.removeClass('active');
            $dots.eq(activeGroup).addClass('active');

            // Update arrows - disable when at start/end groups
            var maxIndex = Math.max(0, totalItems - columnsPerView);
            $prev.prop('disabled', currentIndex === 0);
            $next.prop('disabled', currentIndex >= maxIndex);
        }

        // Go to specific slide group
        function goToSlide(index) {
            var columnsPerView = getColumnsPerView();
            var maxIndex = Math.max(0, totalItems - columnsPerView);
            if (index >= 0 && index <= maxIndex) {
                currentIndex = index;
                updateCarousel();
            }
        }

        // Next slide group
        function nextSlide() {
            var columnsPerView = getColumnsPerView();
            var maxIndex = Math.max(0, totalItems - columnsPerView);
            if (currentIndex < maxIndex) {
                currentIndex++;
            } else {
                currentIndex = 0; // Loop to first group
            }
            updateCarousel();
        }

        // Previous slide group
        function prevSlide() {
            if (currentIndex > 0) {
                currentIndex--;
            } else {
                var columnsPerView = getColumnsPerView();
                var maxIndex = Math.max(0, totalItems - columnsPerView);
                currentIndex = maxIndex; // Loop to last group
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
            var groupIndex = $(this).data('index');
            var slideIndex = groupIndex * 3; // Convert group index to slide index
            goToSlide(slideIndex);
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
        
        // Generate dots based on current columns
        function updateDots() {
            var columnsPerView = getColumnsPerView();
            var totalGroups = Math.ceil(totalItems / columnsPerView);
            $dots.empty();
            for (var i = 0; i < totalGroups; i++) {
                $dots.append('<button class="grp-dot' + (i === 0 ? ' active' : '') + '" data-index="' + i + '"></button>');
            }
            $dots = $carousel.find('.grp-dot'); // Re-select after adding
        }

        updateDots();

        // Handle resize
        $(window).on('resize.carousel', function() {
            updateDots();
            updateCarousel();
        });

        // Initialize
        updateCarousel();
    }

    /**
     * Initialize grid carousels (multi-items per view)
     */
    function initGridCarousels() {
        $('.grp-layout-grid_carousel').each(function() {
            var $carousel = $(this);
            var $track = $carousel.find('.grp-grid-carousel-track');
            var $items = $carousel.find('.grp-review-item');
            var $prev = $carousel.find('.grp-carousel-prev');
            var $next = $carousel.find('.grp-carousel-next');
            var $dots = $carousel.find('.grp-dot');

            var options = $carousel.data('options') || {};
            var colsDesktop = options.cols_desktop || 3;
            var colsTablet = options.cols_tablet || 2;
            var colsMobile = options.cols_mobile || 1;
            var gap = options.gap || 20;

            function getItemsPerView() {
                var width = window.innerWidth;
                if (width <= 640) return colsMobile;
                if (width <= 1024) return colsTablet;
                return colsDesktop;
            }

            function updateWidths() {
                var perView = getItemsPerView();
                var itemWidthPercent = (100 / perView);
                $items.css({
                    width: itemWidthPercent + '%',
                    paddingLeft: (gap/2) + 'px',
                    paddingRight: (gap/2) + 'px'
                });
            }

            var currentPage = 0;
            function getTotalPages() {
                var perView = getItemsPerView();
                return Math.max(1, Math.ceil($items.length / perView));
            }

            function updateTrack() {
                var perView = getItemsPerView();
                var translateX = -(currentPage * 100);
                $track.css('transform', 'translateX(' + translateX + '%)');
                // Dots
                $dots.removeClass('active');
                $dots.eq(currentPage).addClass('active');
                // Arrows
                var totalPages = getTotalPages();
                $prev.prop('disabled', currentPage === 0);
                $next.prop('disabled', currentPage === totalPages - 1);
            }

            function nextPage() {
                var totalPages = getTotalPages();
                currentPage = (currentPage + 1) % totalPages;
                updateTrack();
            }

            function prevPage() {
                var totalPages = getTotalPages();
                currentPage = (currentPage - 1 + totalPages) % totalPages;
                updateTrack();
            }

            // Build dots dynamically based on pages
            function buildDots() {
                var totalPages = getTotalPages();
                var $dotsWrap = $carousel.find('.grp-carousel-dots');
                if ($dotsWrap.length === 0) return;
                $dotsWrap.empty();
                for (var i = 0; i < totalPages; i++) {
                    var $dot = $('<button class="grp-dot" data-index="' + i + '"></button>');
                    if (i === 0) $dot.addClass('active');
                    $dotsWrap.append($dot);
                }
                $dots = $carousel.find('.grp-dot');
                $dots.on('click', function(e) {
                    e.preventDefault();
                    currentPage = parseInt($(this).data('index'), 10) || 0;
                    updateTrack();
                });
            }

            // Init
            updateWidths();
            buildDots();
            updateTrack();

            // Arrows
            $next.on('click', function(e) { e.preventDefault(); nextPage(); });
            $prev.on('click', function(e) { e.preventDefault(); prevPage(); });

            // Autoplay
            if (options.autoplay) {
                var speed = options.speed || 5000;
                var interval = setInterval(nextPage, speed);
                $carousel.on('mouseenter', function(){ clearInterval(interval); });
                $carousel.on('mouseleave', function(){ interval = setInterval(nextPage, speed); });
            }

            // Resize
            $(window).on('resize', function() {
                var prevPages = $dots.length;
                updateWidths();
                buildDots();
                updateTrack();
            });
        });
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