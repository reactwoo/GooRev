# Cloud Server Requirements for Pro Features

## Overview
Pro users **MUST** route all Google Business Profile API calls through the cloud server. Direct API calls are blocked for Pro users.

## Current Implementation

### Plugin Side (GooRev)
- ✅ `is_using_api_server()` - Returns `true` for Pro users (cannot be bypassed)
- ✅ `make_request()` - Blocks Pro users from making direct API calls
- ✅ All public API methods route through cloud server for Pro users:
  - `get_accounts()` → `make_api_server_request('accounts')`
  - `get_locations()` → `make_api_server_request('locations', {account_id})`
  - `get_reviews()` → `make_api_server_request('reviews', {account_id, location_id, page_size})`
  - `get_review()` → `make_api_server_request('review', {account_id, location_id, review_id})`
  - `reply_to_review()` → `make_api_server_request('review/reply', {account_id, location_id, review_id, comment})`

### Cloud Server Side (react-cloud)
**REQUIRED ENDPOINTS** (to be implemented):

1. **GET/POST `/grp-api/v1/accounts`**
   - Returns list of Google Business Profile accounts
   - Requires: JWT token (from license)
   - Uses stored OAuth credentials to call Google API

2. **GET/POST `/grp-api/v1/locations`**
   - Parameters: `account_id`
   - Returns list of locations for the account
   - Requires: JWT token (from license)

3. **GET/POST `/grp-api/v1/reviews`**
   - Parameters: `account_id`, `location_id`, `page_size` (optional)
   - Returns list of reviews for the location
   - Requires: JWT token (from license)

4. **GET/POST `/grp-api/v1/review`**
   - Parameters: `account_id`, `location_id`, `review_id`
   - Returns specific review details
   - Requires: JWT token (from license)

5. **POST `/grp-api/v1/review/reply`**
   - Parameters: `account_id`, `location_id`, `review_id`, `comment`
   - Posts a reply to a review
   - Requires: JWT token (from license)

## Free vs Pro vs Enterprise Requirements

### Free Version (No License - WordPress.org Compliant)
- ✅ **Limited features** (basic review display, single location only)
- ❌ Cannot use cloud server (requires license)
- ✅ **MUST** use custom Google credentials
- ✅ Can make direct API calls (using custom credentials)
- ❌ **No license required** (works without registration)
- ❌ No JWT token needed (direct API calls)
- ❌ **Cannot access Pro features** (multiple locations, advanced styling, etc.)
- ❌ **Limited to first location only** (even if account has multiple locations)

### Free License (Optional Registration)
- ✅ **Limited features** (basic review display, single location only)
- ✅ **CAN** use cloud server for all API calls (easier setup)
- ✅ Can use custom Google credentials (if preferred)
- ✅ Can make direct API calls (if custom credentials provided)
- ✅ **Optional license** (free license for easier setup)
- ✅ JWT token required (if using cloud server)
- ❌ **Cannot access Pro features** (multiple locations, advanced styling, etc.)
- ❌ **Limited to first location only** (even if account has multiple locations)

### Pro Version
- ✅ **Full Pro features** (multiple locations, advanced styling, etc.)
- ✅ **MUST** use cloud server for all API calls
- ❌ Cannot use custom credentials to bypass cloud server
- ❌ Cannot make direct API calls (blocked in code)
- ✅ Requires valid Pro license
- ✅ Requires JWT token for all requests

### Enterprise Version
- ✅ **Full Pro features** (same as Pro)
- ✅ **CAN** use custom Google credentials to bypass cloud server
- ✅ **CAN** make direct API calls if custom credentials are provided
- ✅ Can also use cloud server (if no custom credentials)
- ✅ Requires valid Enterprise license
- ✅ Requires JWT token when using cloud server

## Security Notes
- All Pro API requests must include JWT token in `Authorization: Bearer <token>` header
- Cloud server validates JWT token using `licenseAuth` middleware
- Cloud server uses stored OAuth credentials (from environment) to make Google API calls
- Pro users' OAuth tokens are stored in WordPress database, but API calls go through cloud server

## Next Steps

### Priority 1: Core API Endpoints (Required for Pro/Enterprise) ✅ COMPLETED
1. ✅ **Implemented the 5 required endpoints in `react-cloud/routes/api.js`:**
   - ✅ `GET/POST /grp-api/v1/accounts` - List Google Business Profile accounts
   - ✅ `GET/POST /grp-api/v1/locations` - List locations for an account
   - ✅ `GET/POST /grp-api/v1/reviews` - List reviews for a location
   - ✅ `GET/POST /grp-api/v1/review` - Get specific review details
   - ✅ `POST /grp-api/v1/review/reply` - Reply to a review

2. ✅ **JWT Token Validation:**
   - ✅ Using existing `licenseAuth` middleware
   - ✅ JWT tokens validated for all endpoints
   - ✅ Token expiration handled

3. ✅ **OAuth Token Management:**
   - ✅ Tokens sent from plugin with each request
   - ✅ In-memory token storage implemented (`tokenStorage.js`)
   - ✅ Token expiration and cleanup handled
   - 📋 **TODO**: Replace with persistent storage (Redis/database)

4. ✅ **Feature Gating:**
   - ✅ Free licenses limited to first location only
   - ✅ Free licenses blocked from replying to reviews
   - ✅ License type checked via `packageType` from JWT

5. 📋 **Testing Required:**
   - Test each endpoint with Pro license activation
   - Test with Enterprise license (both cloud server and custom credentials)
   - Test JWT token validation and expiration handling
   - Test OAuth token refresh flow
   - Test free license limitations

### Priority 2: Free License Support
1. **Free License Endpoints:**
   - Ensure free license JWT tokens are accepted
   - Limit free license to single location only
   - Return appropriate error messages for Pro features

2. **Feature Gating:**
   - Check license type in endpoints (`packageType` from JWT)
   - Enforce location limits for free licenses
   - Block Pro features for free licenses

### Priority 3: Error Handling & Logging
1. **Comprehensive Error Handling:**
   - Handle Google API errors gracefully
   - Return user-friendly error messages
   - Log errors for debugging

2. **Rate Limiting:**
   - Implement rate limiting per license/user
   - Respect Google API quotas
   - Return appropriate error messages when limits exceeded

### Priority 4: Documentation
1. **API Documentation:**
   - Document all endpoints
   - Include request/response examples
   - Document error codes and messages

2. **Integration Guide:**
   - Update plugin documentation
   - Create developer guide for custom integrations

