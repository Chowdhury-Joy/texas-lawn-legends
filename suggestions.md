# Suggestions Backlog
Last Updated: 2026-07-30T01:36:00+06:00

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

### Gap today

[`NicheLoader`](app/Support/Niche/NicheLoader.php) aims at this (`reset()` → wipe + reseed) but does not yet restore a full model home:

- Wipes services, add-ons, testimonials, and projects matched by known demo hashes / `is_demo` leads
- Does **not** reset CMS pages, branding/logo edits, users and roles, crews, equipment, invoices, proposals, time entries
- Projects created mid-meeting (no demo hash, no `is_demo` lead) survive — that is the bloat problem above
- Because the wipe is catalog-wide, running it on an install that holds real content is destructive — back up SQLite first

| ID | Item | Direction |
|----|------|-----------|
| D-01 | **True model-home snapshot restore** | Define the full set of demo-mutable data (settings/branding, pages, services, add-ons, testimonials, leads, projects, milestones, progress photos, crews, equipment, time entries, proposals, invoices, non-owner users/roles), clear it on restore, then reseed the active pack so the showroom returns to its seeded state |
| D-02 | **Scope restore to demo installs** | Restore/load only available when the install is flagged as a sales demo; blocked on client production regardless of hub flag (relates to S-07, S-14) |

---

## Fix when testing that flow (functional bugs)

These break QA or CRO even on localhost.

| ID | Issue | Fix direction |
|----|--------|---------------|
| F-01 | **Referral tracking silent fail** — `EstimatorWizard` sets `referred_by_code` but `Lead` model `$fillable` omits it | Add to `$fillable`; test `?ref=` persists on lead |
| F-02 | **Unpublished homepage still renders** — `PageController::home()` skips `is_published` check | Same 404 (or explicit “coming soon”) as other CMS pages |
| F-03 | **Demo pack reset leaves stale CMS pages** — pages not wiped; seeders only `updateOrCreate` known slugs | Wipe/re-seed demo pages or flag demo pages for selective delete (subsumed by **D-01**) |
| F-04 | **Proposal decline overwrites accepted** — `decline()` has no guard; `accept()` does | Only decline when status is `sent`; block after `accepted` |
| F-05 | **Booking accepts arbitrary date/time** — `book()` does not validate against `BookingMatrix` slots | Reject slots not in the offered grid |
| F-06 | **Double-booking race** — two concurrent `book()` calls can take the same slot | Transaction + lock or unique constraint on `scheduled_at` for booked leads |
| F-07 | **Incomplete reserved page slugs** — blocks `estimate`, `portal`, etc. but not `demo`, `proposals`, `invoices`, `api` | Expand reserved list to match all static routes in `routes/web.php` |
| F-08 | **BookingMatrix date cursor mutation** — `$cursor->addDay()` mutates in loop | Use `$cursor->copy()->addDay()` |

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
