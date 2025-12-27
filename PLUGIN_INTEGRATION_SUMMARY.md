# Plugin Integration Summary

## Updated Endpoints

### License Server
- **Old**: `https://reactwoo.com/wp-json/grp-license/v1/`
- **New**: `https://license.reactwoo.com/api/v1/license/`

### Cloud Server (OAuth API)
- **Old**: `https://reactwoo.com/wp-json/grp-api/v1/`
- **New**: `https://cloud.reactwoo.com/grp-api/v1/`

## Changes Made

### 1. License Class (`class-grp-license.php`)

#### Endpoint Updates
- Changed license server URL to `https://license.reactwoo.com/api/v1/license/`
- Updated endpoint mapping:
  - `activate` → `/activate`
  - `check` → `/validate`
  - `deactivate` → `/deactivate`

#### JWT Token Storage
- Stores JWT token from license activation: `grp_license_jwt_token`
- Token is stored when license is activated
- Token is removed when license is deactivated
- New method: `get_jwt_token()` to retrieve token for API requests

#### Response Handling
- Handles license server response format
- Extracts `accessToken` or `access_token` from response
- Stores package information and expiration data

### 2. API Class (`class-grp-api.php`)

#### Cloud Server URL
- Changed default API server URL to `https://cloud.reactwoo.com/grp-api/v1/`

#### JWT Authentication
- Automatically includes JWT token in `Authorization: Bearer <token>` header
- Token is retrieved from license class
- All OAuth requests to cloud server now include authentication

## How It Works

### License Activation Flow

1. **User enters license key** in plugin settings
2. **Plugin sends request** to `https://license.reactwoo.com/api/v1/license/activate`
   - Includes: `license_key`, `site_url`, `plugin_version`, `plugin_slug: 'goorev'`
3. **License server validates** and returns JWT token
4. **Plugin stores**:
   - License key
   - License status
   - JWT token (for API authentication)
   - Package information

### OAuth Flow (Free & Pro)

1. **User clicks "Connect Google"**
2. **Plugin requests OAuth URL** from `https://cloud.reactwoo.com/grp-api/v1/oauth/auth-url`
   - Includes JWT token in `Authorization: Bearer <token>` header (if Pro)
   - Free users: No token required (handled by cloud server)
3. **Cloud server verifies JWT** (if provided)
4. **Returns Google OAuth URL**
5. **User authorizes** on Google
6. **Plugin exchanges code** for tokens via cloud server
7. **Tokens stored** in WordPress options

### Free vs Pro

#### Free Version
- No license key required
- Uses shared cloud API (no custom credentials)
- JWT token not sent (cloud server handles free tier)

#### Pro Version
- License key required
- JWT token sent with all API requests
- Can use custom Google credentials OR shared cloud API
- Pro features unlocked based on license status

## Testing

### Test License Activation

1. Go to **Settings → License**
2. Enter a test license key
3. Click **"Activate License"**
4. Check that:
   - License status shows as "Valid"
   - JWT token is stored (check database: `wp_options` table, `grp_license_jwt_token`)

### Test OAuth Connection

1. Go to **Settings → Google API**
2. Click **"Connect Google Account"**
3. Check that:
   - OAuth URL is generated
   - After authorization, tokens are stored
   - Connection test succeeds

### Verify JWT Token

```php
// In WordPress
$license = new GRP_License();
$token = $license->get_jwt_token();
var_dump($token); // Should show JWT token string
```

## Configuration

### Override API Server URL (if needed)

```php
// In wp-config.php or theme functions.php
define('GRP_API_SERVER_URL', 'https://your-custom-server.com/grp-api/v1/');
```

Or via filter:
```php
add_filter('grp_api_server_url', function() {
    return 'https://your-custom-server.com/grp-api/v1/';
});
```

## Troubleshooting

### "Invalid response from license server"
- Check license server is accessible: `https://license.reactwoo.com/api/v1/license/activate`
- Verify endpoint format matches license server routes
- Check response format matches expected structure

### "Authorization header missing or invalid"
- Verify JWT token is stored after license activation
- Check token hasn't expired
- Verify cloud server has public key configured

### "License activation failed"
- Check license key exists in license server database
- Verify domain matches license record
- Check license status is 'active' in database

## Next Steps

1. **Test license activation** with a real license key
2. **Test OAuth flow** with both free and Pro versions
3. **Verify JWT tokens** are being sent correctly
4. **Monitor logs** on both license server and cloud server

