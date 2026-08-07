# Site Map
Last Updated: 2026-08-08T00:52:42+0600

## Full Site Map

Routes below reflect `routes/web.php` and the Filament admin panel. CMS pages (`/{slug}`) are database-driven; default niche seeders ship `services`, `portfolio`, `about`, and `privacy` in addition to the homepage.

### Public / Marketing

| Route | Purpose | Access |
|---|---|---|
| `/` | Getwebfield agency homepage when `APP_TRIAL_HOST=true`; otherwise niche CMS homepage | Public |
| `/agency` | Getwebfield agency marketing page (same as `/` on trial host) | Public · requires `APP_TRIAL_HOST=true` |
| `/{slug}` | CMS pages on **non-trial** installs only (e.g. `/services`, `/about`) | Public (published only) |
| `/estimate` | 4-step estimator + booking wizard (`EstimatorWizard`) | Public · **Product Part 2+** |
| `/demo` | Sales demo hub — switch industry pack | Public · requires `APP_DEMO_HUB=true`; blocked on trial hosts |
| `POST /demo/load` | Load a niche pack from demo hub | Public · demo hub only |
| `POST /demo/reset` | Reset current pack to clean demo state | Public · demo hub only |

### V3 Trial (requires `APP_TRIAL_HOST=true`)

| Route | Purpose | Access |
|---|---|---|
| `/trial/signup` | Email/password trial account form | Public · trial host |
| `POST /trial/signup` | Store signup in session → niche picker | Public · trial host |
| `/trial/niche` | Pick one industry skin | Public · needs signup session |
| `POST /trial/niche` | Provision isolated workspace → `/trial/{slug}/admin` | Public · trial host |
| `/auth/google` | Start Google OAuth (Socialite) | Public · needs `GOOGLE_CLIENT_*` |
| `/auth/google/callback` | Finish Google OAuth → niche picker | Public · Google callback |
| `/trial/{slug}` | Trial workspace public homepage | Public |
| `/trial/{slug}/estimate` | Estimator + booking | Public · Part 2+ |
| `/trial/{slug}/portal` | Member portal | Public · Part 3+ |
| `/trial/{slug}/dashboard/{hash}` | Client project dashboard | Public token · Part 3+ |
| `/trial/{slug}/proposals/{token}` | View proposal | Public token · Part 3+ |
| `/trial/{slug}/invoices/{token}` | View invoice | Public token · Part 3+ |
| `/trial/{slug}/{page}` | CMS pages for that workspace | Public (published only) |
| `/trial/{slug}/admin` | Filament admin for that workspace | Trial owner login |
| `/trial/{slug}/admin/login` | Filament login | Public (login form) |

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
| **Site Settings** | `/admin/manage-branding`, `/admin/manage-seo`, `/admin/manage-contact`, `/admin/manage-homepage`, `/admin/manage-pricing`, `/admin/manage-operations`, `/admin/manage-product-parts`, `/admin/manage-industry-packs`, `/admin/manage-data-export` | Branding, SEO, contact, homepage legacy settings, pricing, ops alerts, product tier, niche packs, full data export | Admin login + permission |
| **Configuration** | `/admin/users`, `/admin/settings` | Staff accounts, raw settings escape hatch | Admin login (admin role) |

> **Product Part gating:** Part 1 = site + CMS only. Part 2 adds leads/estimate. Part 3 adds full ops (projects, portal, invoices, etc.). Sidebar items hide when the active part is lower.

---

## Customer Journeys

### Journey: Prospect → 15-day trial (V3)

**Goal:** Let a home-service owner try Part 3 alone on Getwebfield hosting — multiple strangers can sign up in parallel.

| Step | Page / action | Goal | CTA / next step |
|---|---|---|---|
| 1 | Agency homepage (`/` or `/agency`) | Understand the product | **Start 15-day trial** → `/trial/signup` (or Google) |
| 2 | `/trial/signup` | Create account (email + visible password, or Google) | Continue → `/trial/niche` |
| 3 | `/trial/niche` | Pick one industry skin | Submit → provision + login → `/trial/{slug}/admin` |
| 4 | `/trial/{slug}` + `/trial/{slug}/admin` | Explore full product with demo banner | Use for up to 15 days |
| 5 | After day 15 (per workspace) | That workspace's admin login blocked; public demo banner remains | Convert = fresh Track A/B install later (no trial data migration) |

**Data touched:** `trial_workspaces`, session `trial_signup`, workspace-scoped `settings` + niche model-home tables via `NicheLoader::provisionWorkspace()`, `users` (trial owner Admin tagged with `trial_workspace_id`).

---

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
| 2 | Header card | See overall progress and the full step track in one block | Scroll to current step |
| 3 | "Happening now" callout | See which step is underway and what follows it | Scroll to timeline |
| 4 | Timeline | Walk the dated steps on a connected rail, open progress photos in a lightbox | Referral CTA below |
| 5 | Referral banner | Copy a personal referral link for a $100 credit | `/estimate?ref={code}` |
| 6 | Invoices | View or print any issued invoice | `/invoices/{token}` |

**Data touched:** `projects`, `milestones` (incl. `completed_at`), `progress_photos`, `invoices` (read only on public side).

**Note:** the referral banner sits below the timeline by design — placing it between the progress bar and the step track split the status narrative in half.

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
| 5 | Bookkeeper | Find invoices by date: tab preset (All / Today / This Week / This Month / Overdue) or Invoice Date From–To + status filter | Reads `invoices.issue_date`, `invoices.status` |
| 6 | Bookkeeper | Mark Paid from the row action once payment clears | Writes `invoices.status` |

**Success criteria:** Overdue invoices escalated once; client can view invoice via token URL; staff can retrieve any invoice by issue-date range, by status, or by both together.

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

---

### Journey: Export all business data (X-01, Track A)

**Trigger:** Owner wants a backup, is handing records to an accountant, or is moving off our hosting.

| Step | Who | Action | Data / systems |
|---|---|---|---|
| 1 | Admin | `/admin/manage-data-export` → read what the archive contains (row counts + upload totals) | `DataExportService::summary()` |
| 2 | Admin | **Download export (.zip)** → confirm modal | Needs `settings.data_export` + Track A |
| 3 | System | Builds `data/*.csv` + `uploads/` + `manifest.json` + `README.txt` | `DataExportService::generate()` |
| 4 | System | Streams the ZIP, deletes it, writes an activity-log entry | `storage/app/private/exports`, `activity_log` |

**Track B:** no Download button — the page explains that Getwebfield runs the export on request and that a buy-out to Track A makes it self-serve. `generate()` refuses server-side.

**Success criteria:** One download, openable in Excel/Sheets, with every uploaded file alongside the records that reference it.

---

### Journey: Export data from a list page (scoped, Track A)

**Trigger:** Bookkeeper wants this month's invoices; ops wants the filtered leads list; content editor wants a CSV of services.

| Step | Who | Action | Data / systems |
|---|---|---|---|
| 1 | Staff | Open a resource list (e.g. `/admin/invoices`) → narrow with tabs/filters/search | `getTableQueryForExport()` |
| 2 | Staff | **Export CSV** (or **Export (.zip)** when line items ride along) | Needs Track A + resource permission |
| 3 | System | Builds CSV or small ZIP for the filtered rows | `DataExportService::generateScoped()` |
| 4 | System | Streams the file, deletes it, writes an activity-log entry | `storage/app/private/exports`, `activity_log` |

**Track B:** button hidden (same licence gate as full export). Users, Settings, and Access Codes have no scoped export.
