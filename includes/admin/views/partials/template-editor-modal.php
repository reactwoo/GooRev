<div id="grp-template-editor-modal" class="grp-template-editor-modal" style="display: none;">
    <div class="grp-template-editor-content">
        <button type="button" class="grp-template-editor-close button-link">×</button>
        <h3><?php esc_html_e('Customize Template', 'google-reviews-plugin'); ?></h3>
        <div id="grp-template-editor-preview" class="grp-template-editor-preview"></div>
        <div class="grp-template-editor-controls">
            <div class="grp-template-editor-column">
                <div class="grp-template-editor-row">
                    <span class="grp-template-editor-label"><?php esc_html_e('Show logo/icon', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <label class="grp-template-checkbox">
                            <input type="checkbox" id="grp-modal-show-logo">
                            <span><?php esc_html_e('Enable', 'google-reviews-plugin'); ?></span>
                        </label>
                    </div>
                </div>
                <div class="grp-template-editor-row">
                    <span class="grp-template-editor-label"><?php esc_html_e('Logo Scale (%)', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field grp-template-editor-slider">
                        <input type="range" id="grp-modal-logo-scale-slider" min="10" max="100">
                        <input type="number" id="grp-modal-logo-scale-number" min="10" max="100">
                    </div>
                </div>
                <div class="grp-template-editor-row">
                    <span class="grp-template-editor-label"><?php esc_html_e('Font Family', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <select id="grp-modal-font-family"></select>
                    </div>
                </div>
                <div class="grp-template-editor-row">
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
                <div class="grp-template-editor-row grp-modal-link-row">
                    <span class="grp-template-editor-label"><?php esc_html_e('Link Text', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <input type="text" id="grp-modal-link-text" placeholder="<?php esc_attr_e('Click here', 'google-reviews-plugin'); ?>">
                    </div>
                </div>
                <div class="grp-template-editor-row">
                    <span class="grp-template-editor-label"><?php esc_html_e('Message Text', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <input type="text" id="grp-modal-message-text" placeholder="<?php esc_attr_e('Scan the QR code to leave a review', 'google-reviews-plugin'); ?>">
                    </div>
                </div>
                <div class="grp-template-editor-row">
                    <span class="grp-template-editor-label"><?php esc_html_e('Padding Top', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <input type="number" id="grp-modal-padding-top" min="0" step="1" value="0" style="width: 90px;">
                    </div>
                </div>
                <div class="grp-template-editor-row">
                    <span class="grp-template-editor-label"><?php esc_html_e('Padding Bottom', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <input type="number" id="grp-modal-padding-bottom" min="0" step="1" value="0" style="width: 90px;">
                    </div>
                </div>
                <div class="grp-template-editor-row">
                    <span class="grp-template-editor-label"><?php esc_html_e('Border Radius Top Left', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <input type="number" id="grp-modal-border-top-left" min="0" step="1" value="0" style="width: 90px;">
                    </div>
                </div>
                <div class="grp-template-editor-row">
                    <span class="grp-template-editor-label"><?php esc_html_e('Border Radius Bottom Left', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <input type="number" id="grp-modal-border-bottom-left" min="0" step="1" value="0" style="width: 90px;">
                    </div>
                </div>
            </div>
            <div class="grp-template-editor-column">
                <div class="grp-template-editor-row">
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
                <div class="grp-template-editor-row">
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
                <div class="grp-template-editor-row grp-modal-link-row">
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
                <div class="grp-template-editor-row">
                    <span class="grp-template-editor-label"><?php esc_html_e('Star Placement', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <select id="grp-modal-star-placement">
                            <option value="below"><?php esc_html_e('Below QR', 'google-reviews-plugin'); ?></option>
                            <option value="above"><?php esc_html_e('Above QR', 'google-reviews-plugin'); ?></option>
                            <option value="overlay"><?php esc_html_e('Overlay QR', 'google-reviews-plugin'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="grp-template-editor-row">
                    <span class="grp-template-editor-label"><?php esc_html_e('Glass Effect', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <label class="grp-template-checkbox">
                            <input type="checkbox" id="grp-modal-glass-effect">
                            <span><?php esc_html_e('Enable', 'google-reviews-plugin'); ?></span>
                        </label>
                    </div>
                </div>
                <div class="grp-template-editor-row">
                    <span class="grp-template-editor-label"><?php esc_html_e('Box Shadow', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <label class="grp-template-checkbox">
                            <input type="checkbox" id="grp-modal-box-shadow-enabled">
                            <span><?php esc_html_e('Enable', 'google-reviews-plugin'); ?></span>
                        </label>
                        <button type="button" class="button" id="grp-template-editor-box-shadow-edit"><?php esc_html_e('Edit', 'google-reviews-plugin'); ?></button>
                    </div>
                </div>
                <div class="grp-template-editor-row">
                    <span class="grp-template-editor-label"><?php esc_html_e('Padding Right', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <input type="number" id="grp-modal-padding-right" min="0" step="1" value="0" style="width: 90px;">
                    </div>
                </div>
                <div class="grp-template-editor-row">
                    <span class="grp-template-editor-label"><?php esc_html_e('Padding Left', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <input type="number" id="grp-modal-padding-left" min="0" step="1" value="0" style="width: 90px;">
                    </div>
                </div>
                <div class="grp-template-editor-row">
                    <span class="grp-template-editor-label"><?php esc_html_e('Border Radius Top Right', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <input type="number" id="grp-modal-border-top-right" min="0" step="1" value="0" style="width: 90px;">
                    </div>
                </div>
                <div class="grp-template-editor-row">
                    <span class="grp-template-editor-label"><?php esc_html_e('Border Radius Bottom Right', 'google-reviews-plugin'); ?></span>
                    <div class="grp-template-editor-field">
                        <input type="number" id="grp-modal-border-bottom-right" min="0" step="1" value="0" style="width: 90px;">
                    </div>
                </div>
            </div>
        </div>
        <div id="grp-template-editor-gradient-section" class="grp-template-editor-section">
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
