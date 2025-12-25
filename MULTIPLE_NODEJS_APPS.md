# Running Multiple Node.js Apps on cPanel

## Yes, You Can Run Multiple Node.js Apps!

cPanel allows you to create **multiple Node.js applications** on the same account. Each app can:
- Run on different ports
- Use different Node.js versions
- Have separate subdomains/domains
- Use different environment variables
- Run independently

---

## Setup Strategy

### Option 1: Separate Subdomains (Recommended)

Create a subdomain for each Node.js app:

```
api1.yourdomain.com  → WooAliAI OAuth API
api2.yourdomain.com  → Google Reviews OAuth API  
api3.yourdomain.com  → Geo Elementor API
```

**Benefits:**
- ✅ Clean separation
- ✅ Easy to manage
- ✅ Independent SSL certificates
- ✅ Easy to identify in logs

### Option 2: Path-Based Routing

Use one subdomain with different paths:

```
api.yourdomain.com/wooaliai/  → WooAliAI API
api.yourdomain.com/goorev/    → Google Reviews API
api.yourdomain.com/geo/       → Geo Elementor API
```

**Benefits:**
- ✅ Single subdomain
- ✅ One SSL certificate
- ⚠️ Requires routing logic in main app

---

## Step-by-Step: Multiple Apps Setup

### App 1: WooAliAI OAuth API

1. **Create Application in cPanel:**
   - Application Root: `/home/username/wooaliai-api`
   - Application URL: `api1.yourdomain.com` (or subdomain of choice)
   - Node.js Version: 18.x or 20.x
   - Startup File: `server.js`
   - Port: Auto-assigned (e.g., 3000)

2. **Create Files:**
   ```
   ~/wooaliai-api/
   ├── package.json
   ├── server.js
   └── .env
   ```

3. **Configure:**
   - Set environment variables in cPanel
   - Install dependencies
   - Start application

### App 2: Google Reviews OAuth API

1. **Create Application in cPanel:**
   - Application Root: `/home/username/goorev-api`
   - Application URL: `api2.yourdomain.com` (or subdomain of choice)
   - Node.js Version: 18.x or 20.x
   - Startup File: `server.js`
   - Port: Auto-assigned (e.g., 3001)

2. **Create Files:**
   ```
   ~/goorev-api/
   ├── package.json
   ├── server.js
   └── .env
   ```

3. **Configure:**
   - Set environment variables in cPanel
   - Install dependencies
   - Start application

### App 3: Geo Elementor API

1. **Create Application in cPanel:**
   - Application Root: `/home/username/geo-api`
   - Application URL: `api3.yourdomain.com` (or subdomain of choice)
   - Node.js Version: 18.x or 20.x
   - Startup File: `server.js`
   - Port: Auto-assigned (e.g., 3002)

2. **Create Files:**
   ```
   ~/geo-api/
   ├── package.json
   ├── server.js
   └── .env
   ```

3. **Configure:**
   - Set environment variables in cPanel
   - Install dependencies
   - Start application

---

## Port Management

**Important:** cPanel automatically assigns ports to Node.js apps. You don't need to manually configure ports.

**How it works:**
- cPanel assigns a unique port to each app (3000, 3001, 3002, etc.)
- The subdomain/domain routes to the correct port automatically
- You can see assigned ports in cPanel Node.js Selector

**In your code:**
```javascript
// Use environment variable for port (cPanel sets this)
const PORT = process.env.PORT || 3000;
```

---

## Resource Considerations

### Memory Usage

Each Node.js app uses memory. Typical usage:
- **Lightweight OAuth API:** ~30-50 MB per app
- **Total for 3 apps:** ~90-150 MB

**Check your cPanel limits:**
- Shared hosting: Usually 256MB-512MB total
- VPS/Reseller: Depends on your plan
- Most hosts allow multiple apps if within memory limits

### CPU Usage

OAuth APIs are very lightweight:
- **Per request:** < 1% CPU
- **Idle:** ~0% CPU
- **3 apps running:** Minimal CPU usage

### Disk Space

Each app needs:
- **Node.js runtime:** ~50-100 MB (shared)
- **Dependencies:** ~20-50 MB per app
- **Code:** < 1 MB per app

**Total:** ~200-300 MB for 3 apps (very manageable)

---

## Best Practices

### 1. Organize by Project

```
~/nodejs-apps/
├── wooaliai-api/
├── goorev-api/
└── geo-api/
```

### 2. Use Environment Variables

Store sensitive data in environment variables (set in cPanel):
- API keys
- Database credentials
- OAuth secrets

### 3. Process Management

cPanel typically uses PM2 or similar. Each app runs as separate process:
- Independent restarts
- Separate logs
- Isolated crashes

### 4. Monitoring

Check app status in cPanel:
- Node.js Selector shows all apps
- View logs per app
- Restart individual apps

### 5. Naming Convention

Use clear names:
- `wooaliai-oauth-api`
- `goorev-oauth-api`
- `geo-elementor-api`

---

## Example: Google Reviews API Structure

```
~/goorev-api/
├── package.json
├── server.js
├── .env
└── .gitignore
```

**package.json:**
```json
{
  "name": "goorev-oauth-api",
  "version": "1.0.0",
  "main": "server.js",
  "scripts": {
    "start": "node server.js"
  },
  "dependencies": {
    "express": "^4.18.2",
    "dotenv": "^16.3.1",
    "axios": "^1.6.0"
  }
}
```

**server.js:**
```javascript
const express = require('express');
const axios = require('axios');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;

app.use(express.json());

// Your endpoints here
app.post('/grp-api/v1/oauth/auth-url', async (req, res) => {
  // Implementation
});

app.post('/grp-api/v1/oauth/token', async (req, res) => {
  // Implementation
});

app.post('/grp-api/v1/oauth/refresh', async (req, res) => {
  // Implementation
});

app.listen(PORT, () => {
  console.log(`Google Reviews OAuth API running on port ${PORT}`);
});
```

---

## Troubleshooting Multiple Apps

### App Won't Start

**Check:**
1. Port conflicts (rare with cPanel auto-assignment)
2. Memory limits
3. Environment variables set correctly
4. Dependencies installed

### Port Already in Use

**Solution:**
- cPanel should auto-assign different ports
- If conflict occurs, contact host support
- Or manually specify port in app settings

### Memory Issues

**If you hit memory limits:**
1. Check memory usage per app
2. Optimize code (remove unused dependencies)
3. Consider upgrading hosting plan
4. Or move one app to different server

### Logs

**View logs per app:**
- In cPanel Node.js Selector → View Logs
- Or via SSH: `pm2 logs app-name`

---

## Cost Impact

**Running 3 Node.js apps on cPanel:**
- **Additional Cost:** $0 (uses existing hosting)
- **Resource Usage:** Minimal (90-150 MB RAM)
- **CPU Usage:** Negligible for OAuth APIs

**vs. Running on EC2:**
- **Savings:** EC2 costs continue, but load reduced
- **Isolation:** OAuth services isolated from main EC2
- **Scalability:** Easy to scale individual apps

---

## Quick Checklist

- [ ] Create subdomain for each app (or use paths)
- [ ] Create Node.js application in cPanel for each
- [ ] Set up separate directories for each app
- [ ] Configure environment variables per app
- [ ] Install dependencies for each app
- [ ] Start all applications
- [ ] Test each endpoint
- [ ] Update WordPress plugins to point to correct URLs
- [ ] Monitor resource usage

---

## Summary

✅ **Yes, you can run multiple Node.js apps on cPanel**

**Recommended Setup:**
- 3 separate subdomains (api1, api2, api3)
- 3 separate Node.js applications
- Independent ports (auto-assigned)
- Shared hosting resources

**Benefits:**
- $0 additional cost
- Clean separation
- Easy management
- Isolated failures
- Independent scaling

**Resource Usage:**
- Memory: ~90-150 MB total
- CPU: Minimal
- Disk: ~200-300 MB total

This is a very cost-effective solution for running multiple lightweight OAuth APIs!

