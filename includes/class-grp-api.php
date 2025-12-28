<?php
/**
 * Google My Business API integration
 *
 * @package Google_Reviews_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GRP_API {
    
    /**
     * Business Profile API base URL (replaces deprecated My Business v4)
     * Docs: businessprofile.googleapis.com/v1
     */
    const API_BASE_URL = 'https://businessprofile.googleapis.com/v1/';
    
    /**
     * OAuth 2.0 endpoints (for direct Google OAuth - used only with custom credentials)
     */
    const OAUTH_AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    const OAUTH_TOKEN_URL = 'https://oauth2.googleapis.com/token';
    
    /**
     * API Server base URL for OAuth proxy
     * Can be overridden via constant: GRP_API_SERVER_URL
     * Or filter: grp_api_server_url
     */
    const DEFAULT_API_SERVER_URL = 'https://cloud.reactwoo.com/grp-api/v1/';
    
    /**
     * API Server endpoints
     */
    private $api_server_url;
    
    /**
     * API credentials (only used when user provides custom credentials)
     */
    private $client_id;
    private $client_secret;
    private $redirect_uri;
    
    /**
     * Access token
     */
    private $access_token;
    private $refresh_token;
    
    /**
     * Last error for debugging
     */
    private $last_error = null;
    
    /**
     * Constructor
     */
    public function __construct() {
        // Get API server URL (can be overridden via constant or filter)
        $this->api_server_url = defined('GRP_API_SERVER_URL') 
            ? GRP_API_SERVER_URL 
            : apply_filters('grp_api_server_url', self::DEFAULT_API_SERVER_URL);
        
        // User-provided credentials (only used when custom credentials are enabled)
        $this->client_id = get_option('grp_google_client_id', '');
        $this->client_secret = get_option('grp_google_client_secret', '');
        
        $this->redirect_uri = admin_url('admin.php?page=google-reviews-settings&action=oauth_callback');
        
        $this->access_token = get_option('grp_google_access_token', '');
        $this->refresh_token = get_option('grp_google_refresh_token', '');
    }
    
    /**
     * Check if using API server (free tier) or custom credentials
     */
    public function is_using_api_server() {
        $license = new GRP_License();
        $has_license = $license->has_license();
        $is_pro = $license->is_pro();
        $is_enterprise = $license->is_enterprise();
        $is_free = $license->is_free();
        
        // Enterprise users can use custom credentials to bypass cloud server
        if ($is_enterprise) {
            $user_client_id = get_option('grp_google_client_id', '');
            // If Enterprise has custom credentials, they can bypass cloud server
            if (!empty($user_client_id)) {
                return false;
            }
            // Otherwise, Enterprise uses cloud server
            return true;
        }
        
        // Pro users MUST use cloud server - cannot bypass
        if ($is_pro) {
            return true;
        }
        
        // Free users: Can use cloud server (if they have a free license) OR custom credentials
        // WordPress.org compliance: Free version works without registration
        if ($is_free) {
            // Free license active - use cloud server
            return true;
        }
        
        // No license: Check if user has custom credentials
        // If yes, allow direct API calls (WordPress.org compliant - no registration required)
        // If no, suggest cloud server (but don't require license for basic functionality)
        $user_client_id = get_option('grp_google_client_id', '');
        if (!empty($user_client_id)) {
            // User has custom credentials - allow direct API calls (no license needed)
            return false;
        }
        
        // No license and no custom credentials: 
        // For WordPress.org compliance, we allow them to proceed but they'll need either:
        // 1. Custom credentials (their own Google Cloud Project)
        // 2. Free license (optional, for easier cloud server access)
        // Default to cloud server attempt, but it will fail without license
        // This allows the plugin to work, but guides users to either setup
        return true;
    }
    
    /**
     * Make request to API server
     */
    private function make_api_server_request($endpoint, $data = array(), $method = 'POST') {
        $url = rtrim($this->api_server_url, '/') . '/' . ltrim($endpoint, '/');
        
        // Get JWT token from license
        $license = new GRP_License();
        $jwt_token = $license->get_jwt_token();
        
        // Add access token to data if available (for Google API calls)
        if ($this->access_token) {
            $data['access_token'] = $this->access_token;
            // Also include refresh token and expiry if available
            if ($this->refresh_token) {
                $data['refresh_token'] = $this->refresh_token;
            }
        }
        
        $headers = array(
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-Site-URL' => home_url(),
            'X-Plugin-Version' => GRP_PLUGIN_VERSION
        );
        
        // Add JWT token if available (required for cloud server authentication when license is active)
        if (!empty($jwt_token)) {
            $headers['Authorization'] = 'Bearer ' . $jwt_token;
        } else {
            // If no token and license is active, this is a problem
            $license_status = $license->get_license_status();
            if ($license_status === 'valid') {
                // License is active but no token - try to refresh
                if (!$this->refresh_jwt_token()) {
                    return new WP_Error('no_jwt_token', __('License is active but authentication token is missing. Please reactivate your license.', 'google-reviews-plugin'));
                }
                // Retry with new token
                $jwt_token = $license->get_jwt_token();
                if (!empty($jwt_token)) {
                    $headers['Authorization'] = 'Bearer ' . $jwt_token;
                }
            }
        }
        
        // Determine HTTP method
        $http_method = strtoupper($method);
        
        $args = array(
            'headers' => $headers,
            'timeout' => 30,
            'method' => $http_method
        );
        
        // For GET requests, add data as query parameters
        if ($http_method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        } else {
            // For POST/PUT/etc, add data as JSON body
            $args['body'] = wp_json_encode($data);
        }
        
        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        // Handle empty response
        if (empty($body)) {
            if ($status_code >= 400) {
                return new WP_Error('api_server_error', sprintf(
                    __('API server returned error status %d with no response body. Please check the cloud server is running.', 'google-reviews-plugin'),
                    $status_code
                ));
            }
            return new WP_Error('empty_response', __('Empty response from API server. Please check the cloud server is running.', 'google-reviews-plugin'));
        }
        
        // Try to decode JSON
        $decoded = json_decode($body, true);
        
        // If JSON decode failed, check if it's an HTML error page or other format
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Check if it's HTML (likely an error page)
            if (stripos($body, '<html') !== false || stripos($body, '<!DOCTYPE') !== false) {
                // Extract title or first meaningful text
                if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $body, $matches)) {
                    $error_title = trim(strip_tags($matches[1]));
                } else {
                    $error_title = __('HTML error page', 'google-reviews-plugin');
                }
                return new WP_Error('html_response', sprintf(
                    __('API server returned HTML instead of JSON (Status: %d). This usually means the endpoint doesn\'t exist or the server is misconfigured. Error: %s', 'google-reviews-plugin'),
                    $status_code,
                    esc_html($error_title)
                ));
            }
            
            // Provide more details about the JSON error
            $json_error = json_last_error_msg();
            $body_preview = strlen($body) > 200 ? substr($body, 0, 200) . '...' : $body;
            return new WP_Error('invalid_response', sprintf(
                __('Invalid JSON response from API server (Status: %d, Error: %s). Response preview: %s', 'google-reviews-plugin'),
                $status_code,
                $json_error,
                esc_html($body_preview)
            ));
        }
        
        // Handle 429 - Rate limit error
        if ($status_code === 429) {
            $error_message = isset($decoded['message']) ? $decoded['message'] : __('Google API rate limit exceeded. Please wait a moment and try again.', 'google-reviews-plugin');
            return new WP_Error('rate_limit', $error_message);
        }
        
        // Handle 401 - JWT token expired, try to refresh
        if ($status_code === 401) {
            // Get error details first
            $error_detail = isset($decoded['message']) ? $decoded['message'] : (isset($decoded['error']) ? $decoded['error'] : '');
            
            // For OAuth token exchange, don't retry - the code might be expired
            if (strpos($endpoint, 'oauth/token') !== false) {
                return new WP_Error('oauth_failed', sprintf(
                    __('OAuth token exchange failed: %s. The authorization code may have expired. Please try connecting again.', 'google-reviews-plugin'),
                    $error_detail
                ));
            }
            
            // Try to refresh the JWT token
            $refreshed = $this->refresh_jwt_token();
            if ($refreshed) {
                // Retry the request with new token
                return $this->make_api_server_request($endpoint, $data, $method);
            }
            
            // If refresh failed, provide helpful error message
            $error_msg = __('Invalid or expired license token.', 'google-reviews-plugin');
            if (!empty($error_detail)) {
                $error_msg .= ' ' . $error_detail;
            }
            $error_msg .= ' ' . __('Please click "Deactivate License" and then reactivate it to get a new token.', 'google-reviews-plugin');
            return new WP_Error('unauthorized', $error_msg);
        }
        
        // Handle 400 - Bad request (common for OAuth errors)
        if ($status_code === 400) {
            $error_detail = isset($decoded['message']) ? $decoded['message'] : (isset($decoded['error']) ? $decoded['error'] : '');
            if (strpos($endpoint, 'oauth/token') !== false || strpos($endpoint, 'oauth/') !== false) {
                return new WP_Error('oauth_failed', sprintf(
                    __('OAuth error: %s. Please check that the redirect URI matches and try connecting again.', 'google-reviews-plugin'),
                    $error_detail
                ));
            }
        }
        
        if ($status_code >= 400) {
            $error_message = isset($decoded['message']) ? $decoded['message'] : __('API server error', 'google-reviews-plugin');
            if (isset($decoded['error'])) {
                if (is_array($decoded['error'])) {
                    $error_message = implode(', ', $decoded['error']);
                } else {
                    $error_message = $decoded['error'];
                }
            }
            return new WP_Error('api_server_error', $error_message);
        }
        
        return $decoded;
    }
    
    /**
     * Refresh JWT token from license server
     */
    private function refresh_jwt_token() {
        $license = new GRP_License();
        $license_key = $license->get_license_key();
        
        if (empty($license_key)) {
            return false;
        }
        
        // Use license check to refresh the token (this calls /activate endpoint which returns new token)
        $result = $license->check_license_status();
        
        if ($result) {
            // Token should be updated by check_license_status
            // Verify token was actually updated
            $new_token = $license->get_jwt_token();
            return !empty($new_token);
        }
        
        return false;
    }
    
    /**
     * Get OAuth authorization URL
     */
    public function get_auth_url() {
        $state = wp_create_nonce('grp_oauth_state');
        
        // Store state for validation
        update_option('grp_oauth_state', $state);
        
        if ($this->is_using_api_server()) {
            // Route through API server
            $response = $this->make_api_server_request('oauth/auth-url', array(
                'redirect_uri' => $this->redirect_uri,
                'state' => $state
            ));
            
            if (is_wp_error($response)) {
                return $response;
            }
            
            return isset($response['auth_url']) ? $response['auth_url'] : new WP_Error('no_auth_url', __('Failed to get authorization URL from API server', 'google-reviews-plugin'));
        } else {
            // Use custom credentials - direct to Google
            if (empty($this->client_id)) {
                return new WP_Error('no_client_id', __('OAuth Client ID is not configured.', 'google-reviews-plugin'));
            }
            
            $params = array(
                'client_id' => $this->client_id,
                'redirect_uri' => $this->redirect_uri,
                'scope' => 'https://www.googleapis.com/auth/business.manage',
                'response_type' => 'code',
                'access_type' => 'offline',
                'prompt' => 'consent',
                'state' => $state
            );
            
            return self::OAUTH_AUTH_URL . '?' . http_build_query($params);
        }
    }
    
    /**
     * Retrieve OAuth tokens from cloud server using state
     * Called after OAuth callback redirects back to WordPress
     */
    public function retrieve_oauth_tokens($state) {
        if (!$this->is_using_api_server()) {
            return new WP_Error('not_using_api_server', __('This method is only available when using the API server.', 'google-reviews-plugin'));
        }

        $response = $this->make_api_server_request('oauth/tokens', array(
            'state' => $state
        ), 'GET');

        if (is_wp_error($response)) {
            $this->last_error = $response;
            return $response;
        }

        if (isset($response['success']) && !$response['success']) {
            $error_msg = isset($response['message']) ? $response['message'] : 'Unknown error from API server';
            $this->last_error = new WP_Error('oauth_failed', $error_msg);
            return $this->last_error;
        }

        if (isset($response['access_token'])) {
            $this->access_token = $response['access_token'];
            $this->refresh_token = isset($response['refresh_token']) ? $response['refresh_token'] : $this->refresh_token;

            update_option('grp_google_access_token', $this->access_token);
            if ($this->refresh_token) {
                update_option('grp_google_refresh_token', $this->refresh_token);
            }

            return $response;
        }

        $this->last_error = new WP_Error('no_access_token', __('Access token not found in response.', 'google-reviews-plugin'));
        return $this->last_error;
    }

    /**
     * Exchange authorization code for tokens (legacy method - kept for backward compatibility)
     */
    public function exchange_code_for_tokens($code) {
        if ($this->is_using_api_server()) {
            // Route through API server
            $response = $this->make_api_server_request('oauth/token', array(
                'code' => $code,
                'redirect_uri' => $this->redirect_uri
            ), 'POST');
            
            if (is_wp_error($response)) {
                // Store error for retrieval
                $this->last_error = $response;
                // Log the error for debugging
                grp_debug_log('OAuth Token Exchange Error', array(
                    'error_message' => $response->get_error_message(),
                    'error_data' => $response->get_error_data()
                ));
                return false;
            }
            
            // Check if response has success flag
            if (isset($response['success']) && !$response['success']) {
                $error_msg = isset($response['message']) ? $response['message'] : 'Unknown error from API server';
                $this->last_error = new WP_Error('oauth_failed', $error_msg);
                grp_debug_log('OAuth Token Exchange Failed', $error_msg);
                return false;
            }
            
            if (isset($response['access_token'])) {
                $this->access_token = $response['access_token'];
                $this->refresh_token = isset($response['refresh_token']) ? $response['refresh_token'] : $this->refresh_token;
                
                update_option('grp_google_access_token', $this->access_token);
                if ($this->refresh_token) {
                    update_option('grp_google_refresh_token', $this->refresh_token);
                }
                
                return true;
            }
            
            // Log if access_token is missing
            grp_debug_log('OAuth Token Exchange: access_token missing in response', $response);
            return false;
        } else {
            // Use custom credentials - direct to Google
            if (empty($this->client_id) || empty($this->client_secret)) {
                return false;
            }
            
            $response = wp_remote_post(self::OAUTH_TOKEN_URL, array(
                'body' => array(
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => $this->redirect_uri
                )
            ));
            
            if (is_wp_error($response)) {
                return false;
            }
            
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            if (isset($data['access_token'])) {
                $this->access_token = $data['access_token'];
                $this->refresh_token = isset($data['refresh_token']) ? $data['refresh_token'] : $this->refresh_token;
                
                update_option('grp_google_access_token', $this->access_token);
                if ($this->refresh_token) {
                    update_option('grp_google_refresh_token', $this->refresh_token);
                }
                
                return true;
            }
            
            return false;
        }
    }
    
    /**
     * Refresh access token
     */
    public function refresh_access_token() {
        if (!$this->refresh_token) {
            return false;
        }
        
        if ($this->is_using_api_server()) {
            // Route through API server
            $response = $this->make_api_server_request('oauth/refresh', array(
                'refresh_token' => $this->refresh_token
            ));
            
            if (is_wp_error($response)) {
                return false;
            }
            
            if (isset($response['access_token'])) {
                $this->access_token = $response['access_token'];
                update_option('grp_google_access_token', $this->access_token);
                
                // Update refresh token if provided
                if (isset($response['refresh_token'])) {
                    $this->refresh_token = $response['refresh_token'];
                    update_option('grp_google_refresh_token', $this->refresh_token);
                }
                
                return true;
            }
            
            return false;
        } else {
            // Use custom credentials - direct to Google
            if (empty($this->client_id) || empty($this->client_secret)) {
                return false;
            }
            
            $response = wp_remote_post(self::OAUTH_TOKEN_URL, array(
                'body' => array(
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                    'refresh_token' => $this->refresh_token,
                    'grant_type' => 'refresh_token'
                )
            ));
            
            if (is_wp_error($response)) {
                return false;
            }
            
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            if (isset($data['access_token'])) {
                $this->access_token = $data['access_token'];
                update_option('grp_google_access_token', $this->access_token);
                return true;
            }
            
            return false;
        }
    }
    
    /**
     * Make API request
     * 
     * IMPORTANT (WordPress.org compliant):
     * - Free users WITHOUT license: Can use custom credentials (no registration required)
     * - Free users WITH license: Can use cloud server (optional, for easier setup)
     * - Pro users: MUST use cloud server (license required)
     * - Enterprise users: CAN use custom credentials OR cloud server
     * 
     * This method is for:
     * - Free users with custom credentials (no license needed - WordPress.org compliant)
     * - Enterprise tier with custom credentials
     * - Internal use when routing through cloud server
     */
    private function make_request($endpoint, $method = 'GET', $data = null) {
        $license = new GRP_License();
        $has_license = $license->has_license();
        $is_pro = $license->is_pro();
        $is_enterprise = $license->is_enterprise();
        $is_free = $license->is_free();
        
        // Pro users (non-Enterprise) cannot make direct API calls - must use cloud server
        if ($is_pro && !$is_enterprise) {
            return new WP_Error('cloud_server_required', __('Pro licenses must use the cloud server. Enterprise licenses can use custom credentials to bypass the cloud server.', 'google-reviews-plugin'));
        }
        
        // Free users with license should use cloud server, but allow direct calls if they have custom credentials
        // This is for WordPress.org compliance - free version works without registration
        if ($is_free) {
            // Free license active - prefer cloud server, but allow custom credentials
            $user_client_id = get_option('grp_google_client_id', '');
            if (empty($user_client_id)) {
                // No custom credentials - should use cloud server
                return new WP_Error('cloud_server_required', __('Free licenses use the cloud server. Enter your Google Cloud credentials below to use direct API calls instead.', 'google-reviews-plugin'));
            }
            // Has custom credentials - allow direct API calls (WordPress.org compliant)
        }
        
        // No license: Allow direct API calls if custom credentials provided (WordPress.org compliant)
        // This allows the plugin to work without requiring registration
        if (!$has_license) {
            $user_client_id = get_option('grp_google_client_id', '');
            if (empty($user_client_id)) {
                return new WP_Error('credentials_required', __('Please either activate a free license (for cloud server access) or enter your Google Cloud credentials below to use direct API calls.', 'google-reviews-plugin'));
            }
            // Has custom credentials - allow direct API calls (no license needed - WordPress.org compliant)
        }
        
        if (!$this->access_token) {
            return new WP_Error('no_token', __('No access token available', 'google-reviews-plugin'));
        }
        
        // Prepare candidates across the modern Business Profile APIs.
        $base_candidates = $this->get_base_candidates($endpoint);
        $last_error = null;
        
        foreach ($base_candidates as $base_url) {
            // Always build fresh args to include the latest token
            $headers = array(
                'Authorization' => 'Bearer ' . $this->access_token,
                'Content-Type' => 'application/json'
            );
            $args = array(
                'headers' => $headers,
                'timeout' => 30
            );
            $method = strtoupper($method);
            $args['method'] = $method;
            if ($data && $method === 'POST') {
                $args['body'] = json_encode($data);
            }

            $url = rtrim($base_url, '/') . '/' . ltrim($endpoint, '/');
            if ($method === 'GET' && $data) {
                $url .= '?' . http_build_query($data);
            }

            $response = wp_remote_request($url, $args);
            if (is_wp_error($response)) {
                $last_error = $response;
                continue;
            }

            $status_code = wp_remote_retrieve_response_code($response);
            $body = wp_remote_retrieve_body($response);
            $service_host = parse_url($base_url, PHP_URL_HOST);

            // If unauthorized, try to refresh token once and retry this call from the top
            if ($status_code === 401) {
                if ($this->refresh_access_token()) {
                    return $this->make_request($endpoint, $method, $data);
                }
                return new WP_Error('unauthorized', __('Invalid or expired token', 'google-reviews-plugin'));
            }

            // 404 on this base – try the next candidate
            if ($status_code === 404) {
                $last_error = new WP_Error('not_found', sprintf(__('API endpoint not found on %s', 'google-reviews-plugin'), parse_url($base_url, PHP_URL_HOST)));
                continue;
            }

            // Other 4xx/5xx – inspect and handle common cases more helpfully
            if ($status_code >= 400) {
                $decoded = json_decode($body, true);
                $error_reason = '';
                $error_status = '';
                $error_message = '';
                $activation_url = '';
                $disabled_service = '';
                $quota_limit_value = '';

                if (is_array($decoded) && isset($decoded['error'])) {
                    $err = $decoded['error'];
                    $error_message = isset($err['message']) ? $err['message'] : '';
                    $error_status = isset($err['status']) ? $err['status'] : '';
                    if (isset($err['details']) && is_array($err['details'])) {
                        foreach ($err['details'] as $detail) {
                            if (isset($detail['@type']) && $detail['@type'] === 'type.googleapis.com/google.rpc.ErrorInfo') {
                                if (!empty($detail['reason'])) {
                                    $error_reason = $detail['reason'];
                                }
                                if (isset($detail['metadata']['activationUrl'])) {
                                    $activation_url = $detail['metadata']['activationUrl'];
                                }
                                if (isset($detail['metadata']['service'])) {
                                    $disabled_service = $detail['metadata']['service'];
                                }
                                if (isset($detail['metadata']['quota_limit_value'])) {
                                    $quota_limit_value = (string) $detail['metadata']['quota_limit_value'];
                                }
                            }
                        }
                    }
                }

                // If this base is disabled at the project, try next base and remember guidance
                if ($status_code === 403 && $error_reason === 'SERVICE_DISABLED') {
                    // Save a rich error to fall back to if all bases fail
                    $last_error = new WP_Error(
                        'service_disabled',
                        sprintf(
                            /* translators: 1: service id, 2: activation url */
                            __('Required Google API is disabled (%1$s). Enable it, then retry. %2$s', 'google-reviews-plugin'),
                            $disabled_service ?: __('Unknown service', 'google-reviews-plugin'),
                            $activation_url ? sprintf(__('Activation link: %s', 'google-reviews-plugin'), esc_url($activation_url)) : ''
                        )
                    );
                    // Try the next candidate base URL
                    continue;
                }

                // Common insufficient scope error – advise re-auth with correct scope
                if ($status_code === 403 && ($error_reason === 'ACCESS_TOKEN_SCOPE_INSUFFICIENT' || stripos($error_message, 'insufficient permissions') !== false)) {
                    return new WP_Error(
                        'insufficient_scope',
                        sprintf(
                            __('API Error (403): Insufficient permissions. Please reconnect and ensure the OAuth scope %s is granted on the correct Google account.', 'google-reviews-plugin'),
                            'https://www.googleapis.com/auth/business.manage'
                        )
                    );
                }

                // Rate limit handling – if Account Management has 0 QPM, prefer other bases
                if ($status_code === 429) {
                    // Friendly guidance for the common 0 QPM case on Account Management
                    $is_account_mgmt = (stripos((string) $service_host, 'mybusinessaccountmanagement.googleapis.com') !== false)
                        || (stripos((string) $disabled_service, 'mybusinessaccountmanagement.googleapis.com') !== false);
                    $is_zero_quota = ($quota_limit_value === '0') || stripos($error_message, "quota_limit_value: '0'") !== false;

                    if ($is_account_mgmt && $is_zero_quota) {
                        // Keep a helpful error to return if all bases fail, but try next base first
                        $last_error = new WP_Error(
                            'rate_limit_zero_quota',
                            __(
                                'Google project has 0 QPM for Business Profile Account Management API. Request GBP API access approval or enable the correct Business Profile APIs. Calls will fall back to other Business Profile endpoints where possible.',
                                'google-reviews-plugin'
                            )
                        );
                        // Try the next candidate base
                        continue;
                    }

                    // Simple backoff-and-retry once for transient 429s
                    $retry_after_header = wp_remote_retrieve_header($response, 'retry-after');
                    $retry_ms = 0;
                    if (!empty($retry_after_header)) {
                        $retry_seconds = is_numeric($retry_after_header) ? (int) $retry_after_header : 1;
                        $retry_ms = max(100, min(5000, $retry_seconds * 1000));
                    } else {
                        $retry_ms = rand(200, 600); // jitter
                    }
                    usleep($retry_ms * 1000);
                    $retry_response = wp_remote_request($url, $args);
                    if (!is_wp_error($retry_response)) {
                        $retry_status = wp_remote_retrieve_response_code($retry_response);
                        if ($retry_status < 400) {
                            return json_decode(wp_remote_retrieve_body($retry_response), true);
                        }
                    }
                }

                // Default: return detailed error message
                $error_msg = '';
                if (!empty($error_message)) {
                    $error_msg = $error_message;
                } elseif (!empty($error_reason)) {
                    $error_msg = $error_reason;
                } elseif (is_array($decoded) && isset($decoded['error'])) {
                    if (is_string($decoded['error'])) {
                        $error_msg = $decoded['error'];
                    } elseif (is_array($decoded['error']) && isset($decoded['error']['message'])) {
                        $error_msg = $decoded['error']['message'];
                    }
                }
                
                if (empty($error_msg)) {
                    // Fallback to showing first 200 chars of body
                    $error_msg = substr($body, 0, 200);
                }
                
                return new WP_Error('api_error', sprintf(__('API Error (%d): %s', 'google-reviews-plugin'), $status_code, $error_msg));
            }

            // Success on this base
            return json_decode($body, true);
        }

        // If we exhausted all bases, return the last error or a generic one
        return $last_error ?: new WP_Error('api_error', __('API request failed on all known Business Profile endpoints.', 'google-reviews-plugin'));
    }

    /**
     * Determine which API base URLs to try for a given endpoint.
     * Prefer modern Business Profile APIs only (no legacy Account Management).
     */
    private function get_base_candidates($endpoint) {
        $endpoint = ltrim($endpoint, '/');
        $business_profile = self::API_BASE_URL; // businessprofile.googleapis.com/v1
        $business_info = 'https://mybusinessbusinessinformation.googleapis.com/v1/';

        // Prefer modern Business Profile hosts only
        if (strpos($endpoint, 'accounts') === 0) {
            return array($business_profile, $business_info);
        }

        // Generic order
        return array($business_profile, $business_info);
    }
    
    /**
     * Get last error (for debugging)
     */
    public function get_last_error() {
        return $this->last_error;
    }
    
    /**
     * Get account information
     * 
     * Note: Free users can access this for basic setup (single location)
     */
    public function get_accounts() {
        // Pro/Enterprise users must use cloud server (if not Enterprise with custom credentials)
        if ($this->is_using_api_server()) {
            $response = $this->make_api_server_request('accounts');
            if (is_wp_error($response)) {
                return $response;
            }
            return $response;
        }
        
        // Free tier or Enterprise with custom credentials - direct API call
        return $this->make_request('accounts');
    }
    
    /**
     * Get locations for an account
     * 
     * Note: Free users can access this but should be limited to first location only
     */
    public function get_locations($account_id) {
        $license = new GRP_License();
        $is_pro = $license->is_pro();
        
        // Clean account_id - remove 'accounts/' prefix if present
        $clean_account_id = preg_replace('#^accounts/?#', '', $account_id);
        
        // Pro/Enterprise users must use cloud server (if not Enterprise with custom credentials)
        if ($this->is_using_api_server()) {
            $response = $this->make_api_server_request('locations', array(
                'account_id' => $clean_account_id,
                'readMask' => 'name,title,storefrontAddress,phoneNumbers,websiteUri,placeId'
            ), 'GET');
            if (is_wp_error($response)) {
                return $response;
            }
            
            // Handle response format
            $locations = isset($response['locations']) ? $response['locations'] : $response;
            
            // Free users: Limit to first location only
            if (!$is_pro && is_array($locations) && count($locations) > 0) {
                $locations = array_slice($locations, 0, 1);
            }
            
            // Return in expected format
            if (isset($response['locations'])) {
                return array('locations' => $locations);
            }
            return $locations;
        }
        
        // Free tier or Enterprise with custom credentials - direct API call
        $params = array(
            'readMask' => 'name,title,storefrontAddress,phoneNumbers,websiteUri,placeId'
        );
        $response = $this->make_request("accounts/{$clean_account_id}/locations", 'GET', $params);
        
        // Free users: Limit to first location only
        if (!$is_pro && !is_wp_error($response)) {
            if (isset($response['locations']) && is_array($response['locations'])) {
                $response['locations'] = array_slice($response['locations'], 0, 1);
            } elseif (is_array($response) && !isset($response['locations'])) {
                // If response is direct array of locations
                $response = array_slice($response, 0, 1);
            }
        }
        
        return $response;
    }
    
    /**
     * Get reviews for a location
     */
    public function get_reviews($account_id, $location_id, $page_size = 50) {
        // Clean location_id - remove 'locations/' prefix if present
        $clean_location_id = preg_replace('#^locations/?#', '', $location_id);
        
        // Pro users must use cloud server
        if ($this->is_using_api_server()) {
            $response = $this->make_api_server_request('reviews', array(
                'account_id' => $account_id,
                'location_id' => $clean_location_id,
                'page_size' => $page_size
            ), 'GET');
            if (is_wp_error($response)) {
                return $response;
            }
            // The cloud server returns {success: true, reviews: [...]}
            // Return it as-is for sync_reviews to handle
            // For other callers, they should check for 'reviews' key
            return $response;
        }
        
        // Free tier with custom credentials - direct API call
        $params = array(
            'pageSize' => $page_size,
            'orderBy' => 'updateTime desc'
        );
        return $this->make_request("accounts/{$account_id}/locations/{$clean_location_id}/reviews", 'GET', $params);
    }
    
    /**
     * Get specific review
     */
    public function get_review($account_id, $location_id, $review_id) {
        // Clean location_id - remove 'locations/' prefix if present
        $clean_location_id = preg_replace('#^locations/?#', '', $location_id);
        
        // Pro users must use cloud server
        if ($this->is_using_api_server()) {
            $response = $this->make_api_server_request('review', array(
                'account_id' => $account_id,
                'location_id' => $clean_location_id,
                'review_id' => $review_id
            ), 'GET');
            if (is_wp_error($response)) {
                return $response;
            }
            // Handle response format
            if (isset($response['review'])) {
                return $response['review'];
            }
            return $response;
        }
        
        // Free tier with custom credentials - direct API call
        return $this->make_request("accounts/{$account_id}/locations/{$clean_location_id}/reviews/{$review_id}");
    }
    
    /**
     * Reply to a review
     */
    public function reply_to_review($account_id, $location_id, $review_id, $comment) {
        // Clean location_id - remove 'locations/' prefix if present
        $clean_location_id = preg_replace('#^locations/?#', '', $location_id);
        // Pro users must use cloud server
        if ($this->is_using_api_server()) {
            $response = $this->make_api_server_request('review/reply', array(
                'account_id' => $account_id,
                'location_id' => $clean_location_id,
                'review_id' => $review_id,
                'comment' => $comment
            ));
            if (is_wp_error($response)) {
                return $response;
            }
            return $response;
        }
        
        // Free tier with custom credentials - direct API call
        $data = array(
            'comment' => $comment
        );
        return $this->make_request(
            "accounts/{$account_id}/locations/{$clean_location_id}/reviews/{$review_id}/reply",
            'POST',
            $data
        );
    }
    
    /**
     * Check if API is connected
     */
    public function is_connected() {
        return !empty($this->access_token);
    }
    
    /**
     * Disconnect API
     */
    public function disconnect() {
        delete_option('grp_google_access_token');
        delete_option('grp_google_refresh_token');
        delete_option('grp_google_account_id');
        delete_option('grp_google_location_id');
        // Clear cached location data
        delete_option('grp_gbp_place_id_default');
        delete_option('grp_gbp_location_name');
        
        $this->access_token = '';
        $this->refresh_token = '';
    }
    
    /**
     * Test API connection
     */
    public function test_connection() {
        $accounts = $this->get_accounts();
        
        if (is_wp_error($accounts)) {
            return $accounts;
        }
        
        return true;
    }

    /**
     * Ensure default account and location are selected and saved to options.
     * Returns true if both account and location are set after this call.
     */
    public function ensure_default_location_selected() {
        $existing_account = get_option('grp_google_account_id', '');
        $existing_location = get_option('grp_google_location_id', '');
        if (!empty($existing_account) && !empty($existing_location)) {
            return true;
        }

        $accounts_response = $this->get_accounts();
        if (is_wp_error($accounts_response)) {
            return false;
        }

        $accounts_list = array();
        if (isset($accounts_response['accounts']) && is_array($accounts_response['accounts'])) {
            $accounts_list = $accounts_response['accounts'];
        } elseif (is_array($accounts_response)) {
            $accounts_list = $accounts_response;
        }

        if (empty($accounts_list)) {
            return false;
        }

        $first_account = $accounts_list[0];
        $account_name = isset($first_account['name']) ? $first_account['name'] : (isset($first_account['accountName']) ? $first_account['accountName'] : '');
        if (empty($account_name)) {
            return false;
        }

        // Extract bare account ID from a name like "accounts/123456789"
        $account_id = preg_replace('#^accounts/+#', '', $account_name);
        update_option('grp_google_account_id', $account_id);

        // Fetch locations for this account and pick the first active one
        $locations_response = $this->get_locations($account_id);
        if (is_wp_error($locations_response)) {
            return false;
        }

        $locations_list = array();
        if (isset($locations_response['locations']) && is_array($locations_response['locations'])) {
            $locations_list = $locations_response['locations'];
        } elseif (is_array($locations_response)) {
            $locations_list = $locations_response;
        }

        if (empty($locations_list)) {
            return false;
        }

        $first_location = $locations_list[0];
        $location_name = isset($first_location['name']) ? $first_location['name'] : '';
        if (empty($location_name)) {
            return false;
        }

        // Extract bare location ID from a name like "accounts/123/locations/456"
        if (preg_match('#/locations/([^/]+)$#', $location_name, $m)) {
            $location_id = $m[1];
        } else {
            // Fallback: if API returns just the ID, clean it
            $location_id = preg_replace('#^(accounts/[^/]+/)?locations/?#', '', $location_name);
        }

        // Ensure location_id is clean (no prefixes)
        $location_id = preg_replace('#^(accounts/[^/]+/)?locations/?#', '', $location_id);
        
        update_option('grp_google_location_id', $location_id);

        return true;
    }
}