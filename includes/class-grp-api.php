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
     * Google My Business API base URL
     */
    const API_BASE_URL = 'https://mybusiness.googleapis.com/v4/';
    
    /**
     * OAuth 2.0 endpoints
     */
    const OAUTH_AUTH_URL = 'https://accounts.google.com/o/oauth2/auth';
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
        
        $headers = array(
            'Authorization' => 'Bearer ' . $this->access_token,
            'Content-Type' => 'application/json'
        );
        
        $args = array(
            'headers' => $headers,
            'timeout' => 30
        );
        
        if ($data && $method === 'POST') {
            $args['body'] = json_encode($data);
        }
        
        $url = self::API_BASE_URL . ltrim($endpoint, '/');
        
        if ($method === 'GET' && $data) {
            $url .= '?' . http_build_query($data);
        }
        
        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        // If unauthorized, try to refresh token
        if ($status_code === 401) {
            if ($this->refresh_access_token()) {
                return $this->make_request($endpoint, $method, $data);
            }
            return new WP_Error('unauthorized', __('Invalid or expired token', 'google-reviews-plugin'));
        }
        
        if ($status_code >= 400) {
            return new WP_Error('api_error', sprintf(__('API Error: %s', 'google-reviews-plugin'), $body));
        }
        
        return json_decode($body, true);
    }
    
    /**
     * Get account information
     */
    public function get_accounts() {
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
        
        return true;
    }
}