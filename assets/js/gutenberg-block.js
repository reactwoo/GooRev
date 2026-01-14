/**
 * Google Reviews Gutenberg Block
 */

(function(blocks, element, components, i18n, serverSideRender) {
    'use strict';
    
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var InspectorControls = blocks.InspectorControls;
    var PanelBody = components.PanelBody;
    var SelectControl = components.SelectControl;
    var ToggleControl = components.ToggleControl;
    var RangeControl = components.RangeControl;
    var TextControl = components.TextControl;
    // ServerSideRender is now a separate package in newer WordPress versions
    var ServerSideRender = serverSideRender || blocks.ServerSideRender || null;
    
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
            layout: {
                type: 'string',
                default: 'carousel'
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
            creative_background: {
                type: 'object',
                default: {
                    type: 'gradient',
                    gradient: 'linear-gradient(135deg, #4285F4 0%, #EA4335 100%)'
                }
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
            creative_avatar_size: {
                type: 'number',
                default: 80
            },
            creative_star_size: {
                type: 'number',
                default: 32
            }
        },
        
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            
            // Check if user has pro license (passed from PHP)
            var isProUser = (typeof window.grp_gutenberg !== 'undefined' && window.grp_gutenberg.isPro === true);

            var styleOptions = grp_gutenberg.styles
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

            var layoutOptions = [
                { label: i18n.__('Carousel (3 columns)', 'google-reviews-plugin'), value: 'carousel' },
                { label: i18n.__('List', 'google-reviews-plugin'), value: 'list' },
                { label: i18n.__('Grid', 'google-reviews-plugin'), value: 'grid' }
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
                            value: attributes.style,
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
                            label: i18n.__('Layout', 'google-reviews-plugin'),
                            value: attributes.layout,
                            options: layoutOptions,
                            onChange: function(value) {
                                setAttributes({ layout: value });
                            }
                        }),
                        isProUser ? [
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
                            }),
                            el(RangeControl, {
                                label: i18n.__('Gap (px)', 'google-reviews-plugin'),
                                value: attributes.gap || 20,
                                onChange: function(value) {
                                    setAttributes({ gap: value });
                                },
                                min: 0,
                                max: 60
                            })
                        ] : el('div', {
                            style: {
                                background: '#f0f8ff',
                                border: '1px solid #007cba',
                                padding: '10px',
                                marginBottom: '10px',
                                borderRadius: '4px'
                            }
                        },
                            el('strong', {}, '📐 Column Controls'),
                            el('br'),
                            el('span', {}, 'Upgrade to Pro to customize column counts and gap spacing for each device. '),
                            el('a', {
                                href: 'https://reactwoo.com/google-reviews-plugin-pro/',
                                target: '_blank',
                                style: { color: '#007cba', textDecoration: 'underline' }
                            }, 'Learn More')
                        ),
                        el(RangeControl, {
                            label: i18n.__('Number of Reviews', 'google-reviews-plugin'),
                            value: attributes.count,
                            onChange: function(value) {
                                setAttributes({ count: value });
                            },
                            min: 1,
                            max: 20
                        }),
                        el(SelectControl, {
                            label: i18n.__('Minimum Rating', 'google-reviews-plugin'),
                            value: attributes.min_rating,
                            options: ratingOptions,
                            onChange: function(value) {
                                setAttributes({ min_rating: parseInt(value) });
                            }
                        }),
                        el(SelectControl, {
                            label: i18n.__('Maximum Rating', 'google-reviews-plugin'),
                            value: attributes.max_rating,
                            options: ratingOptions,
                            onChange: function(value) {
                                setAttributes({ max_rating: parseInt(value) });
                            }
                        }),
                        el(SelectControl, {
                            label: i18n.__('Sort By', 'google-reviews-plugin'),
                            value: attributes.sort_by,
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
                        isProUser ? [
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
                            el(ToggleControl, {
                                label: i18n.__('Show Dots', 'google-reviews-plugin'),
                                checked: attributes.dots,
                                onChange: function(value) {
                                    setAttributes({ dots: value });
                                }
                            }),
                            el(ToggleControl, {
                                label: i18n.__('Show Arrows', 'google-reviews-plugin'),
                                checked: attributes.arrows,
                                onChange: function(value) {
                                    setAttributes({ arrows: value });
                                }
                            })
                        ] : el('div', {
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
                    ),

                    el(PanelBody, {
                        title: i18n.__('Style Customization', 'google-reviews-plugin'),
                        initialOpen: false
                    },
                        isProUser ? [
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
                            ) : null,
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
                            ) : null,
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
                            })
                        ] : el('div', {
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
                                    value: attributes.creative_background?.type || 'linear',
                                    options: [
                                        { label: i18n.__('Linear', 'google-reviews-plugin'), value: 'linear' },
                                        { label: i18n.__('Radial', 'google-reviews-plugin'), value: 'radial' }
                                    ],
                                    onChange: function(value) {
                                        var currentBg = attributes.creative_background || { type: 'linear', angle: 135, start_color: '#4285F4', end_color: '#EA4335' };
                                        setAttributes({
                                            creative_background: Object.assign({}, currentBg, { type: value })
                                        });
                                    }
                                })
                            ),
                            (attributes.creative_background?.type === 'linear' || (!attributes.creative_background?.type && attributes.creative_background?.angle !== undefined)) ? el(RangeControl, {
                                label: i18n.__('Angle (degrees)', 'google-reviews-plugin'),
                                value: attributes.creative_background?.angle || 135,
                                onChange: function(value) {
                                    var currentBg = attributes.creative_background || { type: 'linear', angle: 135, start_color: '#4285F4', end_color: '#EA4335' };
                                    setAttributes({
                                        creative_background: Object.assign({}, currentBg, { angle: value })
                                    });
                                },
                                min: 0,
                                max: 360,
                                step: 1
                            }) : null,
                            el('div', { style: { marginBottom: '12px' } },
                                el('label', { style: { display: 'block', marginBottom: '5px', fontWeight: 'bold' } },
                                    i18n.__('Start Color', 'google-reviews-plugin')
                                ),
                                el(TextControl, {
                                    type: 'color',
                                    value: attributes.creative_background?.start_color || '#4285F4',
                                    onChange: function(value) {
                                        var currentBg = attributes.creative_background || { type: 'linear', angle: 135, start_color: '#4285F4', end_color: '#EA4335' };
                                        setAttributes({
                                            creative_background: Object.assign({}, currentBg, { start_color: value })
                                        });
                                    }
                                })
                            ),
                            el('div', { style: { marginBottom: '12px' } },
                                el('label', { style: { display: 'block', marginBottom: '5px', fontWeight: 'bold' } },
                                    i18n.__('End Color', 'google-reviews-plugin')
                                ),
                                el(TextControl, {
                                    type: 'color',
                                    value: attributes.creative_background?.end_color || '#EA4335',
                                    onChange: function(value) {
                                        var currentBg = attributes.creative_background || { type: 'linear', angle: 135, start_color: '#4285F4', end_color: '#EA4335' };
                                        setAttributes({
                                            creative_background: Object.assign({}, currentBg, { end_color: value })
                                        });
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
                            i18n.__('Displaying', 'google-reviews-plugin') + ' ' + 
                            attributes.count + ' ' + 
                            i18n.__('reviews in', 'google-reviews-plugin') + ' ' + 
                            attributes.layout + ' ' + 
                            i18n.__('layout', 'google-reviews-plugin')
                        )
                    ),
                    ServerSideRender ? el(ServerSideRender, {
                        block: 'google-reviews/reviews',
                        attributes: attributes
                    }) : el('div', { className: 'grp-block-placeholder' },
                        el('p', {}, i18n.__('Preview will be available after saving.', 'google-reviews-plugin'))
                    )
                ),
            ];
        },
        
        save: function() {
            // Server-side rendering
            return null;
        }
    });
    
    // Register Review Button block if addon is enabled
    // Check if the block is registered on PHP side by checking if grp_gutenberg has reviewButtonEnabled
    if (typeof grp_gutenberg !== 'undefined' && grp_gutenberg.reviewButtonEnabled) {
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
                    default: 'default'
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
                
                var styleOptions = [
                    { label: i18n.__('Default', 'google-reviews-plugin'), value: 'default' },
                    { label: i18n.__('Rounded', 'google-reviews-plugin'), value: 'rounded' },
                    { label: i18n.__('Outline', 'google-reviews-plugin'), value: 'outline' },
                    { label: i18n.__('Minimal', 'google-reviews-plugin'), value: 'minimal' }
                ];
                
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
                                value: attributes.button_text,
                                onChange: function(value) {
                                    setAttributes({ button_text: value });
                                }
                            }),
                            el(SelectControl, {
                                label: i18n.__('Button Style', 'google-reviews-plugin'),
                                value: attributes.button_style,
                                options: styleOptions,
                                onChange: function(value) {
                                    setAttributes({ button_style: value });
                                }
                            }),
                            el(SelectControl, {
                                label: i18n.__('Button Size', 'google-reviews-plugin'),
                                value: attributes.button_size,
                                options: sizeOptions,
                                onChange: function(value) {
                                    setAttributes({ button_size: value });
                                }
                            }),
                            el(SelectControl, {
                                label: i18n.__('Alignment', 'google-reviews-plugin'),
                                value: attributes.align,
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
                    
                    el('div', { className: 'grp-review-button-block-editor', style: { textAlign: attributes.align || 'left', padding: '20px' } },
                        ServerSideRender ? el(ServerSideRender, {
                            block: 'google-reviews/review-button',
                            attributes: attributes
                        }) : el('div', { className: 'grp-block-placeholder' },
                            el('p', {}, i18n.__('Preview will be available after saving.', 'google-reviews-plugin'))
                        )
                    )
                ];
            },
            
            save: function() {
                // Server-side rendering
                return null;
            }
        });
    }
    
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
    window.wp.i18n,
    window.wp.serverSideRender || null
);