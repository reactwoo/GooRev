# cPanel Node.js Setup Guide

## Prerequisites

1. cPanel hosting with Node.js support (most modern hosts have this)
2. SSH access or cPanel Node.js Selector
3. Domain/subdomain for the API

> **Note:** You can run multiple Node.js apps on the same cPanel account! See `MULTIPLE_NODEJS_APPS.md` for setting up multiple OAuth APIs (WooAliAI, Google Reviews, Geo Elementor).

## Step 1: Create Node.js Application in cPanel

1. Log into cPanel
2. Find "Node.js Selector" or "Setup Node.js App"
3. Click "Create Application"
4. Configure:
   - **Node.js Version:** 18.x or 20.x (LTS)
   - **Application Root:** `/home/username/grp-api`
   - **Application URL:** `api.yourdomain.com` (or subdomain of choice)
   - **Application Startup File:** `server.js`
   - **Application Mode:** Production

## Step 2: Create Application Files

SSH into your server or use cPanel File Manager:

```bash
cd ~/grp-api
```

Create `package.json`:

```json
{
  "name": "grp-oauth-api",
  "version": "1.0.0",
  "description": "OAuth proxy for Google Reviews Plugin",
  "main": "server.js",
  "scripts": {
    "start": "node server.js"
  },
  "dependencies": {
    "express": "^4.18.2",
    "dotenv": "^16.3.1",
    "axios": "^1.6.0"
  },
  "engines": {
    "node": ">=18.0.0"
  }
}
```

Create `server.js`:

```javascript
const express = require('express');
const axios = require('axios');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(express.json());

// CORS - Allow requests from WordPress sites
app.use((req, res, next) => {
  res.header('Access-Control-Allow-Origin', '*');
  res.header('Access-Control-Allow-Methods', 'POST, OPTIONS');
  res.header('Access-Control-Allow-Headers', 'Content-Type, X-Site-URL, X-Plugin-Version');
  
  if (req.method === 'OPTIONS') {
    return res.sendStatus(200);
  }
  next();
});

// Get OAuth credentials from environment variables
const CLIENT_ID = process.env.GOOGLE_CLIENT_ID;
const CLIENT_SECRET = process.env.GOOGLE_CLIENT_SECRET;

if (!CLIENT_ID || !CLIENT_SECRET) {
  console.error('ERROR: GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET must be set in environment variables');
  process.exit(1);
}

// Health check endpoint
app.get('/health', (req, res) => {
  res.json({ status: 'ok', service: 'grp-oauth-api' });
});

// 1. Get OAuth Authorization URL
app.post('/grp-api/v1/oauth/auth-url', async (req, res) => {
  try {
    const { redirect_uri, state } = req.body;

    if (!redirect_uri || !state) {
      return res.status(400).json({ 
        message: 'Missing required parameters: redirect_uri, state' 
      });
    }

    // Build Google OAuth URL
    const params = new URLSearchParams({
      client_id: CLIENT_ID,
      redirect_uri: redirect_uri,
      scope: 'https://www.googleapis.com/auth/business.manage',
      response_type: 'code',
      access_type: 'offline',
      prompt: 'consent',
      state: state
    });

    const authUrl = `https://accounts.google.com/o/oauth2/v2/auth?${params.toString()}`;

    res.json({ auth_url: authUrl });
  } catch (error) {
    console.error('Error generating auth URL:', error);
    res.status(500).json({ message: 'Internal server error' });
  }
});

// 2. Exchange Authorization Code for Tokens
app.post('/grp-api/v1/oauth/token', async (req, res) => {
  try {
    const { code, redirect_uri } = req.body;

    if (!code || !redirect_uri) {
      return res.status(400).json({ 
        message: 'Missing required parameters: code, redirect_uri' 
      });
    }

    // Exchange code with Google
    const response = await axios.post('https://oauth2.googleapis.com/token', {
      client_id: CLIENT_ID,
      client_secret: CLIENT_SECRET,
      code: code,
      grant_type: 'authorization_code',
      redirect_uri: redirect_uri
    }, {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    });

    res.json(response.data);
  } catch (error) {
    console.error('Error exchanging token:', error.response?.data || error.message);
    
    const errorMessage = error.response?.data?.error_description 
      || error.response?.data?.error 
      || 'Failed to exchange authorization code';
    
    res.status(400).json({ message: errorMessage });
  }
});

// 3. Refresh Access Token
app.post('/grp-api/v1/oauth/refresh', async (req, res) => {
  try {
    const { refresh_token } = req.body;

    if (!refresh_token) {
      return res.status(400).json({ 
        message: 'Missing required parameter: refresh_token' 
      });
    }

    // Refresh token with Google
    const response = await axios.post('https://oauth2.googleapis.com/token', {
      client_id: CLIENT_ID,
      client_secret: CLIENT_SECRET,
      refresh_token: refresh_token,
      grant_type: 'refresh_token'
    }, {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    });

    res.json(response.data);
  } catch (error) {
    console.error('Error refreshing token:', error.response?.data || error.message);
    
    const errorMessage = error.response?.data?.error_description 
      || error.response?.data?.error 
      || 'Failed to refresh token';
    
    res.status(400).json({ message: errorMessage });
  }
});

// 404 handler
app.use((req, res) => {
  res.status(404).json({ message: 'Endpoint not found' });
});

// Error handler
app.use((err, req, res, next) => {
  console.error('Unhandled error:', err);
  res.status(500).json({ message: 'Internal server error' });
});

// Start server
app.listen(PORT, () => {
  console.log(`GRP OAuth API running on port ${PORT}`);
});
```

Create `.env` file:

```env
GOOGLE_CLIENT_ID=your-google-client-id-here
GOOGLE_CLIENT_SECRET=your-google-client-secret-here
PORT=3000
```

**Important:** Add `.env` to `.gitignore` if using version control!

## Step 3: Install Dependencies

In cPanel Node.js Selector:
1. Click "Run NPM Install"
2. Or via SSH: `cd ~/grp-api && npm install`

## Step 4: Set Environment Variables

In cPanel Node.js Selector:
1. Find your application
2. Click "Edit" or "Environment Variables"
3. Add:
   - `GOOGLE_CLIENT_ID` = your Google OAuth Client ID
   - `GOOGLE_CLIENT_SECRET` = your Google OAuth Client Secret
   - `PORT` = 3000 (or port assigned by cPanel)

## Step 5: Start Application

In cPanel Node.js Selector:
1. Click "Start App"
2. Verify it's running (status should show "Running")

## Step 6: Configure Domain/Subdomain

1. In cPanel, go to "Subdomains"
2. Create subdomain: `api.yourdomain.com`
3. Point to: `/home/username/grp-api`
4. Or configure reverse proxy in cPanel

## Step 7: Test Endpoints

Test the health endpoint:
```bash
curl https://api.yourdomain.com/health
```

Should return: `{"status":"ok","service":"grp-oauth-api"}`

## Step 8: Update Plugin Configuration

In WordPress, add to `wp-config.php`:

```php
define('GRP_API_SERVER_URL', 'https://api.yourdomain.com/grp-api/v1/');
```

Or use filter in theme/plugin:

```php
add_filter('grp_api_server_url', function() {
    return 'https://api.yourdomain.com/grp-api/v1/';
});
```

## Step 9: Monitor & Maintain

### Keep Process Running

Most cPanel Node.js setups use PM2 or similar. Check:
- Application status in cPanel
- Logs for errors
- Restart if needed

### Logs

Check logs in cPanel or via SSH:
```bash
cd ~/grp-api
pm2 logs
# or
tail -f ~/logs/grp-api.log
```

### Updates

To update:
1. Stop app in cPanel
2. Update code
3. Run `npm install` if dependencies changed
4. Start app

## Security Best Practices

1. **Environment Variables:** Never commit `.env` to version control
2. **HTTPS:** Ensure SSL certificate is installed for subdomain
3. **Rate Limiting:** Consider adding rate limiting (express-rate-limit)
4. **IP Whitelisting:** Optional - restrict to known WordPress sites
5. **Logging:** Monitor for suspicious activity

## Optional: Add Rate Limiting

Install: `npm install express-rate-limit`

Add to `server.js`:

```javascript
const rateLimit = require('express-rate-limit');

const limiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 100 // limit each IP to 100 requests per windowMs
});

app.use('/grp-api/v1/', limiter);
```

## Troubleshooting

**App won't start:**
- Check Node.js version compatibility
- Verify environment variables are set
- Check logs for errors

**502 Bad Gateway:**
- Verify app is running
- Check port configuration
- Verify reverse proxy settings

**CORS errors:**
- Check CORS headers in code
- Verify request origin

**Token exchange fails:**
- Verify Google credentials are correct
- Check redirect URI matches Google Console
- Verify Google APIs are enabled

## Cost Savings

**Before:** OAuth load on EC2 (shared with other services)
**After:** OAuth on cPanel Node.js (uses existing hosting)
**Savings:** $0 additional cost, reduced EC2 load

---

**Estimated Setup Time:** 30-60 minutes
**Maintenance:** Minimal (check logs occasionally)

