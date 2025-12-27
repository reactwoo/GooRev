# License Architecture

## Overview

**WordPress.org Compliant Architecture**: The free version works without requiring registration, making it compliant with WordPress.org guidelines.

- **Free users WITHOUT license**: Can use custom Google Cloud credentials (no registration required)
- **Free users WITH license**: Can use cloud server for easier setup (optional registration)
- **Pro users**: Must activate license and use cloud server
- **Enterprise users**: Must activate license, can use cloud server OR custom credentials

The license type determines:
1. Which features are available
2. Whether custom Google Cloud credentials can be used (Enterprise only, or free without license)
3. Whether direct API calls are allowed

## License Tiers

### Free Tier (No License Required - WordPress.org Compliant)
- **Activation**: ❌ Not required (plugin works without registration)
- **Cloud Server**: ❌ Cannot use (requires license)
- **Custom Credentials**: ✅ Required (must provide own Google Cloud Project)
- **Direct API Calls**: ✅ Allowed (using custom credentials)
- **JWT Token**: ❌ Not needed (direct API calls)
- **Features**: Limited (single location, basic styling)
- **Use Case**: Users who want to use the plugin without any registration (WordPress.org compliant)

### Free License (Optional Registration)
- **Activation**: ✅ Optional (free license key from license server)
- **Cloud Server**: ✅ Can use cloud server (easier setup)
- **Custom Credentials**: ✅ Can still use (if preferred)
- **Direct API Calls**: ✅ Allowed (if custom credentials provided)
- **JWT Token**: ✅ Required if using cloud server
- **Features**: Limited (single location, basic styling)
- **Use Case**: Users who want easy setup without Google Cloud Project configuration

### Pro License
- **Activation**: Required (paid license key)
- **Cloud Server**: ✅ **MUST** use cloud server for all API calls
- **Custom Credentials**: ❌ Not allowed
- **Direct API Calls**: ❌ Blocked
- **JWT Token**: ✅ Required (provided by license server)
- **Features**: Full Pro features (multiple locations, advanced styling, analytics, etc.)
- **Use Case**: Users who want full features but prefer managed cloud server

### Enterprise License
- **Activation**: Required (paid license key)
- **Cloud Server**: ✅ Optional (can use cloud server OR custom credentials)
- **Custom Credentials**: ✅ Allowed (can bypass cloud server)
- **Direct API Calls**: ✅ Allowed (if custom credentials provided)
- **JWT Token**: ✅ Required when using cloud server
- **Features**: Full Pro features + AI-powered review responses
- **Use Case**: Enterprise users who want full control with their own Google Cloud Project

## License Flow

### 1. Free License Activation
```
User → Plugin Settings → Enter Free License Key → License Server
License Server → Validates → Returns JWT Token + packageType: "goorev-free"
Plugin → Stores JWT Token → Enables Free Features → Routes all API calls through Cloud Server
```

### 3. Pro License Activation
```
User → Plugin Settings → Enter Pro License Key → License Server
License Server → Validates → Returns JWT Token + packageType: "goorev-pro"
Plugin → Stores JWT Token → Enables Pro Features → Routes all API calls through Cloud Server
```

### 4. Enterprise License Activation
```
User → Plugin Settings → Enter Enterprise License Key → License Server
License Server → Validates → Returns JWT Token + packageType: "goorev-enterprise"
Plugin → Stores JWT Token → Enables Enterprise Features
User → Can choose: Cloud Server OR Custom Credentials
```

## License Server Requirements

The license server must support:
1. **Free License Generation**: Create free licenses with `packageType: "goorev-free"`
2. **License Validation**: Validate all license types (free, pro, enterprise)
3. **JWT Token Generation**: Generate JWT tokens for all license types
4. **Package Type Detection**: Return correct `packageType` in activation response

## Plugin Behavior

### Without License
- ❌ Cannot connect to Google Business Profile
- ❌ Cannot use cloud server
- ❌ Cannot use custom credentials
- ✅ Can view settings page
- ✅ Can activate license

### With Free License
- ✅ Can connect to Google Business Profile (via cloud server)
- ✅ Can sync reviews (single location only)
- ✅ Can display reviews (basic features)
- ❌ Cannot use custom credentials
- ❌ Cannot access Pro features

### With Pro License
- ✅ Can connect to Google Business Profile (via cloud server)
- ✅ Can sync reviews (multiple locations)
- ✅ Can display reviews (all Pro features)
- ❌ Cannot use custom credentials
- ❌ Cannot access Enterprise AI features

### With Enterprise License
- ✅ Can connect to Google Business Profile (via cloud server OR custom credentials)
- ✅ Can sync reviews (multiple locations)
- ✅ Can display reviews (all Pro features)
- ✅ Can use AI-powered review responses
- ✅ Can use custom credentials (optional)

## Migration Path

### New Free Users (WordPress.org Compliant)
1. User installs plugin
2. User can choose:
   - **Option A**: Enter Google Cloud credentials (no registration, works immediately)
   - **Option B**: Activate free license (easier setup, cloud server access)
3. Plugin works with either option
4. Limited to free features (single location, basic styling)

### Existing Free Users (With License)
1. User already has free license activated
2. Can continue using cloud server
3. Can switch to custom credentials if preferred
4. Still limited to free features

### Benefits of This Architecture
1. **WordPress.org Compliant**: Free version works without registration
2. **User Choice**: Users can choose setup method (custom credentials OR free license)
3. **Easy Onboarding**: Optional free license for easier setup
4. **Clear Upgrade Path**: Free → Pro → Enterprise
5. **Feature Gating**: License type determines available features
6. **Security**: JWT tokens required for cloud server access
7. **Flexibility**: Enterprise users get maximum control

