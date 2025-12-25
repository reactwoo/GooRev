# Google Reviews Plugin (GooRev)

A WordPress plugin that displays Google Business Profile reviews on your WordPress site with beautiful widgets, shortcodes, and page builder integrations.

## Project Overview

**Plugin Name:** Google Reviews Plugin (GooRev)  
**Version:** 1.0.0  
**Developer:** ReactWoo Ltd  
**License:** GPL v2 or later  
**Website:** https://reactwoo.com/google-reviews-plugin

## Purpose

This plugin allows WordPress site owners to:
- Connect to Google Business Profile APIs to fetch reviews
- Display reviews using multiple pre-designed styles
- Integrate reviews via shortcodes, widgets, and page builders (Elementor, Gutenberg)
- Customize review display with various layouts and themes
- Cache reviews for optimal performance

## Architecture & License Structure

### Free Tier (OAuth Proxy Through API Server)

The **free version** routes OAuth through our API server for easy setup, with **minimal load on our API server**:

- **OAuth Proxy**: OAuth flow is routed through our API server (credentials stored securely on our server, not in the plugin)
- **One-Click Connect**: Simply click "Connect Google Account" - no need to create your own Google Cloud Project
- **Direct Google API Connection**: After OAuth, all API calls go directly from the WordPress site to Google's servers (`businessprofile.googleapis.com`)
- **Optional Custom Credentials**: Free users can optionally use their own Google Cloud Project credentials if preferred
- **Local Caching**: Reviews are cached locally in the WordPress database for performance
- **No License Validation**: Free tier does not require license validation

**Note**: Our API server only handles:
- OAuth authorization URL generation (one-time during setup)
- OAuth token exchange (one-time during setup)
- Token refresh (periodic, when access token expires)

All Google Business Profile API calls go directly from WordPress to Google's servers.

**Free Tier Features:**
- Connect to Business Profile APIs (using user's own credentials)
- 5+ pre-designed styles with light/dark variants
- Carousel and list layouts
- Basic customization options
- Shortcode and widget support
- Elementor and Gutenberg integration
- Review filtering by star rating
- Responsive design
- Basic caching system

### Pro Tier (License Server Connection)

The **Pro version** connects to our license server for validation:

- **License Validation**: Pro users must activate a license key that validates against our license server at `https://reactwoo.com/wp-json/grp-license/v1/`
- **License Server Connection**: The plugin connects to our license server ONLY for:
  - License activation
  - License status checks (daily cron)
  - License deactivation
- **Direct Google API**: Pro users still connect directly to Google APIs (can use their own credentials or shared credentials)
- **Enhanced Features**: Access to advanced features like multiple locations, analytics, review management, etc.

**Pro Tier Features:**
- All free tier features, plus:
- Multiple Google Business locations
- Product/service integration
- Advanced customization options
- Custom CSS editor
- Template builder
- Analytics dashboard
- Review management and moderation
- White label options
- Priority support
- Full REST API access
- Advanced caching options
- Review sentiment analysis
- Export/import functionality

## Technical Architecture

### API Connection Flow

```
Free Tier:
WordPress Site → API Server (reactwoo.com) [OAuth proxy - auth URL, token exchange, refresh]
WordPress Site → Google Business Profile APIs (direct) [all API calls after authentication]

Pro Tier:
WordPress Site → License Server (reactwoo.com) [for validation only]
WordPress Site → API Server (reactwoo.com) [OAuth proxy - optional, can use custom credentials]
WordPress Site → Google Business Profile APIs (direct) [all API calls after authentication]
```

### License System

The license system (`GRP_License` class) handles:
- License key storage and validation
- Daily license status checks via WordPress cron
- License activation/deactivation via REST API
- Pro feature gating based on license status

**License Server Endpoint:** `https://reactwoo.com/wp-json/grp-license/v1/`

**License Actions:**
- `activate` - Activate a license key
- `deactivate` - Deactivate a license key
- `check` - Check license status

### OAuth Proxy System

The OAuth proxy system (`GRP_API` class) routes OAuth through our API server:
- OAuth credentials are stored securely on our server (not in the plugin)
- Free tier automatically uses API server for OAuth
- Pro tier can use API server or custom credentials

**API Server Endpoint:** `https://reactwoo.com/wp-json/grp-api/v1/`

**OAuth Proxy Actions:**
- `oauth/auth-url` - Get Google OAuth authorization URL
- `oauth/token` - Exchange authorization code for access token
- `oauth/refresh` - Refresh expired access token

See `API_SERVER_ENDPOINTS.md` for detailed endpoint documentation.

### Google API Integration

The plugin uses Google's modern Business Profile APIs:
- **Business Profile API** (`businessprofile.googleapis.com/v1`)
- **Business Profile Business Information API** (`mybusinessbusinessinformation.googleapis.com/v1`)
- **Business Profile Performance API** (`businessprofileperformance.googleapis.com/v1`)

All API calls are made directly from the WordPress site to Google's servers using OAuth 2.0 authentication.

## Installation & Setup

### Free Tier Setup

**Easy Setup (Recommended):**
1. Install the plugin
2. Go to Google Reviews > Settings
3. Click "Connect Google Account" (uses our pre-configured setup)
4. Authorize the plugin with your Google account
5. Select your business location
6. Sync reviews

**Custom Credentials (Optional):**
1. Enable "Use my own Google Cloud Project credentials" in Advanced section
2. Create a Google Cloud Project
3. Enable the required Business Profile APIs
4. Create OAuth 2.0 credentials
5. Enter your Client ID and Client Secret
6. Connect your Google account
7. Select your business location
8. Sync reviews

### Pro Tier Setup

1. Purchase a Pro license from https://reactwoo.com/google-reviews-plugin-pro/
2. Install the plugin
3. Go to Google Reviews > Settings > License
4. Enter your license key
5. Activate the license (connects to our license server for validation)
6. Configure Google API credentials (optional - can use shared credentials)
7. Connect your Google account
8. Access Pro features

## File Structure

```
google-reviews-plugin/
├── assets/                    # CSS and JavaScript files
│   ├── css/
│   └── js/
├── includes/
│   ├── admin/                 # Admin interface
│   │   └── views/            # Admin page templates
│   ├── frontend/              # Frontend display
│   │   ├── elementor/        # Elementor integration
│   │   └── class-grp-gutenberg.php
│   ├── class-google-reviews-plugin.php  # Main plugin class
│   ├── class-grp-api.php      # Google API integration
│   ├── class-grp-license.php  # License management
│   ├── class-grp-reviews.php # Review data management
│   ├── class-grp-shortcode.php
│   ├── class-grp-widget.php
│   └── class-grp-cache.php
├── languages/                 # Translation files
├── google-reviews-plugin.php  # Main plugin file
└── uninstall.php             # Cleanup on uninstall
```

## Core Classes

- **Google_Reviews_Plugin**: Main plugin class and initialization
- **GRP_API**: Google Business Profile API integration (direct connection)
- **GRP_License**: License validation and management (connects to license server)
- **GRP_Reviews**: Review data management and display
- **GRP_Cache**: Local caching system
- **GRP_Shortcode**: Shortcode functionality
- **GRP_Widget**: WordPress widget implementation
- **GRP_Admin**: Admin interface management

## Development

### Requirements

- WordPress 5.0+
- PHP 7.4+
- MySQL 5.6+

### Building

```bash
npm install
npm run build
```

## Security & Privacy

- **Free Tier**: 
  - OAuth token exchange happens through our servers (one-time during setup)
  - All API calls go directly to Google's servers after authentication
  - No review data passes through our servers
- **Pro Tier**: 
  - Only license validation data (license key, site URL) is sent to our license server
  - OAuth can use our shared credentials or user's own credentials
  - All API calls go directly to Google's servers
- **Google API**: Users authenticate directly with Google using OAuth 2.0.
- **Data Storage**: Reviews are stored locally in WordPress database.
- **No Personal Data**: No personal user data is transmitted to third parties except necessary API calls to Google's servers.

## Support

- **Support Portal**: https://reactwoo.com/support
- **Email**: support@reactwoo.com
- **Documentation**: https://reactwoo.com/docs

## License

This plugin is licensed under the GPL v2 or later.

## Configuration

### API Server Setup

The plugin routes OAuth through your API server by default. This can be:
- A separate Node.js service on cPanel (recommended for cost savings)
- Your existing EC2 server
- A serverless function (AWS Lambda, Vercel, etc.)

To configure the API server URL:

**Option 1: PHP Constant** (in wp-config.php or a mu-plugin):
```php
define('GRP_API_SERVER_URL', 'https://your-api-server.com/wp-json/grp-api/v1/');
```

**Option 2: WordPress Filter** (in theme functions.php or a plugin):
```php
add_filter('grp_api_server_url', function() {
    return 'https://your-api-server.com/wp-json/grp-api/v1/';
});
```

### Implementing API Server Endpoints

You need to implement three REST API endpoints on your server:

1. **POST /oauth/auth-url** - Generate Google OAuth authorization URL
2. **POST /oauth/token** - Exchange authorization code for access token
3. **POST /oauth/refresh** - Refresh expired access token

See `API_SERVER_ENDPOINTS.md` for complete endpoint documentation, request/response formats, and example implementations.

**For cost optimization and deployment options**, see:
- `LOAD_ANALYSIS.md` - Load assessment and cost comparison
- `CPANEL_NODEJS_SETUP.md` - Step-by-step guide for deploying on cPanel (recommended for cost savings)
- `MULTIPLE_NODEJS_APPS.md` - Guide for running multiple Node.js apps (WooAliAI, Google Reviews, Geo Elementor) on the same cPanel account

**Important**: 
- Store Google OAuth credentials securely on your server (environment variables, secure vault, etc.)
- The OAuth redirect URI must be configured in your Google Cloud Console
- Your Google Cloud Project must have the Business Profile APIs enabled and approved
- The OAuth consent screen must be configured

---

**Key Points**: 
- Free tier uses shared OAuth credentials for easy setup (one-time token exchange through our servers)
- All API calls after authentication go directly from WordPress to Google's servers
- Pro tier only connects to our license server for license validation, not for API proxying
- Users can optionally use their own Google Cloud Project credentials for more control
