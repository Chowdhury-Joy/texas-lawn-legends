# Suggestions Backlog
Last Updated: 2026-08-08T00:52:42+0600

> **Purpose:** Track known bugs, security hardening, and product improvements that are **not** decided or scheduled yet.  
> **Not the same as `decisions.md`** — nothing here is locked in. When an item is approved and implemented, move the outcome to `decisions.md` / `bug_history.md` and remove or mark it done here.

---

## How agents should use this file

1. **Before** proposing architecture, security, demo tooling, onboarding, or production-readiness work — scan this file.
2. **Flag** any item that overlaps the current task (e.g. “this touches demo reset — see Suggestions § Demo hub”).
3. **Do not** silently implement items here unless the user explicitly asks.
4. **Do not** contradict `decisions.md` — if this file conflicts with a logged decision, the decision wins; update this file instead.

---

## Current stage

- **Stage scoreboard:** See [`product-stages.md`](product-stages.md) (V1 demo → V2 parked → V3 trial → V4 SaaS). Keep that file current when stage work ships.
- **Environment:** Local prototype / V1 demo + V3 trial host (Step 2 isolation shipped). V2 client handoff **parked** until a paying client exists.
- **Implication:** Open public multi-signup on trial host is allowed; security items for V2 remain documented for later handoff.

---

## Product direction — demo hub & model-home restore

### What reset is actually for (2026-07-30, clarified)

Reset is a **snapshot restore of a niche's model home** on the Getwebfield sales install. It is **not** a client-facing feature and **not** a plain "wipe everything" button.

**The real workflow it serves:**

Each niche has a pre-built model home — logo, homepage copy, a couple of finished projects, one in-progress job with a crew scheduled — so the first screen a prospect sees never looks empty.

1. Meeting 1 (roofer A): you change the logo, add projects, add an employee/role during the pitch.
2. Before meeting 2 (roofer B): **Restore** that niche's model home so roofer B sees the clean showroom, not roofer A's edits.
3. Switch niche (plumber/cleaning) → load that pack → same restore behaviour per niche.

Without restore, four roofing calls stack four sets of demo projects and the showroom gets bloated.

**Naming:** think of this as **"Restore model home"** / snapshot restore. Same button as today's Reset, clearer intent.

### Who gets it

| Audience | Demo hub / restore |
|----------|--------------------|
| **Getwebfield sales install** | **Keep.** `/demo` hub + Restore (active niche) + Load pack (switch niche and restore its model home). |
| **Paying client site** | **Do not ship.** Their site is a real business, not a showroom — no public `/demo`, no restore. |
| **Non-SaaS (one deploy per client)** | New install, one-time pack seed at onboarding, `APP_DEMO_HUB=false`. |
| **SaaS (multi-tenant)** | New tenant gets a clean workspace at signup — provisioning, not a shared reset. |

**Keep at onboarding (once):** industry pack selection + default seed.  
**Drop or hide for clients:** `POST /demo/reset`, `POST /demo/load`, public `/demo` hub.

### Model-home restore (implemented 2026-07-30)

[`NicheLoader`](app/Support/Niche/NicheLoader.php) **Restore model home** now:

- Clears pitch-mutable ops data: leads, projects, milestones, progress photos, crews, equipment, maintenance logs, time entries, proposals, invoices (+ items), services, add-ons, testimonials, pages, access codes, activity log, notifications
- Removes pitch-added staff (keeps `admin@admin.com`)
- Clears uploaded branding keys (`logo_image`, `favicon`, `og_image`, `hero_media_image`) and pitch-era `color_slate`, then re-applies pack settings defaults
- Re-runs pack content seeders + `AccessCodesSeeder`
- Blocked when `APP_DEMO_HUB=false` (except in unit tests)

UI labels: **Restore model home** on `/demo` and Admin → Industry Packs.

### Ops demo data (implemented 2026-07-30)

All eight packs' `SampleProjectSeeder`s call the shared [`SeedsDemoOps`](database/seeders/Niches/Concerns/SeedsDemoOps.php) trait, so a restored model home also stocks admin **Operations**: one crew (assigned to the sample project), a sent proposal, a sent invoice with line items, two equipment rows, and 2–3 time entries. Money is derived from each pack's `contract_value`, and material/labour cost are set together so the projects table shows a believable 34–62% profit margin.

Also seeded for camera-ready demos: funnel filler leads (3 Partial / 3 Qualified / 3 Contacted / 1 Lost), compressed Unsplash progress photos, and three staff users (Sales / Operations / Bookkeeper).

| ID | Status | Notes |
|----|--------|-------|
| D-01 | **Done** | Full wipe + reseed; see `NicheModelHomeRestoreTest` |
| D-03 | **Done** | Ops demo data (crew, proposal, invoice, equipment, time entries) seeded for all 8 niches |
| D-04 | **Done** | Funnel leads + Unsplash progress photos + demo staff users in `SeedsDemoOps` |
| D-02 | **Partial / enough for now** | Separate host+domain per paying client already isolates Restore; keep `APP_DEMO_HUB=false` on client deploys |

---

## Product direction — 15-day self-serve trial (updated 2026-08-03)

**Goal:** Prospects sign up and try the product alone — no video meeting required for every curious visitor.

**What it is:** A temporary **practice sandbox** on Getwebfield hosting — not a live shopfront for their customers. (`getwebfield.com/...` URLs are fine because owners will not send real customers there.)

| Rule | Detail |
|------|--------|
| Hosting / brand | Lives on Getwebfield host; site does not pretend to be the prospect’s public business |
| URL (Step 1) | Single trial host (`APP_TRIAL_HOST=true`): agency homepage at `/` until provisioned, then niche site at `/` |
| URL (Step 2) | Isolated workspaces at `getwebfield.com/trial/{slug}` — **required before open public multi-signup** |
| Product Part | **Part 3** (Website + Booking + Ops) |
| Niche | Prospect **picks at signup** from registered packs (one industry per account) |
| Public banner | Always-on **demo purpose only** banner |
| Admin access | **Full open** — settings, CMS, branding, ops (nothing view-only) |
| Demo hub / Restore | **Not available** to trialists (Getwebfield sales install only) |
| Signup | Email + password (**password visible at start**, **no confirm-password**) **and** Google OAuth |
| Public estimate / booking | **No submit cap** (3-lead cap dropped 2026-08-03) |
| Duration / expiry | **15 days** → **no admin login** + public demo banner remains |
| Convert to paid | **Start fresh** on a new client install — do **not** migrate trial ops data |
| Billing / card | **None** on trial — no Stripe, no card at signup |
| vs Track B | Trial ≠ free Track B month |
| V2 | **Parked** until a real paying client exists |
| Hosting DB | **MySQL** on trial host (P-11) — not SQLite |

**Relation to SaaS:** Step 1 proves signup → niche → timer on one install. Step 2 adds isolated workspaces. V4 (custom domains + billing) stays later.

| ID | Item | Direction |
|----|------|-----------|
| T-01 | Trial signup + workspace provision | **Done** — email/password + Google; niche picker; seed Part 3; 15-day clock per workspace |
| T-08 | Isolated workspaces | **Done** — `/trial/{slug}` public + `/trial/{slug}/admin`; `trial_workspaces` + scoped rows |
| T-02 | Trial role / permissions | **Full open** (view-only locks dropped 2026-08-03); hide Industry Packs Load/Restore for trialists |
| T-03 | Public demo banner | **Done** — always visible when trial provisioned / demo_mode |
| T-04 | Expiry gate | **Done** — after day **15**: block admin login; keep public demo banner |
| T-05 | Public funnel cap | **Dropped** — no 3-submit cap |
| T-06 | Convert trial → paying client | **Start fresh** install for Track A/B — no trial data migration |
| T-07 | Build timing | **Step 2 shipped** — V2 parked |

---

## Fix when testing that flow (functional bugs)

These break QA or CRO even on localhost.

| ID | Status | Notes |
|----|--------|-------|
| F-01 | **Done** | `referred_by_code` in Lead `$fillable`; `FunctionalBugFixesTest` |
| F-02 | **Done** | Unpublished homepage 404; fresh install still renders empty home |
| F-03 | **Done** | D-01 wipes all pages before reseed |
| F-04 | **Done** | `decline()` blocked after `accepted`; only updates when `sent` |
| F-05 | **Done** | `book()` validates slot via `BookingMatrix::isOfferedSlot()` |
| F-06 | **Done** | `book()` uses transaction + `lockForUpdate()` on slot check |
| F-07 | **Done** | `ReservedPageSlugs` lists all static routes from `web.php` |
| F-08 | **Done** | `BookingMatrix` uses `$cursor->copy()->addDay()` |
| F-09 | **Done** | Estimator mounts sqft at pack min; dynamic slider step; `FunctionalBugFixesTest` |
| F-10 | **Done** | `calculateMany()` checks summed total against custom threshold |
| F-11 | **Done** | `Invoice::generateNextNumber()` uses `withTrashed()`; `InvoicingSystemTest` |
| F-12 | **Done** | `persistLead()` preserves Booked status on step revisit |
| F-13 | **Done** | `book()` rejects when `leadUuid` is null |
| F-14 | **Done** | Fence/Windows/Gutters pack `estimate_custom_threshold` raised to dollar amounts |

---

## Performance (efficiency audit 2026-07-30)

Measured with a query-log harness before and after. Counts are steady-state (warm cache).

| Path | Before | After |
|------|--------|-------|
| `GET /` | 50 | 1 |
| `GET /estimate` | 44 | 4 |
| Estimator steps 1→3 | 48 | 26 |
| `GET /admin` | 38 | 6 |

| ID | Status | Notes |
|----|--------|-------|
| P-01 | **Done** | `Setting::get()` caches misses via `['hit' => bool, 'value' => mixed]` payload |
| P-02 | **Done** | Per-request memo in `Setting`; flushed in `TestCase::setUp()` |
| P-03 | **Done** | `calculateMany()` settings reads collapse for free via P-02 |
| P-04 | **Done** | `CACHE_STORE=file` (session + queue stay `database`) |
| P-05 | **Done** | Estimator resolves services once per step via `selectedServices()` |
| P-06 | **Done** | `RecentActivity` eager-loads `causer` (was the only true N+1) |
| P-07 | **Done** | Widget loops replaced with grouped queries (funnel, weekly trend, revenue, conversion, snapshot, financial) |
| P-08 | **Done** | Charts/tables lazy-load; the two stats widgets stay inline (cheap + above the fold) |
| P-09 | **Done** | Site-version poll 2s → 10s; `SiteVersion::withoutBumping()` gives restore one bump instead of one per row |
| P-10 | **Done** | Indexes on `leads`, `projects`, `invoices`, `activity_log` hot filter/sort columns |

### Deliberately not changed

| Item | Reason |
|------|--------|
| `BookingMatrix::isOfferedSlot()` rebuilds grid | 3 queries, once per booking; this path caused F-05/F-06/F-08 — not worth reopening |
| `PageBlockData::live()` loads all three sets | 3 queries; the class docblock documents the tradeoff, fixing it changes every block template contract |
| Stalled-lead job `everyMinute()` + sync notify | 1 query/min idle; sync notify is deliberate (no queue worker on cPanel) |
| Estimator slider `.live` binding | Costs ~1 query per tick after P-02; add `.debounce` only if it still feels chatty |

### P-11 — SQLite carrying cache + session + queue + app data (open, pre-trial / required for trial host)

Cache now uses the file driver, but session, queue, and app data still share one SQLite file, and SQLite serialises writes. Fine for local prototype and likely fine for a single low-traffic client site. **Trial host (`APP_TRIAL_HOST=true`) must use MySQL** for app data before in-house upload / public trial traffic (T-01+). Documented in `.env.example`.

---

## V2 client features (required for base install)

| ID | Status | Notes |
|----|--------|-------|
| X-01 | **Built · Track A only** | **Full data export** — Admin → Data Export (`/admin/manage-data-export`) builds one ZIP: one CSV per business table (leads, proposals, invoices + items, projects, milestones, progress photos, crews, time entries, equipment, maintenance logs, pages, services, testimonials, addons, access codes, settings, users, permissions, activity log) plus `uploads/`, `manifest.json`, and a README. Gated by `APP_LICENSE_TRACK` (`LicenseTrack`) and the Admin-only `settings.data_export` permission key; password hashes are stripped. **Track B:** page shows a buy-out explanation instead of the button and `DataExportService::generate()` refuses server-side. See `decisions.md` 2026-08-01 (X-01 full data export). |

---

## Defer until production (security & hardening)

Safe to ignore on solo local dev; **launch checklist** for public client sites.

| ID | Issue | Fix direction |
|----|--------|---------------|
| S-01 | **Default admin seeder** — `admin@admin.com` / `pass` | Remove from seeder; require `make:filament-user` or random one-time password |
| S-02 | **Guessable portal codes** — seeder patterns like `LEGENDS-yymd`, `MEMBER-MONTH` | Random codes per install; document in client handoff only |
| S-03 | **Deferred (not V2)** | No rate limit on portal unlock — owner: not needed for base client install |
| S-04 | **Deferred (not V2)** | No rate limit on estimate funnel — owner: not needed for base client install |
| S-05 | **Lead UUID hijacking** — public Livewire `leadUuid` can overwrite another lead | Store lead id in signed session; authorize updates |
| S-06 | **Token URL = full access** — dashboard, proposal, invoice links are secret-URL auth only | Optional PIN/email step; shorter proposal expiry; `Referrer-Policy: no-referrer` on sensitive pages |
| S-07 | **Public demo load/reset when hub enabled** — unauthenticated POST can wipe catalog data | Admin-only or dedicated demo server; never on client production (see **D-02**) |
| S-08 | **GA ID not format-validated** — `partials/analytics.blade.php` uses `{{ $gaId }}` in script src | Regex validate `G-…` / `GTM-…` in `ManageSeo` |
| S-09 | **Stored HTML XSS** — `{!! !!}` in `rich_text`, proposals | HTML sanitizer on save/render (trusted-admin model OK for prototype) |
| S-10 | **Operations webhook SSRF** — admin can POST to any URL | Block private IPs; HTTPS only; optional domain allowlist |
| S-11 | **Session not encrypted** — `SESSION_ENCRYPT=false` in `.env.example` | `SESSION_ENCRYPT=true` + secure cookies in production |
| S-12 | **Portal session ignores deactivated codes** | Re-check `is_active` on each portal action |
| S-13 | **`client_id_restriction` not enforced** — stored but not used to filter orders | Enforce client scope when restriction is set |
| S-14 | **`APP_DEMO_HUB=true` in `.env.example`** | Default `false`; explicit opt-in for sales demo servers only |

---

## Local-only notes (acceptable for now)

| Item | Notes |
|------|--------|
| Demo hub enabled in local | Intentional — this is the sales model-home workflow |
| `APP_DEBUG=true` | Expected locally; must be `false` in production |
| Predictable demo dashboard hashes in seeders | OK for model-home demos |
| `/api/site-version` public | Low risk — version integer only |
| Block path traversal | Guarded in `pages/show.blade.php`; covered by `BlockSecurityTest` |

---

## Suggested fix order at launch

1. **Lock the doors** — remove default admin password, disable public demo wipe on client sites, add portal/estimate rate limits.
2. **Fix the appointment book** — slot validation, double-booking, lead session binding.
3. **Fix broken CRO/ops flows** — referrals, homepage publish, proposal status guards.
4. **Harden content & integrations** — HTML sanitize, analytics ID validation, webhook SSRF guard, encrypted sessions.

---

## Changelog

| Date | Change |
|------|--------|
| 2026-07-30 | Initial backlog from security/bug audit + demo reset product discussion |
| 2026-07-30 | Reframed demo reset as per-niche model-home snapshot restore for sales calls; added D-01 (full restore scope) and D-02 (demo-install gating) |
| 2026-07-30 | Implemented D-01 in `NicheLoader` — full model-home restore + tests; UI renamed to Restore model home |
| 2026-07-30 | Fixed F-01–F-08 functional bugs (referrals, homepage publish, proposals, booking validation/race, reserved slugs, BookingMatrix cursor) |
| 2026-07-30 | Logged 7-day self-serve trial product: demo sandbox, Site Settings+Content view-only, Operations open; backlog T-01–T-07 |
| 2026-07-30 | Locked trial details: Part 3, niche picker, expiry=no login+banner, email/Google signup, 3-submit cap, start-fresh convert, getwebfield.com/trial/{slug}; build timing still TBD |
| 2026-07-30 | Efficiency audit implemented (P-01–P-10): settings cache/memo, file cache driver, widget query consolidation, lazy widgets, poll interval, indexes. P-11 (SQLite triple duty) open for trial prep |
| 2026-07-30 | Added D-03 ops demo data — shared `SeedsDemoOps` seeder concern stocks crew/proposal/invoice/equipment/time entries in all 8 niche model homes |
| 2026-07-31 | D-04 — funnel leads, Unsplash progress photos, demo staff users for admin walkthrough |
| 2026-08-01 | X-01 data export locked **Track A only**; Track B remains consent-only until buy-out |
| 2026-08-01 | V2 **X-01** full data export required; flagged conflict with Track B pricing “no export” line |
| 2026-08-01 | S-03/S-04 rate limits deferred — not required for V2 (owner) |
| 2026-08-01 | Linked Current stage to `product-stages.md` (V1–V4 scoreboard) |
| 2026-08-01 | Model-home restore also clears pitch-era `color_slate` (washes out `text-slate-800`); see bug_history.md |
| 2026-08-08 | V3 Step 2 isolation shipped (T-08): `trial_workspaces`, scoped rows, `/trial/{slug}` + `/trial/{slug}/admin` |
| 2026-08-03 | V3 Step 1 shipped (agency homepage, signup, niche provision, expiry). T-01/T-03/T-04 Step 1 done; T-08 isolation still open |
| 2026-08-03 | V3 trial rules updated: 15 days, full open, demo banner, expiry lock; V2 parked; T-05 dropped; T-08 Step 2 isolation; building Step 1 |
| 2026-08-01 | X-01 built: ZIP of CSVs + uploads, Admin-only permission, `APP_LICENSE_TRACK` gate (Track A) |
