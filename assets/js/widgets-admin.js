/**
 * Review Widgets Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        var $templateSelect = $('#grp_widget_button_default_template');
        var $templateDescription = $('#grp-template-selected-description');
        var $templateProNote = $('#grp-template-pro-note');
        var $previewBtn = $('#grp-preview-button');
        var $previewQr = $('#grp-preview-qr');
        var $previewQrImg = $('#grp-preview-qr-img');
        var $previewTagline = $('#grp-preview-tagline');
        var $previewStarRow = $('#grp-preview-star-row');
        var $starColorInput = $('#grp_widget_template_star_color');
        var $starColorText = $('#grp_widget_template_star_color_text');
        var $starPlacementSelect = $('#grp_widget_template_star_placement');
        var $logoToggle = $('#grp_widget_template_show_logo');
        var $fontFamilyInput = $('#grp_widget_template_font_family');
        var $maxHeightInput = $('#grp_widget_template_max_height');
        var $linkColorInput = $('#grp_widget_template_link_color');
        var $linkColorText = $('#grp_widget_template_link_color_text');
        var $linkTextInput = $('#grp_widget_template_link_text');
        var $maxWidthInput = $('#grp_widget_template_max_width');
        var $boxShadowCheckbox = $('#grp_widget_template_box_shadow_enabled');
        var $boxShadowValue = $('#grp_widget_template_box_shadow_value');
        var $boxShadowEditTrigger = $('#grp-box-shadow-edit');
        var $glassCheckbox = $('#grp_widget_template_glass_effect');
        var $templateEditorModal = $('#grp-template-editor-modal');
        var $templateEditorPreview = $('#grp-template-editor-preview');
        var $modalShowLogo = $('#grp-modal-show-logo');
        var $modalLogoScaleSlider = $('#grp-modal-logo-scale-slider');
        var $modalLogoScaleNumber = $('#grp-modal-logo-scale-number');
        var $modalStarColor = $('#grp-modal-star-color');
        var $modalStarColorText = $('#grp-modal-star-color-text');
        var $modalStarPlacement = $('#grp-modal-star-placement');
        var $modalFontFamily = $('#grp-modal-font-family');
        var $modalTextColor = $('#grp-modal-text-color');
        var $modalTextColorText = $('#grp-modal-text-color-text');
        var $modalBackgroundColor = $('#grp-modal-background-color');
        var $modalBackgroundColorText = $('#grp-modal-background-color-text');
        var $modalGlassEffect = $('#grp-modal-glass-effect');
        var $modalGradientSection = $('#grp-template-editor-gradient-section');
        var $gradientSummaryPreview = $('#grp-gradient-summary-preview');
        var $gradientEditorOpen = $('#grp-gradient-editor-open');
        var $gradientEditorModal = $('#grp-gradient-editor-modal');
        var $gradientEditorClose = $('.grp-gradient-editor-close');
        var $gradientEditorDone = $('#grp-gradient-editor-done');
        var $gradientEditorCancel = $('#grp-gradient-editor-cancel');
        var $gradientEditorType = $('#grp-gradient-type');
        var $gradientEditorAngle = $('#grp-gradient-angle');
        var $gradientEditorAngleNumber = $('#grp-gradient-angle-number');
        var $gradientEditorStartPos = $('#grp-gradient-start-pos');
        var $gradientEditorStartPosNumber = $('#grp-gradient-start-pos-number');
        var $gradientEditorEndPos = $('#grp-gradient-end-pos');
        var $gradientEditorEndPosNumber = $('#grp-gradient-end-pos-number');
        var $gradientEditorMidPos = $('#grp-gradient-mid-pos');
        var $gradientEditorMidPosNumber = $('#grp-gradient-mid-pos-number');
        var $gradientEditorMidEnabled = $('#grp-gradient-mid-enabled');
        var $gradientEditorStartColor = $('#grp-gradient-start-color');
        var $gradientEditorStartColorText = $('#grp-gradient-start-color-text');
        var $gradientEditorStartOpacity = $('#grp-gradient-start-opacity');
        var $gradientEditorStartOpacityValue = $('#grp-gradient-start-opacity-value');
        var $gradientEditorMidColor = $('#grp-gradient-mid-color');
        var $gradientEditorMidColorText = $('#grp-gradient-mid-color-text');
        var $gradientEditorMidOpacity = $('#grp-gradient-mid-opacity');
        var $gradientEditorMidOpacityValue = $('#grp-gradient-mid-opacity-value');
        var $gradientEditorEndColor = $('#grp-gradient-end-color');
        var $gradientEditorEndColorText = $('#grp-gradient-end-color-text');
        var $gradientEditorEndOpacity = $('#grp-gradient-end-opacity');
        var $gradientEditorEndOpacityValue = $('#grp-gradient-end-opacity-value');
        var $gradientEditorPreview = $('#grp-gradient-editor-preview');
        var $modalLinkText = $('#grp-modal-link-text');
        var $modalLinkColor = $('#grp-modal-link-color');
        var $modalLinkColorText = $('#grp-modal-link-color-text');
        var $modalTextColorOpacity = $('#grp-modal-text-color-opacity');
        var $modalTextColorOpacityValue = $('#grp-modal-text-color-opacity-value');
        var $modalBackgroundColorOpacity = $('#grp-modal-background-color-opacity');
        var $modalBackgroundColorOpacityValue = $('#grp-modal-background-color-opacity-value');
        var $modalStarColorOpacity = $('#grp-modal-star-color-opacity');
        var $modalStarColorOpacityValue = $('#grp-modal-star-color-opacity-value');
        var $modalLinkColorOpacity = $('#grp-modal-link-color-opacity');
        var $modalLinkColorOpacityValue = $('#grp-modal-link-color-opacity-value');
        var $modalBoxShadowEnabled = $('#grp-modal-box-shadow-enabled');
        var $modalBoxShadowEdit = $('#grp-template-editor-box-shadow-edit');
        var $modalMessageText = $('#grp-modal-message-text');
        var $modalPaddingTop = $('#grp-modal-padding-top');
        var $modalPaddingRight = $('#grp-modal-padding-right');
        var $modalPaddingBottom = $('#grp-modal-padding-bottom');
        var $modalPaddingLeft = $('#grp-modal-padding-left');
        var $modalBorderTopLeft = $('#grp-modal-border-top-left');
        var $modalBorderTopRight = $('#grp-modal-border-top-right');
        var $modalBorderBottomRight = $('#grp-modal-border-bottom-right');
        var $modalBorderBottomLeft = $('#grp-modal-border-bottom-left');
        var $templateProBadge = $('#grp-template-pro-badge');
        var $logoScaleSlider = $('#grp_widget_template_logo_scale');
        var $logoScaleText = $('#grp_widget_template_logo_scale_text');
        var $gradientRows = $('.grp-gradient-row');
        var $customizeButton = $('#grp-template-editor-open');
        // Styles page (review styles) editor modal
        var $styleEditorModal = $('#grp-style-editor-modal');
        var $styleEditorPreview = $('#grp-style-editor-preview');
        var $styleVariantSelect = $('#grp-style-variant');
        var $styleBgColor = $('#grp-style-background-color');
        var $styleBgText = $('#grp-style-background-text');
        var $styleCardBg = $('#grp-style-card-background');
        var $styleTextColor = $('#grp-style-text-color');
        var $styleTextText = $('#grp-style-text-text');
        var $styleMutedColor = $('#grp-style-muted-color');
        var $styleMutedText = $('#grp-style-muted-text');
        var $styleBorderColor = $('#grp-style-border-color');
        var $styleBorderText = $('#grp-style-border-text');
        var $styleBorderEnabled = $('#grp-style-border-enabled');
        var $styleBorderTop = $('#grp-style-border-top');
        var $styleBorderRight = $('#grp-style-border-right');
        var $styleBorderBottom = $('#grp-style-border-bottom');
        var $styleBorderLeft = $('#grp-style-border-left');
        var $styleBorderWidthsWrap = $('#grp-style-border-widths');
        var $styleAccentColor = $('#grp-style-accent-color');
        var $styleAccentText = $('#grp-style-accent-text');
        var $styleStarColor = $('#grp-style-star-color');
        var $styleStarText = $('#grp-style-star-text');
        // Creative-only controls (styles modal)
        var $styleAvatarSize = $('#grp-style-avatar-size');
        var $styleGradientSummaryPreview = $('#grp-style-gradient-summary-preview');
        var $styleGradientEditorOpen = $('#grp-style-gradient-editor-open');
        var $styleGradientCss = $('#grp-style-gradient-css');
        var $styleCardRadius = $('#grp-style-card-radius'); // hidden field (CSS border-radius value)
        var $styleCardRadiusTL = $('#grp-style-card-radius-tl');
        var $styleCardRadiusTR = $('#grp-style-card-radius-tr');
        var $styleCardRadiusBR = $('#grp-style-card-radius-br');
        var $styleCardRadiusBL = $('#grp-style-card-radius-bl');
        var $styleGlassEffect = $('#grp-style-glass-effect');
        var $styleCardShadowEnabled = $('#grp-style-card-shadow-enabled');
        var $styleCardShadow = $('#grp-style-card-shadow'); // hidden field (CSS box-shadow value)
        var $styleCardShadowEdit = $('#grp-style-card-shadow-edit');
        var $styleShadowModal = $('#grp-style-box-shadow-modal');
        var $styleShadowClose = $('#grp-style-box-shadow-modal-close');
        var $styleShadowRanges = $('.grp-style-shadow-range');
        var $styleShadowNumbers = $('.grp-style-shadow-number');
        var $styleShadowColorPicker = $('#grp-style-box-shadow-color-picker');
        var $styleShadowOpacity = $('#grp-style-box-shadow-opacity');
        var $styleShadowOpacityNumber = $('#grp-style-box-shadow-opacity-number');
        var $styleFontFamily = $('#grp-style-font-family');
        var $styleHeadingWeight = $('#grp-style-heading-weight');
        var $styleBodyWeight = $('#grp-style-body-weight');
        var $styleBodyLineHeight = $('#grp-style-body-line-height');
        var $styleBodyLetterSpacing = $('#grp-style-body-letter-spacing');
        var $styleSave = $('#grp-style-editor-save');
        var $styleReset = $('#grp-style-editor-reset');
        var $styleClose = $('#grp-style-editor-close, #grp-style-editor-close-x');
        var $buttonStyleRows = $('.grp-button-style-row');
        var $buttonSizeRows = $('.grp-button-size-row');
        var $buttonTextColorRows = $('.grp-button-text-color-row');
        var $templateControlNodes = $templateEditorModal.find('[data-templates]');
        var templateMeta = (typeof grpWidgets !== 'undefined' && grpWidgets.button_templates) ? grpWidgets.button_templates : {};
        var templateClassList = Object.keys(templateMeta).map(function(key) {
            return 'grp-review-button-template-' + key;
        });
        var templateCustomizationDefaults = (typeof grpWidgets !== 'undefined' && grpWidgets.template_customization_defaults) ? grpWidgets.template_customization_defaults : {};
        var templateCustomizations = (typeof grpWidgets !== 'undefined' && grpWidgets.template_customizations) ? grpWidgets.template_customizations : {};
        var styleCustomizations = (typeof grpWidgets !== 'undefined' && grpWidgets.style_customizations) ? grpWidgets.style_customizations : {};
        var styleCustomizationDefaults = (typeof grpWidgets !== 'undefined' && grpWidgets.style_customization_defaults) ? grpWidgets.style_customization_defaults : {};
        var logoScaleTouched = false;
        var modalTemplateKey = $templateSelect.length ? ($templateSelect.val() || 'basic') : 'basic';
        var qrCache = {};
        var blankQr = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
        var isPro = false;
        var hasPlaceId = false;
        var logoUrls = {};
        var modalDirty = false;
        if (typeof grpWidgets !== 'undefined') {
            isPro = !!grpWidgets.is_pro;
            hasPlaceId = !!grpWidgets.has_place_id;
            logoUrls = grpWidgets.logo_urls || {};
            if (!isPro && grpWidgets.license_status) {
                var licenseData = grpWidgets.license_data || {};
                var packageType = (licenseData.packageType || licenseData.package_type || '').toLowerCase();
                var proPackages = ['pro', 'enterprise', 'goorev-pro', 'goorev-enterprise', 'pro_with_restricts'];
                if (proPackages.indexOf(packageType) !== -1) {
                    isPro = true;
                }
            }
        }

        // ---------------------------
        // Styles page: Style editor modal (review style preview + CSS variable overrides)
        // ---------------------------
        var activeStyleKey = '';
        var activeStyleVariant = 'light';

        function isValidHexOrShort(color) {
            return /^#([0-9A-F]{3}|[0-9A-F]{6})$/i.test(String(color || '').trim());
        }

        function toCssVarMap(overrides) {
            var map = {};
            if (!overrides) return map;
            if (overrides.background) map['--grp-background'] = overrides.background;
            if (overrides.background_alt) map['--grp-background_alt'] = overrides.background_alt;
            if (overrides.text) map['--grp-text'] = overrides.text;
            if (overrides.muted) map['--grp-muted'] = overrides.muted;
            if (overrides.border) map['--grp-border'] = overrides.border;
            if (overrides.border_top !== undefined && overrides.border_top !== null && String(overrides.border_top) !== '') map['--grp-border_width_top'] = String(overrides.border_top) + 'px';
            if (overrides.border_right !== undefined && overrides.border_right !== null && String(overrides.border_right) !== '') map['--grp-border_width_right'] = String(overrides.border_right) + 'px';
            if (overrides.border_bottom !== undefined && overrides.border_bottom !== null && String(overrides.border_bottom) !== '') map['--grp-border_width_bottom'] = String(overrides.border_bottom) + 'px';
            if (overrides.border_left !== undefined && overrides.border_left !== null && String(overrides.border_left) !== '') map['--grp-border_width_left'] = String(overrides.border_left) + 'px';
            if (overrides.star) map['--grp-star'] = overrides.star;
            if (overrides.accent) map['--grp-accent'] = overrides.accent;
            if (overrides.card_background) map['--grp-card_background'] = overrides.card_background;
            if (overrides.card_radius !== undefined && overrides.card_radius !== null && String(overrides.card_radius) !== '') {
                var r = String(overrides.card_radius).trim();
                // If it's numeric, treat as px; otherwise assume valid CSS border-radius value (e.g. "10px 20px 10px 20px")
                map['--grp-card_radius'] = (/^-?\\d+(\\.\\d+)?$/.test(r) ? (r + 'px') : r);
            }
            if (overrides.card_shadow) map['--grp-card_shadow'] = overrides.card_shadow;
            if (overrides.font_family) map['--grp-font_family'] = overrides.font_family;
            if (overrides.heading_font_weight) map['--grp-heading_font_weight'] = String(overrides.heading_font_weight);
            if (overrides.body_font_weight) map['--grp-body_font_weight'] = String(overrides.body_font_weight);
            if (overrides.body_line_height) map['--grp-body_line_height'] = String(overrides.body_line_height);
            if (overrides.body_letter_spacing !== undefined && overrides.body_letter_spacing !== null && String(overrides.body_letter_spacing) !== '') {
                map['--grp-body_letter_spacing'] = String(overrides.body_letter_spacing) + 'px';
            }
            if (overrides.glass_blur !== undefined && overrides.glass_blur !== null && String(overrides.glass_blur) !== '') {
                var gb = String(overrides.glass_blur).trim();
                map['--grp-glass_blur'] = (/^-?\\d+(\\.\\d+)?$/.test(gb) ? (gb + 'px') : gb);
            }
            if (overrides.gradient_blue) map['--grp-gradient_blue'] = overrides.gradient_blue;
            if (overrides.gradient_red) map['--grp-gradient_red'] = overrides.gradient_red;
            if (overrides.gradient_yellow) map['--grp-gradient_yellow'] = overrides.gradient_yellow;
            if (overrides.gradient_green) map['--grp-gradient_green'] = overrides.gradient_green;
            if (overrides.gradient_css) map['--grp-gradient_css'] = overrides.gradient_css;
            if (overrides.avatar_size !== undefined && overrides.avatar_size !== null && String(overrides.avatar_size) !== '') {
                var asz = String(overrides.avatar_size).trim();
                map['--grp-avatar_size'] = (/^-?\d+(\.\d+)?$/.test(asz) ? (asz + 'px') : asz);
            }
            return map;
        }

        function applyStyleVars($node, vars) {
            if (!$node || !$node.length) return;
            Object.keys(vars || {}).forEach(function(key) {
                $node.css(key, vars[key]);
            });
        }

        function getStyleDefaults(styleKey, variant) {
            if (!styleCustomizationDefaults || !styleCustomizationDefaults[styleKey]) return {};
            if (styleCustomizationDefaults[styleKey][variant]) return styleCustomizationDefaults[styleKey][variant];
            return styleCustomizationDefaults[styleKey].light || {};
        }

        function getStyleStored(styleKey, variant) {
            if (!styleCustomizations || !styleCustomizations[styleKey] || !styleCustomizations[styleKey][variant]) return {};
            return styleCustomizations[styleKey][variant] || {};
        }

        function getEffectiveStyle(styleKey, variant) {
            return $.extend({}, getStyleDefaults(styleKey, variant), getStyleStored(styleKey, variant));
        }

        function syncColorPair($color, $text) {
            var val = String($text.val() || '').trim();
            if (isValidHexOrShort(val)) {
                $color.val(val);
            }
        }

        function clampNumber(val, min, max, fallback) {
            var n = parseFloat(val);
            if (isNaN(n)) return fallback;
            if (typeof min === 'number') n = Math.max(min, n);
            if (typeof max === 'number') n = Math.min(max, n);
            return n;
        }

        function parseRadiusToCorners(value, fallback) {
            var fb = (typeof fallback === 'number' ? fallback : 0);
            var raw = String(value || '').trim();
            if (!raw) {
                return { tl: fb, tr: fb, br: fb, bl: fb };
            }
            // Accept "14", "14px", "14px 10px 8px 6px"
            var parts = raw
                .replace(/px/g, '')
                .trim()
                .split(/\s+/)
                .map(function(p) { return clampNumber(p, 0, 80, fb); });
            if (!parts.length) return { tl: fb, tr: fb, br: fb, bl: fb };
            if (parts.length === 1) return { tl: parts[0], tr: parts[0], br: parts[0], bl: parts[0] };
            if (parts.length === 2) return { tl: parts[0], tr: parts[1], br: parts[0], bl: parts[1] };
            if (parts.length === 3) return { tl: parts[0], tr: parts[1], br: parts[2], bl: parts[1] };
            return { tl: parts[0], tr: parts[1], br: parts[2], bl: parts[3] };
        }

        function updateStyleRadiusHidden() {
            if (!$styleCardRadius.length) return '';
            var tl = clampNumber($styleCardRadiusTL.val(), 0, 80, 0);
            var tr = clampNumber($styleCardRadiusTR.val(), 0, 80, 0);
            var br = clampNumber($styleCardRadiusBR.val(), 0, 80, 0);
            var bl = clampNumber($styleCardRadiusBL.val(), 0, 80, 0);
            var css = [tl + 'px', tr + 'px', br + 'px', bl + 'px'].join(' ');
            $styleCardRadius.val(css);
            return css;
        }

        function parseBoxShadowParts(shadow) {
            // Expected: "0px 8px 32px 0px rgba(0,0,0,0.12)" or "... #000000"
            var s = String(shadow || '').trim();
            if (!s || s === 'none') return null;
            var m = s.match(/(-?\d+(?:\.\d+)?)px\s+(-?\d+(?:\.\d+)?)px\s+(\d+(?:\.\d+)?)px\s+(-?\d+(?:\.\d+)?)px\s+(.+)$/);
            if (!m) return null;
            return {
                h: m[1],
                v: m[2],
                blur: m[3],
                spread: m[4],
                color: String(m[5] || '').trim()
            };
        }

        function buildBoxShadowString(h, v, blur, spread, color) {
            var c = String(color || '#000000').trim();
            return String(h) + 'px ' + String(v) + 'px ' + String(blur) + 'px ' + String(spread) + 'px ' + c;
        }

        function hexToRgba(hex, opacityPercent) {
            var h = String(hex || '').replace('#', '').trim();
            if (h.length === 3) {
                h = h[0] + h[0] + h[1] + h[1] + h[2] + h[2];
            }
            if (h.length !== 6) return 'rgba(0,0,0,' + (opacityPercent/100) + ')';
            var r = parseInt(h.substring(0,2), 16);
            var g = parseInt(h.substring(2,4), 16);
            var b = parseInt(h.substring(4,6), 16);
            var a = Math.max(0, Math.min(100, parseInt(opacityPercent || 0, 10))) / 100;
            return 'rgba(' + r + ',' + g + ',' + b + ',' + a + ')';
        }

        function updateStyleEditorPreviewFromInputs() {
            if (!$styleEditorPreview.length) return;
            var $previewInner = $styleEditorPreview.find('.grp-style-preview').first();
            if (!$previewInner.length) return;

            var radiusCss = updateStyleRadiusHidden();
            var shadowCss = String($styleCardShadow.val() || '').trim();
            var glassBlur = $styleGlassEffect.is(':checked') ? 12 : 0;

            // When glass is enabled, auto-adjust the key colors if the user hasn't changed them away from defaults.
            var defaults = getStyleDefaults(activeStyleKey, activeStyleVariant) || {};
            var autoGlassBackground = '#0F172A';
            var autoGlassCardBg = 'rgba(255,255,255,0.08)';
            var autoGlassBorder = 'rgba(255,255,255,0.15)';

            if ($styleGlassEffect.is(':checked')) {
                if (String($styleBgText.val() || '').trim() === String(defaults.background || '').trim()) {
                    $styleBgText.val(autoGlassBackground);
                    $styleBgColor.val(autoGlassBackground);
                }
                if (String($styleCardBg.val() || '').trim() === String(defaults.card_background || '').trim()) {
                    $styleCardBg.val(autoGlassCardBg);
                }
                if (String($styleBorderText.val() || '').trim() === String(defaults.border || '').trim()) {
                    $styleBorderText.val(autoGlassBorder);
                    // Keep picker in a sane state (can't show rgba)
                    $styleBorderColor.val('#ffffff');
                }
            } else {
                // When glass is turned off, revert auto values back to defaults (only if still on the auto values)
                if (String($styleBgText.val() || '').trim() === autoGlassBackground && defaults.background) {
                    $styleBgText.val(defaults.background);
                    if (isValidHexOrShort(defaults.background)) $styleBgColor.val(defaults.background);
                }
                if (String($styleCardBg.val() || '').trim() === autoGlassCardBg && defaults.card_background) {
                    $styleCardBg.val(defaults.card_background);
                }
                if (String($styleBorderText.val() || '').trim() === autoGlassBorder && defaults.border) {
                    $styleBorderText.val(defaults.border);
                    if (isValidHexOrShort(defaults.border)) $styleBorderColor.val(defaults.border);
                }
            }
            var borderEnabled = $styleBorderEnabled.is(':checked');
            var bwTop = borderEnabled ? clampNumber($styleBorderTop.val(), 0, 20, 0) : 0;
            var bwRight = borderEnabled ? clampNumber($styleBorderRight.val(), 0, 20, 0) : 0;
            var bwBottom = borderEnabled ? clampNumber($styleBorderBottom.val(), 0, 20, 0) : 0;
            var bwLeft = borderEnabled ? clampNumber($styleBorderLeft.val(), 0, 20, 0) : 0;
            if ($styleBorderWidthsWrap.length) {
                $styleBorderWidthsWrap.css('opacity', borderEnabled ? '1' : '0.5');
            }

            var overrides = {
                background: String($styleBgText.val() || '').trim(),
                text: String($styleTextText.val() || '').trim(),
                muted: String($styleMutedText.val() || '').trim(),
                border: String($styleBorderText.val() || '').trim(),
                border_top: bwTop,
                border_right: bwRight,
                border_bottom: bwBottom,
                border_left: bwLeft,
                accent: String($styleAccentText.val() || '').trim(),
                star: String($styleStarText.val() || '').trim(),
                card_background: String($styleCardBg.val() || '').trim(),
                card_radius: String(radiusCss || '').trim(),
                card_shadow: shadowCss,
                avatar_size: String($styleAvatarSize.val() || '').trim(),
                gradient_css: String($styleGradientCss.val() || '').trim(),
                font_family: String($styleFontFamily.val() || '').trim(),
                heading_font_weight: String($styleHeadingWeight.val() || '').trim(),
                body_font_weight: String($styleBodyWeight.val() || '').trim(),
                body_line_height: String($styleBodyLineHeight.val() || '').trim(),
                body_letter_spacing: String($styleBodyLetterSpacing.val() || '').trim(),
                glass_blur: String(glassBlur)
            };
            applyStyleVars($previewInner, toCssVarMap(overrides));

            // Darken preview background when glass is enabled (like widgets template modal)
            if ($styleEditorPreview.length) {
                $styleEditorPreview.toggleClass('grp-glass-preview', $styleGlassEffect.is(':checked'));
            }
        }

        function populateStyleEditor(styleKey, variant) {
            if (!$styleEditorModal.length || !$styleEditorPreview.length) return;
            if (!styleKey) return;
            activeStyleKey = styleKey;
            activeStyleVariant = variant || 'light';
            $styleVariantSelect.val(activeStyleVariant);

            var $source = $('.grp-style-card[data-style="' + styleKey + '"]').find('.grp-style-preview').first();
            var $cloned = $source.clone(true, true);
            if (!$cloned.length) {
                $cloned = $('<div class="grp-style-preview grp-style-' + styleKey + ' grp-theme-' + activeStyleVariant + '"><div class="grp-review"><div class="grp-review-rating"><span class="grp-star grp-star-full">★</span><span class="grp-star grp-star-full">★</span><span class="grp-star grp-star-full">★</span><span class="grp-star grp-star-full">★</span><span class="grp-star grp-star-full">★</span></div><div class="grp-review-text">Sample review</div><div class="grp-review-meta"><div class="grp-author-name">John Doe</div><div class="grp-review-date">Jan 1, 2026</div></div></div></div>');
            }

            $cloned.removeClass('grp-theme-light grp-theme-dark grp-theme-auto').addClass('grp-theme-' + activeStyleVariant);

            var effective = getEffectiveStyle(styleKey, activeStyleVariant);
            $styleBgText.val(effective.background || '');
            $styleTextText.val(effective.text || '');
            $styleMutedText.val(effective.muted || '');
            $styleBorderText.val(effective.border || '');
            // Border widths (defaults depend on style; fallback to 0)
            var bTop = clampNumber(effective.border_top, 0, 20, 0);
            var bRight = clampNumber(effective.border_right, 0, 20, 0);
            var bBottom = clampNumber(effective.border_bottom, 0, 20, 0);
            var bLeft = clampNumber(effective.border_left, 0, 20, 0);
            $styleBorderTop.val(bTop);
            $styleBorderRight.val(bRight);
            $styleBorderBottom.val(bBottom);
            $styleBorderLeft.val(bLeft);
            $styleBorderEnabled.prop('checked', (bTop + bRight + bBottom + bLeft) > 0);
            $styleAccentText.val(effective.accent || '');
            $styleStarText.val(effective.star || '');
            $styleCardBg.val(effective.card_background || '');
            // Creative-only defaults
            $styleAvatarSize.val(effective.avatar_size !== undefined ? effective.avatar_size : '');
            if (activeStyleKey === 'creative') {
                var fallbackGradient = 'linear-gradient(135deg, #4285F4 0%, #EA4335 100%)';
                $styleGradientCss.val(effective.gradient_css || fallbackGradient);
            } else {
                $styleGradientCss.val(effective.gradient_css || '');
            }
            // Radius: store as CSS border-radius value in hidden field, but edit as 4 corners
            var radiusCorners = parseRadiusToCorners(effective.card_radius, 14);
            $styleCardRadiusTL.val(radiusCorners.tl);
            $styleCardRadiusTR.val(radiusCorners.tr);
            $styleCardRadiusBR.val(radiusCorners.br);
            $styleCardRadiusBL.val(radiusCorners.bl);
            updateStyleRadiusHidden();

            // Shadow: stored as CSS box-shadow value in hidden field
            var shadow = String(effective.card_shadow || '').trim();
            if (!shadow || shadow === 'none') {
                $styleCardShadowEnabled.prop('checked', false);
                $styleCardShadow.val('none');
            } else {
                $styleCardShadowEnabled.prop('checked', true);
                $styleCardShadow.val(shadow);
            }

            // Glass: stored as blur amount (px) to allow pure-CSS application
            var gb = clampNumber(effective.glass_blur, 0, 30, 0);
            $styleGlassEffect.prop('checked', gb > 0);
            $styleFontFamily.val(effective.font_family || '');
            $styleHeadingWeight.val(effective.heading_font_weight || '');
            $styleBodyWeight.val(effective.body_font_weight || '');
            $styleBodyLineHeight.val(effective.body_line_height || '');
            $styleBodyLetterSpacing.val(effective.body_letter_spacing !== undefined ? effective.body_letter_spacing : '');

            if (isValidHexOrShort($styleBgText.val())) $styleBgColor.val($styleBgText.val());
            if (isValidHexOrShort($styleTextText.val())) $styleTextColor.val($styleTextText.val());
            if (isValidHexOrShort($styleMutedText.val())) $styleMutedColor.val($styleMutedText.val());
            if (isValidHexOrShort($styleBorderText.val())) $styleBorderColor.val($styleBorderText.val());
            if (isValidHexOrShort($styleAccentText.val())) $styleAccentColor.val($styleAccentText.val());
            if (isValidHexOrShort($styleStarText.val())) $styleStarColor.val($styleStarText.val());
            // Update gradient preview chip if provided
            if ($styleGradientSummaryPreview.length) {
                var gcss = String($styleGradientCss.val() || '').trim();
                if (gcss) $styleGradientSummaryPreview.css('background', gcss);
            }

            $styleEditorPreview.empty().append($cloned);
            applyStyleVars($cloned, toCssVarMap(effective));
            updateStyleEditorPreviewFromInputs();

            // Show/hide creative-only vs non-creative controls
            var isCreative = (activeStyleKey === 'creative');
            $styleEditorModal.toggleClass('grp-style-is-creative', isCreative);
            $styleEditorModal.find('.grp-style-creative-only').toggle(isCreative);
            $styleEditorModal.find('.grp-style-non-creative-only').toggle(!isCreative);
            if (isCreative) {
                // Creative: lock variant and hide selector (stored under light)
                $styleVariantSelect.val('light');
            }
        }

        function closeStyleEditor() {
            if ($styleEditorModal.length) {
                $styleEditorModal.removeClass('grp-template-active').hide();
            }
        }

        function persistStyleCustomizations(styleKey, variant, data, onDone) {
            if (!styleKey || !variant) return;
            $.post((typeof grpWidgets !== 'undefined' ? grpWidgets.ajax_url : ajaxurl), {
                action: 'grp_save_style_customization',
                nonce: (typeof grpWidgets !== 'undefined' ? grpWidgets.nonce : ''),
                style: styleKey,
                variant: variant,
                data: data || {}
            }, function(resp) {
                if (resp && resp.success) {
                    if (!styleCustomizations[styleKey]) styleCustomizations[styleKey] = {};
                    styleCustomizations[styleKey][variant] = (resp.data && resp.data.data) ? resp.data.data : (data || {});
                    var $cardPreview = $('.grp-style-card[data-style="' + styleKey + '"]').find('.grp-style-preview').first();
                    applyStyleVars($cardPreview, toCssVarMap(getEffectiveStyle(styleKey, variant)));
                }
                if (typeof onDone === 'function') onDone(resp);
            });
        }

        window.grpOpenStyleModal = function(styleKey, variant) {
            if (!$styleEditorModal.length) return;
            populateStyleEditor(styleKey, variant || 'light');
            $styleEditorModal.css('display', 'flex').addClass('grp-template-active');
        };

        // Wire modal controls if present
        if ($styleEditorModal.length) {
            $styleVariantSelect.on('change', function() {
                populateStyleEditor(activeStyleKey, $(this).val() || 'light');
            });

            $styleBgColor.on('change', function() { $styleBgText.val($(this).val()); updateStyleEditorPreviewFromInputs(); });
            $styleTextColor.on('change', function() { $styleTextText.val($(this).val()); updateStyleEditorPreviewFromInputs(); });
            $styleMutedColor.on('change', function() { $styleMutedText.val($(this).val()); updateStyleEditorPreviewFromInputs(); });
            $styleBorderColor.on('change', function() { $styleBorderText.val($(this).val()); updateStyleEditorPreviewFromInputs(); });
            $styleAccentColor.on('change', function() { $styleAccentText.val($(this).val()); updateStyleEditorPreviewFromInputs(); });
            $styleStarColor.on('change', function() { $styleStarText.val($(this).val()); updateStyleEditorPreviewFromInputs(); });

            $styleBgText.on('input', function() { syncColorPair($styleBgColor, $styleBgText); updateStyleEditorPreviewFromInputs(); });
            $styleTextText.on('input', function() { syncColorPair($styleTextColor, $styleTextText); updateStyleEditorPreviewFromInputs(); });
            $styleMutedText.on('input', function() { syncColorPair($styleMutedColor, $styleMutedText); updateStyleEditorPreviewFromInputs(); });
            $styleBorderText.on('input', function() { syncColorPair($styleBorderColor, $styleBorderText); updateStyleEditorPreviewFromInputs(); });
            $styleAccentText.on('input', function() { syncColorPair($styleAccentColor, $styleAccentText); updateStyleEditorPreviewFromInputs(); });
            $styleStarText.on('input', function() { syncColorPair($styleStarColor, $styleStarText); updateStyleEditorPreviewFromInputs(); });
            $styleCardBg.on('input', updateStyleEditorPreviewFromInputs);
            $styleBorderEnabled.on('change', updateStyleEditorPreviewFromInputs);
            $styleBorderTop.on('input', updateStyleEditorPreviewFromInputs);
            $styleBorderRight.on('input', updateStyleEditorPreviewFromInputs);
            $styleBorderBottom.on('input', updateStyleEditorPreviewFromInputs);
            $styleBorderLeft.on('input', updateStyleEditorPreviewFromInputs);
            $styleAvatarSize.on('input', updateStyleEditorPreviewFromInputs);

            // Creative gradient editor (reuse existing modal UX)
            function setStyleGradientCss(css) {
                $styleGradientCss.val(css);
                if ($styleGradientSummaryPreview.length) $styleGradientSummaryPreview.css('background', css || 'transparent');
                updateStyleEditorPreviewFromInputs();
            }

            function parseGradientCssForEditor(css) {
                // Best-effort parsing (fallback to defaults)
                var out = {
                    type: 'linear',
                    angle: 135,
                    startPos: 0,
                    endPos: 100,
                    startColor: '#4285F4',
                    startOpacity: 100,
                    endColor: '#EA4335',
                    endOpacity: 100
                };
                var s = String(css || '').trim();
                if (!s) return out;
                // linear-gradient(135deg, rgba(...) 0%, rgba(...) 100%)
                var mLin = s.match(/linear-gradient\(\s*([0-9.]+)deg\s*,\s*(.+)\s*\)/i);
                if (mLin) {
                    out.type = 'linear';
                    out.angle = parseFloat(mLin[1]) || out.angle;
                    var stops = mLin[2].split(',');
                    // Not perfect, so keep defaults unless we can detect hex
                    return out;
                }
                var mRad = s.match(/radial-gradient\(\s*circle\s*,\s*(.+)\s*\)/i);
                if (mRad) {
                    out.type = 'radial';
                    return out;
                }
                return out;
            }

            if ($styleGradientEditorOpen.length && $gradientEditorModal.length) {
                $styleGradientEditorOpen.on('click', function(e) {
                    e.preventDefault();
                    if (activeStyleKey !== 'creative') return;

                    var preset = parseGradientCssForEditor($styleGradientCss.val());
                    $gradientEditorType.val(preset.type);
                    $gradientEditorAngle.val(preset.angle);
                    $gradientEditorAngleNumber.val(preset.angle);
                    $gradientEditorStartPos.val(preset.startPos);
                    $gradientEditorStartPosNumber.val(preset.startPos);
                    $gradientEditorEndPos.val(preset.endPos);
                    $gradientEditorEndPosNumber.val(preset.endPos);
                    $gradientEditorStartColor.val(preset.startColor);
                    $gradientEditorStartColorText.val(preset.startColor);
                    $gradientEditorStartOpacity.val(preset.startOpacity);
                    $gradientEditorStartOpacityValue.text(preset.startOpacity + '%');
                    $gradientEditorEndColor.val(preset.endColor);
                    $gradientEditorEndColorText.val(preset.endColor);
                    $gradientEditorEndOpacity.val(preset.endOpacity);
                    $gradientEditorEndOpacityValue.text(preset.endOpacity + '%');

                    // 3-stop defaults for Creative
                    if ($gradientEditorMidPos.length) {
                        $gradientEditorMidPos.val(50);
                        $gradientEditorMidPosNumber.val(50);
                    }
                    if ($gradientEditorMidColor.length) {
                        $gradientEditorMidColor.val('#FBBC05');
                        $gradientEditorMidColorText.val('#FBBC05');
                    }
                    if ($gradientEditorMidOpacity.length) {
                        $gradientEditorMidOpacity.val(100);
                        $gradientEditorMidOpacityValue.text('100%');
                    }

                    // Reuse existing preview update handler by triggering inputs
                    $gradientEditorModal.addClass('grp-active').css('display', 'flex');
                    $('.grp-gradient-mid-toggle').show();
                    $('.grp-gradient-mid-stop').show();
                    if ($gradientEditorMidEnabled.length) {
                        $gradientEditorMidEnabled.prop('checked', true);
                    }
                    $gradientEditorStartColor.trigger('input');
                    $gradientEditorEndColor.trigger('input');
                    $gradientEditorAngle.trigger('input');
                    $gradientEditorStartPos.trigger('input');
                    $gradientEditorEndPos.trigger('input');

                    // Mark context so done button writes to styles instead of templates
                    $gradientEditorModal.data('grp-context', 'style');
                    updateGradientEditorPreview();
                });
            }
            $styleCardRadiusTL.on('input', updateStyleEditorPreviewFromInputs);
            $styleCardRadiusTR.on('input', updateStyleEditorPreviewFromInputs);
            $styleCardRadiusBR.on('input', updateStyleEditorPreviewFromInputs);
            $styleCardRadiusBL.on('input', updateStyleEditorPreviewFromInputs);
            $styleGlassEffect.on('change', updateStyleEditorPreviewFromInputs);

            $styleCardShadowEnabled.on('change', function() {
                var enabled = $(this).is(':checked');
                if (!enabled) {
                    $styleCardShadow.val('none');
                } else {
                    // If blank, seed a reasonable default
                    if (!String($styleCardShadow.val() || '').trim() || String($styleCardShadow.val() || '').trim() === 'none') {
                        $styleCardShadow.val('0px 8px 32px 0px rgba(0,0,0,0.12)');
                    }
                }
                updateStyleEditorPreviewFromInputs();
            });

            $styleCardShadowEdit.on('click', function(e) {
                e.preventDefault();
                if (!$styleShadowModal.length) return;
                $styleCardShadowEnabled.prop('checked', true).trigger('change');

                var parts = parseBoxShadowParts($styleCardShadow.val());
                var h = parts ? parts.h : 0;
                var v = parts ? parts.v : 8;
                var blur = parts ? parts.blur : 32;
                var spread = parts ? parts.spread : 0;
                var color = parts ? parts.color : 'rgba(0,0,0,0.12)';

                function syncShadowControl(target, value) {
                    $styleShadowRanges.filter('[data-target="' + target + '"]').val(value);
                    $styleShadowNumbers.filter('[data-target="' + target + '"]').val(value);
                }

                syncShadowControl('h', h);
                syncShadowControl('v', v);
                syncShadowControl('blur', blur);
                syncShadowControl('spread', spread);

                // Opacity: if rgba(), extract alpha; otherwise use 12%
                var op = 12;
                var rgba = String(color || '').trim().match(/^rgba\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*,\s*(0|1|0?\.\d+)\s*\)$/i);
                if (rgba && rgba[1] !== undefined) {
                    op = Math.round(parseFloat(rgba[1]) * 100);
                }
                $styleShadowOpacity.val(op);
                $styleShadowOpacityNumber.val(op);

                // If it's not a hex color, keep picker at black (rgba will be reconstructed)
                if (/^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(String(color || '').trim())) {
                    $styleShadowColorPicker.val(color);
                } else {
                    $styleShadowColorPicker.val('#000000');
                }

                $styleShadowModal.show();
            });

            function updateStyleShadowString() {
                var h = $styleShadowNumbers.filter('[data-target="h"]').val() || 0;
                var v = $styleShadowNumbers.filter('[data-target="v"]').val() || 0;
                var blur = $styleShadowNumbers.filter('[data-target="blur"]').val() || 0;
                var spread = $styleShadowNumbers.filter('[data-target="spread"]').val() || 0;
                var op = $styleShadowOpacityNumber.val() || $styleShadowOpacity.val() || 12;
                $styleShadowOpacity.val(op);
                $styleShadowOpacityNumber.val(op);
                var color = hexToRgba(($styleShadowColorPicker.val() || '#000000'), op);
                $styleCardShadow.val(buildBoxShadowString(h, v, blur, spread, color));
                updateStyleEditorPreviewFromInputs();
            }

            $styleShadowRanges.on('input', function() {
                var target = $(this).data('target');
                $styleShadowNumbers.filter('[data-target="' + target + '"]').val($(this).val());
                updateStyleShadowString();
            });
            $styleShadowNumbers.on('input', function() {
                var target = $(this).data('target');
                $styleShadowRanges.filter('[data-target="' + target + '"]').val($(this).val());
                updateStyleShadowString();
            });
            $styleShadowColorPicker.on('input', updateStyleShadowString);
            $styleShadowOpacity.on('input', function() {
                $styleShadowOpacityNumber.val($(this).val());
                updateStyleShadowString();
            });
            $styleShadowOpacityNumber.on('input', function() {
                $styleShadowOpacity.val($(this).val());
                updateStyleShadowString();
            });
            $styleShadowClose.on('click', function() { $styleShadowModal.hide(); });
            $(window).on('click', function(ev) {
                if ($styleShadowModal.is(ev.target)) $styleShadowModal.hide();
            });
            $styleFontFamily.on('change', updateStyleEditorPreviewFromInputs);
            $styleHeadingWeight.on('change', updateStyleEditorPreviewFromInputs);
            $styleBodyWeight.on('change', updateStyleEditorPreviewFromInputs);
            $styleBodyLineHeight.on('input', updateStyleEditorPreviewFromInputs);
            $styleBodyLetterSpacing.on('input', updateStyleEditorPreviewFromInputs);

            $styleSave.on('click', function() {
                var radiusCss = updateStyleRadiusHidden();
                var glassBlur = $styleGlassEffect.is(':checked') ? 12 : 0;
                var borderEnabled = $styleBorderEnabled.is(':checked');
                var bwTop = borderEnabled ? clampNumber($styleBorderTop.val(), 0, 20, 0) : 0;
                var bwRight = borderEnabled ? clampNumber($styleBorderRight.val(), 0, 20, 0) : 0;
                var bwBottom = borderEnabled ? clampNumber($styleBorderBottom.val(), 0, 20, 0) : 0;
                var bwLeft = borderEnabled ? clampNumber($styleBorderLeft.val(), 0, 20, 0) : 0;
                var payload = {
                    background: String($styleBgText.val() || '').trim(),
                    text: String($styleTextText.val() || '').trim(),
                    muted: String($styleMutedText.val() || '').trim(),
                    border: String($styleBorderText.val() || '').trim(),
                    border_top: String(bwTop),
                    border_right: String(bwRight),
                    border_bottom: String(bwBottom),
                    border_left: String(bwLeft),
                    accent: String($styleAccentText.val() || '').trim(),
                    star: String($styleStarText.val() || '').trim(),
                    card_background: String($styleCardBg.val() || '').trim(),
                    card_radius: String(radiusCss || '').trim(),
                    card_shadow: String($styleCardShadow.val() || '').trim(),
                    avatar_size: String($styleAvatarSize.val() || '').trim(),
                    gradient_css: String($styleGradientCss.val() || '').trim(),
                    font_family: String($styleFontFamily.val() || '').trim(),
                    heading_font_weight: String($styleHeadingWeight.val() || '').trim(),
                    body_font_weight: String($styleBodyWeight.val() || '').trim(),
                    body_line_height: String($styleBodyLineHeight.val() || '').trim(),
                    body_letter_spacing: String($styleBodyLetterSpacing.val() || '').trim(),
                    glass_blur: String(glassBlur)
                };
                persistStyleCustomizations(activeStyleKey, ($styleVariantSelect.val() || 'light'), payload, function(resp) {
                    if (resp && resp.success) {
                        alert('Style saved.');
                    } else {
                        alert('Failed to save style.');
                    }
                });
            });

            $styleReset.on('click', function() {
                persistStyleCustomizations(activeStyleKey, ($styleVariantSelect.val() || 'light'), {}, function() {
                    populateStyleEditor(activeStyleKey, ($styleVariantSelect.val() || 'light'));
                });
            });

            $styleClose.on('click', function(e) {
                e.preventDefault();
                closeStyleEditor();
            });

            $styleEditorModal.on('click', function(e) {
                if ($(e.target).is($styleEditorModal)) {
                    closeStyleEditor();
                }
            });
        }

        window.updateGRPPreview = function() {
            updatePreview();
        };

        function getTemplateData(key) {
            if (templateMeta[key]) {
                return templateMeta[key];
            }
            return templateMeta.basic || {};
        }

        function safeTrimValue($element) {
            if (!$element || !$element.length) {
                return '';
            }
            var value = $element.val();
            if (typeof value === 'string') {
                return value.trim();
            }
            return value || '';
        }

        function updateTemplateDescription(key) {
            var templateData = getTemplateData(key);
            if ($templateDescription.length) {
                $templateDescription.text(templateData.description || '');
            }
            if ($templateProNote.length) {
                var text = '';
                if (templateData.pro && !isPro) {
                    if (typeof grpWidgets !== 'undefined' && grpWidgets.strings && grpWidgets.strings.templateProMessage) {
                        text = grpWidgets.strings.templateProMessage;
                    }
                }
                $templateProNote.text(text).toggle(!!text);
            }
        }

        function getTemplateCustomization(key) {
            var defaults = templateCustomizationDefaults[key] || templateCustomizationDefaults.basic || {};
            var stored = templateCustomizations[key] || {};
            return $.extend({}, defaults, stored);
        }

        function ensureTemplateCustomizationEntry(key) {
            var templateKey = key || modalTemplateKey || ($templateSelect.length ? ($templateSelect.val() || 'basic') : 'basic');
            if (!templateKey) {
                return null;
            }
            if (!templateCustomizations[templateKey]) {
                templateCustomizations[templateKey] = {};
            }
            return templateCustomizations[templateKey];
        }

        function updateTemplateCustomizationValue(field, value, key) {
            var entry = ensureTemplateCustomizationEntry(key);
            if (!entry) {
                return;
            }
            if (entry[field] === value) {
                return;
            }
            entry[field] = value;
            modalDirty = true;
        }

        function parsePositiveInt(value) {
            var parsed = parseInt(value, 10);
            if (isNaN(parsed) || parsed < 0) {
                return 0;
            }
            return parsed;
        }

        function clampValue(value, min, max) {
            var parsed = parseInt(value, 10);
            if (isNaN(parsed)) {
                parsed = typeof min === 'number' ? min : 0;
            }
            if (typeof min === 'number') {
                parsed = Math.max(parsed, min);
            }
            if (typeof max === 'number') {
                parsed = Math.min(parsed, max);
            }
            return parsed;
        }

        function bindModalNumberInput($input, fieldName) {
            if (!$input || !$input.length) {
                return;
            }
            $input.on('input', function() {
                var value = parsePositiveInt($(this).val());
                updateTemplateCustomizationValue(fieldName, value);
                updatePreview();
                updateModalPreview();
            });
        }

        function combineColorWithOpacity(color, opacity, fallback) {
            if (!color) {
                return fallback || '';
            }
            var hex = color.replace('#', '');
            if (/^(?:[0-9a-f]{3}|[0-9a-f]{6})$/i.test(hex)) {
                var r, g, b;
                if (hex.length === 3) {
                    r = parseInt(hex.charAt(0) + hex.charAt(0), 16);
                    g = parseInt(hex.charAt(1) + hex.charAt(1), 16);
                    b = parseInt(hex.charAt(2) + hex.charAt(2), 16);
                } else {
                    r = parseInt(hex.substr(0, 2), 16);
                    g = parseInt(hex.substr(2, 2), 16);
                    b = parseInt(hex.substr(4, 2), 16);
                }
                var opacityValue = (typeof opacity === 'undefined' || opacity === '' || opacity === null) ? 100 : parseInt(opacity, 10);
                if (isNaN(opacityValue)) {
                    opacityValue = 100;
                }
                if (opacityValue >= 100) {
                    return '#' + hex;
                }
                var alpha = (opacityValue / 100).toFixed(2);
                return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
            }
            return color;
        }

        function buildGradientBackground(customization) {
            if (!customization.gradient_start || !customization.gradient_end) {
                return '';
            }
            var type = customization.gradient_type || 'linear';
            var angle = clampValue(customization.gradient_angle, 0, 360) || 135;
            var startPos = clampValue(customization.gradient_start_pos, 0, 100);
            var endPos = clampValue(customization.gradient_end_pos, 0, 100);
            var startColor = combineColorWithOpacity(customization.gradient_start, customization.gradient_start_opacity, customization.gradient_start);
            var endColor = combineColorWithOpacity(customization.gradient_end, customization.gradient_end_opacity, customization.gradient_end);
            if (!startColor || !endColor) {
                return '';
            }
            // Optional mid stop (used by Creative style editor; templates can ignore it)
            var hasMid = !!customization.gradient_mid;
            var midPos = clampValue(customization.gradient_mid_pos, 0, 100);
            var midColor = combineColorWithOpacity(customization.gradient_mid, customization.gradient_mid_opacity, customization.gradient_mid);

            if (type === 'radial') {
                // radial-gradient(circle, color pos%, color pos%, ...)
                var stops = [startColor + ' ' + startPos + '%'];
                if (hasMid && midColor) stops.push(midColor + ' ' + midPos + '%');
                stops.push(endColor + ' ' + endPos + '%');
                return 'radial-gradient(circle, ' + stops.join(', ') + ')';
            }

            var stops2 = [startColor + ' ' + startPos + '%'];
            if (hasMid && midColor) stops2.push(midColor + ' ' + midPos + '%');
            stops2.push(endColor + ' ' + endPos + '%');
            return 'linear-gradient(' + angle + 'deg, ' + stops2.join(', ') + ')';
        }

        function updateGradientSummaryPreview(customization) {
            if (!$gradientSummaryPreview.length) {
                return;
            }
            var config = customization || getTemplateCustomization(modalTemplateKey);
            var gradient = buildGradientBackground(config);
            if (gradient) {
                $gradientSummaryPreview.css('background', gradient);
            } else {
                var fallback = combineColorWithOpacity(config.background_color, config.background_color_opacity, '#2b2b2b');
                $gradientSummaryPreview.css('background', fallback || '#2b2b2b');
            }
        }

        function buildWrapperStyle(customization, templateKey) {
            var styles = [];
            var paddingTop = customization.padding_top || 0;
            var paddingRight = customization.padding_right || 0;
            var paddingBottom = customization.padding_bottom || 0;
            var paddingLeft = customization.padding_left || 0;
            styles.push('padding:' + paddingTop + 'px ' + paddingRight + 'px ' + paddingBottom + 'px ' + paddingLeft + 'px');
            var radius = [
                (customization.border_radius_top_left || 0) + 'px',
                (customization.border_radius_top_right || 0) + 'px',
                (customization.border_radius_bottom_right || 0) + 'px',
                (customization.border_radius_bottom_left || 0) + 'px'
            ].join(' ');
            styles.push('border-radius:' + radius);
            if (customization.font_family) {
                styles.push('font-family:' + customization.font_family);
            }
            if (customization.max_width && customization.max_width > 0) {
                styles.push('max-width:' + customization.max_width + 'px');
            }
            if (customization.max_height && customization.max_height > 0) {
                styles.push('max-height:' + customization.max_height + 'px');
            }
            if (customization.box_shadow_enabled && customization.box_shadow_value) {
                styles.push('box-shadow:' + customization.box_shadow_value);
            } else {
                styles.push('box-shadow:none');
            }
            var customBackground = buildGradientBackground(customization);
            if (templateKey === 'creative-pro' && customBackground) {
                styles.push('background:' + customBackground);
            } else {
                var bgColor = combineColorWithOpacity(customization.background_color, customization.background_color_opacity, '');
                if (bgColor) {
                    styles.push('background-color:' + bgColor);
                }
            }
            var textColor = combineColorWithOpacity(customization.text_color, customization.text_color_opacity, '');
            if (textColor) {
                styles.push('color:' + textColor);
            }
            return styles.join('; ');
        }

        function showPreviewQr(src) {
            if ($previewQr.length && $previewQrImg.length) {
                $previewQrImg.attr('src', src);
                $previewQr.addClass('has-qr');
            }
            $previewBtn.find('.grp-card-qr img, .grp-layout1-qr img, .grp-layout2-qr img').attr('src', src);
            if ($templateEditorPreview.length) {
                $templateEditorPreview
                    .find('.grp-card-qr img, .grp-layout1-qr img, .grp-layout2-qr img, .grp-qr-frame img')
                    .attr('src', src);
            }
        }

        function hidePreviewQr() {
            if ($previewQr.length && $previewQrImg.length) {
                $previewQr.removeClass('has-qr');
                $previewQrImg.attr('src', blankQr);
            }
            $previewBtn.find('.grp-card-qr img, .grp-layout1-qr img, .grp-layout2-qr img').attr('src', blankQr);
            if ($templateEditorPreview.length) {
                $templateEditorPreview
                    .find('.grp-card-qr img, .grp-layout1-qr img, .grp-layout2-qr img, .grp-qr-frame img')
                    .attr('src', blankQr);
            }
        }

        function fetchPreviewQr(size) {
            size = parseInt(size, 10) || 100;
            if (!hasPlaceId || typeof grpWidgets === 'undefined' || !grpWidgets.ajax_url) {
                hidePreviewQr();
                return;
            }

            if (qrCache[size]) {
                showPreviewQr(qrCache[size]);
                return;
            }

            $.ajax({
                url: grpWidgets.ajax_url,
                type: 'POST',
                data: {
                    action: 'grp_generate_qr',
                    nonce: grpWidgets.nonce,
                    size: size
                },
                success: function(response) {
                    if (response.success && response.data && response.data.qr_url) {
                        qrCache[size] = response.data.qr_url;
                        showPreviewQr(response.data.qr_url);
                    } else {
                        hidePreviewQr();
                    }
                },
                error: function() {
                    hidePreviewQr();
                }
            });
        }

        function escapeHtml(text) {
            return $('<div>').text(text).html();
        }

        function buildUnderline(colors) {
            var html = '';
            if (!Array.isArray(colors)) {
                return html;
            }
            colors.forEach(function(color) {
                if (color) {
                    html += '<span style="background: ' + color + ';"></span>';
                }
            });
            return html;
        }

        function renderPreviewContent(templateType, templateData, options) {
            var qrHtml = templateData && templateData.qr ? '<div class="grp-qr-frame"><img id="grp-preview-qr-img" src="' + blankQr + '" alt="QR"></div>' : '';
            var linkColor = options.linkColor || '#111111';
            var showLink = options.showLink !== false;
            var linkHtml = showLink && options.reviewUrl ? '<a href="' + options.reviewUrl + '" target="_blank" rel="noopener" style="color:' + linkColor + ';">' + escapeHtml(options.linkText || 'Click here') + '</a>' : '';
            var wrapperStyleAttr = options.wrapperStyle ? ' style="' + options.wrapperStyle + '"' : '';

            if (templateType === 'layout1') {
                return '<div class="grp-layout1-preview"' + wrapperStyleAttr + '>' +
                    '<div class="grp-layout1-qr">' + qrHtml + '</div>' +
                    '<div class="grp-layout1-details">' +
                        (options.showLogo && options.logoIconUrl ? '<img src="' + options.logoIconUrl + '" class="grp-layout1-logo-img" alt="Google" style="width:' + options.logoScale + '%;">' : '') +
                        '<div class="grp-layout1-stars" style="color:' + options.starColor + ';">' + options.starText + '</div>' +
                        '<div class="grp-layout1-title">' + options.title + '</div>' +
                        '<div class="grp-layout1-subtitle">' + options.subtitle + '</div>' +
                        '<div class="grp-layout1-underline">' + buildUnderline(templateData.underline_colors) + '</div>' +
                        (linkHtml ? '<div class="grp-layout1-link" style="color:' + linkColor + ';">' + linkHtml + '</div>' : '') +
                    '</div>' +
                '</div>';
            }

            if (templateType === 'layout2') {
                var darkClass = templateData.dark ? ' grp-layout-dark' : '';
                return '<div class="grp-layout2-preview' + darkClass + '"' + wrapperStyleAttr + '>' +
                    (options.showLogo && options.logoClassicUrl ? '<img src="' + options.logoClassicUrl + '" class="grp-layout2-logo-img" alt="Google" style="width:' + options.logoScale + '%;">' : '') +
                    '<div class="grp-layout2-stars-qr">' +
                        '<div class="grp-layout2-stars" style="color:' + options.starColor + ';">' + options.starText + '</div>' +
                        '<div class="grp-layout2-qr">' + qrHtml + '</div>' +
                    '</div>' +
                    '<div class="grp-layout2-heading">' + options.title + '</div>' +
                    '<div class="grp-layout2-subtitle">' + options.subtitle + '</div>' +
                    '<div class="grp-layout2-link" style="color:' + linkColor + ';">' + linkHtml + '</div>' +
                    '<div class="grp-layout2-underline">' + buildUnderline(templateData.underline_colors) + '</div>' +
                '</div>';
            }

            if (templateType === 'card') {
                var inner = '<div class="grp-review-card"' + wrapperStyleAttr + '>';
                if (options.showLogo && options.logoClassicUrl) {
                    inner += '<img src="' + options.logoClassicUrl + '" class="grp-card-logo-img" alt="Google" style="width:' + options.logoScale + '%;">';
                }
                inner += '<div class="grp-card-stars" style="color:' + options.starColor + ';">' + options.starText + '</div>';
                inner += '<div class="grp-card-heading">' + options.title + '</div>';
                inner += '<div class="grp-card-subtitle">' + options.subtitle + '</div>';
                inner += '<div class="grp-card-qr">' + qrHtml + '</div>';
                inner += '<div class="grp-card-link">' + linkHtml + '</div>';
                inner += '</div>';
                return inner;
            }

            return '<span class="grp-preview-star-row" style="color:' + options.starColor + ';">' + options.starText + '</span>' +
                '<span class="grp-preview-qr" id="grp-preview-qr">' + qrHtml + '</span>' +
                '<div class="grp-preview-content">' +
                    (options.showLogo ? '<span class="grp-review-button-icon">⭐</span>' : '') +
                    '<span class="grp-review-button-text" id="grp-preview-text">' + options.title + '</span>' +
                    '<span class="grp-preview-tagline" id="grp-preview-tagline">' + options.subtitle + '</span>' +
                '</div>';
        }

        function toggleTemplateControls(templateKey) {
            if (!$templateControlNodes.length || !templateKey) {
                return;
            }
            templateKey = String(templateKey || '').trim();
            if (!templateKey) return;

            // Normalize older aliases just in case.
            if (templateKey === 'layout-1') templateKey = 'layout1';
            if (templateKey === 'layout-2') templateKey = 'layout2';
            if (templateKey === 'layout-3') templateKey = 'layout3';

            $templateControlNodes.each(function() {
                var $node = $(this);
                var templates = $node.data('templates');
                if (!templates) {
                    $node.show();
                    return;
                }
                var templateList = templates.toString().trim().split(/\\s+/);
                var show = templateList.indexOf(templateKey) !== -1;
                $node.toggle(show);
            });

            // Safety net: if we somehow hid everything (bad template key or bad data attr),
            // show all controls rather than rendering an empty modal.
            var matched = 0;
            $templateControlNodes.each(function() {
                var templates = $(this).data('templates');
                if (!templates) {
                    matched++;
                    return;
                }
                var templateList = templates.toString().trim().split(/\\s+/);
                if (templateList.indexOf(templateKey) !== -1) {
                    matched++;
                }
            });
            if (matched === 0) {
                console.warn('[GRP] No template controls matched key; showing all controls as fallback', {
                    templateKey: templateKey
                });
                $templateEditorModal.find('.grp-template-editor-controls [data-templates]').show();
            }
        }

        function isValidHex(color) {
            return /^#[0-9A-F]{6}$/i.test(color);
        }

        function populateTemplateModal() {
            // Widgets page has a template selector; styles page (and other callers) can set modalTemplateKey
            // directly via grpOpenTemplateModal(templateKey). Do not overwrite it when selector isn't present.
            modalTemplateKey = $templateSelect.length
                ? ($templateSelect.val() || 'basic')
                : (modalTemplateKey || 'basic');
            var templateData = getTemplateData(modalTemplateKey);
            var customization = getTemplateCustomization(modalTemplateKey);
            var isCreative = modalTemplateKey === 'creative-pro';

            toggleTemplateControls(modalTemplateKey);
            applyCustomizationToDom(modalTemplateKey);

            $modalBoxShadowEnabled.prop('checked', !!customization.box_shadow_enabled);
            updateModalPreview();
        }

        function updateModalPreview() {
            if (!$templateEditorPreview.length) {
                return;
            }
            var templateData = getTemplateData(modalTemplateKey || 'basic');
            var previewUrl = $previewBtn.attr('href') || '#';
            var modalLogoScale = parseInt($modalLogoScaleSlider.val(), 10);
            if (isNaN(modalLogoScale) || modalLogoScale <= 0) {
                modalLogoScale = 50;
            }
            var modalStarColorValue = $modalStarColorText.val() || '#FBBD05';
            if (!isValidHex(modalStarColorValue)) {
                modalStarColorValue = '#FBBD05';
            }
            var modalTextColorValue = $modalTextColorText.val() || '#ffffff';
            if (!isValidHex(modalTextColorValue)) {
                modalTextColorValue = '#ffffff';
            }
            var modalBackgroundColorValue = $modalBackgroundColorText.val() || '#2b2b2b';
            if (!isValidHex(modalBackgroundColorValue)) {
                modalBackgroundColorValue = '#2b2b2b';
            }
            var modalMaxWidth = parseInt($maxWidthInput.val(), 10) || 0;
            var modalMaxHeight = parseInt($maxHeightInput.val(), 10) || 0;
            var isModalButton = templateData.type === 'button';
            var modalStarPlacementValue = $modalStarPlacement.val() || 'below';
            var previewWrapperStyles = [];
            if (isModalButton && modalMaxWidth > 0) {
                previewWrapperStyles.push('max-width: ' + modalMaxWidth + 'px');
            }
            if (isModalButton && modalMaxHeight > 0) {
                previewWrapperStyles.push('max-height: ' + modalMaxHeight + 'px');
            }
            var previewWrapperClass = 'grp-template-modal-preview-inner grp-star-placement-' + modalStarPlacementValue;
            var previewWrapperAttr = previewWrapperStyles.length ? ' style="' + previewWrapperStyles.join('; ') + '"' : '';

            var modalCustomization = getTemplateCustomization(modalTemplateKey);
            var modalWrapperStyle = buildWrapperStyle(modalCustomization, modalTemplateKey);
            var modalMessageText = modalCustomization.message_text || templateData.subtitle || templateData.tagline || '';
            var modalLinkText = modalCustomization.link_text || safeTrimValue($linkTextInput) || 'Click here';
            var previewHtml = '<div class="' + previewWrapperClass + '"' + previewWrapperAttr + '>' +
                renderPreviewContent(templateData.type || 'button', templateData, {
                title: escapeHtml($('#grp_widget_button_default_text').val() || 'Leave us a review'),
                subtitle: escapeHtml(modalMessageText),
                linkText: escapeHtml(modalLinkText),
                showLogo: $modalShowLogo.is(':checked'),
                starColor: modalStarColorValue,
                starText: templateData.stars ? '★★★★★' : '',
                logoIconUrl: logoUrls.icon || '',
                logoClassicUrl: logoUrls.classic || '',
                reviewUrl: previewUrl,
                linkColor: $linkColorText.length ? ($linkColorText.val() || '#111111') : '#111111',
                logoScale: modalLogoScale,
                showLink: templateData.show_link !== false,
                wrapperStyle: modalWrapperStyle
            }) + '</div>';
            $templateEditorPreview.html(previewHtml);
            var $modalPreviewInner = $templateEditorPreview.find('.grp-template-modal-preview-inner');
            var $modalTemplateRoot = $modalPreviewInner.children().first();
            var modalRootStyles = [];
            if (isValidHex(modalTextColorValue)) {
                modalRootStyles.push('color: ' + modalTextColorValue);
            }
            if (isValidHex(modalBackgroundColorValue)) {
                modalRootStyles.push('background-color: ' + modalBackgroundColorValue);
            }
            if ($modalFontFamily.val()) {
                modalRootStyles.push('font-family: ' + $modalFontFamily.val());
            }
            if (isModalButton && modalMaxHeight > 0) {
                modalRootStyles.push('max-height: ' + modalMaxHeight + 'px');
            }
            if (isModalButton && modalMaxWidth > 0) {
                modalRootStyles.push('max-width: ' + modalMaxWidth + 'px');
            }
            var trimmedBoxShadow = safeTrimValue($boxShadowValue);
            if ($modalBoxShadowEnabled.is(':checked') && trimmedBoxShadow) {
                modalRootStyles.push('box-shadow: ' + trimmedBoxShadow);
            } else {
                modalRootStyles.push('box-shadow: none');
            }
            var combinedModalStyles = modalRootStyles.slice();
            if (modalWrapperStyle) {
                combinedModalStyles.push(modalWrapperStyle);
            }
            $modalTemplateRoot.attr('style', combinedModalStyles.join('; '));

            if ($modalGlassEffect.is(':checked')) {
                $modalTemplateRoot.addClass('grp-glass-effect');
            } else {
                $modalTemplateRoot.removeClass('grp-glass-effect');
            }
            $templateEditorPreview.toggleClass('grp-glass-preview', $modalGlassEffect.is(':checked'));
            updateGradientSummaryPreview(modalCustomization);
            var sanitizedModalPreviewUrl = String(previewUrl || '').trim();
            if (templateData.qr && hasPlaceId && sanitizedModalPreviewUrl && sanitizedModalPreviewUrl !== '#') {
                fetchPreviewQr(templateData.qr_size || 135);
            }
        }

        function persistTemplateCustomizations(templateKey) {
            templateKey = templateKey || modalTemplateKey;
            if (!modalDirty || !templateKey || typeof grpWidgets === 'undefined' || !grpWidgets.ajax_url) {
                return;
            }
            var customization = templateCustomizations[templateKey];
            if (!customization) {
                return;
            }
            $.ajax({
                url: grpWidgets.ajax_url,
                type: 'POST',
                data: {
                    action: 'grp_save_template_customization',
                    nonce: grpWidgets.nonce,
                    template: templateKey,
                    customizations: JSON.stringify(customization)
                },
                success: function(response) {
                    if (response.success && response.data && response.data.customizations) {
                        templateCustomizations[templateKey] = response.data.customizations;
                        modalDirty = false;
                        updateGradientSummaryPreview(response.data.customizations);
                        updatePreview();
                        updateModalPreview();
                    } else {
                        console.warn('Failed to save template customizations', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Template customization save failed', status, error);
                }
            });
        }

        function populateGradientEditor(customization) {
            var config = customization || getTemplateCustomization(modalTemplateKey);
            var angle = clampValue(config.gradient_angle, 0, 360) || 135;
            $gradientEditorType.val(config.gradient_type || 'linear');
            $gradientEditorAngle.val(angle);
            $gradientEditorAngleNumber.val(angle);
            $gradientEditorStartPos.val(clampValue(config.gradient_start_pos, 0, 100));
            $gradientEditorStartPosNumber.val(clampValue(config.gradient_start_pos, 0, 100));
            $gradientEditorEndPos.val(clampValue(config.gradient_end_pos, 0, 100));
            $gradientEditorEndPosNumber.val(clampValue(config.gradient_end_pos, 0, 100));
            var startColor = config.gradient_start || '#0091ff';
            var endColor = config.gradient_end || '#612c1f';
            $gradientEditorStartColor.val(startColor);
            $gradientEditorStartColorText.val(startColor);
            $gradientEditorEndColor.val(endColor);
            $gradientEditorEndColorText.val(endColor);
            var startOpacity = clampValue(config.gradient_start_opacity, 0, 100) || 100;
            var endOpacity = clampValue(config.gradient_end_opacity, 0, 100) || 100;
            $gradientEditorStartOpacity.val(startOpacity);
            $gradientEditorStartOpacityValue.text(startOpacity + '%');
            $gradientEditorEndOpacity.val(endOpacity);
            $gradientEditorEndOpacityValue.text(endOpacity + '%');
            // Mid stop (optional; only shown for Creative style editor context)
            if ($gradientEditorMidPos.length) {
                $gradientEditorMidPos.val(clampValue(config.gradient_mid_pos, 0, 100) || 50);
                $gradientEditorMidPosNumber.val(clampValue(config.gradient_mid_pos, 0, 100) || 50);
            }
            if ($gradientEditorMidColor.length) {
                var midColor = config.gradient_mid || '#FBBC05';
                $gradientEditorMidColor.val(midColor);
                $gradientEditorMidColorText.val(midColor);
            }
            if ($gradientEditorMidOpacity.length) {
                var midOpacity = clampValue(config.gradient_mid_opacity, 0, 100) || 100;
                $gradientEditorMidOpacity.val(midOpacity);
                $gradientEditorMidOpacityValue.text(midOpacity + '%');
            }
            updateGradientEditorPreview();
        }

        function updateGradientEditorPreview() {
            var ctx = $gradientEditorModal.data('grp-context') || 'template';
            var previewData = {
                gradient_type: $gradientEditorType.val(),
                gradient_angle: clampValue($gradientEditorAngleNumber.val(), 0, 360) || 135,
                gradient_start_pos: clampValue($gradientEditorStartPosNumber.val(), 0, 100),
                gradient_end_pos: clampValue($gradientEditorEndPosNumber.val(), 0, 100),
                gradient_start: $gradientEditorStartColor.val(),
                gradient_end: $gradientEditorEndColor.val(),
                gradient_start_opacity: clampValue($gradientEditorStartOpacity.val(), 0, 100),
                gradient_end_opacity: clampValue($gradientEditorEndOpacity.val(), 0, 100),
            };
            if (ctx === 'style' && $gradientEditorMidColor.length && (!$gradientEditorMidEnabled.length || $gradientEditorMidEnabled.is(':checked'))) {
                previewData.gradient_mid = $gradientEditorMidColor.val();
                previewData.gradient_mid_pos = clampValue($gradientEditorMidPosNumber.val(), 0, 100);
                previewData.gradient_mid_opacity = clampValue($gradientEditorMidOpacity.val(), 0, 100);
            }
            var previewGradient = buildGradientBackground(previewData);
            if (previewGradient) {
                $gradientEditorPreview.css('background', previewGradient);
            } else {
                $gradientEditorPreview.css('background', '#232323');
            }
        }

        function bindGradientEditorRange($range, $number, min, max) {
            if (!$range.length || !$number.length) {
                return;
            }
            $range.on('input change', function() {
                var value = clampValue($(this).val(), min, max);
                $(this).val(value);
                $number.val(value);
                updateGradientEditorPreview();
            });
            $number.on('input change', function() {
                var value = clampValue($(this).val(), min, max);
                $(this).val(value);
                $range.val(value);
                updateGradientEditorPreview();
            });
        }

        function openGradientEditor() {
            // Default context: template editor (2-stop gradient)
            if ($gradientEditorModal.length) {
                $gradientEditorModal.data('grp-context', 'template');
            }
            $('.grp-gradient-mid-stop').hide();
            $('.grp-gradient-mid-toggle').hide();
            populateGradientEditor();
            if ($gradientEditorModal.length) {
                $gradientEditorModal.addClass('grp-active').css('display', 'flex');
            }
        }

        function closeGradientEditor() {
            if ($gradientEditorModal.length) {
                $gradientEditorModal.removeClass('grp-active').hide();
            }
        }

        function closeTemplateModal() {
            persistTemplateCustomizations(modalTemplateKey);
            $templateEditorModal.removeClass('grp-template-active');
            $templateEditorModal.css('display', 'none');
            if ($gradientEditorModal.is(':visible')) {
                $gradientEditorModal.hide();
            }
        }
        
        function updatePreview() {
            var text = $('#grp_widget_button_default_text').val();
            var style = $('#grp_widget_button_default_style').val();
            var size = $('#grp_widget_button_default_size').val();
            var templateKey = $templateSelect.length ? ($templateSelect.val() || 'basic') : 'basic';
            var templateData = getTemplateData(templateKey);
            applyCustomizationToDom(templateKey);
            var customization = getTemplateCustomization(templateKey);
            var previewUrl = $previewBtn.attr('href') || '#';
            var starPlacement = customization.star_placement || 'below';
            var normalizedPlacement = starPlacement === 'bottom' ? 'below' : starPlacement;
            var linkText = customization.link_text || safeTrimValue($linkTextInput) || 'Click here';
            var subtitleText = customization.message_text || templateData.subtitle || templateData.tagline || 'Scan the QR code to leave a review!';
            var logoScale = customization.logo_scale || parseInt($logoScaleSlider.val(), 10) || 30;
            var glassEffect = !!customization.glass_effect;
            var computedTextColor = combineColorWithOpacity(customization.text_color, customization.text_color_opacity, '#ffffff');
            var computedBackgroundColor = combineColorWithOpacity(customization.background_color, customization.background_color_opacity, '#2b2b2b');
            var computedStarColor = combineColorWithOpacity(customization.star_color, customization.star_color_opacity, '#FBBD05');
            var computedLinkColor = combineColorWithOpacity(customization.link_color, customization.link_color_opacity, '#ffffff');

            // Update template description/pro note
            updateTemplateDescription(templateKey);

            var classes = [
                'grp-review-button',
                'grp-review-button-template-' + templateKey,
                'grp-star-placement-' + normalizedPlacement
            ];
            if (templateData.type === 'button') {
                classes.splice(1, 0, 'grp-review-button-' + style);
                classes.splice(2, 0, 'grp-review-button-' + size);
            }
            $previewBtn.attr('class', classes.join(' '));
            if (glassEffect) {
                $previewBtn.addClass('grp-glass-effect');
            } else {
                $previewBtn.removeClass('grp-glass-effect');
            }

            var styles = [];
            if (computedTextColor) {
                styles.push('color: ' + computedTextColor);
            }
            if (templateData.type === 'button') {
                if (computedBackgroundColor) {
                    styles.push('background-color: ' + computedBackgroundColor);
                }
                if (customization.font_family) {
                    styles.push('font-family: ' + customization.font_family);
                }
                if (customization.max_height > 0) {
                    styles.push('max-height: ' + customization.max_height + 'px');
                }
                if (customization.max_width > 0) {
                    styles.push('max-width: ' + customization.max_width + 'px');
                }
                if (customization.box_shadow_enabled && customization.box_shadow_value) {
                    styles.push('box-shadow: ' + customization.box_shadow_value);
                }
            }
                $previewBtn.attr('style', styles.join('; '));

            var wrapperStyle = buildWrapperStyle(customization, templateKey);
            var previewHtml = renderPreviewContent(templateData.type || 'button', templateData, {
                title: escapeHtml(text || 'Leave us a review'),
                subtitle: escapeHtml(subtitleText),
                linkText: escapeHtml(linkText),
                showLogo: !!customization.show_logo,
                starColor: computedStarColor,
                starText: templateData.stars ? '★★★★★' : '',
                logoIconUrl: logoUrls.icon || '',
                logoClassicUrl: logoUrls.classic || '',
                reviewUrl: previewUrl,
                linkColor: computedLinkColor,
                logoScale: logoScale,
                showLink: templateData.show_link !== false,
                wrapperStyle: wrapperStyle
            });
            $previewBtn.html(previewHtml);

            toggleTemplateControls(templateKey);
            updateCustomizeButtonVisibility(templateKey);
            updateModalPreview();
            updateGradientSummaryPreview(customization);

            var sanitizedPreviewUrl = String(previewUrl || '').trim();
            if (templateData.qr && hasPlaceId && sanitizedPreviewUrl && sanitizedPreviewUrl !== '#') {
                fetchPreviewQr(templateData.qr_size || 135);
            } else {
                hidePreviewQr();
            }
        }

        function updateCustomizeButtonVisibility(templateKey) {
            if (!$customizeButton.length) {
                return;
            }
            var templateData = getTemplateData(templateKey);
            if (templateData.type === 'button') {
                $customizeButton.hide();
                if ($templateProBadge.length) {
                    $templateProBadge.hide();
                }
                return;
            }
            $customizeButton.show();
            var requiresPro = !!templateData.pro;
            $customizeButton.attr('data-template-key', templateKey);
            $customizeButton.attr('data-template-pro', requiresPro ? '1' : '0');
            $customizeButton.attr('data-is-pro', isPro ? '1' : '0');
            if (requiresPro && !isPro) {
                if ($templateProBadge.length) {
                    $templateProBadge.show();
                }
                $customizeButton.attr('title', 'Available in Pro only');
            } else {
                if ($templateProBadge.length) {
                    $templateProBadge.hide();
                }
                $customizeButton.attr('title', 'Customize this layout');
            }
        }

        // Color picker sync and preview font/color updates
        $('#grp_widget_button_default_color').on('change', function() {
            var color = $(this).val();
            $('#grp_widget_button_default_color_text').val(color);
            updatePreview();
        });
        
        $('#grp_widget_button_default_color_text').on('input', function() {
            var val = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                $('#grp_widget_button_default_color').val(val);
            }
            updatePreview();
        });
        
        $('#grp_widget_button_default_bg_color').on('change', function() {
            var color = $(this).val();
            $('#grp_widget_button_default_bg_color_text').val(color);
            updatePreview();
        });
        
        $('#grp_widget_button_default_bg_color_text').on('input', function() {
            var val = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                $('#grp_widget_button_default_bg_color').val(val);
            }
            updatePreview();
        });

        // Star color sync
        $starColorInput.on('change', function() {
            var color = $(this).val();
            $starColorText.val(color);
            updatePreview();
        });

        $starColorText.on('input', function() {
            var val = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                $starColorInput.val(val);
            }
            updatePreview();
        });
        
        $linkColorInput.on('change', function() {
            var color = $(this).val();
            $linkColorText.val(color);
            updatePreview();
        });

        $linkColorText.on('input', function() {
            var val = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                $linkColorInput.val(val);
            }
            updatePreview();
        });

        $linkTextInput.on('input', function() {
            updatePreview();
        });

        $logoScaleSlider.on('input', function() {
            var value = $(this).val();
            logoScaleTouched = true;
            $logoScaleText.val(value);
            updatePreview();
        });

        $logoScaleText.on('input', function() {
            var value = $(this).val();
            if (value === '') {
                return;
            }
            logoScaleTouched = true;
            if (!isNaN(value) && value >= 10 && value <= 100) {
                $logoScaleSlider.val(value);
            }
            updatePreview();
        });
        
        // Clear color buttons
        $('#grp-clear-text-color').on('click', function() {
            $('#grp_widget_button_default_color_text').val('');
            updatePreview();
        });
        
        $('#grp-clear-bg-color').on('click', function() {
            $('#grp_widget_button_default_bg_color_text').val('');
            updatePreview();
        });
        
        // Update preview triggers
        $('#grp_widget_button_default_text').on('input', updatePreview);
        $('#grp_widget_button_default_style').on('change', updatePreview);
        $('#grp_widget_button_default_size').on('change', updatePreview);
        $templateSelect.on('change', updatePreview);
        $starPlacementSelect.on('change', updatePreview);
        $logoToggle.on('change', updatePreview);
        $('#grp-template-editor-open').on('click', function() {
            if (!$templateEditorModal.length) {
                return;
            }
            // Only gate Pro-only templates. Layout 1/2/3 are customizable without Pro.
            var activeKey = ($(this).attr('data-template-key') || '').toString().trim() || ($templateSelect.length ? ($templateSelect.val() || 'basic') : 'basic');
            var activeTemplate = getTemplateData(activeKey) || {};
            var requiresPro = !!activeTemplate.pro;
            if (requiresPro && !isPro) {
                var proMessage = (typeof grpWidgets !== 'undefined' && grpWidgets.strings && grpWidgets.strings.templateProMessage) ? grpWidgets.strings.templateProMessage : 'Upgrade to Pro to unlock this layout.';
                alert(proMessage);
                return;
            }
            console.log('[GRP DEBUG] Customize modal click', {
                isPro: isPro,
                licenseStatus: typeof grpWidgets !== 'undefined' ? grpWidgets.license_status : undefined,
                licensePackage: typeof grpWidgets !== 'undefined' ? (grpWidgets.license_data || {}).packageType : undefined,
            });
            populateTemplateModal();
            $templateEditorModal.css('display', 'flex');
            $templateEditorModal.addClass('grp-template-active');
        });

        window.grpOpenTemplateModal = function(templateKey) {
            if (!templateKey) {
                templateKey = $templateSelect.length ? ($templateSelect.val() || 'basic') : 'basic';
            }
            // Gate only Pro-only templates.
            var requested = getTemplateData(templateKey) || {};
            if (requested.pro && !isPro) {
                var proMessage = (typeof grpWidgets !== 'undefined' && grpWidgets.strings && grpWidgets.strings.templateProMessage) ? grpWidgets.strings.templateProMessage : 'Upgrade to Pro to unlock this layout.';
                alert(proMessage);
                return;
            }
            modalTemplateKey = templateKey;
            populateTemplateModal();
            $templateEditorModal.css('display', 'flex');
            $templateEditorModal.addClass('grp-template-active');
        };

        // Styles page: ensure the Customize button always opens the style editor modal.
        // (Even if the inline script in styles.php is cached/blocked for any reason.)
        $(document).on('click', '.grp-customize-style', function(e) {
            // Only handle on pages where the style editor modal exists.
            if (!$styleEditorModal.length) return;
            e.preventDefault();

            var $trigger = $(this);
            console.log('[GRP] styles customize click', {
                hasStyleModal: !!$styleEditorModal.length,
                hasOpenFn: (typeof window.grpOpenStyleModal === 'function'),
                styleKey: $trigger.data('style'),
                isProBlocked: $trigger.hasClass('grp-pro-feature')
            });
            if ($trigger.hasClass('grp-pro-feature')) {
                alert((typeof grpWidgets !== 'undefined' && grpWidgets.strings && grpWidgets.strings.templateProMessage)
                    ? grpWidgets.strings.templateProMessage
                    : 'Upgrade to Pro to customize styles.');
                return;
            }

            var styleKey = ($trigger.data('style') || '').toString();
            var $card = $trigger.closest('.grp-style-card');
            var variant = ($card.find('.grp-variant-btn.active').data('variant') || 'light');

            if (typeof window.grpOpenStyleModal === 'function') {
                window.grpOpenStyleModal(styleKey, variant);
            } else {
                console.error('grpOpenStyleModal is not defined. widgets-admin.js may have failed to initialize.');
            }
        });

        $templateEditorModal.on('click', function(e) {
            if ($(e.target).is($templateEditorModal)) {
                closeTemplateModal();
            }
        });

        $('.grp-template-editor-close, #grp-template-editor-close').on('click', closeTemplateModal);

        $gradientEditorOpen.on('click', function() {
            if (!isPro) {
                var proMessage = (typeof grpWidgets !== 'undefined' && grpWidgets.strings && grpWidgets.strings.templateProMessage) ? grpWidgets.strings.templateProMessage : 'Upgrade to Pro to edit gradients.';
                alert(proMessage);
                return;
            }
            openGradientEditor();
        });

        $gradientEditorClose.on('click', closeGradientEditor);
        $gradientEditorCancel.on('click', closeGradientEditor);
        $gradientEditorModal.on('click', function(e) {
            if ($(e.target).is($gradientEditorModal)) {
                closeGradientEditor();
            }
        });
        $gradientEditorType.on('change', updateGradientEditorPreview);
        bindGradientEditorRange($gradientEditorAngle, $gradientEditorAngleNumber, 0, 360);
        bindGradientEditorRange($gradientEditorStartPos, $gradientEditorStartPosNumber, 0, 100);
        bindGradientEditorRange($gradientEditorEndPos, $gradientEditorEndPosNumber, 0, 100);
        bindGradientEditorRange($gradientEditorMidPos, $gradientEditorMidPosNumber, 0, 100);
        $gradientEditorStartColor.on('change', function() {
            var color = $(this).val();
            $gradientEditorStartColorText.val(color);
            updateGradientEditorPreview();
        });
        $gradientEditorStartColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                $gradientEditorStartColor.val(value);
                updateGradientEditorPreview();
            }
        });
        $gradientEditorEndColor.on('change', function() {
            var color = $(this).val();
            $gradientEditorEndColorText.val(color);
            updateGradientEditorPreview();
        });
        $gradientEditorEndColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                $gradientEditorEndColor.val(value);
                updateGradientEditorPreview();
            }
        });
        $gradientEditorStartOpacity.on('input change', function() {
            var value = clampValue($(this).val(), 0, 100);
            $(this).val(value);
            $gradientEditorStartOpacityValue.text(value + '%');
            updateGradientEditorPreview();
        });
        $gradientEditorMidOpacity.on('input change', function() {
            var value = clampValue($(this).val(), 0, 100);
            $(this).val(value);
            $gradientEditorMidOpacityValue.text(value + '%');
            updateGradientEditorPreview();
        });
        $gradientEditorEndOpacity.on('input change', function() {
            var value = clampValue($(this).val(), 0, 100);
            $(this).val(value);
            $gradientEditorEndOpacityValue.text(value + '%');
            updateGradientEditorPreview();
        });

        $gradientEditorMidColor.on('input', function() {
            var color = $(this).val();
            $gradientEditorMidColorText.val(color);
            updateGradientEditorPreview();
        });
        $gradientEditorMidColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                $gradientEditorMidColor.val(value);
                updateGradientEditorPreview();
            }
        });

        if ($gradientEditorMidEnabled.length) {
            $gradientEditorMidEnabled.on('change', function() {
                var enabled = $(this).is(':checked');
                $('.grp-gradient-mid-stop').toggle(enabled);
                updateGradientEditorPreview();
            });
        }
        $gradientEditorDone.on('click', function() {
            var ctx = $gradientEditorModal.data('grp-context') || 'template';
            var startColor = $gradientEditorStartColor.val();
            var endColor = $gradientEditorEndColor.val();
            var config = {
                gradient_type: $gradientEditorType.val(),
                gradient_angle: clampValue($gradientEditorAngleNumber.val(), 0, 360),
                gradient_start_pos: clampValue($gradientEditorStartPosNumber.val(), 0, 100),
                gradient_end_pos: clampValue($gradientEditorEndPosNumber.val(), 0, 100),
                gradient_start: startColor,
                gradient_end: endColor,
                gradient_start_opacity: clampValue($gradientEditorStartOpacity.val(), 0, 100),
                gradient_end_opacity: clampValue($gradientEditorEndOpacity.val(), 0, 100),
            };
            if (ctx === 'style') {
                if ($gradientEditorMidColor.length && (!$gradientEditorMidEnabled.length || $gradientEditorMidEnabled.is(':checked'))) {
                    config.gradient_mid = $gradientEditorMidColor.val();
                    config.gradient_mid_pos = clampValue($gradientEditorMidPosNumber.val(), 0, 100);
                    config.gradient_mid_opacity = clampValue($gradientEditorMidOpacity.val(), 0, 100);
                }
                var css = buildGradientBackground(config);
                $styleGradientCss.val(css || '');
                if ($styleGradientSummaryPreview.length) {
                    $styleGradientSummaryPreview.css('background', css || 'transparent');
                }
                updateStyleEditorPreviewFromInputs();
                $gradientEditorModal.removeData('grp-context');
                closeGradientEditor();
                return;
            }

            var templateKey = modalTemplateKey;
            updateTemplateCustomizationValue('gradient_type', config.gradient_type, templateKey);
            updateTemplateCustomizationValue('gradient_angle', config.gradient_angle, templateKey);
            updateTemplateCustomizationValue('gradient_start_pos', config.gradient_start_pos, templateKey);
            updateTemplateCustomizationValue('gradient_end_pos', config.gradient_end_pos, templateKey);
            updateTemplateCustomizationValue('gradient_start', config.gradient_start, templateKey);
            updateTemplateCustomizationValue('gradient_end', config.gradient_end, templateKey);
            updateTemplateCustomizationValue('gradient_start_opacity', config.gradient_start_opacity, templateKey);
            updateTemplateCustomizationValue('gradient_end_opacity', config.gradient_end_opacity, templateKey);
            updateGradientSummaryPreview(getTemplateCustomization(templateKey));
            updatePreview();
            updateModalPreview();
            closeGradientEditor();
        });

        $modalShowLogo.on('change', function() {
            $logoToggle.prop('checked', $(this).is(':checked'));
            updateTemplateCustomizationValue('show_logo', $(this).is(':checked') ? 1 : 0);
            updatePreview();
            updateModalPreview();
        });

        function syncLogoScale(value) {
            $modalLogoScaleSlider.val(value);
            $modalLogoScaleNumber.val(value);
            $logoScaleSlider.val(value);
            $logoScaleText.val(value);
        }

        function syncTextColor(value) {
            $modalTextColor.val(value);
            $modalTextColorText.val(value);
            $('#grp_widget_button_default_color').val(value);
            $('#grp_widget_button_default_color_text').val(value);
        }

        function syncBackgroundColor(value) {
            $modalBackgroundColor.val(value);
            $modalBackgroundColorText.val(value);
            $('#grp_widget_button_default_bg_color').val(value);
            $('#grp_widget_button_default_bg_color_text').val(value);
        }

        function syncLinkText(value) {
            $modalLinkText.val(value);
            $linkTextInput.val(value);
        }

        function syncLinkColor(value) {
            $modalLinkColor.val(value);
            $modalLinkColorText.val(value);
            $linkColorInput.val(value);
            $linkColorText.val(value);
        }

        function applyCustomizationToDom(templateKey) {
            var customization = getTemplateCustomization(templateKey);
            if (!customization) {
                return;
            }
            $logoToggle.prop('checked', !!customization.show_logo);
            syncLogoScale(customization.logo_scale || 30);
            $fontFamilyInput.val(customization.font_family || '');
            $modalFontFamily.val(customization.font_family || '');
            syncTextColor(customization.text_color || '#ffffff');
            syncBackgroundColor(customization.background_color || '#2b2b2b');
            var starColor = customization.star_color || '#FBBD05';
            $modalStarColor.val(starColor);
            $modalStarColorText.val(starColor);
            $starColorInput.val(starColor);
            $starColorText.val(starColor);
            var linkColor = customization.link_color || '#ffffff';
            $modalLinkColor.val(linkColor);
            $modalLinkColorText.val(linkColor);
            $linkColorInput.val(linkColor);
            $linkColorText.val(linkColor);
            var linkText = customization.link_text || '';
            $modalLinkText.val(linkText);
            $linkTextInput.val(linkText);
            var starPlacementValue = customization.star_placement || 'below';
            $modalStarPlacement.val(starPlacementValue);
            $starPlacementSelect.val(starPlacementValue);
            $glassCheckbox.prop('checked', !!customization.glass_effect);
            $modalGlassEffect.prop('checked', !!customization.glass_effect);
            $boxShadowCheckbox.prop('checked', !!customization.box_shadow_enabled);
            $modalBoxShadowEnabled.prop('checked', !!customization.box_shadow_enabled);
            $boxShadowValue.val(customization.box_shadow_value || '');
            $maxWidthInput.val(customization.max_width || 0);
            $maxHeightInput.val(customization.max_height || 0);
            if ($('#grp-modal-message-text').length) {
                $('#grp-modal-message-text').val(customization.message_text || '');
            }
            if ($('#grp-modal-padding-top').length) {
                $('#grp-modal-padding-top').val(customization.padding_top || 0);
                $('#grp-modal-padding-right').val(customization.padding_right || 0);
                $('#grp-modal-padding-bottom').val(customization.padding_bottom || 0);
                $('#grp-modal-padding-left').val(customization.padding_left || 0);
            }
            if ($('#grp-modal-border-top-left').length) {
                $('#grp-modal-border-top-left').val(customization.border_radius_top_left || 0);
                $('#grp-modal-border-top-right').val(customization.border_radius_top_right || 0);
                $('#grp-modal-border-bottom-right').val(customization.border_radius_bottom_right || 0);
                $('#grp-modal-border-bottom-left').val(customization.border_radius_bottom_left || 0);
            }
            if ($modalTextColorOpacity.length) {
                var textOpacity = clampValue(customization.text_color_opacity, 0, 100) || 100;
                $modalTextColorOpacity.val(textOpacity);
                $modalTextColorOpacityValue.text(textOpacity + '%');
            }
            if ($modalBackgroundColorOpacity.length) {
                var bgOpacity = clampValue(customization.background_color_opacity, 0, 100) || 100;
                $modalBackgroundColorOpacity.val(bgOpacity);
                $modalBackgroundColorOpacityValue.text(bgOpacity + '%');
            }
            if ($modalStarColorOpacity.length) {
                var starOpacity = clampValue(customization.star_color_opacity, 0, 100) || 100;
                $modalStarColorOpacity.val(starOpacity);
                $modalStarColorOpacityValue.text(starOpacity + '%');
            }
            if ($modalLinkColorOpacity.length) {
                var linkOpacity = clampValue(customization.link_color_opacity, 0, 100) || 100;
                $modalLinkColorOpacity.val(linkOpacity);
                $modalLinkColorOpacityValue.text(linkOpacity + '%');
            }
            updateGradientSummaryPreview(customization);
        }

        $modalTextColor.on('change', function() {
            var color = $(this).val();
            syncTextColor(color);
            updateTemplateCustomizationValue('text_color', color);
            updatePreview();
            updateModalPreview();
        });

        $modalTextColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                syncTextColor(value);
                updateTemplateCustomizationValue('text_color', value);
                updatePreview();
                updateModalPreview();
            }
        });

        $modalTextColorOpacity.on('input change', function() {
            var value = clampValue($(this).val(), 0, 100);
            $(this).val(value);
            $modalTextColorOpacityValue.text(value + '%');
            updateTemplateCustomizationValue('text_color_opacity', value);
            updatePreview();
            updateModalPreview();
        });

        $modalLinkText.on('input', function() {
            var value = $(this).val();
            syncLinkText(value);
            updateTemplateCustomizationValue('link_text', value);
            updatePreview();
            updateModalPreview();
        });

        $modalLinkColor.on('change', function() {
            var color = $(this).val();
            syncLinkColor(color);
            updateTemplateCustomizationValue('link_color', color);
            updatePreview();
            updateModalPreview();
        });

        $modalLinkColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                syncLinkColor(value);
                updateTemplateCustomizationValue('link_color', value);
                updatePreview();
                updateModalPreview();
            }
        });

        $modalLinkColorOpacity.on('input change', function() {
            var value = clampValue($(this).val(), 0, 100);
            $(this).val(value);
            $modalLinkColorOpacityValue.text(value + '%');
            updateTemplateCustomizationValue('link_color_opacity', value);
            updatePreview();
            updateModalPreview();
        });

        $modalBackgroundColor.on('change', function() {
            var color = $(this).val();
            syncBackgroundColor(color);
            updateTemplateCustomizationValue('background_color', color);
            updatePreview();
            updateModalPreview();
        });

        $modalBackgroundColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                syncBackgroundColor(value);
                updateTemplateCustomizationValue('background_color', value);
                updatePreview();
                updateModalPreview();
            }
        });

        $modalBackgroundColorOpacity.on('input change', function() {
            var value = clampValue($(this).val(), 0, 100);
            $(this).val(value);
            $modalBackgroundColorOpacityValue.text(value + '%');
            updateTemplateCustomizationValue('background_color_opacity', value);
            updatePreview();
            updateModalPreview();
        });

        $modalGlassEffect.on('change', function() {
            $glassCheckbox.prop('checked', $(this).is(':checked'));
            updateTemplateCustomizationValue('glass_effect', $(this).is(':checked') ? 1 : 0);
            updatePreview();
            updateModalPreview();
        });

        $modalBoxShadowEnabled.on('change', function() {
            $boxShadowCheckbox.prop('checked', $(this).is(':checked'));
            updateTemplateCustomizationValue('box_shadow_enabled', $(this).is(':checked') ? 1 : 0);
            updatePreview();
            updateModalPreview();
        });

        $modalBoxShadowEdit.on('click', function() {
            $boxShadowEditTrigger.trigger('click');
        });

        $modalLogoScaleSlider.on('input', function() {
            var value = $(this).val();
            logoScaleTouched = true;
            syncLogoScale(value);
            updateTemplateCustomizationValue('logo_scale', parsePositiveInt(value));
            updatePreview();
            updateModalPreview();
        });

        $modalLogoScaleNumber.on('input', function() {
            var value = $(this).val();
            if (value === '') {
                return;
            }
            if (!isNaN(value) && value >= 10 && value <= 100) {
                logoScaleTouched = true;
                syncLogoScale(value);
                updateTemplateCustomizationValue('logo_scale', parsePositiveInt(value));
                updatePreview();
                updateModalPreview();
            }
        });

        $modalStarColor.on('change', function() {
            var color = $(this).val();
            $modalStarColorText.val(color);
            $starColorInput.val(color);
            $starColorText.val(color);
            updateTemplateCustomizationValue('star_color', color);
            updatePreview();
            updateModalPreview();
        });

        $modalStarColorText.on('input', function() {
            var value = $(this).val();
            if (isValidHex(value)) {
                $modalStarColor.val(value);
                $starColorInput.val(value);
                $starColorText.val(value);
                updateTemplateCustomizationValue('star_color', value);
                updatePreview();
                updateModalPreview();
            }
        });

        $modalStarColorOpacity.on('input change', function() {
            var value = clampValue($(this).val(), 0, 100);
            $(this).val(value);
            $modalStarColorOpacityValue.text(value + '%');
            updateTemplateCustomizationValue('star_color_opacity', value);
            updatePreview();
            updateModalPreview();
        });

        $modalStarPlacement.on('change', function() {
            $starPlacementSelect.val($(this).val());
            updateTemplateCustomizationValue('star_placement', $(this).val());
            updatePreview();
            updateModalPreview();
        });

        $modalMessageText.on('input', function() {
            updateTemplateCustomizationValue('message_text', $(this).val());
            updatePreview();
            updateModalPreview();
        });

        bindModalNumberInput($modalPaddingTop, 'padding_top');
        bindModalNumberInput($modalPaddingRight, 'padding_right');
        bindModalNumberInput($modalPaddingBottom, 'padding_bottom');
        bindModalNumberInput($modalPaddingLeft, 'padding_left');
        bindModalNumberInput($modalBorderTopLeft, 'border_radius_top_left');
        bindModalNumberInput($modalBorderTopRight, 'border_radius_top_right');
        bindModalNumberInput($modalBorderBottomRight, 'border_radius_bottom_right');
        bindModalNumberInput($modalBorderBottomLeft, 'border_radius_bottom_left');

        $modalFontFamily.on('change', function() {
            var value = $(this).val();
            $fontFamilyInput.val(value);
            updateTemplateCustomizationValue('font_family', value);
            updatePreview();
            updateModalPreview();
        });

        $maxWidthInput.on('input', function() {
            var value = parsePositiveInt($(this).val());
            updateTemplateCustomizationValue('max_width', value);
            updatePreview();
            updateModalPreview();
        });
        $maxHeightInput.on('input', function() {
            var value = parsePositiveInt($(this).val());
            updateTemplateCustomizationValue('max_height', value);
            updatePreview();
            updateModalPreview();
        });
        $fontFamilyInput.on('change input', function() {
            var value = $(this).val();
            updateTemplateCustomizationValue('font_family', value);
            updatePreview();
            updateModalPreview();
        });
        $boxShadowCheckbox.on('change', function() {
            var enabled = $(this).is(':checked') ? 1 : 0;
            updateTemplateCustomizationValue('box_shadow_enabled', enabled);
            updatePreview();
            updateModalPreview();
        });
        $boxShadowValue.on('input', function() {
            updateTemplateCustomizationValue('box_shadow_value', $(this).val());
            updatePreview();
            updateModalPreview();
        });
        $glassCheckbox.on('change', function() {
            updateTemplateCustomizationValue('glass_effect', $(this).is(':checked') ? 1 : 0);
            updatePreview();
            updateModalPreview();
        });

        // Initialize preview on load
        updatePreview();
        
        // Copy shortcode
        $('.grp-copy-shortcode').on('click', function() {
            var shortcode = $(this).data('shortcode');
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(shortcode).select();
            document.execCommand('copy');
            $temp.remove();
            $(this).text('Copied!');
            var $btn = $(this);
            setTimeout(function() {
                $btn.text('Copy');
            }, 2000);
        });
        
        // Generate QR code
        $('#grp-generate-qr').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var originalText = $btn.text();
            $btn.prop('disabled', true).text('Generating...');
            
            var size = $('#grp-qr-size').val();
            
            // Check if grpWidgets is defined
            if (typeof grpWidgets === 'undefined') {
                console.error('grpWidgets is not defined. Make sure widgets-admin.js is properly enqueued.');
                alert('An error occurred. Please refresh the page and try again.');
                $btn.prop('disabled', false).text(originalText);
                return;
            }
            
            $.ajax({
                url: grpWidgets.ajax_url,
                type: 'POST',
                data: {
                    action: 'grp_generate_qr',
                    nonce: grpWidgets.nonce,
                    size: size
                },
                success: function(response) {
                    if (response.success) {
                        $('#grp-qr-preview').html('<img src="' + response.data.qr_url + '" alt="QR Code" style="max-width: 100%;">');
                        $('#grp-qr-download').show();
                        $('#grp-qr-download-link').attr('href', response.data.qr_url);
                    } else {
                        var errorMsg = response.data || 'Failed to generate QR code';
                        alert(errorMsg);
                        console.error('QR Code generation failed:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('QR Code generation error:', error, xhr.responseText);
                    var errorMsg = 'An error occurred while generating the QR code. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.data) {
                        errorMsg = xhr.responseJSON.data;
                    }
                    alert(errorMsg);
                },
                complete: function() {
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        });
    });
    
})(jQuery);


