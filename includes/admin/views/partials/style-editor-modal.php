<?php
/**
 * Style editor modal (used on Styles page)
 *
 * @package Google_Reviews_Plugin
 */
if (!defined('ABSPATH')) {
    exit;
}
?>

<div id="grp-style-editor-modal" class="grp-template-editor-modal grp-style-editor-modal" style="display: none;">
    <div class="grp-template-editor-content grp-style-editor-content">
        <button type="button" class="grp-template-editor-close button-link" id="grp-style-editor-close-x">×</button>
        <h3><?php esc_html_e('Customize Style', 'google-reviews-plugin'); ?></h3>
        <div class="grp-template-editor-body grp-style-editor-body">
            <div class="grp-template-editor-preview-col">
                <div id="grp-style-editor-preview" class="grp-template-editor-preview"></div>
            </div>
            <div class="grp-template-editor-controls-col">
                <div class="grp-template-editor-controls">
                    <div class="grp-template-editor-column">
                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Variant', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field">
                                <select id="grp-style-variant">
                                    <option value="light"><?php esc_html_e('Light', 'google-reviews-plugin'); ?></option>
                                    <option value="dark"><?php esc_html_e('Dark', 'google-reviews-plugin'); ?></option>
                                    <option value="auto"><?php esc_html_e('Auto', 'google-reviews-plugin'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Background', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field grp-template-color-field">
                                <input type="color" id="grp-style-background-color">
                                <input type="text" id="grp-style-background-text" placeholder="#FFFFFF">
                            </div>
                        </div>

                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Card Background', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field">
                                <input type="text" id="grp-style-card-background" placeholder="rgba(255,255,255,0.95)">
                            </div>
                        </div>

                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Text', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field grp-template-color-field">
                                <input type="color" id="grp-style-text-color">
                                <input type="text" id="grp-style-text-text" placeholder="#111111">
                            </div>
                        </div>

                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Muted Text', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field grp-template-color-field">
                                <input type="color" id="grp-style-muted-color">
                                <input type="text" id="grp-style-muted-text" placeholder="#6B7280">
                            </div>
                        </div>
                    </div>

                    <div class="grp-template-editor-column">
                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Border', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field grp-template-color-field">
                                <input type="color" id="grp-style-border-color">
                                <input type="text" id="grp-style-border-text" placeholder="#E5E7EB">
                            </div>
                        </div>

                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Accent', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field grp-template-color-field">
                                <input type="color" id="grp-style-accent-color">
                                <input type="text" id="grp-style-accent-text" placeholder="#4285F4">
                            </div>
                        </div>

                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Stars', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field grp-template-color-field">
                                <input type="color" id="grp-style-star-color">
                                <input type="text" id="grp-style-star-text" placeholder="#FBBC05">
                            </div>
                        </div>

                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Sample Review Text', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field">
                                <input type="text" id="grp-style-sample-review-text" placeholder="<?php esc_attr_e('This is a sample review…', 'google-reviews-plugin'); ?>">
                            </div>
                        </div>

                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Sample Author Name', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field">
                                <input type="text" id="grp-style-sample-author-name" placeholder="<?php esc_attr_e('John Doe', 'google-reviews-plugin'); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grp-template-editor-footer">
                    <button type="button" class="button" id="grp-style-editor-reset"><?php esc_html_e('Reset', 'google-reviews-plugin'); ?></button>
                    <button type="button" class="button button-primary" id="grp-style-editor-save"><?php esc_html_e('Save', 'google-reviews-plugin'); ?></button>
                    <button type="button" class="button" id="grp-style-editor-close"><?php esc_html_e('Done', 'google-reviews-plugin'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

