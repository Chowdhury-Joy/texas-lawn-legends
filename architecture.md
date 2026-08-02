# Architecture Overview
Last Updated: 2026-08-02T00:07:05+06:00

## Overview

Texas Lawn Legends is a Laravel-based “Systematizing Growth” platform for home-service businesses. One codebase powers a CMS-editable marketing site (page builder), a multi-step lead/estimate funnel, token-gated client project dashboards, a monthly-code member portal, and a Filament admin panel for day-to-day operations. The product ships in three tiers (**Product Parts**) and can be skinned per industry via **Niche Packs** (lawn, cleaning, roofing, pressure washing, window cleaning, gutters, fence/deck, pest), with a public `/demo` hub for sales pitches.

## Getting Started

**Prerequisites:** PHP 8.3+, Composer, Node.js, npm

**Install & run locally:**

```bash
composer install
npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build          # or `npm run dev` while editing frontend
php artisan serve
```

**One-command setup:** `composer run setup`

**Full dev stack (server + queue + logs + Vite):** `composer run dev`

**Tests:** `composer run test`

**Seeded admin login:** `admin@admin.com` / `pass` at `/admin`

**Key environment variables (names only):**

| Variable | Purpose |
|---|---|
| `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL` | Core Laravel app config |
| `APP_NICHE` | Default industry pack (`lawn`, `cleaning`, `roofing`, `pressure`, `windows`, `gutters`, `fence`, `pest`) |
| `APP_DEMO_HUB` | Enables public `/demo` hub for pack switching |
| `APP_LICENSE_TRACK` | How the install was sold: `a` = Track A "Own it" (self-serve full data export), `b` = Track B "Rent it" (export by Getwebfield consent only). Defaults to `a` in local, `b` everywhere else |
| `DB_CONNECTION` (+ host/database/user/password) | Database (SQLite default in dev) |
| `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` | Session, cache, queue backends. `CACHE_STORE=file` is the default so heavy settings reads stay off the database; session and queue remain `database` |
| `MAIL_*` | Outbound mail (log driver in dev) |
| `AWS_*` | Optional S3 storage |
| `VITE_APP_NAME` | Frontend build label |

## Tech Stack

- **Backend:** PHP 8.3, Laravel 13.8
- **Admin UI:** Filament v4 (resources, custom settings pages, widgets, role/permission gating) with a Vite-compiled panel theme at `resources/css/filament/admin/theme.css` so custom admin Blade Tailwind utilities are included
- **Reactive UI:** Livewire 3.8 (`EstimatorWizard`, `PortalGate`, admin heading hook)
- **Frontend:** Tailwind CSS v4 (CSS-first via `@tailwindcss/vite`), Alpine.js (via Livewire), Vite 8 — public site via `resources/css/app.css`; Filament admin via the panel theme above
- **Design tokens:** `resources/css/design-tokens.css` (Getwebfield anti-slop system)
- **Activity logging:** Spatie Laravel Activity Log
- **Database:** SQLite (dev default); MySQL-compatible via Laravel config
- **Fonts:** Bunny CDN via `laravel-vite-plugin/fonts` (Hanken Grotesk, IBM Plex Mono defaults)

## Folder Structure

| Path | Responsibility |
|---|---|
| `app/Models/` | Eloquent models (Setting, Page, Lead, Project, Invoice, etc.) |
| `app/Http/Controllers/` | Public routes: pages, dashboard, invoices, proposals, demo hub |
| `app/Http/Middleware/` | `RequireProductPart` — gates public routes by product tier |
| `app/Livewire/` | `EstimatorWizard`, `PortalGate` |
| `app/Filament/` | Admin panel: Resources, Pages (settings), Widgets, Auth |
| `app/Services/` | `EstimatePricingEngine`, `BookingMatrix`, `OperationsNotifier`, `MonthlyCodeAuthenticator`, `DataExportService` |
| `app/Support/` | `PageBlocks`, `ProductFeatures`, `AccessPermissions`, `FilamentContentHeader`, `ColorContrast`, `Niche/*` packs |
| `app/Enums/` | `ProductPart`, `LicenseTrack`, `LeadStatus`, `ProjectStatus`, `UserRole`, etc. |
| `app/Events/` + `app/Listeners/` | `AddonOrdered`, `ProposalAccepted` → ops notifications |
| `app/Console/Commands/` | `leads:escalate-stalled`, `app:escalate-overdue-invoices`, `niche:load` |
| `app/helpers.php` | Global helpers: `setting()`, `setting_image()`, `public_url()`, `body_ink()`, `product_part()`, `license_track()`, `niche_label()`, `niche_favicon()`, etc. |
| `config/niche.php` | Registered industry packs and demo hub flag |
| `config/license.php` | Licence track for this install (drives self-serve data export) |
| `database/migrations/` | Schema (settings, pages, leads, projects, ops tables, permissions) |
| `database/seeders/Niches/` | Per-pack content seeders (pages, services, testimonials, sample project + ops demo data) |
| `database/seeders/assets/progress-photos/` | Compressed Unsplash JPEGs copied into public storage when seeding progress photos |
| `public/images/favicons/` | One SVG browser-tab icon per industry pack (`{niche}.svg`), served as the favicon fallback when branding has no upload |
| `resources/views/blocks/` | Page-builder block Blade partials (hero, FAQ, gallery, etc.) |
| `resources/views/layouts/` | Public site shell (SEO, fonts, design tokens) |
| `resources/css/` | `app.css` + `design-tokens.css` |
| `routes/web.php` | Public routes, product-part gates, CMS catch-all |
| `routes/console.php` | Scheduler: stalled leads (every minute), overdue invoices (daily) |
| `docs/` | Internal pricing/proposal docs (not runtime) |
| `product-stages.md` | Living V1–V4 stage scoreboard (demo → client install → trial → SaaS); see `.cursor/rules/product-stages.mdc` |
| `suggestions.md` | Living backlog of unscheduled bugs, security hardening, and launch items — consult before demo/production work; see `.cursor/rules/suggestions-backlog.mdc` |

## Key Components

### Settings & CMS layer
Nearly all site copy, branding, pricing math, and ops config lives in the `settings` table (key/value, typed). Global helpers `setting()` and `setting_image()` resolve values via `App\Models\Setting`, which caches each key in two layers: a per-request static memo and the persistent cache store. Cached payloads are shaped `['hit' => bool, 'value' => mixed]` so a missing key is cacheable and stays distinguishable from a null value. `Setting::set()` / `Setting::forget()` bust both layers; `Setting::flushRequestCache()` clears the memo (called in `TestCase::setUp()`). Filament settings pages (`ManageBranding`, `ManageSeo`, `ManageContact`, `ManageHomepage`, `ManagePricing`, `ManageOperations`, `ManageProductParts`, `ManageIndustryPacks`) edit these keys through forms. Settings form shells (`settings-form.blade.php`, `manage-homepage.blade.php`) use the full content width; page-builder Create/Edit keep their own `max-w-5xl`. A generic `SettingResource` is the escape hatch for raw keys.

Brand color tokens flow through `App\Support\ColorContrast`: primary/accent fills get auto near-black/near-white label ink (`contrast_ink()` / `accent_ink()`), while structural slate (`color_slate` → `--color-slate-800`) is clamped with `body_ink()` so light CMS picks cannot wash out body/caption text on white.

### Site version / admin live reload
`App\Support\SiteVersion` owns the `site_version` cache value that authenticated pages poll (every 10s via `/api/site-version`) to auto-reload after content changes. The `TriggersSiteReload` trait bumps it on save/delete for `Setting`, `Page`, `Service`, `Testimonial`, `Project`, and `ProgressPhoto`. Bulk operations wrap in `SiteVersion::withoutBumping()` and call `bumpNow()` once at the end — `NicheLoader` restore uses this so a reseed triggers one reload, not one per row.

### Page builder (Part 1 — Website + CMS)
The `pages` table stores block JSON. `App\Support\PageBlocks` defines block types, Filament form schemas, and labels. `App\Support\ReservedPageSlugs` lists URL segments reserved by static routes so CMS slugs cannot collide. `PageController` renders `resources/views/pages/show.blade.php`, which includes matching `resources/views/blocks/*` partials. Homepage is the page where `is_home = true`; unpublished homepage records 404 (fresh installs without a row still render an empty home).

### Product Parts (feature packaging)
Three tiers controlled by `product_part` setting:
1. **Website + CMS** — pages, services, testimonials, branding
2. **+ Booking** — `/estimate`, leads, pricing settings
3. **+ Ops** — projects, crews, invoices, proposals, equipment, portal, dashboard

`App\Support\ProductFeatures` maps admin resources and public routes to minimum part. `RequireProductPart` middleware gates public routes; Filament resources use `canAccess()` + `ProductFeatures::allows()`.

### Niche Packs (industry skin)
`config/niche.php` registers eight packs: `LawnPack`, `CleaningPack`, `RoofingPack`, `PressurePack`, `WindowsPack`, `GuttersPack`, `FencePack`, `PestPack`. `NicheResolver` reads `APP_NICHE` or `active_niche` setting. `NicheLoader` restores a niche model home on the sales demo install: wipes pitch-mutable ops/CMS/catalog data, clears upload branding keys plus `color_slate`, re-applies pack settings (including `estimate_teaser_*` defaults), reseeds content + access codes, sets `demo_mode`. Blocked when `APP_DEMO_HUB=false`. Public `/demo` hub (`DemoHubController`) and Admin → Industry Packs expose Load / Restore model home. `niche_label()` provides industry vocabulary in views; `niche_favicon()` resolves the browser-tab icon.

Each pack's `SampleProjectSeeder` seeds a demo booked lead, project, and milestones, calls `seedDemoProgressPhotos()` (compressed Unsplash assets from `database/seeders/assets/progress-photos` → `storage/app/public/progress-photos`), then calls the shared `Database\Seeders\Niches\Concerns\SeedsDemoOps` trait to stock admin Operations: one crew (assigned to the sample project), a sent proposal, a sent invoice with line items, two equipment rows, two or three time entries, funnel filler leads (3 Partial / 3 Qualified / 3 Contacted / 1 Lost, staggered over 7 days), and three demo staff users (Sales, Operations, Bookkeeper). Proposal and invoice amounts are derived from the project's `contract_value`; time entries feed `labor_cost` and a per-niche `material_share` sets `material_cost`, so the projects table shows a believable profit margin.

### Estimator funnel (Part 2)
`App\Livewire\EstimatorWizard` — 4-step wizard on `/estimate`. Captures partial leads on every step advance via `persistLead()` (includes `referred_by_code` from `?ref=`). `EstimatePricingEngine` computes low/high range from sqft, neighborhood, complexity, and service multipliers. `BookingMatrix` builds available slots and exposes `isOfferedSlot()`; `book()` validates against the grid, then books inside a transaction with `lockForUpdate()` to prevent double-booking.

The homepage Instant Valuation popup (Alpine in `layouts/app.blade.php`) is a separate teaser: it uses `estimate_teaser_low_per_unit`, `estimate_teaser_high_per_unit`, and `estimate_teaser_maintain_multiplier` from Admin → Estimator & Pricing — not the full pricing engine.

### Client dashboard (Part 3)
`/dashboard/{hash}` — auth via `Project.unique_dashboard_hash` (no login). Shows milestones and progress photos grouped by step. Photo `<img>` URLs go through `public_url()` (root-relative `/storage/...`) so they keep working when `APP_URL`'s host/port differs from the request.

Page order is deliberate and reads as one status narrative: header card (title, status, overall progress bar, horizontal step track — the bar and track are one uninterrupted block), then a "Happening now / Then" callout naming the current and next step, then the timeline, then the referral banner, then invoices. The current step is the first `in_progress` milestone, falling back to the first `pending` one; it is enlarged and ringed in both the track and the timeline. The timeline draws its vertical rail per row (node plus connecting line inside each row) rather than as one absolute element, so steps with wildly different card heights cannot break it — and the container carries no `space-y`, since sibling margins would cut gaps into the rail. Completed steps show `Milestone.completed_at`, which `Milestone::booted()` stamps on the transition to Completed and clears when a step is reopened; an explicitly supplied date wins, which is how the niche seeders stagger demo dates off `Project.started_at`. The timeline heading comes from the pack's `timeline_heading` label (Build / Service / Treatment Timeline), not a literal string.

### Member portal (Part 3)
`/portal` — `PortalGate` Livewire component. `MonthlyCodeAuthenticator` validates month-scoped access codes. Unlocked state shows add-on grid; orders dispatch `AddonOrdered` event.

### Proposals & invoices (Part 3)
Token-based public URLs: `/proposals/{token}`, `/invoices/{token}`. Accept/decline flows on proposals trigger `ProposalAccepted` → ops notification. Decline is blocked once a proposal is accepted. Proposal image-showcase blocks use `public_url()` the same way as the client dashboard.

The admin invoice list (`/admin/invoices`) is date-first: `ListInvoices::getTabs()` offers All / Today / This Week / This Month / Overdue presets (each badged with a live count), with **All** deliberately first so it is the default active tab — a day with no invoices would otherwise open on an empty table. `InvoicesTable` adds an `issue_date` range filter (`issued_from` / `issued_until` date pickers, inclusive via `whereDate`, cross-bounded so From cannot exceed To) laid out beside the existing status filter through `filtersFormColumns(3)`. There is no separate invoice "type" — status *is* the type. The table sorts by `issue_date` desc (not `created_at`) so row order matches the "Invoice Date" column being displayed and filtered; `issue_date` is already indexed.

### Admin panel (`/admin`)
Filament panel via `AdminPanelProvider`. Auto-discovers Resources, custom Pages, and Widgets (lead funnel, revenue chart, needs attention). Role-based access via `UserRole` enum + `AccessPermissions` registry + per-user permission overrides. Panel favicon comes from `niche_favicon()`. Global topbar is off: brand lives in the sidebar; each page opens with a Figma-style content header (`fi-content-shell-header` + `FilamentContentHeader`) — page title left, ← back on Create/Edit only, primary Save/Create actions right (Delete is danger-zoned in the form footer). Shell colors are mode-aware: light mode uses white sidebar/header on gray-50 content with dark ink; dark mode keeps the Lux Figma charcoal shell (`html.dark` CSS vars). That footer layout comes from the `HasPrimarySaveAndDangerDelete` concern, applied to all 16 `Edit*` resource pages: Save becomes the sole header action, and `getFormActions()` returns Cancel + an outlined danger `DeleteAction`. The delete action binds its record explicitly (`->record($this->getRecord())`) because the custom Page create/edit Blade views echo each form action directly instead of going through Filament's `Actions` schema component, which is what would normally inject it. `EditUser` narrows the action to hide Delete on your own account, reaching the base version through a trait alias (`baseDangerDeleteAction`) — `parent::` cannot reach a trait method. Admin theme loads Geist Sans + Geist Mono, applies Figma Text/sm, Text/xs, and mono title styles, and sets `--fi-shell-inline` padding (16 / 40 / 80px by breakpoint). Ungrouped table row actions are panel-wide `button()->outlined()` so View/Edit read as real controls. Widgets aggregate with grouped queries rather than per-day/per-status loops; charts and table widgets lazy-load, while the two stats overview widgets (`BusinessSnapshot`, `FinancialOverview`) render inline since they are above-the-fold and cost two queries each.

### Full data export (X-01 — Track A)
`App\Services\DataExportService` builds one ZIP the client admin downloads from **Admin → Data Export** (`ManageDataExport`): `data/<table>.csv` per business table (leads, proposals, invoices + items, projects, milestones, progress photos, crews, time entries, equipment, maintenance logs, pages, services, testimonials, addons, access codes, settings, users, permissions, activity log), `uploads/` mirroring the `public` disk (dotfiles like Laravel's `.gitignore` skipped, so the count matches what the owner actually uploaded), plus `manifest.json` (row counts, upload totals, site snapshot) and a plain-text `README.txt`. The archive is named and titled from `setting('site_name')`, falling back to `config('app.name')` — the same pattern the admin panel's `brandName()` uses. Rows stream via `lazy()` into a temp stream, so a long leads table never sits in memory as one string. `users.password` and `users.remember_token` are stripped — credentials, not records. Framework scratch tables (cache, jobs, sessions) are skipped.

Availability is a licence fact, not a CMS setting: `App\Enums\LicenseTrack` reads `config('license.track')` ← `APP_LICENSE_TRACK`, defaulting to Track B (locked) for anything but local, so a rented install cannot grant itself an export from the admin panel. `generate()` throws on Track B regardless of who calls it; the page is visible on both tracks but swaps the download action for a buy-out explanation. Access needs the Admin-only `settings.data_export` permission key. Each export is written to `storage/app/private/exports`, streamed with `deleteFileAfterSend()`, and recorded in the activity log.

**Scoped exports (resource list pages):** Most Operations and Site Content list pages expose an **Export CSV** header action via the `ExportsResourceData` concern. One table → a single CSV; related tables (invoices + `invoice_items`, equipment + `maintenance_logs`) → a small ZIP. The export uses `getTableQueryForExport()` so it respects the active tab, filters, search, and sort — staff get the rows they are looking at, not a silent full-table dump. Gated to Track A like the full export, but permission comes from the resource (`resource.leads`, `resource.invoices`, etc.): if you can open the list, you can export it. Users, Settings, and Access Codes deliberately have no scoped export — those stay at the full-export trust level.

### Operations alerting
`OperationsNotifier::dispatch()` — logs + optional webhook POST. Triggers:
- Event-driven: addon orders, proposal acceptance
- Scheduled: `leads:escalate-stalled` (every minute), `app:escalate-overdue-invoices` (daily)
- Demo mode mutes webhooks; demo leads tagged `is_demo`

### SEO
Dynamic `/robots.txt` and `/sitemap.xml` routes. `partials/seo.blade.php` builds meta, OG, JSON-LD from settings. Private pages set `$noindex`. Favicons resolve through the `niche_favicon()` helper: the uploaded `favicon` branding setting wins, otherwise the active pack's `public/images/favicons/{niche}.svg` (same helper feeds the Filament panel's `->favicon()`).

## Data Flow

```
Visitor → Public Blade/Livewire views
              ↓ reads
         setting() / niche_label() ← settings table + NichePack
              ↓
         Page blocks JSON → block partials

Estimator: form steps → persistLead() → leads table
         → EstimatePricingEngine → displayed range
         → booking slot → lead.status = booked

Sales win: staff creates Project in Filament → unique_dashboard_hash
         → milestones + progress_photos → client dashboard
         → milestone marked Completed → completed_at stamped → dated step on client timeline

Portal: monthly code → session unlock → AddonOrdered event → OperationsNotifier

Proposals: staff creates → public token URL → accept/decline → ProposalAccepted → ops alert

Invoice lookup: staff opens /admin/invoices → tab preset (All default / Today / week / month / Overdue)
         → optional issue_date From–To + status filter (AND-ed, each usable alone)
         → row actions: View/Print token URL, Mark Paid

Data export: admin opens /admin/manage-data-export (Track A) → confirm → DataExportService
         → CSV per table + uploads/ + manifest + README → ZIP streamed, then deleted
         → activity_log entry

Scoped export: staff on /admin/leads (etc.) → Export CSV → DataExportService::generateScoped()
         → filtered rows as CSV (or small ZIP when related tables ship together)
         → activity_log entry

Admin edits: Filament form → Setting::set() / model save → cache bust → site_version bump
```

**Core tables:** `settings`, `pages`, `services`, `testimonials`, `leads`, `projects`, `milestones`, `progress_photos`, `addons`, `access_codes`, `proposals`, `invoices`, `invoice_items`, `crews`, `equipment`, `maintenance_logs`, `time_entries`, `users`, `permissions`, `activity_log`, plus Laravel system tables (`cache`, `jobs`, `sessions`, `notifications`).

## Integrations

| Integration | Where | Notes |
|---|---|---|
| **Webhook (outbound)** | `operations_webhook_url` setting → `OperationsNotifier` | POST on alerts; muted in demo mode |
| **Email alerts** | `operations_alert_email` setting | Referenced by ops notifier |
| **Google Analytics / GTM** | Settings → injected in layout head | IDs from `ManageSeo` |
| **Bunny Fonts CDN** | Vite font plugin + branding font setting | Non-default fonts loaded at build/runtime |
| **Local/S3 storage** | Laravel filesystem | Image uploads (logo, progress photos, page blocks) |
| **Spatie Activity Log** | Lead/Project relation managers | Audit trail in admin |
| **Scheduler (cron)** | `routes/console.php` | Designed for cPanel single-cron hosting; no persistent queue worker required for escalation |
