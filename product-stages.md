# Product Stages
Last Updated: 2026-08-01T18:04:11+0600

> **Purpose:** Living map of where this product is in the V1 → V4 journey.  
> **Audience:** Humans + agents. Update this file whenever stage status, exit criteria, or scope changes.  
> **Not the same as** `suggestions.md` (backlog) or `decisions.md` (locked choices) — this is the **stage scoreboard**.

---

## The four stages (locked intent)

| Stage | Where it lives | Job in plain English |
|-------|----------------|----------------------|
| **V1** | Our website | Base **demo** for video / sales showcase — looks finished on camera |
| **V2** | Their website | Base **client install** — real production for one paying business, low handoff work |
| **V3** | Our website | **SaaS demo** — self-serve trial period on Getwebfield hosting |
| **V4** | Their website | **SaaS client install** — real production provisioned like SaaS (not hand-built every time) |

**How to remember it:** odd numbers (V1, V3) = **our** site for selling/trying. Even numbers (V2, V4) = **their** site for real customers.

| Pair | Meaning |
|------|---------|
| V1 → V2 | Sell with a showroom, then deliver one install at a time (Track A / Track B style) |
| V3 → V4 | Same product story, but signup + provisioning replace most manual setup |

---

## Current position

| Stage | Status | Rough readiness |
|-------|--------|-----------------|
| **V1** | **Active / nearly done** | ~90% |
| **V2** | **Next target** | Product features ~60–70%; safe repeatable handoff ~30% |
| **V3** | Not started (rules decided) | ~0% code · product rules locked in `suggestions.md` / `decisions.md` |
| **V4** | Future | ~0% — after V2 proof and V3 trial |

**One-line status:** Finishing **V1** (video-ready demo). Next milestone is **V2** (first real client on their hosting with a boring, low-work install). Do not jump to V3/V4 before V2 works once.

```
V1 ██████████░░  almost done (video-ready)
V2 ██████░░░░░░  next — lock doors + handoff checklist
V3 ░░░░░░░░░░░░  rules yes, engineering no
V4 ░░░░░░░░░░░░  after V2 sales proof + V3 trial
```

---

## Exit criteria (what “done” means)

### V1 — Base demo (our site)

**Done when:** A niche model home can be filmed or pitched without apologizing for empty admin or broken flows.

| Criterion | Status |
|-----------|--------|
| 8 industry packs + demo hub + Restore model home | Done |
| Product Parts 1–3 gated correctly | Done |
| Estimator → lead → project → proposal → invoice → portal story works | Done |
| Ops demo data (crew, proposal, invoice, photos, staff roles) | Done |
| Demo hub never ships on paying client installs (`APP_DEMO_HUB=false`) | Rule logged; enforce on every V2 deploy |
| Showcase script / which niche to film | Optional polish |

### V2 — Base client installment (their site)

**Done when:** A paying client can go live on **their** hosting with a short checklist and almost no custom engineering.

| Criterion | Status |
|-----------|--------|
| Feature set (CMS, booking, ops) works on a real domain | Mostly ready |
| Demo hub / restore **off** on client deploys | Required at handoff (D-02) |
| No default admin password in production seed | Open (S-01) |
| Rate limits on estimate + portal | **Not required for V2** (deferred — owner decision 2026-08-01) |
| Production `.env` defaults (debug off, demo hub false, session encrypt) | Open (S-11, S-14) |
| Written upload / handoff checklist (what we do vs what client does) | Not written yet |
| **Business can export all their data** (admin self-serve download) | **Done · Track A only** (**X-01**) — Admin → Data Export streams a ZIP of CSVs + uploads; enabled by `APP_LICENSE_TRACK=a`. Track B: no self-serve export (super-admin consent only; unlocks after buy-out to Track A) |
| Stripe / full Email-SMS / negotiation engine | **Not required for V2 exit** — stay manual/offline-friendly |

### V3 — SaaS demo / trial (our site)

**Done when:** A prospect can sign up alone, use a time-boxed sandbox, and convert without a Zoom for every curious visitor.

| Criterion | Status |
|-----------|--------|
| Product rules (Part 3, niche picker, 7-day expiry, 3-lead cap, view-only settings) | Decided (T-01–T-07) |
| Signup + workspace provision + expiry gate | Not built |
| URL shape `getwebfield.com/trial/{slug}` | Decided, not built |
| Convert = start fresh Track A/B install (no trial data migration) | Decided |

### V4 — SaaS client installment (their site)

**Done when:** A paying SaaS customer gets a real production site through provisioning (not a one-off manual copy of the repo every time).

| Criterion | Status |
|-----------|--------|
| Auto provisioning / custom domain / recurring billing | Not started |
| Evaluate after ~5 manually delivered clients | Per stakeholder plan |

---

## What each stage is *not*

| Stage | Do not confuse with |
|-------|---------------------|
| V1 | A paying client’s live shopfront |
| V2 | Multi-tenant SaaS or self-serve signup |
| V3 | Their white-label production site (trial stays Getwebfield-branded sandbox) |
| V4 | “Just turn on Stripe” — needs provisioning + billing + tenant isolation |

---

## Agent / maintainer rules

1. **Read this file** before proposing work that sounds like demo, client handoff, trial, or SaaS.
2. **Update this file in the same task** when any of these change:
   - Stage status or readiness %
   - Exit criteria checked off or added
   - A V2 handoff / security item ships (cross-link `suggestions.md` IDs)
   - Trial (V3) or SaaS (V4) scope is decided or built
3. Set **Last Updated** from the system clock (`date +"%Y-%m-%dT%H:%M:%S%z"`), never guess.
4. Append a short line under **Changelog** below (newest first).
5. **Precedence:** `decisions.md` > this file’s “locked intent” table. If a decision changes the stage model, update both.

---

## Changelog

| When | Change |
|------|--------|
| 2026-08-01 | X-01 locked **Track A only**; Track B stays consent-only until buy-out. |
| 2026-08-01 | V2 exit: client self-serve **full data export** required (X-01). Conflicts with Track B “no export” pricing line — pending confirm. |
| 2026-08-01 | V2: rate limits (S-03/S-04) marked not required for exit — owner deferred. |
| 2026-08-01 | Created file. Locked V1–V4 intent. Current position: V1 nearly done, V2 next, V3 rules-only, V4 future. |
| 2026-08-01 | X-01 built — self-serve export shipped for Track A; `APP_LICENSE_TRACK` added to the handoff checklist. |
