# API Server Load Analysis & Recommendations

## Actual Load Assessment

### Google Reviews Plugin OAuth Endpoints

**Load Pattern:**
1. **`/oauth/auth-url`** - Called **once per user** during initial setup
   - Frequency: ~1-2 times per user lifetime
   - Processing: Simple URL building (microseconds)
   - No external API calls

2. **`/oauth/token`** - Called **once per user** during initial setup
   - Frequency: ~1-2 times per user lifetime
   - Processing: One HTTP request to Google OAuth API (~200-500ms)
   - Lightweight operation

3. **`/oauth/refresh`** - Called when access token expires
   - Frequency: ~1-2 times per hour per **active** user
   - Processing: One HTTP request to Google OAuth API (~200-500ms)
   - Only happens when user is actively using the plugin

### Load Calculation Example

**Scenario: 1,000 active users**
- Initial setup: 1,000 users × 2 calls = 2,000 calls (one-time)
- Token refresh: 1,000 users × 2 calls/hour = 2,000 calls/hour
- **Peak load: ~0.55 requests/second** (very low)

**Conclusion:** The OAuth proxy endpoints are **extremely lightweight** and generate minimal load.

---

## Cost & Architecture Options

### Option 1: Separate Node.js Service on cPanel ✅ **RECOMMENDED**

**Pros:**
- ✅ Very low cost (uses existing cPanel hosting)
- ✅ Isolates OAuth service from main EC2
- ✅ Easy to deploy and maintain
- ✅ Can handle thousands of requests easily
- ✅ Node.js is perfect for lightweight API services

**Cons:**
- ⚠️ Requires Node.js support on cPanel (most hosts support this)
- ⚠️ Need to set up PM2 or similar process manager
- ⚠️ May need to configure reverse proxy if cPanel doesn't allow direct Node.js ports

**Implementation:**
- Deploy simple Express.js API on cPanel
- Use PM2 to keep it running
- Point plugin to: `https://api.yourdomain.com/grp-api/v1/`

**Estimated Cost:** $0 (uses existing hosting)

---

### Option 2: Serverless (AWS Lambda / Vercel / Netlify) ✅ **BEST FOR SCALE**

**Pros:**
- ✅ Pay-per-use (extremely cheap for low volume)
- ✅ Auto-scaling
- ✅ No server management
- ✅ Built-in HTTPS
- ✅ Very fast cold starts for this use case

**Cons:**
- ⚠️ Requires serverless setup knowledge
- ⚠️ Cold starts (minimal impact for OAuth)
- ⚠️ Vendor lock-in

**Cost Estimate:**
- AWS Lambda: ~$0.20 per million requests (essentially free for your volume)
- Vercel/Netlify: Free tier covers thousands of requests

**Implementation:**
- Deploy as serverless function
- Point plugin to serverless endpoint

**Estimated Cost:** $0-5/month

---

### Option 3: Keep on EC2 (Current Setup)

**Pros:**
- ✅ Already running
- ✅ No additional setup needed
- ✅ Full control

**Cons:**
- ❌ Adds load to EC2 (even if minimal)
- ❌ EC2 costs continue regardless of usage
- ❌ If EC2 goes down, all 3 projects affected

**When to Use:**
- If EC2 is already running 24/7 for other services
- If you want everything in one place
- If load is truly minimal and not a concern

**Estimated Cost:** $0 additional (but EC2 costs continue)

---

### Option 4: Separate Micro EC2 Instance

**Pros:**
- ✅ Isolates OAuth service
- ✅ Can use t3.micro or t4g.nano (very cheap)
- ✅ Full control

**Cons:**
- ❌ Additional EC2 cost (~$3-5/month)
- ❌ Still need to manage server
- ❌ Overkill for this lightweight service

**Estimated Cost:** $3-5/month

---

## Recommendation

### **Best Option: Node.js on cPanel** 🏆

**Why:**
1. **Cost:** $0 additional (uses existing hosting)
2. **Performance:** More than sufficient for OAuth proxy
3. **Isolation:** Doesn't affect EC2 or other services
4. **Simplicity:** Easy to deploy and maintain
5. **Scalability:** Can easily handle thousands of users

**Setup Steps:**
1. Create Node.js app on cPanel
2. Deploy Express.js API with 3 endpoints
3. Use PM2 to keep it running
4. Configure subdomain (api.yourdomain.com)
5. Update plugin to point to new endpoint

---

## Load Comparison

| Service | Requests/Month (1K users) | Server Load | Cost |
|---------|---------------------------|-------------|------|
| Google Reviews OAuth | ~60,000 | Very Low | $0 |
| WooAliAI | ? | ? | ? |
| Geo Elementor | ? | ? | ? |
| **Total EC2** | ? | ? | $? |

**Recommendation:** Move OAuth services to cPanel Node.js to:
- Reduce EC2 load
- Save costs
- Isolate services
- Better scalability

---

## Implementation Guide for cPanel Node.js

See `CPANEL_NODEJS_SETUP.md` for detailed setup instructions.


