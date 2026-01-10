<?php
if (!isset($font_sans_serif)) {
    $font_sans_serif = array(
        'Inter, sans-serif' => __('Inter', 'google-reviews-plugin'),
        'Roboto, sans-serif' => __('Roboto', 'google-reviews-plugin'),
        'Open Sans, sans-serif' => __('Open Sans', 'google-reviews-plugin'),
        'Lato, sans-serif' => __('Lato', 'google-reviews-plugin'),
        'Montserrat, sans-serif' => __('Montserrat', 'google-reviews-plugin'),
        'Poppins, sans-serif' => __('Poppins', 'google-reviews-plugin'),
        'Raleway, sans-serif' => __('Raleway', 'google-reviews-plugin'),
        'Nunito, sans-serif' => __('Nunito', 'google-reviews-plugin'),
        'Source Sans Pro, sans-serif' => __('Source Sans Pro', 'google-reviews-plugin'),
        'Ubuntu, sans-serif' => __('Ubuntu', 'google-reviews-plugin'),
        'Work Sans, sans-serif' => __('Work Sans', 'google-reviews-plugin'),
        'DM Sans, sans-serif' => __('DM Sans', 'google-reviews-plugin'),
    );
}
if (!isset($font_serif)) {
    $font_serif = array(
        'Merriweather, serif' => __('Merriweather', 'google-reviews-plugin'),
        'Lora, serif' => __('Lora', 'google-reviews-plugin'),
        'Playfair Display, serif' => __('Playfair Display', 'google-reviews-plugin'),
        'PT Serif, serif' => __('PT Serif', 'google-reviews-plugin'),
        'Crimson Text, serif' => __('Crimson Text', 'google-reviews-plugin'),
        'Libre Baskerville, serif' => __('Libre Baskerville', 'google-reviews-plugin'),
        'Bitter, serif' => __('Bitter', 'google-reviews-plugin'),
        'Georgia, serif' => __('Georgia', 'google-reviews-plugin'),
    );
}
if (!isset($font_display)) {
    $font_display = array(
        'Oswald, sans-serif' => __('Oswald', 'google-reviews-plugin'),
        'Bebas Neue, sans-serif' => __('Bebas Neue', 'google-reviews-plugin'),
        'Righteous, cursive' => __('Righteous', 'google-reviews-plugin'),
    );
}
?>
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
<div id="grp-template-editor-modal" class="grp-template-editor-modal" style="display: none;">
    <div class="grp-template-editor-content">
        <button type="button" class="grp-template-editor-close button-link">×</button>
        <h3><?php esc_html_e('Customize Template', 'google-reviews-plugin'); ?></h3>
        <div id="grp-template-editor-preview" class="grp-template-editor-preview"></div>
        <div class="grp-template-editor-controls">
            <div class="grp-template-editor-column">
                <div class="grp-template-editor-row" data-templates="layout1 layout2 layout3 creative-pro">
                    <span class="grp-template-editor-label"><?php esc_html_e('Show logo/icon', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <label class="grp-template-checkbox">
                            <input type="checkbox" id="grp-modal-show-logo">
                            <span><?php esc_html_e('Enable', 'google-reviews-plugin'); ?></span>
                        </label>
                    </div>
                </div>
                <div class="grp-template-editor-row" data-templates="layout1 layout2 layout3 creative-pro">
                    <span class="grp-template-editor-label"><?php esc_html_e('Logo Scale (%)', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field grp-template-editor-slider">
                        <input type="range" id="grp-modal-logo-scale-slider" min="10" max="100">
                        <input type="number" id="grp-modal-logo-scale-number" min="10" max="100">
                    </div>
                </div>
                <div class="grp-template-editor-row" data-templates="layout1 layout2 layout3 creative-pro basic">
                    <span class="grp-template-editor-label"><?php esc_html_e('Font Family', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <select id="grp-modal-font-family">
                            <option value=""><?php esc_html_e('Inherit (Theme Font)', 'google-reviews-plugin'); ?></option>
                            <option value="inherit"><?php esc_html_e('Inherit', 'google-reviews-plugin'); ?></option>
                            <optgroup label="<?php esc_attr_e('Sans-Serif', 'google-reviews-plugin'); ?>">
                                <?php foreach ($font_sans_serif as $value => $label): ?>
                                    <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="<?php esc_attr_e('Serif', 'google-reviews-plugin'); ?>">
                                <?php foreach ($font_serif as $value => $label): ?>
                                    <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="<?php esc_attr_e('Display', 'google-reviews-plugin'); ?>">
                                <?php foreach ($font_display as $value => $label): ?>
                                    <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="grp-template-editor-row" data-templates="basic layout1 layout2 layout3 creative-pro">
                    <span class="grp-template-editor-label"><?php esc_html_e('Text Color', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field grp-template-color-field">
                        <input type="color" id="grp-modal-text-color">
                        <input type="text" id="grp-modal-text-color-text">
                        <div class="grp-template-color-opacity">
                            <input type="range" id="grp-modal-text-color-opacity" min="0" max="100" value="100">
                            <span id="grp-modal-text-color-opacity-value">100%</span>
                        </div>
                    </div>
                </div>
                <div class="grp-template-editor-row" data-templates="layout2 layout3 creative-pro">
                    <span class="grp-template-editor-label"><?php esc_html_e('Link Text', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <input type="text" id="grp-modal-link-text" placeholder="<?php esc_attr_e('Click here', 'google-reviews-plugin'); ?>">
                    </div>
                </div>
                <div class="grp-template-editor-row" data-templates="layout2 layout3 creative-pro">
                    <span class="grp-template-editor-label"><?php esc_html_e('Message Text', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <input type="text" id="grp-modal-message-text" placeholder="<?php esc_attr_e('Scan the QR code to leave a review', 'google-reviews-plugin'); ?>">
                    </div>
                </div>
            </div>
            <div class="grp-template-editor-column">
                <div class="grp-template-editor-row" data-templates="basic layout1 layout2 layout3 creative-pro">
                    <span class="grp-template-editor-label"><?php esc_html_e('Background Color', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field grp-template-color-field">
                        <input type="color" id="grp-modal-background-color">
                        <input type="text" id="grp-modal-background-color-text">
                        <div class="grp-template-color-opacity">
                            <input type="range" id="grp-modal-background-color-opacity" min="0" max="100" value="100">
                            <span id="grp-modal-background-color-opacity-value">100%</span>
                        </div>
                    </div>
                </div>
                <div class="grp-template-editor-row" data-templates="basic layout1 layout2 layout3 creative-pro">
                    <span class="grp-template-editor-label"><?php esc_html_e('Star Color', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field grp-template-color-field">
                        <input type="color" id="grp-modal-star-color">
                        <input type="text" id="grp-modal-star-color-text">
                        <div class="grp-template-color-opacity">
                            <input type="range" id="grp-modal-star-color-opacity" min="0" max="100" value="100">
                            <span id="grp-modal-star-color-opacity-value">100%</span>
                        </div>
                    </div>
                </div>
                <div class="grp-template-editor-row" data-templates="layout2 layout3 creative-pro">
                    <span class="grp-template-editor-label"><?php esc_html_e('Link Color', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field grp-template-color-field">
                        <input type="color" id="grp-modal-link-color">
                        <input type="text" id="grp-modal-link-color-text">
                        <div class="grp-template-color-opacity">
                            <input type="range" id="grp-modal-link-color-opacity" min="0" max="100" value="100">
                            <span id="grp-modal-link-color-opacity-value">100%</span>
                        </div>
                    </div>
                </div>
                <div class="grp-template-editor-row" data-templates="layout2 layout3 creative-pro">
                    <span class="grp-template-editor-label"><?php esc_html_e('Star Placement', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <select id="grp-modal-star-placement">
                            <option value="below"><?php esc_html_e('Below QR', 'google-reviews-plugin'); ?></option>
                            <option value="above"><?php esc_html_e('Above QR', 'google-reviews-plugin'); ?></option>
                            <option value="overlay"><?php esc_html_e('Overlay QR', 'google-reviews-plugin'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="grp-template-editor-row" data-templates="layout1 layout2 layout3 creative-pro">
                    <span class="grp-template-editor-label"><?php esc_html_e('Glass Effect', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <label class="grp-template-checkbox">
                            <input type="checkbox" id="grp-modal-glass-effect">
                            <span><?php esc_html_e('Enable', 'google-reviews-plugin'); ?></span>
                        </label>
                    </div>
                </div>
                <div class="grp-template-editor-row" data-templates="basic layout1 layout2 layout3 creative-pro">
                    <span class="grp-template-editor-label"><?php esc_html_e('Box Shadow', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <label class="grp-template-checkbox">
                            <input type="checkbox" id="grp-modal-box-shadow-enabled">
                            <span><?php esc_html_e('Enable', 'google-reviews-plugin'); ?></span>
                        </label>
                        <button type="button" class="button" id="grp-template-editor-box-shadow-edit"><?php esc_html_e('Edit', 'google-reviews-plugin'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="grp-template-editor-controls">
            <div class="grp-template-editor-column">
                <div class="grp-template-editor-row grp-inline-spacing-row" data-templates="layout1 layout2 layout3 creative-pro">
                    <span class="grp-template-editor-label"><?php esc_html_e('Padding', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field grp-spacing-group">
                        <div class="grp-spacing-input">
                            <input type="number" id="grp-modal-padding-top" min="0" step="1" value="0">
                            <span><?php esc_html_e('Top', 'google-reviews-plugin'); ?></span>
                        </div>
                        <div class="grp-spacing-input">
                            <input type="number" id="grp-modal-padding-right" min="0" step="1" value="0">
                            <span><?php esc_html_e('Right', 'google-reviews-plugin'); ?></span>
                        </div>
                        <div class="grp-spacing-input">
                            <input type="number" id="grp-modal-padding-bottom" min="0" step="1" value="0">
                            <span><?php esc_html_e('Bottom', 'google-reviews-plugin'); ?></span>
                        </div>
                        <div class="grp-spacing-input">
                            <input type="number" id="grp-modal-padding-left" min="0" step="1" value="0">
                            <span><?php esc_html_e('Left', 'google-reviews-plugin'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grp-template-editor-column">
                <div class="grp-template-editor-row grp-inline-spacing-row" data-templates="layout1 layout2 layout3 creative-pro">
                    <span class="grp-template-editor-label"><?php esc_html_e('Border Radius', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field grp-spacing-group">
                        <div class="grp-spacing-input">
                            <input type="number" id="grp-modal-border-top-left" min="0" step="1" value="0">
                            <span><?php esc_html_e('Top Left', 'google-reviews-plugin'); ?></span>
                        </div>
                        <div class="grp-spacing-input">
                            <input type="number" id="grp-modal-border-top-right" min="0" step="1" value="0">
                            <span><?php esc_html_e('Top Right', 'google-reviews-plugin'); ?></span>
                        </div>
                        <div class="grp-spacing-input">
                            <input type="number" id="grp-modal-border-bottom-right" min="0" step="1" value="0">
                            <span><?php esc_html_e('Bottom Right', 'google-reviews-plugin'); ?></span>
                        </div>
                        <div class="grp-spacing-input">
                            <input type="number" id="grp-modal-border-bottom-left" min="0" step="1" value="0">
                            <span><?php esc_html_e('Bottom Left', 'google-reviews-plugin'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="grp-template-editor-gradient-section" class="grp-template-editor-section" data-templates="creative-pro">
            <div class="grp-template-editor-row grp-gradient-summary-row">
                <span class="grp-template-editor-label"><?php esc_html_e('Gradient', 'google-reviews-plugin'); ?></span>
                <div class="grp-template-editor-field grp-gradient-summary-field">
                    <div id="grp-gradient-summary-preview" class="grp-gradient-summary-preview"></div>
                    <button type="button" class="button" id="grp-gradient-editor-open"><?php esc_html_e('Edit Gradient', 'google-reviews-plugin'); ?></button>
                </div>
            </div>
        </div>
        <div class="grp-template-editor-footer">
            <button type="button" class="button" id="grp-template-editor-close"><?php esc_html_e('Done', 'google-reviews-plugin'); ?></button>
        </div>
    </div>
</div>
