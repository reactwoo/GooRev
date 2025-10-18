/**
 * Google Reviews Gutenberg Block
 */

(function(blocks, element, components, i18n) {
    'use strict';
    
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var InspectorControls = blocks.InspectorControls;
    var PanelBody = components.PanelBody;
    var SelectControl = components.SelectControl;
    var ToggleControl = components.ToggleControl;
    var RangeControl = components.RangeControl;
    var TextControl = components.TextControl;
    var ServerSideRender = blocks.ServerSideRender;
    
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
            }
        },
        
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            
            var styleOptions = grp_gutenberg.styles.map(function(style) {
                return {
                    label: style.label,
                    value: style.value
                };
            });
            
            var layoutOptions = [
                { label: i18n.__('Carousel', 'google-reviews-plugin'), value: 'carousel' },
                { label: i18n.__('List', 'google-reviews-plugin'), value: 'list' },
                { label: i18n.__('Grid', 'google-reviews-plugin'), value: 'grid' },
                { label: i18n.__('Grid Carousel', 'google-reviews-plugin'), value: 'grid_carousel' }
            ];

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
                        }),
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
                        })
                    ),
                    
                    el(PanelBody, { 
                        title: i18n.__('Carousel Options', 'google-reviews-plugin'), 
                        initialOpen: false,
                        className: (attributes.layout !== 'carousel' && attributes.layout !== 'grid_carousel') ? 'grp-hidden' : ''
                    },
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
                    el(ServerSideRender, {
                        block: 'google-reviews/reviews',
                        attributes: attributes
                    })
                )
            ];
        },
        
        save: function() {
            // Server-side rendering
            return null;
        }
    });
    
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
    window.wp.i18n
);