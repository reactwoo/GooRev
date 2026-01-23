/**
 * Google Reviews Gutenberg Block
 */

(function(blocks, element, components, i18n, serverSideRender, blockEditor) {
    'use strict';

    // Global fallback for isProUser to prevent ReferenceError in case of scope issues
    // Local variables with the same name inside block edit functions will override this.
    if (typeof window !== 'undefined' && typeof window.grpIsProUser === 'undefined') {
        window.grpIsProUser = false;
    }

    // Check if required components are available
    if (!blocks || !element || !components || !i18n) {
        console.error('Google Reviews Gutenberg: Required dependencies not available', {
            blocks: !!blocks,
            element: !!element,
            components: !!components,
            i18n: !!i18n
        });
        return;
    }

    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    
    // InspectorControls is in blockEditor (wp.blockEditor) in newer WordPress, or wp.editor in older versions
    // Fallback to blocks for very old WordPress versions
    var InspectorControls = (blockEditor && blockEditor.InspectorControls) || 
                            (blocks && blocks.InspectorControls) ||
                            (window.wp && window.wp.blockEditor && window.wp.blockEditor.InspectorControls) ||
                            (window.wp && window.wp.editor && window.wp.editor.InspectorControls) ||
                            null;
    
    // Check InspectorControls
    if (!InspectorControls) {
        console.error('Google Reviews Gutenberg: InspectorControls not available', {
            blockEditor: !!blockEditor,
            'blockEditor.InspectorControls': !!(blockEditor && blockEditor.InspectorControls),
            'blocks.InspectorControls': !!(blocks && blocks.InspectorControls),
            'wp.blockEditor': !!(window.wp && window.wp.blockEditor),
            'wp.editor': !!(window.wp && window.wp.editor)
        });
        return;
    }
    
    // Check if all required components are available
    if (!components.PanelBody || !components.SelectControl || !components.ToggleControl) {
        console.error('Google Reviews Gutenberg: Required WordPress components not available', {
            PanelBody: !!components.PanelBody,
            SelectControl: !!components.SelectControl,
            ToggleControl: !!components.ToggleControl,
            RangeControl: !!components.RangeControl,
            TextControl: !!components.TextControl
        });
        return;
    }
    
    var PanelBody = components.PanelBody;
    var SelectControl = components.SelectControl;
    var ToggleControl = components.ToggleControl;
    // RangeControl might not be available in older WordPress versions - check and provide fallback
    var RangeControl = components.RangeControl;
    if (!RangeControl) {
        console.warn('Google Reviews Gutenberg: RangeControl not available, some features may be limited');
        RangeControl = function(props) {
            return el('div', { style: { marginBottom: '10px' } },
                el('label', { style: { display: 'block', marginBottom: '5px' } }, props.label),
                el('input', {
                    type: 'number',
                    value: props.value !== undefined ? props.value : (props.default || 0),
                    min: props.min || 0,
                    max: props.max || 100,
                    onChange: function(e) {
                        if (props.onChange) {
                            props.onChange(parseInt(e.target.value) || 0);
                        }
                    },
                    style: { width: '100%', padding: '8px' }
                })
            );
        };
    }
    
    var TextControl = components.TextControl;
    if (!TextControl && components.TextareaControl) {
        TextControl = components.TextareaControl;
    }
    if (!TextControl) {
        console.warn('Google Reviews Gutenberg: TextControl not available, some features may be limited');
        TextControl = function(props) {
            return el('div', { style: { marginBottom: '10px' } },
                el('label', { style: { display: 'block', marginBottom: '5px' } }, props.label),
                el('input', {
                    type: 'text',
                    value: props.value || '',
                    onChange: function(e) {
                        if (props.onChange) {
                            props.onChange(e.target.value);
                        }
                    },
                    style: { width: '100%', padding: '8px' }
                })
            );
        };
    }
    // ServerSideRender is now a separate package in newer WordPress versions
    // Try multiple locations: wp.serverSideRender, wp.editor.ServerSideRender, blocks.ServerSideRender
    var ServerSideRender = (serverSideRender && serverSideRender.default) || serverSideRender || 
                            (window.wp && window.wp.serverSideRender) || 
                            (window.wp && window.wp.editor && window.wp.editor.ServerSideRender) ||
                            (blocks && blocks.ServerSideRender) || 
                            null;
    
    console.log('Google Reviews Gutenberg: Starting block registration...');
    console.log('ServerSideRender available:', !!ServerSideRender, {
        'serverSideRender param': !!serverSideRender,
        'wp.serverSideRender': !!(window.wp && window.wp.serverSideRender),
        'wp.editor.ServerSideRender': !!(window.wp && window.wp.editor && window.wp.editor.ServerSideRender),
        'blocks.ServerSideRender': !!(blocks && blocks.ServerSideRender)
    });

    // Check if registerBlockType is available
    if (typeof registerBlockType === 'undefined') {
        console.error('Google Reviews Gutenberg: registerBlockType is not available');
        return;
    }

    console.log('Registering Google Reviews Gutenberg block...');

    try {
        registerBlockType('google-reviews/reviews', {
        title: i18n.__('Google Reviews', 'google-reviews-plugin'),
        description: i18n.__('Display Google Business reviews with customizable styles and layouts.', 'google-reviews-plugin'),
        icon: 'star-filled',
        category: 'widgets',
        keywords: [
            i18n.__('google', 'google-reviews-plugin'),
            i18n.__('reviews', 'google-reviews-plugin'),
            i18n.__('testimonials', 'google-reviews-plugin'),
            i18n.__('ratings', 'google-reviews-plugin')
        ],
        attributes: {
            style: {
                type: 'string',
                default: 'modern'
            },
            theme: {
                type: 'string',
                default: 'light'
            },
            layout: {
                type: 'string',
                default: 'carousel'
            },
            cols_desktop: {
                type: 'number',
                default: 3
            },
            cols_tablet: {
                type: 'number',
                default: 2
            },
            cols_mobile: {
                type: 'number',
                default: 1
            },
            gap: {
                type: 'number',
                default: 20
            },
            count: {
                type: 'number',
                default: 5
            },
            min_rating: {
                type: 'number',
                default: 1
            },
            max_rating: {
                type: 'number',
                default: 5
            },
            sort_by: {
                type: 'string',
                default: 'newest'
            },
            show_avatar: {
                type: 'boolean',
                default: true
            },
            show_date: {
                type: 'boolean',
                default: true
            },
            show_rating: {
                type: 'boolean',
                default: true
            },
            show_reply: {
                type: 'boolean',
                default: true
            },
            autoplay: {
                type: 'boolean',
                default: true
            },
            speed: {
                type: 'number',
                default: 5000
            },
            dots: {
                type: 'boolean',
                default: true
            },
            arrows: {
                type: 'boolean',
                default: true
            },
            consistent_height: {
                type: 'boolean',
                default: false
            },
            // Creative style specific attributes
            // Note: creative_background is removed as it's an object type that causes REST API validation errors
            // Instead, we use creative_gradient_type, creative_gradient_angle, creative_gradient_start, creative_gradient_end
            creative_gradient_type: {
                type: 'string',
                default: 'linear'
            },
            creative_gradient_angle: {
                type: 'number',
                default: 135
            },
            creative_gradient_start: {
                type: 'string',
                default: '#4285F4'
            },
            creative_gradient_end: {
                type: 'string',
                default: '#EA4335'
            },
            creative_text_color: {
                type: 'string',
                default: '#ffffff'
            },
            creative_date_color: {
                type: 'string',
                default: '#ffffff'
            },
            creative_star_color: {
                type: 'string',
                default: '#FFD700'
            },
            creative_glass_effect: {
                type: 'string',
                default: 'no'
            },
            creative_border_radius_value: {
                type: 'number',
                default: 16
            },
            creative_avatar_size: {
                type: 'number',
                default: 80
            },
            creative_star_size: {
                type: 'number',
                default: 32
            },
            arrow_size: {
                type: 'number',
                default: 40
            },
            arrow_icon_size: {
                type: 'number',
                default: 18
            },
            arrow_background_color: {
                type: 'string',
                default: 'rgba(0, 0, 0, 0.5)'
            },
            arrow_hover_background_color: {
                type: 'string',
                default: 'rgba(0, 0, 0, 0.7)'
            },
            arrow_icon_color: {
                type: 'string',
                default: '#ffffff'
            },
            arrow_border_radius: {
                type: 'number',
                default: 50
            },
            arrow_horizontal_position: {
                type: 'number',
                default: 0
            },
            arrow_vertical_position: {
                type: 'number',
                default: 0
            },
            arrow_icon: {
                type: 'string',
                default: 'chevron'
            },
            arrow_box_shadow: {
                type: 'string',
                default: ''
            },
            // Style customization attributes
            custom_text_color: {
                type: 'string',
                default: ''
            },
            custom_background_color: {
                type: 'string',
                default: ''
            },
            custom_border_color: {
                type: 'string',
                default: ''
            },
            custom_accent_color: {
                type: 'string',
                default: ''
            },
            custom_star_color: {
                type: 'string',
                default: ''
            },
            custom_font_size: {
                type: 'number',
                default: 0
            },
            custom_name_font_size: {
                type: 'number',
                default: 0
            },
            custom_padding: {
                type: 'number',
                default: 16
            },
            custom_border_radius: {
                type: 'number',
                default: 8
            },
            custom_box_shadow: {
                type: 'string',
                default: ''
            },
            custom_text_align: {
                type: 'string',
                default: 'left'
            },
            custom_avatar_size: {
                type: 'number',
                default: 40
            },
            // Dot styling attributes
            dot_color: {
                type: 'string',
                default: '#ccc'
            },
            dot_active_color: {
                type: 'string',
                default: '#007cba'
            },
            dot_size: {
                type: 'number',
                default: 12
            },
            dot_spacing: {
                type: 'number',
                default: 8
            },
            dot_border_radius: {
                type: 'number',
                default: 50
            }
        },
        
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            
            // Check if user has pro license (passed from PHP)
            // Handle both boolean and string ('1'/'0') formats from PHP
            var isProValue = (typeof window.grp_gutenberg !== 'undefined' && window.grp_gutenberg.isPro);
            var isProUser = isProValue === true || isProValue === 1 || isProValue === '1';
            
            // Get responsive columns based on viewport (for editor preview)
            // Use matchMedia to detect viewport size in editor
            function getColumnsForViewport() {
                if (typeof window === 'undefined' || !window.matchMedia) {
                    return attributes.cols_desktop || 3;
                }
                var isMobile = window.matchMedia('(max-width: 640px)').matches;
                var isTablet = window.matchMedia('(max-width: 1024px)').matches && !isMobile;
                
                if (isMobile) {
                    return attributes.cols_mobile || 1;
                } else if (isTablet) {
                    return attributes.cols_tablet || 2;
                } else {
                    return attributes.cols_desktop || 3;
                }
            }
            
            var effectiveColumns = getColumnsForViewport();
            
            // License is now checked once per block render; avoid noisy console logs in production

            // Get style options with fallback
            var styleOptions = [];
            if (typeof window.grp_gutenberg !== 'undefined' && window.grp_gutenberg.styles) {
                styleOptions = window.grp_gutenberg.styles
                    .filter(function(style) {
                        // Filter out creative style for free users
                        return isProUser || style.value !== 'creative';
                    })
                    .map(function(style) {
                        return {
                            label: style.label,
                            value: style.value
                        };
                    });
            } else {
                // Fallback style options
                styleOptions = [
                    { label: i18n.__('Modern', 'google-reviews-plugin'), value: 'modern' },
                    { label: i18n.__('Classic', 'google-reviews-plugin'), value: 'classic' },
                    { label: i18n.__('Minimal', 'google-reviews-plugin'), value: 'minimal' }
                ];
                if (isProUser) {
                    styleOptions.push({ label: i18n.__('Creative', 'google-reviews-plugin'), value: 'creative' });
                }
            }

            var layoutOptions = [
                { label: i18n.__('Carousel', 'google-reviews-plugin'), value: 'carousel' },
                { label: i18n.__('Grid', 'google-reviews-plugin'), value: 'grid' },
                { label: i18n.__('List', 'google-reviews-plugin'), value: 'list' }
            ];

            // Add grid_carousel only for pro users
            if (isProUser) {
                layoutOptions.push({ label: i18n.__('Grid Carousel', 'google-reviews-plugin'), value: 'grid_carousel' });
            }

            var themeOptions = [
                { label: i18n.__('Light', 'google-reviews-plugin'), value: 'light' },
                { label: i18n.__('Dark', 'google-reviews-plugin'), value: 'dark' },
                { label: i18n.__('Auto', 'google-reviews-plugin'), value: 'auto' }
            ];
            
            var sortOptions = [
                { label: i18n.__('Newest First', 'google-reviews-plugin'), value: 'newest' },
                { label: i18n.__('Oldest First', 'google-reviews-plugin'), value: 'oldest' },
                { label: i18n.__('Highest Rating', 'google-reviews-plugin'), value: 'highest_rating' },
                { label: i18n.__('Lowest Rating', 'google-reviews-plugin'), value: 'lowest_rating' }
            ];
            
            var ratingOptions = [
                { label: '1 ' + i18n.__('Star', 'google-reviews-plugin'), value: 1 },
                { label: '2 ' + i18n.__('Stars', 'google-reviews-plugin'), value: 2 },
                { label: '3 ' + i18n.__('Stars', 'google-reviews-plugin'), value: 3 },
                { label: '4 ' + i18n.__('Stars', 'google-reviews-plugin'), value: 4 },
                { label: '5 ' + i18n.__('Stars', 'google-reviews-plugin'), value: 5 }
            ];
            
            return [
                el(InspectorControls, {},
                    el(PanelBody, { title: i18n.__('Content Settings', 'google-reviews-plugin'), initialOpen: true },
                        el(SelectControl, {
                            label: i18n.__('Style', 'google-reviews-plugin'),
                            value: attributes.style || 'modern',
                            options: styleOptions,
                            onChange: function(value) {
                                setAttributes({ style: value });
                            }
                        }),
                        el(SelectControl, {
                            label: i18n.__('Theme', 'google-reviews-plugin'),
                            value: attributes.theme || 'light',
                            options: themeOptions,
                            onChange: function(value) {
                                setAttributes({ theme: value });
                            }
                        }),
                        el(SelectControl, {
                            label: i18n.__('Layout Type', 'google-reviews-plugin'),
                            value: attributes.layout || 'carousel',
                            options: layoutOptions,
                            onChange: function(value) {
                                setAttributes({ layout: value });
                            }
                        }),
                        el(RangeControl, {
                            label: i18n.__('Columns (Desktop)', 'google-reviews-plugin'),
                            value: attributes.cols_desktop || 3,
                            onChange: function(value) {
                                setAttributes({ cols_desktop: value });
                            },
                            min: 1,
                            max: 6
                        }),
                        el(RangeControl, {
                            label: i18n.__('Gap (px)', 'google-reviews-plugin'),
                            value: attributes.gap || 20,
                            onChange: function(value) {
                                setAttributes({ gap: value });
                            },
                            min: 0,
                            max: 60
                        }),
                        isProUser ? el('div', {},
                            el(RangeControl, {
                                label: i18n.__('Columns (Tablet)', 'google-reviews-plugin'),
                                value: attributes.cols_tablet || 2,
                                onChange: function(value) {
                                    setAttributes({ cols_tablet: value });
                                },
                                min: 1,
                                max: 4
                            }),
                            el(RangeControl, {
                                label: i18n.__('Columns (Mobile)', 'google-reviews-plugin'),
                                value: attributes.cols_mobile || 1,
                                onChange: function(value) {
                                    setAttributes({ cols_mobile: value });
                                },
                                min: 1,
                                max: 3
                            })
                        ) : el('div', {
                            style: {
                                background: '#f0f8ff',
                                border: '1px solid #007cba',
                                padding: '10px',
                                marginBottom: '10px',
                                borderRadius: '4px'
                            }
                        },
                            el('strong', {}, '📐 Responsive Column Controls'),
                            el('br'),
                            el('span', {}, 'Upgrade to Pro to customize tablet and mobile column counts. '),
                            el('a', {
                                href: 'https://reactwoo.com/google-reviews-plugin-pro/',
                                target: '_blank',
                                style: { color: '#007cba', textDecoration: 'underline' }
                            }, 'Learn More')
                        ),
                        el(RangeControl, {
                            label: i18n.__('Number of Reviews', 'google-reviews-plugin'),
                            value: attributes.count || 10,
                            onChange: function(value) {
                                setAttributes({ count: value });
                            },
                            min: 1,
                            max: 20
                        }),
                        el(SelectControl, {
                            label: i18n.__('Minimum Rating', 'google-reviews-plugin'),
                            value: attributes.min_rating || 1,
                            options: ratingOptions,
                            onChange: function(value) {
                                setAttributes({ min_rating: parseInt(value) });
                            }
                        }),
                        el(SelectControl, {
                            label: i18n.__('Maximum Rating', 'google-reviews-plugin'),
                            value: attributes.max_rating || 5,
                            options: ratingOptions,
                            onChange: function(value) {
                                setAttributes({ max_rating: parseInt(value) });
                            }
                        }),
                        el(SelectControl, {
                            label: i18n.__('Sort By', 'google-reviews-plugin'),
                            value: attributes.sort_by || 'newest',
                            options: sortOptions,
                            onChange: function(value) {
                                setAttributes({ sort_by: value });
                            }
                        })
                    ),
                    
                    el(PanelBody, { title: i18n.__('Display Options', 'google-reviews-plugin'), initialOpen: false },
                        el(ToggleControl, {
                            label: i18n.__('Show Avatar', 'google-reviews-plugin'),
                            checked: attributes.show_avatar,
                            onChange: function(value) {
                                setAttributes({ show_avatar: value });
                            }
                        }),
                        el(ToggleControl, {
                            label: i18n.__('Show Date', 'google-reviews-plugin'),
                            checked: attributes.show_date,
                            onChange: function(value) {
                                setAttributes({ show_date: value });
                            }
                        }),
                        el(ToggleControl, {
                            label: i18n.__('Show Rating', 'google-reviews-plugin'),
                            checked: attributes.show_rating,
                            onChange: function(value) {
                                setAttributes({ show_rating: value });
                            }
                        }),
                        el(ToggleControl, {
                            label: i18n.__('Show Business Reply', 'google-reviews-plugin'),
                            checked: attributes.show_reply,
                            onChange: function(value) {
                                setAttributes({ show_reply: value });
                            }
                        }),
                        el(ToggleControl, {
                            label: i18n.__('Consistent Card Height', 'google-reviews-plugin'),
                            checked: attributes.consistent_height || false,
                            onChange: function(value) {
                                setAttributes({ consistent_height: value });
                            }
                        })
                    ),
                    
                    el(PanelBody, {
                        title: i18n.__('Carousel Options', 'google-reviews-plugin'),
                        initialOpen: false,
                        className: (attributes.layout !== 'carousel' && attributes.layout !== 'grid_carousel') ? 'grp-hidden' : ''
                    },
                        el('div', {},
                            el(ToggleControl, {
                                label: i18n.__('Show Arrows', 'google-reviews-plugin'),
                                checked: attributes.arrows !== undefined ? attributes.arrows : true,
                                onChange: function(value) {
                                    setAttributes({ arrows: value });
                                }
                            }),
                            el(ToggleControl, {
                                label: i18n.__('Show Dots', 'google-reviews-plugin'),
                                checked: attributes.dots !== undefined ? attributes.dots : true,
                                onChange: function(value) {
                                    setAttributes({ dots: value });
                                }
                            }),
                            isProUser ? el('div', {},
                                el(ToggleControl, {
                                    label: i18n.__('Autoplay', 'google-reviews-plugin'),
                                    checked: attributes.autoplay,
                                    onChange: function(value) {
                                        setAttributes({ autoplay: value });
                                    }
                                }),
                                el(RangeControl, {
                                    label: i18n.__('Speed (ms)', 'google-reviews-plugin'),
                                    value: attributes.speed,
                                    onChange: function(value) {
                                        setAttributes({ speed: value });
                                    },
                                    min: 1000,
                                    max: 10000,
                                    step: 500,
                                    disabled: !attributes.autoplay
                                }),
                                el('div', { style: { marginTop: '16px', paddingTop: '16px', borderTop: '1px solid #ddd' } },
                                    el('strong', { style: { display: 'block', marginBottom: '8px' } }, i18n.__('Arrow Styling', 'google-reviews-plugin')),
                                    el('div', { style: { marginBottom: '12px' } },
                                        el('label', { style: { display: 'block', marginBottom: '4px', fontSize: '12px' } }, i18n.__('Arrow Size (px)', 'google-reviews-plugin')),
                                        el(RangeControl, {
                                            value: attributes.arrow_size || 32,
                                            onChange: function(value) {
                                                setAttributes({ arrow_size: value });
                                            },
                                            min: 20,
                                            max: 60,
                                            step: 2
                                        })
                                    ),
                                    el('div', { style: { marginBottom: '12px' } },
                                        el('label', { style: { display: 'block', marginBottom: '4px', fontSize: '12px' } }, i18n.__('Arrow Icon Color', 'google-reviews-plugin')),
                                        el(TextControl, {
                                            type: 'color',
                                            value: attributes.arrow_icon_color || '#ffffff',
                                            onChange: function(value) {
                                                setAttributes({ arrow_icon_color: value });
                                            }
                                        })
                                    ),
                                    el('div', { style: { marginBottom: '12px' } },
                                        el('label', { style: { display: 'block', marginBottom: '4px', fontSize: '12px' } }, i18n.__('Arrow Background Color', 'google-reviews-plugin')),
                                        el(TextControl, {
                                            type: 'color',
                                            value: attributes.arrow_background_color || 'rgba(0, 0, 0, 0.5)',
                                            onChange: function(value) {
                                                setAttributes({ arrow_background_color: value });
                                            }
                                        })
                                    ),
                                    el('div', { style: { marginBottom: '12px' } },
                                        el('label', { style: { display: 'block', marginBottom: '4px', fontSize: '12px' } }, i18n.__('Arrow Hover Background', 'google-reviews-plugin')),
                                        el(TextControl, {
                                            type: 'color',
                                            value: attributes.arrow_hover_background_color || 'rgba(0, 0, 0, 0.7)',
                                            onChange: function(value) {
                                                setAttributes({ arrow_hover_background_color: value });
                                            }
                                        })
                                    ),
                                    el('div', { style: { marginBottom: '12px' } },
                                        el('label', { style: { display: 'block', marginBottom: '4px', fontSize: '12px' } }, i18n.__('Arrow Icon Size (px)', 'google-reviews-plugin')),
                                        el(RangeControl, {
                                            value: attributes.arrow_icon_size || 18,
                                            onChange: function(value) {
                                                setAttributes({ arrow_icon_size: value });
                                            },
                                            min: 12,
                                            max: 32,
                                            step: 1
                                        })
                                    ),
                                    el(SelectControl, {
                                        label: i18n.__('Arrow Icon', 'google-reviews-plugin'),
                                        value: attributes.arrow_icon || 'chevron',
                                        options: [
                                            { label: i18n.__('Chevron', 'google-reviews-plugin'), value: 'chevron' },
                                            { label: i18n.__('Angle', 'google-reviews-plugin'), value: 'angle' },
                                            { label: i18n.__('Double', 'google-reviews-plugin'), value: 'double' },
                                            { label: i18n.__('Arrow', 'google-reviews-plugin'), value: 'arrow' }
                                        ],
                                        onChange: function(value) {
                                            setAttributes({ arrow_icon: value });
                                        }
                                    }),
                                    el('div', { style: { marginBottom: '12px' } },
                                        el('label', { style: { display: 'block', marginBottom: '4px', fontSize: '12px' } }, i18n.__('Arrow Border Radius (%)', 'google-reviews-plugin')),
                                        el(RangeControl, {
                                            value: attributes.arrow_border_radius || 50,
                                            onChange: function(value) {
                                                setAttributes({ arrow_border_radius: value });
                                            },
                                            min: 0,
                                            max: 50,
                                            step: 1
                                        })
                                    ),
                                    el(TextControl, {
                                        label: i18n.__('Arrow Box Shadow', 'google-reviews-plugin'),
                                        value: attributes.arrow_box_shadow || '',
                                        onChange: function(value) {
                                            setAttributes({ arrow_box_shadow: value });
                                        },
                                        placeholder: '0 2px 6px rgba(0,0,0,0.2)'
                                    }),
                                    el('div', { style: { marginBottom: '12px' } },
                                        el('label', { style: { display: 'block', marginBottom: '4px', fontSize: '12px' } }, i18n.__('Arrow Horizontal Position (px)', 'google-reviews-plugin')),
                                        el(RangeControl, {
                                            value: attributes.arrow_horizontal_position || 0,
                                            onChange: function(value) {
                                                setAttributes({ arrow_horizontal_position: value });
                                            },
                                            min: -100,
                                            max: 100,
                                            step: 5
                                        })
                                    ),
                                    el('div', { style: { marginBottom: '12px' } },
                                        el('label', { style: { display: 'block', marginBottom: '4px', fontSize: '12px' } }, i18n.__('Arrow Vertical Position (px)', 'google-reviews-plugin')),
                                        el(RangeControl, {
                                            value: attributes.arrow_vertical_position || 0,
                                            onChange: function(value) {
                                                setAttributes({ arrow_vertical_position: value });
                                            },
                                            min: -50,
                                            max: 50,
                                            step: 1
                                        })
                                    ),
                                    // Dot Styling Controls
                                    el('div', { style: { marginTop: '20px', paddingTop: '20px', borderTop: '1px solid #ddd' } },
                                        el('h4', { style: { margin: '0 0 12px 0', fontSize: '13px', fontWeight: 'bold' } }, i18n.__('Dot Styling', 'google-reviews-plugin')),
                                        el('div', { style: { marginBottom: '12px' } },
                                            el('label', { style: { display: 'block', marginBottom: '4px', fontSize: '12px' } }, i18n.__('Dot Color', 'google-reviews-plugin')),
                                            el(TextControl, {
                                                type: 'color',
                                                value: attributes.dot_color || '#ccc',
                                                onChange: function(value) {
                                                    setAttributes({ dot_color: value });
                                                }
                                            })
                                        ),
                                        el('div', { style: { marginBottom: '12px' } },
                                            el('label', { style: { display: 'block', marginBottom: '4px', fontSize: '12px' } }, i18n.__('Active Dot Color', 'google-reviews-plugin')),
                                            el(TextControl, {
                                                type: 'color',
                                                value: attributes.dot_active_color || '#007cba',
                                                onChange: function(value) {
                                                    setAttributes({ dot_active_color: value });
                                                }
                                            })
                                        ),
                                        el('div', { style: { marginBottom: '12px' } },
                                            el('label', { style: { display: 'block', marginBottom: '4px', fontSize: '12px' } }, i18n.__('Dot Size (px)', 'google-reviews-plugin')),
                                            el(RangeControl, {
                                                value: attributes.dot_size || 12,
                                                onChange: function(value) {
                                                    setAttributes({ dot_size: value });
                                                },
                                                min: 6,
                                                max: 24,
                                                step: 1
                                            })
                                        ),
                                        el('div', { style: { marginBottom: '12px' } },
                                            el('label', { style: { display: 'block', marginBottom: '4px', fontSize: '12px' } }, i18n.__('Dot Spacing (px)', 'google-reviews-plugin')),
                                            el(RangeControl, {
                                                value: attributes.dot_spacing || 8,
                                                onChange: function(value) {
                                                    setAttributes({ dot_spacing: value });
                                                },
                                                min: 0,
                                                max: 30,
                                                step: 1
                                            })
                                        ),
                                        el('div', { style: { marginBottom: '12px' } },
                                            el('label', { style: { display: 'block', marginBottom: '4px', fontSize: '12px' } }, i18n.__('Dot Border Radius (%)', 'google-reviews-plugin')),
                                            el(RangeControl, {
                                                value: attributes.dot_border_radius || 50,
                                                onChange: function(value) {
                                                    setAttributes({ dot_border_radius: value });
                                                },
                                                min: 0,
                                                max: 50,
                                                step: 1
                                            })
                                        )
                                    )
                                )
                            ) : el('div', {
                            style: {
                                background: '#f0f8ff',
                                border: '1px solid #007cba',
                                padding: '10px',
                                marginBottom: '10px',
                                borderRadius: '4px'
                            }
                        },
                            el('strong', {}, '⚙️ Carousel Controls'),
                            el('br'),
                            el('span', {}, 'Upgrade to Pro to customize autoplay speed, show/hide dots and arrows. '),
                            el('a', {
                                href: 'https://reactwoo.com/google-reviews-plugin-pro/',
                                target: '_blank',
                                style: { color: '#007cba', textDecoration: 'underline' }
                            }, 'Learn More')
                        )
                        )  // Close outer div wrapper from line 533
                    ),

                    el(PanelBody, {
                        title: i18n.__('Style Customization', 'google-reviews-plugin'),
                        initialOpen: false
                    },
                        isProUser ? el('div', {},
                            el('div', { style: { marginBottom: '16px' } },
                                el('label', { style: { display: 'block', marginBottom: '8px', fontWeight: 'bold' } },
                                    i18n.__('Text Color', 'google-reviews-plugin')
                                ),
                                el(TextControl, {
                                    type: 'color',
                                    value: attributes.custom_text_color || '',
                                    onChange: function(value) {
                                        setAttributes({ custom_text_color: value });
                                    },
                                    placeholder: '#111827'
                                })
                            ),
                            el('div', { style: { marginBottom: '16px' } },
                                el('label', { style: { display: 'block', marginBottom: '8px', fontWeight: 'bold' } },
                                    i18n.__('Card Background Color', 'google-reviews-plugin')
                                ),
                                el(TextControl, {
                                    type: 'color',
                                    value: attributes.custom_background_color || '',
                                    onChange: function(value) {
                                        setAttributes({ custom_background_color: value });
                                    },
                                    placeholder: '#FFFFFF'
                                })
                            ),
                            (attributes.style === 'classic' || attributes.style === 'corporate') ? el('div', { style: { marginBottom: '16px' } },
                                el('label', { style: { display: 'block', marginBottom: '8px', fontWeight: 'bold' } },
                                    i18n.__('Border Color', 'google-reviews-plugin')
                                ),
                                el(TextControl, {
                                    type: 'color',
                                    value: attributes.custom_border_color || '',
                                    onChange: function(value) {
                                        setAttributes({ custom_border_color: value });
                                    },
                                    placeholder: '#D1D5DB'
                                })
                            ) : el('div'),
                            (attributes.style === 'modern' || attributes.style === 'corporate' || attributes.style === 'minimal') ? el('div', { style: { marginBottom: '16px' } },
                                el('label', { style: { display: 'block', marginBottom: '8px', fontWeight: 'bold' } },
                                    i18n.__('Accent Color', 'google-reviews-plugin')
                                ),
                                el(TextControl, {
                                    type: 'color',
                                    value: attributes.custom_accent_color || '',
                                    onChange: function(value) {
                                        setAttributes({ custom_accent_color: value });
                                    },
                                    placeholder: '#4285F4'
                                })
                            ) : el('div'),
                            el('div', { style: { marginBottom: '16px' } },
                                el('label', { style: { display: 'block', marginBottom: '8px', fontWeight: 'bold' } },
                                    i18n.__('Star Color', 'google-reviews-plugin')
                                ),
                                el(TextControl, {
                                    type: 'color',
                                    value: attributes.custom_star_color || '',
                                    onChange: function(value) {
                                        setAttributes({ custom_star_color: value });
                                    },
                                    placeholder: '#FBBC05'
                                })
                            ),
                            el(RangeControl, {
                                label: i18n.__('Body Text Size (px)', 'google-reviews-plugin'),
                                value: attributes.custom_font_size || 15,
                                onChange: function(value) {
                                    setAttributes({ custom_font_size: value });
                                },
                                min: 10,
                                max: 24,
                                step: 1
                            }),
                            el(RangeControl, {
                                label: i18n.__('Name Text Size (px)', 'google-reviews-plugin'),
                                value: attributes.custom_name_font_size || 14,
                                onChange: function(value) {
                                    setAttributes({ custom_name_font_size: value });
                                },
                                min: 10,
                                max: 20,
                                step: 1
                            }),
                            el(SelectControl, {
                                label: i18n.__('Text Alignment', 'google-reviews-plugin'),
                                value: attributes.custom_text_align || 'left',
                                options: [
                                    { label: i18n.__('Left', 'google-reviews-plugin'), value: 'left' },
                                    { label: i18n.__('Center', 'google-reviews-plugin'), value: 'center' },
                                    { label: i18n.__('Right', 'google-reviews-plugin'), value: 'right' }
                                ],
                                onChange: function(value) {
                                    setAttributes({ custom_text_align: value });
                                }
                            }),
                            el(RangeControl, {
                                label: i18n.__('Card Padding (px)', 'google-reviews-plugin'),
                                value: attributes.custom_padding !== undefined ? attributes.custom_padding : 16,
                                onChange: function(value) {
                                    setAttributes({ custom_padding: value });
                                },
                                min: 0,
                                max: 40,
                                step: 1
                            }),
                            el(RangeControl, {
                                label: i18n.__('Card Border Radius (px)', 'google-reviews-plugin'),
                                value: attributes.custom_border_radius !== undefined ? attributes.custom_border_radius : 8,
                                onChange: function(value) {
                                    setAttributes({ custom_border_radius: value });
                                },
                                min: 0,
                                max: 40,
                                step: 1
                            }),
                            el(RangeControl, {
                                label: i18n.__('Avatar Size (px)', 'google-reviews-plugin'),
                                value: attributes.custom_avatar_size !== undefined ? attributes.custom_avatar_size : 40,
                                onChange: function(value) {
                                    setAttributes({ custom_avatar_size: value });
                                },
                                min: 20,
                                max: 80,
                                step: 1
                            }),
                            el(TextControl, {
                                label: i18n.__('Card Box Shadow', 'google-reviews-plugin'),
                                value: attributes.custom_box_shadow || '',
                                onChange: function(value) {
                                    setAttributes({ custom_box_shadow: value });
                                },
                                placeholder: '0 2px 6px rgba(0,0,0,0.15)'
                            })
                        ) : el('div', {
                            style: {
                                background: '#fff3cd',
                                border: '1px solid #ffc107',
                                padding: '15px',
                                marginBottom: '10px',
                                borderRadius: '4px'
                            }
                        },
                            el('strong', {}, '🎨 Advanced Styling'),
                            el('br'),
                            el('span', {}, 'Unlock unlimited customization options: colors, fonts, spacing, borders, and more. '),
                            el('a', {
                                href: 'https://reactwoo.com/google-reviews-plugin-pro/',
                                target: '_blank',
                                style: { color: '#856404', textDecoration: 'underline', fontWeight: 'bold' }
                            }, 'Upgrade to Pro')
                        )
                    ),

                    // Creative Style Options Panel
                    el(PanelBody, {
                        title: i18n.__('Creative Style Options', 'google-reviews-plugin'),
                        initialOpen: false,
                        className: (attributes.style !== 'creative') ? 'grp-hidden' : ''
                    },
                        // Gradient Background Section
                        el('div', { style: { marginBottom: '20px', padding: '15px', background: '#f8f9fa', borderRadius: '4px' } },
                            el('h4', { style: { margin: '0 0 10px 0', color: '#23282d' } }, i18n.__('Gradient Background', 'google-reviews-plugin')),
                            el('div', { style: { marginBottom: '12px' } },
                                el('label', { style: { display: 'block', marginBottom: '5px', fontWeight: 'bold' } },
                                    i18n.__('Gradient Type', 'google-reviews-plugin')
                                ),
                                el(SelectControl, {
                                    value: attributes.creative_gradient_type || 'linear',
                                    options: [
                                        { label: i18n.__('Linear', 'google-reviews-plugin'), value: 'linear' },
                                        { label: i18n.__('Radial', 'google-reviews-plugin'), value: 'radial' }
                                    ],
                                    onChange: function(value) {
                                        setAttributes({ creative_gradient_type: value });
                                    }
                                })
                            ),
                            (attributes.creative_gradient_type === 'linear') ? el(RangeControl, {
                                label: i18n.__('Angle (degrees)', 'google-reviews-plugin'),
                                value: attributes.creative_gradient_angle || 135,
                                onChange: function(value) {
                                    setAttributes({ creative_gradient_angle: value });
                                },
                                min: 0,
                                max: 360,
                                step: 1
                            }) : el('div'),
                            el('div', { style: { marginBottom: '12px' } },
                                el('label', { style: { display: 'block', marginBottom: '5px', fontWeight: 'bold' } },
                                    i18n.__('Start Color', 'google-reviews-plugin')
                                ),
                                el(TextControl, {
                                    type: 'color',
                                    value: attributes.creative_gradient_start || '#4285F4',
                                    onChange: function(value) {
                                        setAttributes({ creative_gradient_start: value });
                                    }
                                })
                            ),
                            el('div', { style: { marginBottom: '12px' } },
                                el('label', { style: { display: 'block', marginBottom: '5px', fontWeight: 'bold' } },
                                    i18n.__('End Color', 'google-reviews-plugin')
                                ),
                                el(TextControl, {
                                    type: 'color',
                                    value: attributes.creative_gradient_end || '#EA4335',
                                    onChange: function(value) {
                                        setAttributes({ creative_gradient_end: value });
                                    }
                                })
                            )
                        ),
                        // Text Colors
                        el('div', { style: { marginBottom: '16px' } },
                            el('label', { style: { display: 'block', marginBottom: '8px', fontWeight: 'bold' } },
                                i18n.__('Text Color', 'google-reviews-plugin')
                            ),
                            el(TextControl, {
                                type: 'color',
                                value: attributes.creative_text_color || '#ffffff',
                                onChange: function(value) {
                                    setAttributes({ creative_text_color: value });
                                }
                            })
                        ),
                        el('div', { style: { marginBottom: '16px' } },
                            el('label', { style: { display: 'block', marginBottom: '8px', fontWeight: 'bold' } },
                                i18n.__('Date Color', 'google-reviews-plugin')
                            ),
                            el(TextControl, {
                                type: 'color',
                                value: attributes.creative_date_color || '#ffffff',
                                onChange: function(value) {
                                    setAttributes({ creative_date_color: value });
                                }
                            })
                        ),
                        el('div', { style: { marginBottom: '16px' } },
                            el('label', { style: { display: 'block', marginBottom: '8px', fontWeight: 'bold' } },
                                i18n.__('Star Color', 'google-reviews-plugin')
                            ),
                            el(TextControl, {
                                type: 'color',
                                value: attributes.creative_star_color || '#FFD700',
                                onChange: function(value) {
                                    setAttributes({ creative_star_color: value });
                                }
                            })
                        ),
                        // Glass Effect
                        el(ToggleControl, {
                            label: i18n.__('Glass Effect', 'google-reviews-plugin'),
                            checked: (attributes.creative_glass_effect === 'yes'),
                            onChange: function(value) {
                                setAttributes({ creative_glass_effect: value ? 'yes' : 'no' });
                            }
                        }),
                        el(RangeControl, {
                            label: i18n.__('Border Radius (px)', 'google-reviews-plugin'),
                            value: attributes.creative_border_radius_value || 16,
                            onChange: function(value) {
                                setAttributes({ creative_border_radius_value: value });
                            },
                            min: 0,
                            max: 40,
                            step: 1
                        }),
                        // Avatar Size
                        el(RangeControl, {
                            label: i18n.__('Avatar Size (px)', 'google-reviews-plugin'),
                            value: attributes.creative_avatar_size || 80,
                            onChange: function(value) {
                                setAttributes({ creative_avatar_size: value });
                            },
                            min: 20,
                            max: 120,
                            step: 4
                        }),
                        // Star Size
                        el(RangeControl, {
                            label: i18n.__('Star Size (px)', 'google-reviews-plugin'),
                            value: attributes.creative_star_size || 32,
                            onChange: function(value) {
                                setAttributes({ creative_star_size: value });
                            },
                            min: 12,
                            max: 48,
                            step: 2
                        })
                    )
                ),

                    el('div', { className: 'grp-gutenberg-block-editor' },
                    el('div', { className: 'grp-block-header' },
                        el('h3', {}, i18n.__('Google Reviews', 'google-reviews-plugin')),
                        el('p', { className: 'grp-block-description' }, 
                            i18n.__('Editor preview matches frontend layout and styles.', 'google-reviews-plugin')
                        )
                    ),
                    // Use ServerSideRender for live preview if available
                    ServerSideRender ? el('div', {
                        className: 'grp-gutenberg-block-wrapper',
                        style: (function() {
                            var styleVars = {
                                '--grp-cols-desktop': (attributes.cols_desktop && attributes.cols_desktop > 0) ? attributes.cols_desktop : 3,
                                '--grp-cols-tablet': (attributes.cols_tablet && attributes.cols_tablet > 0) ? attributes.cols_tablet : 2,
                                '--grp-cols-mobile': (attributes.cols_mobile && attributes.cols_mobile > 0) ? attributes.cols_mobile : 1,
                                '--grp-cols': effectiveColumns, // Use responsive column count
                                '--grp-gap': (attributes.gap && attributes.gap > 0 ? attributes.gap : 20) + 'px'
                            };
                            
                            // Style customization variables - use both naming conventions for compatibility
                            if (attributes.custom_text_color) {
                                styleVars['--grp-text-color'] = attributes.custom_text_color;
                                styleVars['--grp-text'] = attributes.custom_text_color;
                            }
                            if (attributes.custom_background_color) {
                                styleVars['--grp-card-bg'] = attributes.custom_background_color;
                                styleVars['--grp-card_background'] = attributes.custom_background_color;
                            }
                            if (attributes.custom_border_color) {
                                styleVars['--grp-border-color'] = attributes.custom_border_color;
                                styleVars['--grp-border'] = attributes.custom_border_color;
                            }
                            if (attributes.custom_accent_color) {
                                styleVars['--grp-accent-color'] = attributes.custom_accent_color;
                                styleVars['--grp-accent'] = attributes.custom_accent_color;
                            }
                            if (attributes.custom_star_color) {
                                styleVars['--grp-star-color'] = attributes.custom_star_color;
                                styleVars['--grp-star'] = attributes.custom_star_color;
                            }
                            if (attributes.custom_font_size) {
                                styleVars['--grp-font-size'] = attributes.custom_font_size + 'px';
                                styleVars['--grp-body-size'] = attributes.custom_font_size + 'px';
                            }
                            if (attributes.custom_name_font_size) {
                                styleVars['--grp-name-font-size'] = attributes.custom_name_font_size + 'px';
                                styleVars['--grp-name-size'] = attributes.custom_name_font_size + 'px';
                            }
                            if (attributes.custom_padding !== undefined && attributes.custom_padding !== null) {
                                styleVars['--grp-card-padding'] = attributes.custom_padding + 'px';
                            }
                            if (attributes.custom_border_radius !== undefined && attributes.custom_border_radius !== null) {
                                styleVars['--grp-card-radius'] = attributes.custom_border_radius + 'px';
                            }
                            if (attributes.custom_box_shadow) {
                                styleVars['--grp-card-shadow'] = attributes.custom_box_shadow;
                            }
                            if (attributes.custom_avatar_size) {
                                styleVars['--grp-avatar-size'] = attributes.custom_avatar_size + 'px';
                            }
                            if (attributes.custom_text_align) {
                                styleVars['--grp-text-align'] = attributes.custom_text_align;
                                styleVars['--grp-meta-justify'] = attributes.custom_text_align === 'center' ? 'center' : (attributes.custom_text_align === 'right' ? 'flex-end' : 'flex-start');
                                styleVars['--grp-meta-align'] = attributes.custom_text_align === 'center' ? 'center' : (attributes.custom_text_align === 'right' ? 'flex-end' : 'flex-start');
                            }

                            if (attributes.style === 'creative') {
                                var gradientType = attributes.creative_gradient_type || 'linear';
                                var startColor = attributes.creative_gradient_start || '#4285F4';
                                var endColor = attributes.creative_gradient_end || '#EA4335';
                                var angle = attributes.creative_gradient_angle || 135;
                                var gradient = gradientType === 'radial'
                                    ? 'radial-gradient(circle, ' + startColor + ' 0%, ' + endColor + ' 100%)'
                                    : 'linear-gradient(' + angle + 'deg, ' + startColor + ' 0%, ' + endColor + ' 100%)';

                                styleVars['--grp-card-bg'] = gradient;
                                styleVars['--grp-text-color'] = attributes.creative_text_color || '#ffffff';
                                styleVars['--grp-date-color'] = attributes.creative_date_color || '#ffffff';
                                styleVars['--grp-star-color'] = attributes.creative_star_color || '#FFD700';
                                styleVars['--grp-avatar-size'] = (attributes.creative_avatar_size || 80) + 'px';
                                styleVars['--grp-star-size'] = (attributes.creative_star_size || 32) + 'px';
                                styleVars['--grp-card-radius'] = (attributes.creative_border_radius_value || 16) + 'px';

                                if (attributes.creative_glass_effect === 'yes') {
                                    styleVars['--grp-card-bg'] = 'rgba(255, 255, 255, 0.25)';
                                    styleVars['--grp-border-color'] = 'rgba(255, 255, 255, 0.3)';
                                    styleVars['--grp-creative-blur'] = '20px';
                                } else {
                                    styleVars['--grp-creative-blur'] = '0px';
                                }
                            }
                            
                            // Arrow styling variables (match Elementor naming)
                            if (attributes.arrow_size) {
                                styleVars['--grp-arrow-size'] = attributes.arrow_size + 'px';
                            }
                            if (attributes.arrow_icon_size) {
                                styleVars['--grp-arrow-icon-size'] = attributes.arrow_icon_size + 'px';
                            }
                            if (attributes.arrow_icon_color) {
                                styleVars['--grp-arrow-icon-color'] = attributes.arrow_icon_color;
                                styleVars['--grp-arrow-color'] = attributes.arrow_icon_color;
                            }
                            if (attributes.arrow_background_color) {
                                styleVars['--grp-arrow-bg'] = attributes.arrow_background_color;
                                styleVars['--grp-arrow-background-color'] = attributes.arrow_background_color;
                            }
                            if (attributes.arrow_hover_background_color) {
                                styleVars['--grp-arrow-hover-bg'] = attributes.arrow_hover_background_color;
                                styleVars['--grp-arrow-hover-background-color'] = attributes.arrow_hover_background_color;
                            }
                            if (attributes.arrow_border_radius !== undefined) {
                                styleVars['--grp-arrow-radius'] = attributes.arrow_border_radius + '%';
                                styleVars['--grp-arrow-border-radius'] = attributes.arrow_border_radius + '%';
                            }
                            if (attributes.arrow_box_shadow) {
                                styleVars['--grp-arrow-box-shadow'] = attributes.arrow_box_shadow;
                            }
                            if (attributes.arrow_horizontal_position !== undefined) {
                                styleVars['--grp-arrow-horizontal'] = attributes.arrow_horizontal_position + 'px';
                            }
                            if (attributes.arrow_vertical_position !== undefined) {
                                styleVars['--grp-arrow-vertical'] = attributes.arrow_vertical_position + 'px';
                            }
                            
                            // Dot styling variables
                            if (attributes.dot_color) {
                                styleVars['--grp-dot-color'] = attributes.dot_color;
                            }
                            if (attributes.dot_active_color) {
                                styleVars['--grp-dot-active-color'] = attributes.dot_active_color;
                            }
                            if (attributes.dot_size) {
                                styleVars['--grp-dot-size'] = attributes.dot_size + 'px';
                            }
                            if (attributes.dot_spacing !== undefined) {
                                styleVars['--grp-dot-spacing'] = attributes.dot_spacing + 'px';
                            }
                            if (attributes.dot_border_radius !== undefined) {
                                styleVars['--grp-dot-radius'] = attributes.dot_border_radius + '%';
                            }
                            
                            return styleVars;
                        })()
                    },
                        el(ServerSideRender, {
                        block: 'google-reviews/reviews',
                        attributes: (function() {
                            // Filter and sanitize attributes for ServerSideRender
                            // Only pass simple types (string, number, boolean) - not objects
                            var sanitized = {
                                style: attributes.style || 'modern',
                                theme: attributes.theme || 'light',
                                layout: attributes.layout || 'carousel',
                                count: attributes.count || 5,
                                min_rating: attributes.min_rating || 1,
                                max_rating: attributes.max_rating || 5,
                                sort_by: attributes.sort_by || 'newest',
                                show_avatar: attributes.show_avatar !== undefined ? attributes.show_avatar : true,
                                show_date: attributes.show_date !== undefined ? attributes.show_date : true,
                                show_rating: attributes.show_rating !== undefined ? attributes.show_rating : true,
                                show_reply: attributes.show_reply !== undefined ? attributes.show_reply : true,
                                autoplay: attributes.autoplay !== undefined ? attributes.autoplay : true,
                                speed: attributes.speed || 5000,
                                dots: attributes.dots !== undefined ? attributes.dots : true,
                                arrows: attributes.arrows !== undefined ? attributes.arrows : true,
                                consistent_height: attributes.consistent_height !== undefined ? attributes.consistent_height : false,
                                // Ensure column defaults are correct - free users should get 3 columns for carousel
                                cols_desktop: (attributes.cols_desktop && attributes.cols_desktop > 0) ? attributes.cols_desktop : 3,
                                cols_tablet: (attributes.cols_tablet && attributes.cols_tablet > 0) ? attributes.cols_tablet : 2,
                                cols_mobile: (attributes.cols_mobile && attributes.cols_mobile > 0) ? attributes.cols_mobile : 1,
                                gap: (attributes.gap && attributes.gap > 0) ? attributes.gap : 20
                            };
                            
                            // Add custom colors if set (for ServerSideRender)
                            if (attributes.custom_text_color && typeof attributes.custom_text_color === 'string') sanitized.custom_text_color = attributes.custom_text_color;
                            if (attributes.custom_background_color && typeof attributes.custom_background_color === 'string') sanitized.custom_background_color = attributes.custom_background_color;
                            if (attributes.custom_border_color && typeof attributes.custom_border_color === 'string') sanitized.custom_border_color = attributes.custom_border_color;
                            if (attributes.custom_accent_color && typeof attributes.custom_accent_color === 'string') sanitized.custom_accent_color = attributes.custom_accent_color;
                            if (attributes.custom_star_color && typeof attributes.custom_star_color === 'string') sanitized.custom_star_color = attributes.custom_star_color;
                            if (attributes.custom_font_size && typeof attributes.custom_font_size === 'number') sanitized.custom_font_size = attributes.custom_font_size;
                            if (attributes.custom_name_font_size && typeof attributes.custom_name_font_size === 'number') sanitized.custom_name_font_size = attributes.custom_name_font_size;
                            if (attributes.custom_padding !== undefined && typeof attributes.custom_padding === 'number') sanitized.custom_padding = attributes.custom_padding;
                            if (attributes.custom_border_radius !== undefined && typeof attributes.custom_border_radius === 'number') sanitized.custom_border_radius = attributes.custom_border_radius;
                            if (attributes.custom_avatar_size !== undefined && typeof attributes.custom_avatar_size === 'number') sanitized.custom_avatar_size = attributes.custom_avatar_size;
                            if (attributes.custom_text_align && typeof attributes.custom_text_align === 'string') sanitized.custom_text_align = attributes.custom_text_align;
                            if (attributes.custom_box_shadow && typeof attributes.custom_box_shadow === 'string') sanitized.custom_box_shadow = attributes.custom_box_shadow;
                            
                            // Add arrow styling attributes
                            if (attributes.arrow_size && typeof attributes.arrow_size === 'number') sanitized.arrow_size = attributes.arrow_size;
                            if (attributes.arrow_icon_size && typeof attributes.arrow_icon_size === 'number') sanitized.arrow_icon_size = attributes.arrow_icon_size;
                            if (attributes.arrow_background_color && typeof attributes.arrow_background_color === 'string') sanitized.arrow_background_color = attributes.arrow_background_color;
                            if (attributes.arrow_hover_background_color && typeof attributes.arrow_hover_background_color === 'string') sanitized.arrow_hover_background_color = attributes.arrow_hover_background_color;
                            if (attributes.arrow_icon_color && typeof attributes.arrow_icon_color === 'string') sanitized.arrow_icon_color = attributes.arrow_icon_color;
                            if (attributes.arrow_border_radius !== undefined && typeof attributes.arrow_border_radius === 'number') sanitized.arrow_border_radius = attributes.arrow_border_radius;
                            if (attributes.arrow_horizontal_position !== undefined && typeof attributes.arrow_horizontal_position === 'number') sanitized.arrow_horizontal_position = attributes.arrow_horizontal_position;
                            if (attributes.arrow_vertical_position !== undefined && typeof attributes.arrow_vertical_position === 'number') sanitized.arrow_vertical_position = attributes.arrow_vertical_position;
                            if (attributes.arrow_icon && typeof attributes.arrow_icon === 'string') sanitized.arrow_icon = attributes.arrow_icon;
                            if (attributes.arrow_box_shadow && typeof attributes.arrow_box_shadow === 'string') sanitized.arrow_box_shadow = attributes.arrow_box_shadow;
                            
                            // Add dot styling attributes
                            if (attributes.dot_color && typeof attributes.dot_color === 'string') sanitized.dot_color = attributes.dot_color;
                            if (attributes.dot_active_color && typeof attributes.dot_active_color === 'string') sanitized.dot_active_color = attributes.dot_active_color;
                            if (attributes.dot_size && typeof attributes.dot_size === 'number') sanitized.dot_size = attributes.dot_size;
                            if (attributes.dot_spacing !== undefined && typeof attributes.dot_spacing === 'number') sanitized.dot_spacing = attributes.dot_spacing;
                            if (attributes.dot_border_radius !== undefined && typeof attributes.dot_border_radius === 'number') sanitized.dot_border_radius = attributes.dot_border_radius;
                            
                            // Add creative style attributes if they exist and are simple types (skip objects)
                            if (attributes.creative_gradient_type && typeof attributes.creative_gradient_type === 'string') sanitized.creative_gradient_type = attributes.creative_gradient_type;
                            if (attributes.creative_gradient_angle && typeof attributes.creative_gradient_angle === 'number') sanitized.creative_gradient_angle = attributes.creative_gradient_angle;
                            if (attributes.creative_gradient_start && typeof attributes.creative_gradient_start === 'string') sanitized.creative_gradient_start = attributes.creative_gradient_start;
                            if (attributes.creative_gradient_end && typeof attributes.creative_gradient_end === 'string') sanitized.creative_gradient_end = attributes.creative_gradient_end;
                            if (attributes.creative_text_color && typeof attributes.creative_text_color === 'string') sanitized.creative_text_color = attributes.creative_text_color;
                            if (attributes.creative_date_color && typeof attributes.creative_date_color === 'string') sanitized.creative_date_color = attributes.creative_date_color;
                            if (attributes.creative_star_color && typeof attributes.creative_star_color === 'string') sanitized.creative_star_color = attributes.creative_star_color;
                            if (attributes.creative_glass_effect && typeof attributes.creative_glass_effect === 'string') sanitized.creative_glass_effect = attributes.creative_glass_effect;
                            if (attributes.creative_border_radius_value !== undefined && typeof attributes.creative_border_radius_value === 'number') sanitized.creative_border_radius_value = attributes.creative_border_radius_value;
                            if (attributes.creative_avatar_size && typeof attributes.creative_avatar_size === 'number') sanitized.creative_avatar_size = attributes.creative_avatar_size;
                            if (attributes.creative_star_size && typeof attributes.creative_star_size === 'number') sanitized.creative_star_size = attributes.creative_star_size;
                            
                            return sanitized;
                        })(),
                        // Include all relevant attributes in key to force re-render when any change
                        key: 'grp-reviews-' + 
                            (attributes.style || 'modern') + '-' + 
                            (attributes.theme || 'light') + '-' + 
                            (attributes.layout || 'carousel') + '-' + 
                            (attributes.count || 5) + '-' + 
                            (attributes.cols_desktop || 3) + '-' + 
                            (attributes.cols_tablet || 2) + '-' + 
                            (attributes.cols_mobile || 1) + '-' + 
                            (attributes.gap || 20) + '-' + 
                            (attributes.custom_text_color || '') + '-' + 
                            (attributes.custom_background_color || '') + '-' + 
                            (attributes.custom_border_color || '') + '-' + 
                            (attributes.custom_accent_color || '') + '-' + 
                            (attributes.custom_star_color || '') + '-' + 
                            (attributes.custom_font_size || '') + '-' + 
                            (attributes.custom_name_font_size || '') + '-' + 
                            (attributes.custom_padding || '') + '-' + 
                            (attributes.custom_border_radius || '') + '-' + 
                            (attributes.custom_avatar_size || '') + '-' + 
                            (attributes.custom_text_align || '') + '-' + 
                            (attributes.custom_box_shadow || '') + '-' + 
                            (attributes.arrow_size || '') + '-' + 
                            (attributes.arrow_icon_color || '') + '-' + 
                            (attributes.arrow_background_color || '') + '-' + 
                            (attributes.arrow_horizontal_position || '') + '-' + 
                            (attributes.arrow_vertical_position || '') + '-' + 
                            (attributes.arrow_icon || '') + '-' + 
                            (attributes.arrow_box_shadow || '') + '-' + 
                            (attributes.dot_color || '') + '-' + 
                            (attributes.dot_active_color || '') + '-' + 
                            (attributes.dot_size || '') + '-' + 
                            (attributes.dot_spacing || '') + '-' + 
                            (attributes.dot_border_radius || '') + '-' + 
                            (attributes.creative_gradient_type || '') + '-' +
                            (attributes.creative_gradient_angle || '') + '-' +
                            (attributes.creative_gradient_start || '') + '-' +
                            (attributes.creative_gradient_end || '') + '-' +
                            (attributes.creative_text_color || '') + '-' +
                            (attributes.creative_date_color || '') + '-' +
                            (attributes.creative_star_color || '') + '-' +
                            (attributes.creative_glass_effect || '') + '-' +
                            (attributes.creative_border_radius_value || '') + '-' +
                            (attributes.creative_avatar_size || '') + '-' +
                            (attributes.creative_star_size || '')
                        })
                    ) : el('div', { className: 'grp-block-placeholder grp-block-preview' },
                        el('div', { className: 'grp-preview-header' },
                            el('h3', {}, i18n.__('Google Reviews Block', 'google-reviews-plugin'))
                        ),
                        el('div', { className: 'grp-preview-content' },
                            el('p', {}, i18n.__('Configure your reviews display options in the sidebar.', 'google-reviews-plugin')),
                            el('p', {}, i18n.__('Preview will be available after saving the post.', 'google-reviews-plugin'))
                        )
                    )
                ),
            ];
        },
        
        save: function() {
            // Server-side rendering
            return null;
        }
    });
    } catch (error) {
        console.error('Google Reviews Gutenberg: Error registering reviews block:', error);
    }
    
    // Register Review Button block if addon is enabled
    // Check if the block is registered on PHP side by checking if grp_gutenberg has reviewButtonEnabled
    if (typeof window.grp_gutenberg !== 'undefined' && window.grp_gutenberg.reviewButtonEnabled) {
    console.log('Registering Google Reviews Button Gutenberg block...');

    try {
        registerBlockType('google-reviews/review-button', {
            title: i18n.__('Review Button', 'google-reviews-plugin'),
            description: i18n.__('Add a button that links to your Google Business Profile review page.', 'google-reviews-plugin'),
            icon: 'star-filled',
            category: 'widgets',
            keywords: [
                i18n.__('google', 'google-reviews-plugin'),
                i18n.__('review', 'google-reviews-plugin'),
                i18n.__('button', 'google-reviews-plugin'),
                i18n.__('link', 'google-reviews-plugin')
            ],
            attributes: {
                button_text: {
                    type: 'string',
                    default: i18n.__('Leave us a review', 'google-reviews-plugin')
                },
                button_style: {
                    type: 'string',
                    default: 'basic'
                },
                button_size: {
                    type: 'string',
                    default: 'medium'
                },
                align: {
                    type: 'string',
                    default: 'left'
                },
                text_color: {
                    type: 'string'
                },
                background_color: {
                    type: 'string'
                }
            },
            
            edit: function(props) {
                var attributes = props.attributes;
                var setAttributes = props.setAttributes;
                
                // Check if user has pro license (passed from PHP)
                var isProValue = (typeof window.grp_gutenberg !== 'undefined' && window.grp_gutenberg.isPro);
                var isProUser = isProValue === true || isProValue === 1 || isProValue === '1';
                
                // Button style options - include both CSS styles and templates
                var styleOptions = [
                    { label: i18n.__('Basic', 'google-reviews-plugin'), value: 'basic' },
                    { label: i18n.__('Modern', 'google-reviews-plugin'), value: 'modern' },
                    { label: i18n.__('Default', 'google-reviews-plugin'), value: 'default' },
                    { label: i18n.__('Rounded', 'google-reviews-plugin'), value: 'rounded' },
                    { label: i18n.__('Outline', 'google-reviews-plugin'), value: 'outline' },
                    { label: i18n.__('Minimal', 'google-reviews-plugin'), value: 'minimal' }
                ];
                
                // Add pro templates if user has pro
                if (isProUser) {
                    styleOptions.push(
                        { label: i18n.__('Elegant', 'google-reviews-plugin'), value: 'elegant' },
                        { label: i18n.__('Bold', 'google-reviews-plugin'), value: 'bold' },
                        { label: i18n.__('Minimalist', 'google-reviews-plugin'), value: 'minimalist' },
                        { label: i18n.__('Card', 'google-reviews-plugin'), value: 'card' },
                        { label: i18n.__('Creative', 'google-reviews-plugin'), value: 'creative' },
                        { label: i18n.__('Layout 1', 'google-reviews-plugin'), value: 'layout1' },
                        { label: i18n.__('Layout 2', 'google-reviews-plugin'), value: 'layout2' },
                        { label: i18n.__('Layout 3', 'google-reviews-plugin'), value: 'layout3' },
                        { label: i18n.__('Creative Pro', 'google-reviews-plugin'), value: 'creative-pro' }
                    );
                }
                
                var sizeOptions = [
                    { label: i18n.__('Small', 'google-reviews-plugin'), value: 'small' },
                    { label: i18n.__('Medium', 'google-reviews-plugin'), value: 'medium' },
                    { label: i18n.__('Large', 'google-reviews-plugin'), value: 'large' }
                ];
                
                var alignOptions = [
                    { label: i18n.__('Left', 'google-reviews-plugin'), value: 'left' },
                    { label: i18n.__('Center', 'google-reviews-plugin'), value: 'center' },
                    { label: i18n.__('Right', 'google-reviews-plugin'), value: 'right' }
                ];
                
                return [
                    el(InspectorControls, {},
                        el(PanelBody, { title: i18n.__('Button Settings', 'google-reviews-plugin'), initialOpen: true },
                            el(TextControl, {
                                label: i18n.__('Button Text', 'google-reviews-plugin'),
                                value: attributes.button_text || __('Leave us a review', 'google-reviews-plugin'),
                                onChange: function(value) {
                                    setAttributes({ button_text: value });
                                }
                            }),
                            el(SelectControl, {
                                label: i18n.__('Button Style', 'google-reviews-plugin'),
                                value: attributes.button_style || 'basic',
                                options: styleOptions,
                                onChange: function(value) {
                                    setAttributes({ button_style: value });
                                }
                            }),
                            el(SelectControl, {
                                label: i18n.__('Button Size', 'google-reviews-plugin'),
                                value: attributes.button_size || 'medium',
                                options: sizeOptions,
                                onChange: function(value) {
                                    setAttributes({ button_size: value });
                                }
                            }),
                            el(SelectControl, {
                                label: i18n.__('Alignment', 'google-reviews-plugin'),
                                value: attributes.align || 'left',
                                options: alignOptions,
                                onChange: function(value) {
                                    setAttributes({ align: value });
                                }
                            })
                        ),
                        
                        el(PanelBody, { title: i18n.__('Colors', 'google-reviews-plugin'), initialOpen: false },
                            el('div', { style: { marginBottom: '16px' } },
                                el('label', { style: { display: 'block', marginBottom: '8px', fontWeight: 'bold' } }, 
                                    i18n.__('Text Color', 'google-reviews-plugin')
                                ),
                                el(TextControl, {
                                    type: 'color',
                                    value: attributes.text_color || '',
                                    onChange: function(value) {
                                        setAttributes({ text_color: value });
                                    },
                                    placeholder: '#ffffff'
                                })
                            ),
                            el('div', { style: { marginBottom: '16px' } },
                                el('label', { style: { display: 'block', marginBottom: '8px', fontWeight: 'bold' } }, 
                                    i18n.__('Background Color', 'google-reviews-plugin')
                                ),
                                el(TextControl, {
                                    type: 'color',
                                    value: attributes.background_color || '',
                                    onChange: function(value) {
                                        setAttributes({ background_color: value });
                                    },
                                    placeholder: '#0073aa'
                                })
                            )
                        )
                    ),
                    
                    // Use ServerSideRender for live preview if available
                    ServerSideRender ? el(ServerSideRender, {
                        block: 'google-reviews/review-button',
                        attributes: {
                            button_text: attributes.button_text || i18n.__('Leave us a review', 'google-reviews-plugin'),
                            button_style: attributes.button_style || 'basic',
                            button_size: attributes.button_size || 'medium',
                            align: attributes.align || 'left',
                            text_color: attributes.text_color || '',
                            background_color: attributes.background_color || ''
                        },
                        // Include all attributes in key to force re-render when any change
                        key: 'grp-review-button-' + 
                            (attributes.button_text || 'default') + '-' + 
                            (attributes.button_style || 'basic') + '-' + 
                            (attributes.button_size || 'medium') + '-' + 
                            (attributes.align || 'left') + '-' + 
                            (attributes.text_color || '') + '-' + 
                            (attributes.background_color || '')
                    }) : el('div', { className: 'grp-review-button-block-editor', style: { textAlign: attributes.align || 'left', padding: '20px' } },
                        el('div', { className: 'grp-block-placeholder grp-button-preview' },
                            el('div', { className: 'grp-preview-header' },
                                el('h3', {}, i18n.__('Review Button Block', 'google-reviews-plugin'))
                            ),
                            el('div', { className: 'grp-preview-content' },
                                el('p', {}, i18n.__('Configure your button options in the sidebar.', 'google-reviews-plugin')),
                                el('div', { className: 'grp-button-preview-sample', style: { padding: '10px', background: '#f0f0f0', borderRadius: '4px', textAlign: 'center', margin: '10px 0' } },
                                    attributes.button_text || i18n.__('Leave a Review', 'google-reviews-plugin')
                                ),
                                el('p', {}, i18n.__('Preview will be available after saving the post.', 'google-reviews-plugin'))
                            )
                        )
                    )
                ];
            },
            
            save: function() {
                // Server-side rendering
                return null;
            }
        });
    } catch (error) {
        console.error('Google Reviews Gutenberg: Error registering review button block:', error);
    }
    }
    
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
    window.wp.i18n,
    (window.wp.serverSideRender || (window.wp.editor && window.wp.editor.ServerSideRender ? window.wp.editor : null) || null), // ServerSideRender
    (window.wp.blockEditor || window.wp.editor || window.wp.blocks) // blockEditor for InspectorControls
);