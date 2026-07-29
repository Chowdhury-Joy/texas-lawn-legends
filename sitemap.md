# Site Map
Last Updated: 2026-07-30T00:26:00+06:00

## Full Site Map

Routes below reflect `routes/web.php` and the Filament admin panel. CMS pages (`/{slug}`) are database-driven; default niche seeders ship `services`, `portfolio`, `about`, and `privacy` in addition to the homepage.

### Public / Marketing

| Route | Purpose | Access |
|---|---|---|
| `/` | Homepage (page builder blocks from `pages` where `is_home = true`) | Public |
| `/{slug}` | CMS pages (e.g. `/services`, `/about`, `/portfolio`, `/privacy`) | Public (published only) |
| `/estimate` | 4-step estimator + booking wizard (`EstimatorWizard`) | Public · **Product Part 2+** |
| `/demo` | Sales demo hub — switch industry pack | Public · requires `APP_DEMO_HUB=true` or local env |
| `POST /demo/load` | Load a niche pack from demo hub | Public · demo hub only |
| `POST /demo/reset` | Reset current pack to clean demo state | Public · demo hub only |

### Client / Token-gated (no login)

| Route | Purpose | Access |
|---|---|---|
| `/dashboard/{hash}` | Client project progress (milestones + photos) | Public token URL · **Product Part 3+** |
| `/proposals/{token}` | View proposal | Public token URL · **Product Part 3+** |
| `POST /proposals/{token}/accept` | Accept proposal | Public token URL · **Product Part 3+** |
| `POST /proposals/{token}/decline` | Decline proposal | Public token URL · **Product Part 3+** |
| `/invoices/{invoice:token}` | View invoice | Public token URL · **Product Part 3+** |
| `/portal` | Member portal — monthly code unlock + add-on orders | Public · **Product Part 3+** |

### Utility / SEO

| Route | Purpose | Access |
|---|---|---|
| `/robots.txt` | Dynamic robots rules (respects `robots_index` setting) | Public |
| `/sitemap.xml` | Dynamic XML sitemap (homepage + gated routes + CMS pages) | Public |
| `/api/site-version` | Site version for admin live-reload polling | Public |

### Admin Panel (`/admin`)

| Area | Route pattern | Purpose | Access |
|---|---|---|---|
| Login | `/admin/login` | Filament auth | Public (login form) |
| Dashboard | `/admin` | Ops widgets (leads, revenue, needs attention) | Admin login |
| **Site Content** | `/admin/pages`, `/admin/services`, `/admin/testimonials`, `/admin/addons` | CMS pages, services, social proof, portal add-ons | Admin login + role/permission + Product Part |
| **Operations** | `/admin/leads`, `/admin/projects`, `/admin/milestones`, `/admin/progress-photos`, `/admin/proposals`, `/admin/invoices`, `/admin/crews`, `/admin/equipment`, `/admin/time-entries`, `/admin/access-codes`, `/admin/manage-schedule` | Leads through billing and field ops | Admin login + role/permission + **Product Part 2–3** (varies) |
| **Site Settings** | `/admin/manage-branding`, `/admin/manage-seo`, `/admin/manage-contact`, `/admin/manage-homepage`, `/admin/manage-pricing`, `/admin/manage-operations`, `/admin/manage-product-parts`, `/admin/manage-industry-packs` | Branding, SEO, contact, homepage legacy settings, pricing, ops alerts, product tier, niche packs | Admin login + permission |
| **Configuration** | `/admin/users`, `/admin/settings` | Staff accounts, raw settings escape hatch | Admin login (admin role) |

> **Product Part gating:** Part 1 = site + CMS only. Part 2 adds leads/estimate. Part 3 adds full ops (projects, portal, invoices, etc.). Sidebar items hide when the active part is lower.

---

## Customer Journeys

### Journey: Visitor → Booked lead (primary conversion)

**Goal:** Turn a website visitor into a scheduled estimate/consultation.

| Step | Page / action | Goal | CTA / next step |
|---|---|---|---|
| 1 | Homepage or CMS page (`/`, `/services`, etc.) | Understand offer, build trust | Hero or section CTA → `/estimate` (Part 2+) or contact |
| 2 | `/estimate` Step 1 | Select service scope + confirm service area | Continue → Step 2 |
| 3 | `/estimate` Step 2 | Enter sqft + complexity | Continue → pricing computed → Step 3 |
| 4 | `/estimate` Step 3 | Provide contact details | Continue → Step 4 |
| 5 | `/estimate` Step 4 | Review estimate range (or custom-project path) | Pick date/time slot → Confirm booking |
| 6 | Confirmation | Lead captured as `booked` with `scheduled_at` | Optional: return to site or await staff follow-up |

**Data touched:** `leads` (upserted from step 1 onward as `partial`, then `qualified`/`booked`), `services`, pricing settings.

**Abandonment capture:** Every "Continue" calls `persistLead()` so step-1 dropouts still land in `leads`.

---

### Journey: Existing client → Project dashboard

**Goal:** Let a paying client see job progress without logging in.

| Step | Page / action | Goal | CTA / next step |
|---|---|---|---|
| 1 | Staff sends link | Client opens unique URL | `/dashboard/{hash}` |
| 2 | Dashboard | View milestones, status, progress photos | Read-only — no login required |

**Data touched:** `projects`, `milestones`, `progress_photos` (read only on public side).

---

### Journey: Member → Portal add-on order

**Goal:** Let recurring members order add-on services between visits.

| Step | Page / action | Goal | CTA / next step |
|---|---|---|---|
| 1 | `/portal` (locked) | Enter monthly access code | Unlock |
| 2 | `/portal` (unlocked) | Browse available add-ons | Order add-on |
| 3 | Confirmation | Order logged; ops notified | Continue browsing or lock portal |

**Data touched:** `access_codes` (validate + increment usage), session unlock state, `addons`, `AddonOrdered` event → `OperationsNotifier`.

---

### Journey: Prospect → Proposal accept/decline

**Goal:** Close a deal via a shareable proposal link.

| Step | Page / action | Goal | CTA / next step |
|---|---|---|---|
| 1 | Staff sends proposal link | Prospect opens `/proposals/{token}` | Review line items / terms |
| 2 | Decision | Accept or decline | POST accept or decline |
| 3 | Outcome | Status updated; ops notified on accept | Staff follows up in admin |

**Data touched:** `proposals`, `ProposalAccepted` event → `OperationsNotifier`.

---

### Journey: Sales demo → Industry switch

**Goal:** Pitch different home-service niches from one install during a call.

| Step | Page / action | Goal | CTA / next step |
|---|---|---|---|
| 1 | `/demo` | Pick industry card | POST load pack → site reloads as model home |
| 2 | Public site walkthrough | Show hero, estimate, admin edit | Live demo |
| 3 | After call | Reset to clean state | `/demo` reset or Admin → Industry Packs → Reset |

**Data touched:** `settings` (`active_niche`, `demo_mode`), showcase tables wiped + reseeded, demo leads tagged `is_demo`.

---

## Admin/Staff Journeys

### Journey: Stalled lead → Escalation alert

**Trigger:** Lead is `qualified`, has no `scheduled_at`, and has exceeded `lead_escalation_minutes` since last update.

| Step | Who | Action | Data / systems |
|---|---|---|---|
| 1 | Scheduler | `leads:escalate-stalled` runs every minute | Reads `leads` |
| 2 | System | `OperationsNotifier` fires (log + webhook if configured) | Webhook, logs |
| 3 | System | Sets `leads.escalated_at` (no double-fire) | `leads` update |

**Success criteria:** Ops team notified once per stalled lead; lead marked escalated.

---

### Journey: New lead → Won project → Client dashboard

**Trigger:** Visitor completes estimator or staff creates lead manually.

| Step | Who | Action | Data / systems |
|---|---|---|---|
| 1 | System / visitor | Lead captured via `/estimate` or admin | `leads` |
| 2 | Sales / ops staff | Review lead in `/admin/leads`, qualify or book | `leads.status`, `scheduled_at` |
| 3 | Ops staff | Create `Project` linked to lead when job is won | `projects` (+ auto `unique_dashboard_hash`) |
| 4 | Ops staff | Add milestones + upload progress photos | `milestones`, `progress_photos` |
| 5 | Ops staff | Copy dashboard URL to client | Client visits `/dashboard/{hash}` |

**Success criteria:** Client sees accurate milestone timeline and photos; project status reflects reality.

---

### Journey: Proposal → Accepted job

**Trigger:** Staff creates proposal for a prospect.

| Step | Who | Action | Data / systems |
|---|---|---|---|
| 1 | Sales staff | Create proposal in `/admin/proposals` | `proposals` (+ public token) |
| 2 | Sales staff | Send `/proposals/{token}` link to prospect | Email/manual |
| 3 | Prospect | Accepts via public page | `proposals.status`, `ProposalAccepted` event |
| 4 | Ops | Notified; optionally create project from lead | `projects`, `OperationsNotifier` |

**Success criteria:** Proposal status = accepted; ops alerted; project can be created.

---

### Journey: Invoice → Payment follow-up

**Trigger:** Staff creates invoice; due date passes unpaid.

| Step | Who | Action | Data / systems |
|---|---|---|---|
| 1 | Bookkeeper | Create invoice in `/admin/invoices` | `invoices`, `invoice_items` |
| 2 | Bookkeeper | Send `/invoices/{token}` link to client | Email/manual |
| 3 | Scheduler | `app:escalate-overdue-invoices` runs daily | Reads overdue `invoices` |
| 4 | System | Ops notifier fires for overdue items | Webhook, logs |

**Success criteria:** Overdue invoices escalated once; client can view invoice via token URL.

---

### Journey: Portal add-on order → Ops action

**Trigger:** Member orders add-on from unlocked `/portal`.

| Step | Who | Action | Data / systems |
|---|---|---|---|
| 1 | Member | Enters valid monthly code, orders add-on | `access_codes`, session, `addons` |
| 2 | System | `AddonOrdered` event dispatched | Event bus |
| 3 | System | `NotifyOperationsOfAddonOrder` → `OperationsNotifier` | Webhook, logs (muted in demo mode) |
| 4 | Ops staff | Fulfills order offline | External scheduling/billing |

**Success criteria:** Ops receives alert with add-on title and client context; demo orders tagged and webhooks muted when `demo_mode` is on.

---

### Journey: Sales demo → Reset pack

**Trigger:** Demo call ends or next meeting needs clean model home.

| Step | Who | Action | Data / systems |
|---|---|---|---|
| 1 | Sales | POST `/demo/reset` or Admin → Industry Packs → Reset | `NicheLoader::reset()` |
| 2 | System | Wipes showcase content, reseeds active pack | `settings`, services, pages, testimonials, etc. |
| 3 | System | Sets `demo_mode = true` | `settings` |

**Success criteria:** Site returns to pristine model-home state for the active niche; no prospect-edited content remains.

---

### Journey: Change product tier (sales packaging)

**Trigger:** Client buys a lower or higher Product Part.

| Step | Who | Action | Data / systems |
|---|---|---|---|
| 1 | Admin | `/admin/manage-product-parts` → select Part 1/2/3 → Save | `settings.product_part` |
| 2 | System | Full page redirect rebuilds admin sidebar | Filament nav |
| 3 | System | Public routes gated by `RequireProductPart` middleware | `/estimate`, `/portal`, etc. appear or 404 |

**Success criteria:** Admin menu and public routes match the new tier immediately after save + redirect.
