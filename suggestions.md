# Suggestions Backlog
Last Updated: 2026-07-30T04:05:00+06:00

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

- **Environment:** Local prototype only — not production yet.
- **Implication:** Most security items are **documented for launch**, not urgent today. Functional bugs still matter when testing flows locally.

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
- Clears uploaded branding keys (`logo_image`, `favicon`, `og_image`, `hero_media_image`) then re-applies pack settings defaults
- Re-runs pack content seeders + `AccessCodesSeeder`
- Blocked when `APP_DEMO_HUB=false` (except in unit tests)

UI labels: **Restore model home** on `/demo` and Admin → Industry Packs.

| ID | Status | Notes |
|----|--------|-------|
| D-01 | **Done** | Full wipe + reseed; see `NicheModelHomeRestoreTest` |
| D-02 | **Partial / enough for now** | Separate host+domain per paying client already isolates Restore; keep `APP_DEMO_HUB=false` on client deploys |

---

## Product direction — 7-day self-serve trial (decided 2026-07-30)

**Goal:** Prospects sign up and try the product alone — no video meeting required for every curious visitor.

**What it is:** A temporary **practice sandbox** on Getwebfield hosting with Getwebfield branding — not a live shopfront for their customers.

| Rule | Detail |
|------|--------|
| Hosting / brand | Lives on Getwebfield host; site does not pretend to be the prospect’s public business |
| URL | `getwebfield.com/trial/{slug}` (e.g. `/trial/acme`) |
| Product Part | **Part 3** (Website + Booking + Ops) |
| Niche | Prospect **picks at signup** from registered packs (lawn, cleaning, roofing, pressure, windows, gutters, fence, pest) |
| Public banner | Always-on **“Demo purpose only”** (or equivalent) |
| Logo / branding | Locked — cannot rebrand as theirs |
| **Site Settings** | **View only** (Product Parts, Branding, Industry Packs, SEO, Contact, Homepage Content, Estimator & Pricing, Operations Alerts) |
| **Site Content** | **View only** (Pages, Services, Testimonials, Add-ons — including homepage) |
| **Configuration** | **View only** (Users, raw Settings escape hatch — no create/edit/delete) |
| **Operations** | **Fully open** (Leads, Projects, Invoices, crews, portal tools, etc.) |
| Demo hub / Restore | **Not available** to trialists (Getwebfield sales install only) |
| Signup | Email + password (**password visible at start**, **no confirm-password** on trial) **and** Google OAuth |
| Public estimate / booking | **Max 3 submissions** per trial; **banner every time** they submit (demo reminder) |
| Duration / expiry | **7 days** → **no admin login** + public demo banner remains |
| Convert to paid | **Start fresh** on a new client install — do **not** migrate trial ops data |
| Billing / card | **None** on trial — no Stripe, no card at signup; pay only when becoming a Track A/B client |
| vs Track B | Trial ≠ free Track B month; “no free month” still applies once they are a paying renter |
| When to build | **Still deciding** (sell with meetings first vs build trial now) |

**Relation to SaaS:** Trial needs signup + isolated workspace + timer + permission locks. That is the first SaaS-shaped slice; full multi-tenant billing/custom domains can wait.

| ID | Item | Direction |
|----|------|-----------|
| T-01 | Trial signup + workspace provision | Email/password (visible, no confirm) + Google; niche picker; seed Part 3 template; slug under `getwebfield.com/trial/{slug}`; start 7-day clock |
| T-02 | Trial role / permissions | View-only Site Settings + Site Content + Configuration; full Operations; hide Industry Packs Load/Restore |
| T-03 | Public demo banner + locked logo | Always visible on public pages; block branding edits in admin |
| T-04 | Expiry gate | After day 7: **block admin login**; keep public demo banner |
| T-05 | Public funnel cap | Max **3** estimate/booking submits per trial; show demo banner on each submit |
| T-06 | Convert trial → paying client | **Start fresh** install for Track A/B — no trial data migration; billing starts only then (manual for now) |
| T-07 | Build timing | Undecided — product rules locked; engineering start date TBD |

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

### P-11 — SQLite carrying cache + session + queue + app data (open, pre-trial)

Cache now uses the file driver, but session, queue, and app data still share one SQLite file, and SQLite serialises writes. Fine for local prototype and likely fine for a single low-traffic client site. **Revisit before the 7-day trial sandbox ships** (T-01+): concurrent estimator submissions plus admin polling will contend on the write lock. Likely direction is MySQL for app data on any multi-tenant/trial host.

---

## Defer until production (security & hardening)

Safe to ignore on solo local dev; **launch checklist** for public client sites.

| ID | Issue | Fix direction |
|----|--------|---------------|
| S-01 | **Default admin seeder** — `admin@admin.com` / `pass` | Remove from seeder; require `make:filament-user` or random one-time password |
| S-02 | **Guessable portal codes** — seeder patterns like `LEGENDS-yymd`, `MEMBER-MONTH` | Random codes per install; document in client handoff only |
| S-03 | **No rate limit on portal unlock** | Throttle Livewire unlock (e.g. 5 attempts / 15 min / IP) |
| S-04 | **No rate limit on estimate funnel** | Throttle `persistLead` / Livewire steps; optional honeypot or CAPTCHA on contact step |
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
