<?php
/**
 * Reviews view
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap grp-reviews-page">
    <h1><?php esc_html_e('Google Reviews', 'google-reviews-plugin'); ?></h1>
    
    <div class="grp-reviews-header">
        <div class="grp-reviews-actions">
            <button id="grp-sync-reviews" class="button button-primary">
                <?php esc_html_e('Sync Reviews', 'google-reviews-plugin'); ?>
            </button>
            <button id="grp-clear-cache" class="button">
                <?php esc_html_e('Clear Cache', 'google-reviews-plugin'); ?>
            </button>
            <?php if (!empty($all_reviews)): ?>
                <button id="grp-clear-reviews" class="button" style="color: #dc3232; border-color: #dc3232;">
                    <?php esc_html_e('Clear All Reviews', 'google-reviews-plugin'); ?>
                </button>
            <?php endif; ?>
        </div>
        
        <div class="grp-reviews-filters">
            <select id="grp-rating-filter">
                <option value=""><?php esc_html_e('All Ratings', 'google-reviews-plugin'); ?></option>
                <option value="5">5 <?php esc_html_e('Stars', 'google-reviews-plugin'); ?></option>
                <option value="4">4 <?php esc_html_e('Stars', 'google-reviews-plugin'); ?></option>
                <option value="3">3 <?php esc_html_e('Stars', 'google-reviews-plugin'); ?></option>
                <option value="2">2 <?php esc_html_e('Stars', 'google-reviews-plugin'); ?></option>
                <option value="1">1 <?php esc_html_e('Star', 'google-reviews-plugin'); ?></option>
            </select>
            
            <select id="grp-sort-filter">
                <option value="newest"><?php esc_html_e('Newest First', 'google-reviews-plugin'); ?></option>
                <option value="oldest"><?php esc_html_e('Oldest First', 'google-reviews-plugin'); ?></option>
                <option value="highest_rating"><?php esc_html_e('Highest Rating', 'google-reviews-plugin'); ?></option>
                <option value="lowest_rating"><?php esc_html_e('Lowest Rating', 'google-reviews-plugin'); ?></option>
            </select>
        </div>
    </div>
    
    <?php if (!empty($all_reviews)): ?>
        <div class="grp-reviews-stats">
            <div class="grp-stat">
                <span class="grp-stat-number"><?php echo count($all_reviews); ?></span>
                <span class="grp-stat-label"><?php esc_html_e('Total Reviews', 'google-reviews-plugin'); ?></span>
            </div>
            <div class="grp-stat">
                <span class="grp-stat-number">
                    <?php
                    $avg_rating = 0;
                    if (!empty($all_reviews)) {
                        $total_rating = array_sum(wp_list_pluck($all_reviews, 'rating'));
                        $avg_rating = round($total_rating / count($all_reviews), 1);
                    }
                    echo $avg_rating;
                    ?>
                </span>
                <span class="grp-stat-label"><?php esc_html_e('Average Rating', 'google-reviews-plugin'); ?></span>
            </div>
            <div class="grp-stat">
                <span class="grp-stat-number">
                    <?php
                    $five_star_count = count(array_filter($all_reviews, function($review) {
                        return $review['rating'] == 5;
                    }));
                    echo $five_star_count;
                    ?>
                </span>
                <span class="grp-stat-label"><?php esc_html_e('5-Star Reviews', 'google-reviews-plugin'); ?></span>
            </div>
        </div>
        
        <div class="grp-reviews-list">
            <?php foreach ($all_reviews as $review): ?>
                <div class="grp-review-item" data-rating="<?php echo esc_attr($review['rating']); ?>">
                    <div class="grp-review-header">
                        <div class="grp-review-rating">
                            <?php echo $review['stars_html']; ?>
                        </div>
                        <div class="grp-review-date">
                            <?php echo esc_html($review['time_formatted']); ?>
                        </div>
                    </div>
                    
                    <div class="grp-review-content">
                        <?php if (!empty($review['text'])): ?>
                            <div class="grp-review-text">
                                <?php echo wp_kses_post($review['text']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="grp-review-meta">
                            <?php if (!empty($review['author_photo'])): ?>
                                <div class="grp-review-avatar">
                                    <img src="<?php echo esc_url($review['author_photo']); ?>" 
                                         alt="<?php echo esc_attr($review['author_name']); ?>">
                                </div>
                            <?php endif; ?>
                            
                            <div class="grp-review-author">
                                <span class="grp-author-name"><?php echo esc_html($review['author_name']); ?></span>
                                <?php if ($review['is_anonymous']): ?>
                                    <span class="grp-anonymous"><?php esc_html_e('(Anonymous)', 'google-reviews-plugin'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($review['reply']['text'])): ?>
                            <div class="grp-review-reply">
                                <div class="grp-reply-header">
                                    <strong><?php esc_html_e('Business Response', 'google-reviews-plugin'); ?></strong>
                                    <span class="grp-reply-date"><?php echo esc_html($review['reply']['time']); ?></span>
                                </div>
                                <div class="grp-reply-text">
                                    <?php echo wp_kses_post($review['reply']['text']); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="grp-review-actions">
                        <?php if (!empty($review['review_url'])): ?>
                            <a href="<?php echo esc_url($review['review_url']); ?>" 
                               target="_blank" 
                               class="button button-small">
                                <?php esc_html_e('View on Google', 'google-reviews-plugin'); ?>
                            </a>
                        <?php endif; ?>
                        
                        <button class="button button-small grp-copy-review" 
                                data-review-text="<?php echo esc_attr($review['text']); ?>">
                            <?php esc_html_e('Copy Text', 'google-reviews-plugin'); ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="grp-no-reviews">
            <h3><?php esc_html_e('No Reviews Found', 'google-reviews-plugin'); ?></h3>
            <p><?php esc_html_e('No reviews have been synced yet. Click the "Sync Reviews" button to get started.', 'google-reviews-plugin'); ?></p>
            <button id="grp-sync-reviews" class="button button-primary">
                <?php esc_html_e('Sync Reviews', 'google-reviews-plugin'); ?>
            </button>
        </div>
    <?php endif; ?>
</div>

<style>
.grp-reviews-page {
    max-width: 1200px;
}

.grp-reviews-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0;
    padding: 20px;
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}

.grp-reviews-actions {
    display: flex;
    gap: 10px;
}

.grp-reviews-filters {
    display: flex;
    gap: 10px;
}

.grp-reviews-filters select {
    padding: 5px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.grp-reviews-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
    margin: 20px 0;
    padding: 20px;
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}

.grp-stat {
    text-align: center;
}

.grp-stat-number {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #0073aa;
}

.grp-stat-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
}

.grp-reviews-list {
    display: grid;
    gap: 20px;
    margin: 20px 0;
}

.grp-review-item {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.grp-review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.grp-review-rating {
    display: flex;
    align-items: center;
    gap: 2px;
}

.grp-star {
    color: #ffc107;
    font-size: 16px;
}

.grp-review-date {
    color: #666;
    font-size: 12px;
}

.grp-review-content {
    margin-bottom: 15px;
}

.grp-review-text {
    color: #333;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 15px;
}

.grp-review-meta {
    display: flex;
    align-items: center;
    gap: 10px;
}

.grp-review-avatar img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
}

.grp-author-name {
    font-weight: 600;
    color: #333;
}

.grp-anonymous {
    color: #666;
    font-size: 12px;
    font-style: italic;
}

.grp-review-reply {
    margin-top: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 4px;
    border-left: 4px solid #007cba;
}

.grp-reply-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    font-weight: 600;
    color: #333;
}

.grp-reply-text {
    color: #666;
    font-size: 14px;
}

.grp-review-actions {
    display: flex;
    gap: 10px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.grp-no-reviews {
    text-align: center;
    padding: 60px 20px;
    background: #f9f9f9;
    border: 2px dashed #ddd;
    border-radius: 4px;
    margin: 40px 0;
}

.grp-no-reviews h3 {
    color: #666;
    margin-bottom: 10px;
}

.grp-no-reviews p {
    color: #999;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .grp-reviews-header {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .grp-reviews-filters {
        justify-content: center;
    }
    
    .grp-reviews-stats {
        grid-template-columns: 1fr;
    }
    
    .grp-review-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .grp-review-actions {
        flex-direction: column;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Filter reviews by rating
    $('#grp-rating-filter').on('change', function() {
        var rating = $(this).val();
        filterReviews();
    });
    
    // Sort reviews
    $('#grp-sort-filter').on('change', function() {
        var sortBy = $(this).val();
        sortReviews(sortBy);
    });
    
    // Copy review text
    $('.grp-copy-review').on('click', function() {
        var text = $(this).data('review-text');
        navigator.clipboard.writeText(text).then(function() {
            alert('<?php esc_js_e('Review text copied to clipboard!', 'google-reviews-plugin'); ?>');
        });
    });
    
    function filterReviews() {
        var rating = $('#grp-rating-filter').val();
        
        $('.grp-review-item').each(function() {
            var $item = $(this);
            var itemRating = $item.data('rating');
            
            if (!rating || itemRating == rating) {
                $item.show();
            } else {
                $item.hide();
            }
        });
    }
    
    function sortReviews(sortBy) {
        var $container = $('.grp-reviews-list');
        var $items = $container.find('.grp-review-item');
        
        $items.sort(function(a, b) {
            var aRating = parseInt($(a).data('rating'));
            var bRating = parseInt($(b).data('rating'));
            var aDate = new Date($(a).find('.grp-review-date').text());
            var bDate = new Date($(b).find('.grp-review-date').text());
            
            switch(sortBy) {
                case 'newest':
                    return bDate - aDate;
                case 'oldest':
                    return aDate - bDate;
                case 'highest_rating':
                    return bRating - aRating;
                case 'lowest_rating':
                    return aRating - bRating;
                default:
                    return 0;
            }
        });
        
        $container.empty().append($items);
    }
});
</script>