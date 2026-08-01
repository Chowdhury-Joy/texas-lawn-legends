# Architecture Overview
Last Updated: 2026-08-01T18:04:11+0600

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
| `app/Services/` | `EstimatePricingEngine`, `BookingMatrix`, `OperationsNotifier`, `MonthlyCodeAuthenticator` |
| `app/Support/` | `PageBlocks`, `ProductFeatures`, `AccessPermissions`, `FilamentContentHeader`, `Niche/*` packs |
| `app/Enums/` | `ProductPart`, `LeadStatus`, `ProjectStatus`, `UserRole`, etc. |
| `app/Events/` + `app/Listeners/` | `AddonOrdered`, `ProposalAccepted` → ops notifications |
| `app/Console/Commands/` | `leads:escalate-stalled`, `app:escalate-overdue-invoices`, `niche:load` |
| `app/helpers.php` | Global helpers: `setting()`, `product_part()`, `niche_label()`, etc. |
| `config/niche.php` | Registered industry packs and demo hub flag |
| `database/migrations/` | Schema (settings, pages, leads, projects, ops tables, permissions) |
| `database/seeders/Niches/` | Per-pack content seeders (pages, services, testimonials, sample project + ops demo data) |
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
Nearly all site copy, branding, pricing math, and ops config lives in the `settings` table (key/value, typed). Global helpers `setting()` and `setting_image()` resolve values via `App\Models\Setting`, which caches each key in two layers: a per-request static memo and the persistent cache store. Cached payloads are shaped `['hit' => bool, 'value' => mixed]` so a missing key is cacheable and stays distinguishable from a null value. `Setting::set()` / `Setting::forget()` bust both layers; `Setting::flushRequestCache()` clears the memo (called in `TestCase::setUp()`). Filament settings pages (`ManageBranding`, `ManageSeo`, `ManageContact`, `ManageHomepage`, `ManagePricing`, `ManageOperations`, `ManageProductParts`, `ManageIndustryPacks`) edit these keys through forms. A generic `SettingResource` is the escape hatch for raw keys.

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
`config/niche.php` registers eight packs: `LawnPack`, `CleaningPack`, `RoofingPack`, `PressurePack`, `WindowsPack`, `GuttersPack`, `FencePack`, `PestPack`. `NicheResolver` reads `APP_NICHE` or `active_niche` setting. `NicheLoader` restores a niche model home on the sales demo install: wipes pitch-mutable ops/CMS/catalog data, clears upload branding keys, re-applies pack settings, reseeds content + access codes, sets `demo_mode`. Blocked when `APP_DEMO_HUB=false`. Public `/demo` hub (`DemoHubController`) and Admin → Industry Packs expose Load / Restore model home. `niche_label()` provides industry vocabulary in views.

Each pack's `SampleProjectSeeder` seeds a demo lead, project, milestones, and photos, then calls the shared `Database\Seeders\Niches\Concerns\SeedsDemoOps` trait to stock admin Operations: one crew (assigned to the sample project), a sent proposal, a sent invoice with line items, two equipment rows, and two or three time entries. Proposal and invoice amounts are derived from the project's `contract_value`; time entries feed `labor_cost` and a per-niche `material_share` sets `material_cost`, so the projects table shows a believable profit margin.

### Estimator funnel (Part 2)
`App\Livewire\EstimatorWizard` — 4-step wizard on `/estimate`. Captures partial leads on every step advance via `persistLead()` (includes `referred_by_code` from `?ref=`). `EstimatePricingEngine` computes low/high range from sqft, neighborhood, complexity, and service multipliers. `BookingMatrix` builds available slots and exposes `isOfferedSlot()`; `book()` validates against the grid, then books inside a transaction with `lockForUpdate()` to prevent double-booking.

### Client dashboard (Part 3)
`/dashboard/{hash}` — auth via `Project.unique_dashboard_hash` (no login). Shows milestones and progress photos grouped by step.

### Member portal (Part 3)
`/portal` — `PortalGate` Livewire component. `MonthlyCodeAuthenticator` validates month-scoped access codes. Unlocked state shows add-on grid; orders dispatch `AddonOrdered` event.

### Proposals & invoices (Part 3)
Token-based public URLs: `/proposals/{token}`, `/invoices/{token}`. Accept/decline flows on proposals trigger `ProposalAccepted` → ops notification. Decline is blocked once a proposal is accepted.

### Admin panel (`/admin`)
Filament panel via `AdminPanelProvider`. Auto-discovers Resources, custom Pages, and Widgets (lead funnel, revenue chart, needs attention). Role-based access via `UserRole` enum + `AccessPermissions` registry + per-user permission overrides. Global topbar is off: brand lives in the sidebar; each page opens with a Figma-style dark content header (`fi-content-shell-header` + `FilamentContentHeader`) — page title left, ← back on Create/Edit only, primary Save/Create actions right (Delete is danger-zoned in the form footer). Admin theme loads Geist Sans + Geist Mono and applies Figma Text/sm, Text/xs, and mono title styles. Widgets aggregate with grouped queries rather than per-day/per-status loops; charts and table widgets lazy-load, while the two stats overview widgets (`BusinessSnapshot`, `FinancialOverview`) render inline since they are above-the-fold and cost two queries each.

### Operations alerting
`OperationsNotifier::dispatch()` — logs + optional webhook POST. Triggers:
- Event-driven: addon orders, proposal acceptance
- Scheduled: `leads:escalate-stalled` (every minute), `app:escalate-overdue-invoices` (daily)
- Demo mode mutes webhooks; demo leads tagged `is_demo`

### SEO
Dynamic `/robots.txt` and `/sitemap.xml` routes. `partials/seo.blade.php` builds meta, OG, JSON-LD from settings. Private pages set `$noindex`.

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

Portal: monthly code → session unlock → AddonOrdered event → OperationsNotifier

Proposals: staff creates → public token URL → accept/decline → ProposalAccepted → ops alert

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
