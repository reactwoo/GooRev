# Addons Roadmap

This document outlines the planned addons for the Google Reviews Plugin and their implementation status.

## Implemented Addons

### ✅ WooCommerce Integration (Pro/Enterprise)
- **Status**: Fully implemented
- **Features**: Post-purchase review invites, automated email sending, coupon incentives, click tracking
- **Settings Page**: `google-reviews-woocommerce`

## Planned Addons

### 🔲 Review Inbox (Pro/Enterprise)
**Description**: Centralized review management inbox with filters, status tracking, and review organization.

**Features**:
- Unified inbox for all reviews
- Star rating filters (1-5 stars)
- Keyword search across reviews
- Internal status tracking: "Needs reply", "Resolved", "Escalate"
- Scheduled review sync (configurable intervals)
- New/unread review indicators
- Review assignment to team members

**Technical Requirements**:
- WordPress cron / Action Scheduler for sync
- Custom post type or custom table for review statuses
- AJAX-powered inbox interface
- Integration with existing `GRP_Reviews` class

**Settings Page**: `google-reviews-inbox`

---

### 🔲 Review Replies (Enterprise Only)
**Description**: Reply to Google reviews directly from WordPress with approval workflows and templates.

**Features**:
- Reply directly from WordPress admin
- Draft and approval workflow (optional)
- Reply templates/snippets library
- Multi-user permissions system
- Agency and client role support
- Bulk reply actions
- Reply history and versioning

**Technical Requirements**:
- Google Business Profile API `updateReply` endpoint
- Permission system integration
- Custom post type for draft replies
- Template management system
- Integration with Policy Toolkit for compliance

**Settings Page**: `google-reviews-replies`

**API Endpoints Used**:
- `accounts.locations.reviews.updateReply`
- `accounts.locations.reviews.deleteReply`

---

### 🔲 AI Assisted Replies (Enterprise + AI Credits)
**Description**: AI-powered reply suggestions with compliance guardrails and human-in-the-loop approval.

**Features**:
- AI-powered reply suggestions based on review text
- Custom business tone configuration
- Compliance guardrails (never mention incentives, request removal, or disclose personal data)
- Human-in-the-loop approval (required by default)
- AI credit management and usage tracking
- Suggestion history and feedback loop

**Technical Requirements**:
- AI service integration (OpenAI, Anthropic, etc.)
- AI credit tracking system
- Compliance rule engine
- Integration with Review Replies addon
- Optional: Fine-tuning for business-specific tone

**Settings Page**: `google-reviews-ai`

**Dependencies**: Review Replies addon

---

### 🔲 Alerts & Routing (Pro/Enterprise)
**Description**: Integrate with Slack, Teams, email, and helpdesk systems for automated alerts and ticket creation.

**Features**:
- Slack notifications (webhook integration)
- Microsoft Teams notifications
- Email alerts with customizable templates
- Helpdesk integration:
  - Zendesk ticket creation
  - Freshdesk ticket creation
- CRM integration:
  - HubSpot contact/note creation
- Custom alert rules:
  - Alert on 1-2 star reviews
  - Alert if no reply in 48 hours
  - Alert on keyword matches
- Alert scheduling (business hours)

**Technical Requirements**:
- Webhook support for Slack/Teams
- Email templating system
- Helpdesk API integrations
- CRM API integrations
- Alert rule engine
- Integration with Review Inbox for status tracking

**Settings Page**: `google-reviews-alerts`

---

### 🔲 Review Request Widgets (Free → Pro Upgrade)
**Description**: QR codes, review buttons, and on-site widgets to request reviews from customers.

**Features**:
- QR code generator per location
- "Leave us a review" button widgets
- Click tracking and analytics
- Conversion tracking (click → review submitted)
- Widget customization (colors, text, placement)
- Shortcode generator
- Elementor/Gutenberg widgets
- Mobile-optimized display

**Technical Requirements**:
- QR code generation library
- Click tracking endpoint
- Analytics integration
- Frontend widget rendering
- Shortcode implementation
- Integration with existing widget/block system

**Settings Page**: `google-reviews-widgets`

**Upgrade Path**: Free users get basic widgets, Pro users get analytics and advanced customization

---

### 🔲 Multi-Location / Agency Console (Enterprise Only)
**Description**: Manage multiple Google Business Profile locations with health metrics and centralized control.

**Features**:
- Manage multiple GBP locations for a brand/client
- Location health dashboard:
  - Last review date
  - Average rating trend over time
  - Response time metrics
  - Review volume trends
- Location grouping (by brand, region, etc.)
- Agency client management
- Location comparison tools
- Bulk actions across locations
- Location-specific settings

**Technical Requirements**:
- Multi-location data model
- Dashboard with charts/graphs
- Analytics aggregation
- Client/agency permission system
- Integration with all other addons (per-location)

**Settings Page**: `google-reviews-multi-location`

---

### 🔲 Policy & Safety Toolkit (Enterprise Only)
**Description**: Centralized response guidelines, compliance checks, and audit logs for review management.

**Features**:
- Centralized response guidelines
- Blocklist phrases (automatic flagging)
- Compliance checks before sending replies:
  - No incentive mentions
  - No removal requests
  - No personal data disclosure
  - Brand voice compliance
- Audit log:
  - Who replied, when, what
  - Approval workflow tracking
  - Compliance violations logged
- Agency compliance reporting
- Custom compliance rules

**Technical Requirements**:
- Rule engine for compliance checking
- Blocklist management system
- Audit log database table
- Integration with Review Replies addon
- Report generation system

**Settings Page**: `google-reviews-policy`

**Dependencies**: Review Replies addon (for reply compliance)

---

## Implementation Priority

1. **Review Inbox** - Core functionality for managing reviews
2. **Review Replies** - Essential for review management workflow
3. **Review Request Widgets** - Quick win, drives reviews
4. **Policy & Safety Toolkit** - Critical for compliance when replying
5. **Alerts & Routing** - Enhanced workflow automation
6. **Multi-Location Console** - Enterprise feature, lower priority
7. **AI Assisted Replies** - Advanced feature, requires AI infrastructure

## Addon Dependencies

```
Review Inbox (standalone)
├── Review Replies (depends on Review Inbox)
│   ├── AI Assisted Replies (depends on Review Replies)
│   └── Policy & Safety Toolkit (depends on Review Replies)
├── Alerts & Routing (depends on Review Inbox)
└── Multi-Location Console (works with all addons)
```

## Notes

- All addons integrate with the existing `GRP_API` class for Google Business Profile API calls
- Settings pages follow the pattern: `google-reviews-{addon-slug}`
- Enterprise-only addons require `requires_enterprise => true`
- Pro addons require `requires_pro => true` (works for both Pro and Enterprise)
- Free addons have `requires_pro => false` but may have upgrade paths

