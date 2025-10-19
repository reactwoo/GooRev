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
     * OAuth 2.0 endpoints
     */
    const OAUTH_AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    const OAUTH_TOKEN_URL = 'https://oauth2.googleapis.com/token';
    
    /**
     * API credentials
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
     * Constructor
     */
    public function __construct() {
        $this->client_id = get_option('grp_google_client_id', '');
        $this->client_secret = get_option('grp_google_client_secret', '');
        $this->redirect_uri = admin_url('admin.php?page=google-reviews-settings&action=oauth_callback');
        
        $this->access_token = get_option('grp_google_access_token', '');
        $this->refresh_token = get_option('grp_google_refresh_token', '');
    }
    
    /**
     * Get OAuth authorization URL
     */
    public function get_auth_url() {
        $params = array(
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'scope' => 'https://www.googleapis.com/auth/business.manage',
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => wp_create_nonce('grp_oauth_state')
        );
        
        return self::OAUTH_AUTH_URL . '?' . http_build_query($params);
    }
    
    /**
     * Exchange authorization code for tokens
     */
    public function exchange_code_for_tokens($code) {
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
            
            // Try to auto-select a default account/location if not set yet
            $this->ensure_default_location_selected();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Refresh access token
     */
    public function refresh_access_token() {
        if (!$this->refresh_token) {
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
    
    /**
     * Make API request
     */
    private function make_request($endpoint, $method = 'GET', $data = null) {
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
                    $is_account_mgmt = (stripos((string) $service_host, 'mybusinessaccountmanagement.googleapis.com') !== false);
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

                // Default: return raw details for visibility
                return new WP_Error('api_error', sprintf(__('API Error (%d): %s', 'google-reviews-plugin'), $status_code, $body));
            }

            // Success on this base
            return json_decode($body, true);
        }

        // If we exhausted all bases, return the last error or a generic one
        return $last_error ?: new WP_Error('api_error', __('API request failed on all known Business Profile endpoints.', 'google-reviews-plugin'));
    }

    /**
     * Determine which API base URLs to try for a given endpoint.
     * We prioritize Account Management for account-scoped endpoints,
     * and fall back to other Business Profile API hosts.
     */
    private function get_base_candidates($endpoint) {
        $endpoint = ltrim($endpoint, '/');
        $account_management = 'https://mybusinessaccountmanagement.googleapis.com/v1/';
        $business_profile = self::API_BASE_URL; // businessprofile.googleapis.com/v1
        $business_info = 'https://mybusinessbusinessinformation.googleapis.com/v1/';

        // Prefer modern Business Profile hosts; de-prioritize legacy Account Management
        // If endpoint clearly starts with accounts/, try the unified Business Profile first
        if (strpos($endpoint, 'accounts') === 0) {
            return array($business_profile, $business_info, $account_management);
        }

        // Generic order
        return array($business_profile, $business_info, $account_management);
    }
    
    /**
     * Get account information
     */
    public function get_accounts() {
        // Business Profile Account Management uses v1 accounts list
        return $this->make_request('accounts');
    }
    
    /**
     * Get locations for an account
     */
    public function get_locations($account_id) {
        return $this->make_request("accounts/{$account_id}/locations");
    }
    
    /**
     * Get reviews for a location
     */
    public function get_reviews($account_id, $location_id, $page_size = 50) {
        $params = array(
            'pageSize' => $page_size,
            'orderBy' => 'updateTime desc'
        );
        // Reviews are available via Business Profile API
        return $this->make_request("accounts/{$account_id}/locations/{$location_id}/reviews", 'GET', $params);
    }
    
    /**
     * Get specific review
     */
    public function get_review($account_id, $location_id, $review_id) {
        return $this->make_request("accounts/{$account_id}/locations/{$location_id}/reviews/{$review_id}");
    }
    
    /**
     * Reply to a review
     */
    public function reply_to_review($account_id, $location_id, $review_id, $comment) {
        $data = array(
            'comment' => $comment
        );
        
        return $this->make_request(
            "accounts/{$account_id}/locations/{$location_id}/reviews/{$review_id}/reply",
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
        // If connection works, ensure defaults are selected for convenience
        $this->ensure_default_location_selected();
        
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
            // Fallback: if API returns just the ID
            $location_id = $location_name;
        }

        update_option('grp_google_location_id', $location_id);

        return true;
    }
}