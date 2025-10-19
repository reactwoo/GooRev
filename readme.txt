=== Google Reviews Plugin ===
Contributors: reactwoo
Tags: google reviews, testimonials, ratings, business reviews, google business profile, google my business, shortcode, widget, elementor, gutenberg
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display Google Business reviews on your WordPress site with beautiful widgets and shortcodes. Free and Pro versions available.

== Description ==

The Google Reviews Plugin allows you to easily display your Google Business reviews on your WordPress website. With multiple display styles, responsive design, and seamless integration with popular page builders, this plugin helps you showcase customer feedback and build trust with potential customers.

= Key Features =

* **Google Business Profile Integration** - Connect your Google Business Profile account and sync reviews automatically
* **Multiple Display Styles** - Choose from 5+ pre-designed styles including Modern, Classic, Minimal, Corporate, and Creative
* **Flexible Layouts** - Display reviews as a carousel or list with customizable options
* **Page Builder Support** - Works with Elementor, Gutenberg, and other popular page builders
* **Widget Support** - Add reviews to your sidebar or any widget area
* **Shortcode Support** - Use shortcodes to display reviews anywhere on your site
* **Responsive Design** - Looks great on all devices
* **Customizable** - Extensive styling options and custom CSS support
* **Performance Optimized** - Built-in caching and performance optimizations

= Free Version Features =

* Connect to Business Profile APIs (Business Profile, Business Information)
* 5+ pre-designed styles with light/dark variants
* Carousel and list layouts
* Basic customization options
* Shortcode and widget support
* Elementor and Gutenberg integration
* Review filtering by star rating
* Responsive design
* Basic caching

= Pro Version Features =

* **Multiple Locations** - Connect multiple Google Business locations
* **Product Integration** - Link reviews to specific products or services
* **Advanced Customization** - Custom CSS editor, advanced styling options, and template builder
* **Analytics Dashboard** - Detailed analytics and performance insights
* **Review Management** - Curate, moderate, and manage reviews
* **White Label Options** - Remove branding and customize admin interface
* **Priority Support** - Get priority support and faster response times
* **API Access** - Full REST API access for custom integrations

= Installation =

1. Upload the plugin files to the `/wp-content/plugins/google-reviews-plugin` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to Google Reviews > Settings to configure your Google API credentials
4. Connect your Google Business account
5. Sync your reviews
6. Use shortcodes, widgets, or page builders to display reviews

= Configuration =

1. **Google API Setup**:
   - Create a Google Cloud Project
   - Enable the following Business Profile APIs in your project:
     - Business Profile API (`businessprofile.googleapis.com`)
     - Business Profile Business Information API (`mybusinessbusinessinformation.googleapis.com`)
     - Business Profile Performance API (`businessprofileperformance.googleapis.com`)
   - Create OAuth 2.0 credentials
   - Add the redirect URI: `your-site.com/wp-admin/admin.php?page=google-reviews-settings&action=oauth_callback`
   - Ensure the OAuth scope is granted: `https://www.googleapis.com/auth/business.manage`
   - Reference: OAuth 2.0 scopes catalog `https://developers.google.com/identity/protocols/oauth2/scopes`

2. **Connect Account**:
   - Enter your Client ID and Client Secret in the settings
   - Click "Connect Account" to authorize the plugin
   - Select your business location

3. **Sync Reviews**:
   - Click "Sync Reviews" to import your Google Business reviews
   - Reviews will be cached for better performance

= Usage =

= Shortcodes =

Basic shortcode:
`[google_reviews]`

Advanced shortcode with options:
`[google_reviews style="modern" theme="light" layout="carousel" count="5" min_rating="4"]`

Available parameters:
* `style` - Review display style (modern, classic, minimal, corporate, creative)
* `theme` - Theme variant (light, dark, auto)
* `layout` - Display layout (carousel, list)
* `cols_desktop`, `cols_tablet`, `cols_mobile` - Columns for grid/grid_carousel
* `gap` - Gap between items in px for grid/grid_carousel
* `count` - Number of reviews to display (1-50)
* `min_rating` - Minimum star rating to display (1-5)
* `max_rating` - Maximum star rating to display (1-5)
* `sort_by` - Sort reviews by (newest, oldest, highest_rating, lowest_rating)
* `show_avatar` - Show reviewer avatar (true/false)
* `show_date` - Show review date (true/false)
* `show_rating` - Show star rating (true/false)
* `show_reply` - Show business replies (true/false)
* `autoplay` - Enable carousel autoplay (true/false)
* `speed` - Carousel speed in milliseconds (1000-10000)
* `dots` - Show carousel dots (true/false)
* `arrows` - Show carousel arrows (true/false)

= Widgets =

1. Go to Appearance > Widgets
2. Find the "Google Reviews" widget
3. Drag it to your desired widget area
4. Configure the display options
5. Save the widget

= Page Builders =

**Elementor:**
1. Edit a page with Elementor
2. Search for "Google Reviews" in the widget panel
3. Drag the widget to your page
4. Configure the settings in the widget panel

**Gutenberg:**
1. Edit a page or post
2. Click the "+" button to add a block
3. Search for "Google Reviews"
4. Add the block and configure the settings
   - Theme: Light/Dark/Auto
   - Layout: List/Carousel/Grid/Grid Carousel
   - Columns: Desktop/Tablet/Mobile
   - Gap: px spacing

= Styling =

The plugin includes 5+ pre-designed styles:
* **Modern** - Clean and contemporary design with subtle shadows
* **Classic** - Traditional design with clean lines and professional look
* **Minimal** - Minimalist design focusing on content
* **Corporate** - Professional business design with structured layout
* **Creative** - Artistic design with creative elements and animations

Each style includes light, dark, and auto variants. Auto follows the user's OS/browser preference.

Examples:
`[google_reviews style="modern" theme="light" layout="list" count="3"]`
`[google_reviews style="modern" theme="dark" layout="list" count="3"]`
`[google_reviews style="modern" theme="auto" layout="list" count="3"]`

= Custom CSS =

You can add custom CSS in the Styles section of the admin panel to further customize the appearance of your reviews.

= Troubleshooting =

**Reviews are not displaying:**
- Check if your Google API credentials are correct
- Ensure your account is connected
- Try syncing reviews manually
- Check if there are reviews available for your business

**Connection failed:**
- Verify your Client ID and Client Secret
- Check if the redirect URI is correct
- Ensure the required Business Profile APIs are enabled
- Check your internet connection

**Styling issues:**
- Check if your theme CSS is conflicting
- Try using custom CSS to override styles
- Clear any caching plugins
- Check browser developer tools for errors

= Frequently Asked Questions =

= Do I need a Google Business Profile? =

Yes, you need a Google Business Profile with reviews to use this plugin.

= How do I get Google API credentials? =

1. Go to the Google Cloud Console
2. Create a new project or select an existing one
3. Enable the Business Profile APIs (Business Profile, Business Information, Performance)
4. Create OAuth 2.0 credentials
5. Add the redirect URI provided in the plugin settings
6. Ensure the scope `https://www.googleapis.com/auth/business.manage` is requested

= Can I customize the appearance? =

Yes, the plugin includes multiple pre-designed styles and supports custom CSS for advanced customization.

= Does the plugin work with page builders? =

Yes, the plugin works with Elementor, Gutenberg, and other popular page builders.

= Is the plugin responsive? =

Yes, the plugin is fully responsive and looks great on all devices.

= How often are reviews synced? =

Reviews are synced automatically every hour, or you can sync them manually from the admin panel.

= Can I filter reviews by rating? =

Yes, you can filter reviews to show only specific star ratings.

= Does the plugin support multiple languages? =

Yes, the plugin is translation ready and includes language files.

= Support =

For support, please visit our support portal at https://reactwoo.com/support or email us at support@reactwoo.com.

= Changelog ==

= 1.0.0 =
* Initial release
* Business Profile API integration
* 5+ pre-designed styles
* Carousel and list layouts
* Shortcode and widget support
* Elementor and Gutenberg integration
* Review filtering and sorting
* Responsive design
* Caching system
* Admin dashboard
* Help documentation

= Upgrade Notice =

= 1.0.0 =
Initial release of the Google Reviews Plugin. Install to start displaying your Google Business reviews on your WordPress site.

== Screenshots ==

1. Admin Dashboard - Overview of reviews and connection status
2. Settings Page - Google API configuration and display settings
3. Reviews Management - View and manage synced reviews
4. Style Customization - Choose from multiple pre-designed styles
5. Shortcode Examples - Different shortcode configurations
6. Widget Configuration - WordPress widget settings
7. Elementor Integration - Elementor widget in action
8. Gutenberg Block - Gutenberg block editor
9. Frontend Display - Reviews displayed on the frontend
10. Mobile Responsive - Reviews on mobile devices

== Upgrade to Pro ==

Unlock advanced features with the Pro version:

* Multiple Locations
* Product Integration
* Advanced Customization
* Analytics Dashboard
* Review Management
* White Label Options
* Priority Support
* API Access

[Get Pro Version](https://reactwoo.com/google-reviews-plugin-pro/)

== Credits ==

Developed by ReactWoo Ltd - https://reactwoo.com

== License ==

This plugin is licensed under the GPL v2 or later.

== Privacy Policy ==

This plugin connects to Google's Business Profile APIs to fetch reviews. No personal data is stored or transmitted to third parties except for the necessary API calls to Google's servers.