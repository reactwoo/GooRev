# WordPress.org Compliance

## Overview

This plugin is designed to be compliant with WordPress.org guidelines, which require that free plugins work without requiring external registration or accounts.

## Architecture

### Free Version (No License Required)

The free version works **without requiring registration**:

- ✅ Users can enter their own Google Cloud credentials
- ✅ Plugin works with direct API calls to Google
- ✅ No external registration required
- ✅ Fully functional free version

**This makes the plugin WordPress.org compliant.**

### Free License (Optional Registration)

Users can optionally activate a free license for easier setup:

- ✅ Free license provides access to cloud server
- ✅ No Google Cloud Project setup required
- ✅ Easier OAuth flow
- ✅ Still limited to free features (single location, basic styling)

**This is optional** - users can still use the plugin without a license by providing their own credentials.

### Pro License (Required Registration)

Pro features require a paid license:

- ✅ Must activate Pro license
- ✅ Must use cloud server
- ✅ Full Pro features unlocked
- ✅ Standard freemium model (WordPress.org approved)

### Enterprise License (Required Registration)

Enterprise features require a paid license:

- ✅ Must activate Enterprise license
- ✅ Can use cloud server OR custom credentials
- ✅ Full Pro features + AI capabilities
- ✅ Maximum flexibility

## WordPress.org Guidelines Compliance

### ✅ Compliant Aspects

1. **Free Version Works Without Registration**
   - Users can use the plugin with their own Google Cloud credentials
   - No external account required
   - Fully functional free version

2. **Optional Registration for Enhanced Experience**
   - Free license is optional (for easier setup)
   - Users can choose: own credentials OR free license
   - No forced registration

3. **Clear Freemium Model**
   - Free version is fully functional
   - Pro/Enterprise require paid licenses
   - Clear feature differentiation

4. **No Hidden Costs**
   - Free version clearly limited to free features
   - Pro/Enterprise pricing clearly stated
   - No surprise requirements

### Implementation Details

#### For Free Users (No License)

```php
// User can enter custom credentials
// Plugin works with direct API calls
// No license check required
// WordPress.org compliant
```

#### For Free Users (With License)

```php
// User activates free license
// Gets JWT token
// Can use cloud server (easier setup)
// Still limited to free features
```

#### For Pro/Enterprise Users

```php
// User must activate paid license
// Gets JWT token
// Pro: Must use cloud server
// Enterprise: Can use cloud server OR custom credentials
```

## User Flow

### Free User (No Registration)

1. Install plugin
2. Enter Google Cloud credentials (Client ID, Client Secret)
3. Connect Google Business Profile
4. Use plugin (limited features)

### Free User (With Registration)

1. Install plugin
2. Activate free license (optional)
3. Connect Google Business Profile (via cloud server)
4. Use plugin (limited features, easier setup)

### Pro User

1. Install plugin
2. Purchase Pro license
3. Activate Pro license
4. Connect Google Business Profile (via cloud server)
5. Use plugin (full Pro features)

### Enterprise User

1. Install plugin
2. Purchase Enterprise license
3. Activate Enterprise license
4. Choose: Cloud server OR custom credentials
5. Connect Google Business Profile
6. Use plugin (full features + AI)

## Benefits of This Architecture

1. **WordPress.org Compliant**: Free version works without registration
2. **User Choice**: Users can choose their preferred setup method
3. **Easy Onboarding**: Optional free license for easier setup
4. **Clear Upgrade Path**: Free → Pro → Enterprise
5. **Flexibility**: Enterprise users get maximum control

## License Server Requirements

The license server must support:

1. **Free License Generation**: Create free licenses (optional for users)
2. **License Validation**: Validate all license types
3. **JWT Token Generation**: Generate tokens for all license types
4. **Package Type Detection**: Return correct `packageType` in response

## Summary

- ✅ **Free version works without registration** (WordPress.org compliant)
- ✅ **Optional free license** for easier setup (cloud server access)
- ✅ **Pro/Enterprise require paid licenses** (standard freemium)
- ✅ **Clear feature differentiation** between tiers
- ✅ **User choice** in setup method

This architecture ensures WordPress.org compliance while providing a clear upgrade path and flexible setup options.

