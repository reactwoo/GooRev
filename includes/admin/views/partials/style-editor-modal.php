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
                        <div class="grp-template-editor-row grp-style-non-creative-only">
                            <span class="grp-template-editor-label"><?php esc_html_e('Variant', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field">
                                <select id="grp-style-variant">
                                    <option value="light"><?php esc_html_e('Light', 'google-reviews-plugin'); ?></option>
                                    <option value="dark"><?php esc_html_e('Dark', 'google-reviews-plugin'); ?></option>
                                    <option value="auto"><?php esc_html_e('Auto', 'google-reviews-plugin'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="grp-template-editor-row grp-style-non-creative-only">
                            <span class="grp-template-editor-label"><?php esc_html_e('Background', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field grp-template-color-field">
                                <input type="color" id="grp-style-background-color">
                                <input type="text" id="grp-style-background-text" placeholder="#FFFFFF">
                            </div>
                        </div>

                        <div class="grp-template-editor-row grp-style-non-creative-only">
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

                        <div class="grp-template-editor-row grp-inline-spacing-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Card Radius', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field grp-spacing-group">
                                <div class="grp-spacing-input">
                                    <input type="number" id="grp-style-card-radius-tl" min="0" step="1" value="0">
                                    <span><?php esc_html_e('Top-left', 'google-reviews-plugin'); ?></span>
                                </div>
                                <div class="grp-spacing-input">
                                    <input type="number" id="grp-style-card-radius-tr" min="0" step="1" value="0">
                                    <span><?php esc_html_e('Top-right', 'google-reviews-plugin'); ?></span>
                                </div>
                                <div class="grp-spacing-input">
                                    <input type="number" id="grp-style-card-radius-br" min="0" step="1" value="0">
                                    <span><?php esc_html_e('Bottom-right', 'google-reviews-plugin'); ?></span>
                                </div>
                                <div class="grp-spacing-input">
                                    <input type="number" id="grp-style-card-radius-bl" min="0" step="1" value="0">
                                    <span><?php esc_html_e('Bottom-left', 'google-reviews-plugin'); ?></span>
                                </div>
                            </div>
                            <input type="hidden" id="grp-style-card-radius" value="">
                        </div>

                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Glass Effect', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field">
                                <label class="grp-template-checkbox">
                                    <input type="checkbox" id="grp-style-glass-effect">
                                    <span><?php esc_html_e('Enable', 'google-reviews-plugin'); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Card Shadow', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field">
                                <label class="grp-template-checkbox">
                                    <input type="checkbox" id="grp-style-card-shadow-enabled">
                                    <span><?php esc_html_e('Enable', 'google-reviews-plugin'); ?></span>
                                </label>
                                <button type="button" class="button" id="grp-style-card-shadow-edit"><?php esc_html_e('Edit', 'google-reviews-plugin'); ?></button>
                                <input type="hidden" id="grp-style-card-shadow" value="">
                            </div>
                        </div>
                    </div>

                    <div class="grp-template-editor-column">
                        <div class="grp-template-editor-row grp-inline-spacing-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Border', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field" style="display:flex; flex-direction:column; gap:10px;">
                                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                                    <label class="grp-template-checkbox" style="display:flex; align-items:center; gap:8px;">
                                        <input type="checkbox" id="grp-style-border-enabled">
                                        <span><?php esc_html_e('Enable', 'google-reviews-plugin'); ?></span>
                                    </label>
                                    <div class="grp-template-color-field">
                                        <input type="color" id="grp-style-border-color">
                                        <input type="text" id="grp-style-border-text" placeholder="#E5E7EB">
                                    </div>
                                </div>
                                <div class="grp-spacing-group" id="grp-style-border-widths" style="opacity: 0.8;">
                                    <div class="grp-spacing-input">
                                        <input type="number" id="grp-style-border-top" min="0" step="1" value="0">
                                        <span><?php esc_html_e('Top', 'google-reviews-plugin'); ?></span>
                                    </div>
                                    <div class="grp-spacing-input">
                                        <input type="number" id="grp-style-border-right" min="0" step="1" value="0">
                                        <span><?php esc_html_e('Right', 'google-reviews-plugin'); ?></span>
                                    </div>
                                    <div class="grp-spacing-input">
                                        <input type="number" id="grp-style-border-bottom" min="0" step="1" value="0">
                                        <span><?php esc_html_e('Bottom', 'google-reviews-plugin'); ?></span>
                                    </div>
                                    <div class="grp-spacing-input">
                                        <input type="number" id="grp-style-border-left" min="0" step="1" value="0">
                                        <span><?php esc_html_e('Left', 'google-reviews-plugin'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grp-template-editor-row grp-style-non-creative-only">
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

                        <div class="grp-template-editor-row grp-style-creative-only">
                            <span class="grp-template-editor-label"><?php esc_html_e('Avatar Size', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field">
                                <input type="number" id="grp-style-avatar-size" min="20" max="120" step="1" placeholder="80">
                                <span>px</span>
                            </div>
                        </div>

                        <div class="grp-template-editor-row grp-style-creative-only">
                            <span class="grp-template-editor-label"><?php esc_html_e('Gradient', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field" style="display:flex; align-items:center; gap:10px;">
                                <div id="grp-style-gradient-summary-preview" style="width: 92px; height: 28px; border-radius: 6px; border: 1px solid rgba(0,0,0,0.12);"></div>
                                <button type="button" class="button" id="grp-style-gradient-editor-open"><?php esc_html_e('Edit', 'google-reviews-plugin'); ?></button>
                                <input type="hidden" id="grp-style-gradient-css" value="">
                            </div>
                        </div>

                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Font Family', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field">
                                <select id="grp-style-font-family">
                                    <option value=""><?php esc_html_e('Inherit (Theme Font)', 'google-reviews-plugin'); ?></option>
                                    <option value="inherit"><?php esc_html_e('Inherit', 'google-reviews-plugin'); ?></option>
                                    <option value="Inter, sans-serif"><?php esc_html_e('Inter', 'google-reviews-plugin'); ?></option>
                                    <option value="Roboto, sans-serif"><?php esc_html_e('Roboto', 'google-reviews-plugin'); ?></option>
                                    <option value="Open Sans, sans-serif"><?php esc_html_e('Open Sans', 'google-reviews-plugin'); ?></option>
                                    <option value="Lato, sans-serif"><?php esc_html_e('Lato', 'google-reviews-plugin'); ?></option>
                                    <option value="Montserrat, sans-serif"><?php esc_html_e('Montserrat', 'google-reviews-plugin'); ?></option>
                                    <option value="Poppins, sans-serif"><?php esc_html_e('Poppins', 'google-reviews-plugin'); ?></option>
                                    <option value="Raleway, sans-serif"><?php esc_html_e('Raleway', 'google-reviews-plugin'); ?></option>
                                    <option value="Nunito, sans-serif"><?php esc_html_e('Nunito', 'google-reviews-plugin'); ?></option>
                                    <option value="Source Sans Pro, sans-serif"><?php esc_html_e('Source Sans Pro', 'google-reviews-plugin'); ?></option>
                                    <option value="Ubuntu, sans-serif"><?php esc_html_e('Ubuntu', 'google-reviews-plugin'); ?></option>
                                    <option value="Work Sans, sans-serif"><?php esc_html_e('Work Sans', 'google-reviews-plugin'); ?></option>
                                    <option value="DM Sans, sans-serif"><?php esc_html_e('DM Sans', 'google-reviews-plugin'); ?></option>
                                    <option value="Georgia, serif"><?php esc_html_e('Georgia', 'google-reviews-plugin'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Heading Weight', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field">
                                <select id="grp-style-heading-weight">
                                    <?php foreach (array(300,400,500,600,700,800,900) as $w): ?>
                                        <option value="<?php echo esc_attr($w); ?>"><?php echo esc_html($w); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Body Weight', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field">
                                <select id="grp-style-body-weight">
                                    <?php foreach (array(300,400,500,600,700) as $w): ?>
                                        <option value="<?php echo esc_attr($w); ?>"><?php echo esc_html($w); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Line Height', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field">
                                <input type="number" id="grp-style-body-line-height" min="1" max="2.5" step="0.1" placeholder="1.6">
                            </div>
                        </div>

                        <div class="grp-template-editor-row">
                            <span class="grp-template-editor-label"><?php esc_html_e('Letter Spacing', 'google-reviews-plugin'); ?></span>
                            <div class="grp-template-editor-field">
                                <input type="number" id="grp-style-body-letter-spacing" min="-2" max="10" step="0.1" placeholder="0">
                                <span>px</span>
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

<!-- Reuse the same shadow editor UX as the widgets page -->
<div id="grp-style-box-shadow-modal" class="grp-box-shadow-modal" style="display: none;">
    <div class="grp-box-shadow-modal-content">
        <h3><?php esc_html_e('Box Shadow Editor', 'google-reviews-plugin'); ?></h3>
        <label>
            <?php esc_html_e('Color', 'google-reviews-plugin'); ?>
            <input type="color" id="grp-style-box-shadow-color-picker" value="#000000">
        </label>
        <div class="grp-box-shadow-controls" style="margin-top: 12px;">
            <div class="grp-shadow-control">
                <span class="grp-shadow-label"><?php esc_html_e('Opacity', 'google-reviews-plugin'); ?></span>
                <input type="range" id="grp-style-box-shadow-opacity" min="0" max="100" value="12">
                <input type="number" id="grp-style-box-shadow-opacity-number" min="0" max="100" value="12">
            </div>
        </div>
        <div class="grp-box-shadow-controls">
            <?php foreach (array(
                array('name' => 'Horizontal', 'id' => 'h', 'min' => -50, 'max' => 50, 'value' => 0),
                array('name' => 'Vertical', 'id' => 'v', 'min' => -50, 'max' => 50, 'value' => 8),
                array('name' => 'Blur', 'id' => 'blur', 'min' => 0, 'max' => 100, 'value' => 32),
                array('name' => 'Spread', 'id' => 'spread', 'min' => -50, 'max' => 50, 'value' => 0),
            ) as $control): ?>
                <div class="grp-shadow-control">
                    <span class="grp-shadow-label"><?php echo esc_html($control['name']); ?></span>
                    <input type="range" class="grp-style-shadow-range" data-target="<?php echo esc_attr($control['id']); ?>" min="<?php echo esc_attr($control['min']); ?>" max="<?php echo esc_attr($control['max']); ?>" value="<?php echo esc_attr($control['value']); ?>">
                    <input type="number" class="grp-style-shadow-number" data-target="<?php echo esc_attr($control['id']); ?>" min="<?php echo esc_attr($control['min']); ?>" max="<?php echo esc_attr($control['max']); ?>" value="<?php echo esc_attr($control['value']); ?>">
                </div>
            <?php endforeach; ?>
        </div>
        <div class="grp-box-shadow-actions">
            <button type="button" id="grp-style-box-shadow-modal-close" class="button"><?php esc_html_e('Done', 'google-reviews-plugin'); ?></button>
        </div>
    </div>
</div>

<!-- Gradient editor modal (reused UX: type, angle, positions, opacities) -->
<div id="grp-gradient-editor-modal" class="grp-gradient-editor-modal" style="display: none;">
    <div class="grp-gradient-editor-content">
        <button type="button" class="grp-gradient-editor-close button-link">×</button>
        <h3><?php esc_html_e('Gradient Background Settings', 'google-reviews-plugin'); ?></h3>
        <div class="grp-gradient-editor-row">
            <span><?php esc_html_e('Gradient Type', 'google-reviews-plugin'); ?></span>
            <select id="grp-gradient-type">
                <option value="linear"><?php esc_html_e('Linear', 'google-reviews-plugin'); ?></option>
                <option value="radial"><?php esc_html_e('Radial', 'google-reviews-plugin'); ?></option>
            </select>
        </div>
        <div class="grp-gradient-editor-row">
            <span><?php esc_html_e('Gradient Angle', 'google-reviews-plugin'); ?></span>
            <div class="grp-gradient-editor-range-group">
                <input type="range" id="grp-gradient-angle" min="0" max="360">
                <input type="number" id="grp-gradient-angle-number" min="0" max="360">
            </div>
        </div>
        <div class="grp-gradient-editor-row">
            <span><?php esc_html_e('Start Position', 'google-reviews-plugin'); ?></span>
            <div class="grp-gradient-editor-range-group">
                <input type="range" id="grp-gradient-start-pos" min="0" max="100">
                <input type="number" id="grp-gradient-start-pos-number" min="0" max="100">
            </div>
        </div>
        <div class="grp-gradient-editor-row">
            <span><?php esc_html_e('End Position', 'google-reviews-plugin'); ?></span>
            <div class="grp-gradient-editor-range-group">
                <input type="range" id="grp-gradient-end-pos" min="0" max="100">
                <input type="number" id="grp-gradient-end-pos-number" min="0" max="100">
            </div>
        </div>
        <div class="grp-gradient-editor-row grp-gradient-mid-toggle">
            <span><?php esc_html_e('Mid Stop', 'google-reviews-plugin'); ?></span>
            <div class="grp-gradient-editor-range-group" style="justify-content:flex-start;">
                <label class="grp-template-checkbox" style="display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" id="grp-gradient-mid-enabled" checked>
                    <span><?php esc_html_e('Enable', 'google-reviews-plugin'); ?></span>
                </label>
            </div>
        </div>
        <div class="grp-gradient-editor-row grp-gradient-mid-stop">
            <span><?php esc_html_e('Mid Position', 'google-reviews-plugin'); ?></span>
            <div class="grp-gradient-editor-range-group">
                <input type="range" id="grp-gradient-mid-pos" min="0" max="100">
                <input type="number" id="grp-gradient-mid-pos-number" min="0" max="100">
            </div>
        </div>
        <div class="grp-gradient-editor-row">
            <span><?php esc_html_e('Start Color', 'google-reviews-plugin'); ?></span>
            <div class="grp-template-color-field">
                <input type="color" id="grp-gradient-start-color">
                <input type="text" id="grp-gradient-start-color-text" style="width: 90px;">
                <div class="grp-template-color-opacity">
                    <input type="range" id="grp-gradient-start-opacity" min="0" max="100" value="100">
                    <span id="grp-gradient-start-opacity-value">100%</span>
                </div>
            </div>
        </div>
        <div class="grp-gradient-editor-row grp-gradient-mid-stop">
            <span><?php esc_html_e('Mid Color', 'google-reviews-plugin'); ?></span>
            <div class="grp-template-color-field">
                <input type="color" id="grp-gradient-mid-color">
                <input type="text" id="grp-gradient-mid-color-text" style="width: 90px;">
                <div class="grp-template-color-opacity">
                    <input type="range" id="grp-gradient-mid-opacity" min="0" max="100" value="100">
                    <span id="grp-gradient-mid-opacity-value">100%</span>
                </div>
            </div>
        </div>
        <div class="grp-gradient-editor-row">
            <span><?php esc_html_e('End Color', 'google-reviews-plugin'); ?></span>
            <div class="grp-template-color-field">
                <input type="color" id="grp-gradient-end-color">
                <input type="text" id="grp-gradient-end-color-text" style="width: 90px;">
                <div class="grp-template-color-opacity">
                    <input type="range" id="grp-gradient-end-opacity" min="0" max="100" value="100">
                    <span id="grp-gradient-end-opacity-value">100%</span>
                </div>
            </div>
        </div>
        <div class="grp-gradient-editor-preview" id="grp-gradient-editor-preview"></div>
        <div class="grp-gradient-editor-actions">
            <button type="button" class="button" id="grp-gradient-editor-cancel"><?php esc_html_e('Cancel', 'google-reviews-plugin'); ?></button>
            <button type="button" class="button button-primary" id="grp-gradient-editor-done"><?php esc_html_e('Save', 'google-reviews-plugin'); ?></button>
        </div>
    </div>
</div>

