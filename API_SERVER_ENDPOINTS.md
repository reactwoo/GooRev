# API Server Endpoints Documentation

This document describes the REST API endpoints that need to be implemented on your API server to handle OAuth proxy functionality for the Google Reviews Plugin.

## Base URL

Default: `https://reactwoo.com/wp-json/grp-api/v1/`

Can be overridden via constant: `GRP_API_SERVER_URL` or filter: `grp_api_server_url`

## Authentication

All requests include the following headers:
- `Content-Type: application/json`
- `X-Site-URL: {site_url}` - The WordPress site URL making the request
- `X-Plugin-Version: {version}` - The plugin version

## Endpoints

### 1. Get OAuth Authorization URL

**Endpoint:** `POST /oauth/auth-url`

**Request Body:**
```json
{
  "redirect_uri": "https://example.com/wp-admin/admin.php?page=google-reviews-settings&action=oauth_callback",
  "state": "nonce_string"
}
```

**Response (Success):**
```json
{
  "auth_url": "https://accounts.google.com/o/oauth2/v2/auth?client_id=...&redirect_uri=...&scope=...&response_type=code&access_type=offline&prompt=consent&state=..."
}
```

**Response (Error):**
```json
{
  "message": "Error message here"
}
```

**Implementation Notes:**
- Use your Google Cloud Project OAuth credentials (Client ID and Secret stored securely on your server)
- Build the Google OAuth authorization URL with:
  - `client_id`: Your Google OAuth Client ID
  - `redirect_uri`: The redirect_uri from the request
  - `scope`: `https://www.googleapis.com/auth/business.manage`
  - `response_type`: `code`
  - `access_type`: `offline`
  - `prompt`: `consent`
  - `state`: The state from the request (for CSRF protection)

---

### 2. Exchange Authorization Code for Tokens

**Endpoint:** `POST /oauth/token`

**Request Body:**
```json
{
  "code": "authorization_code_from_google",
  "redirect_uri": "https://example.com/wp-admin/admin.php?page=google-reviews-settings&action=oauth_callback"
}
```

**Response (Success):**
```json
{
  "access_token": "ya29.a0AfH6SMC...",
  "refresh_token": "1//0gX...",
  "expires_in": 3599,
  "token_type": "Bearer"
}
```

**Response (Error):**
```json
{
  "message": "Error message here"
}
```

**Implementation Notes:**
- Exchange the authorization code with Google's OAuth token endpoint
- Use your Google OAuth Client ID and Secret (stored securely on your server)
- Make a POST request to `https://oauth2.googleapis.com/token` with:
  - `client_id`: Your Google OAuth Client ID
  - `client_secret`: Your Google OAuth Client Secret
  - `code`: The authorization code from the request
  - `grant_type`: `authorization_code`
  - `redirect_uri`: The redirect_uri from the request
- Return the tokens to the WordPress plugin

---

### 3. Refresh Access Token

**Endpoint:** `POST /oauth/refresh`

**Request Body:**
```json
{
  "refresh_token": "1//0gX..."
}
```

**Response (Success):**
```json
{
  "access_token": "ya29.a0AfH6SMC...",
  "expires_in": 3599,
  "token_type": "Bearer"
}
```

**Response (Error):**
```json
{
  "message": "Error message here"
}
```

**Implementation Notes:**
- Refresh the access token using the refresh token
- Make a POST request to `https://oauth2.googleapis.com/token` with:
  - `client_id`: Your Google OAuth Client ID
  - `client_secret`: Your Google OAuth Client Secret
  - `refresh_token`: The refresh token from the request
  - `grant_type`: `refresh_token`
- Return the new access token (and optionally a new refresh token if provided)

---

## Error Handling

All endpoints should return appropriate HTTP status codes:
- `200` - Success
- `400` - Bad Request (invalid parameters)
- `401` - Unauthorized (authentication failed)
- `500` - Internal Server Error

Error responses should include a `message` field with a human-readable error description.

## Security Considerations

1. **Rate Limiting**: Implement rate limiting to prevent abuse
2. **Validation**: Validate all input parameters
3. **HTTPS Only**: All endpoints must be served over HTTPS
4. **Credential Storage**: Store Google OAuth credentials securely (environment variables, secure vault, etc.)
5. **Logging**: Log requests for debugging and security monitoring
6. **CORS**: If needed, configure CORS appropriately (though WordPress makes server-to-server requests)

## Example Implementation (WordPress REST API)

If you're using WordPress REST API on your server, here's a basic example:

```php
// Register routes
add_action('rest_api_init', function() {
    register_rest_route('grp-api/v1', '/oauth/auth-url', array(
        'methods' => 'POST',
        'callback' => 'grp_api_get_auth_url',
        'permission_callback' => '__return_true'
    ));
    
    register_rest_route('grp-api/v1', '/oauth/token', array(
        'methods' => 'POST',
        'callback' => 'grp_api_exchange_token',
        'permission_callback' => '__return_true'
    ));
    
    register_rest_route('grp-api/v1', '/oauth/refresh', array(
        'methods' => 'POST',
        'callback' => 'grp_api_refresh_token',
        'permission_callback' => '__return_true'
    ));
});

function grp_api_get_auth_url($request) {
    $redirect_uri = $request->get_param('redirect_uri');
    $state = $request->get_param('state');
    
    // Get your OAuth credentials (from secure storage)
    $client_id = getenv('GRP_GOOGLE_CLIENT_ID'); // or from secure config
    
    $params = array(
        'client_id' => $client_id,
        'redirect_uri' => $redirect_uri,
        'scope' => 'https://www.googleapis.com/auth/business.manage',
        'response_type' => 'code',
        'access_type' => 'offline',
        'prompt' => 'consent',
        'state' => $state
    );
    
    $auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    
    return new WP_REST_Response(array('auth_url' => $auth_url), 200);
}

function grp_api_exchange_token($request) {
    $code = $request->get_param('code');
    $redirect_uri = $request->get_param('redirect_uri');
    
    // Get your OAuth credentials (from secure storage)
    $client_id = getenv('GRP_GOOGLE_CLIENT_ID');
    $client_secret = getenv('GRP_GOOGLE_CLIENT_SECRET');
    
    $response = wp_remote_post('https://oauth2.googleapis.com/token', array(
        'body' => array(
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirect_uri
        )
    ));
    
    if (is_wp_error($response)) {
        return new WP_Error('token_exchange_failed', 'Failed to exchange token', array('status' => 500));
    }
    
    $body = json_decode(wp_remote_retrieve_body($response), true);
    
    if (isset($body['error'])) {
        return new WP_Error('token_exchange_failed', $body['error_description'] ?? 'Token exchange failed', array('status' => 400));
    }
    
    return new WP_REST_Response($body, 200);
}

function grp_api_refresh_token($request) {
    $refresh_token = $request->get_param('refresh_token');
    
    // Get your OAuth credentials (from secure storage)
    $client_id = getenv('GRP_GOOGLE_CLIENT_ID');
    $client_secret = getenv('GRP_GOOGLE_CLIENT_SECRET');
    
    $response = wp_remote_post('https://oauth2.googleapis.com/token', array(
        'body' => array(
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token'
        )
    ));
    
    if (is_wp_error($response)) {
        return new WP_Error('token_refresh_failed', 'Failed to refresh token', array('status' => 500));
    }
    
    $body = json_decode(wp_remote_retrieve_body($response), true);
    
    if (isset($body['error'])) {
        return new WP_Error('token_refresh_failed', $body['error_description'] ?? 'Token refresh failed', array('status' => 400));
    }
    
    return new WP_REST_Response($body, 200);
}
```

## Testing

Test each endpoint with:
- Valid requests
- Invalid/missing parameters
- Invalid authorization codes
- Expired refresh tokens
- Rate limiting scenarios

## Monitoring

Monitor:
- Request volume
- Error rates
- Response times
- Token refresh frequency
- Failed authentication attempts


