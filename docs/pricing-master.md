# Getwebfield — Pricing & Terms (Master Reference)

Internal reference document. Covers the **US / UK / CA / AU** pricing model only — the Bangladesh venture runs as a separate track with its own pricing and is not covered here.

**All amounts USD.** Same published prices in all markets above. No PPP.

Document version: July 2026.

---

## 1. The shape of it

Two ways to work with us — **Own it** (Track A, one-time purchase) or **Rent it** (Track B, monthly subscription) — across three product tiers. Tier 3 is purchase-only.

| | Track A — Own it | Track B — Rent it |
|---|---|---|
| Tiers available | 1, 2, 3 | 1, 2 only |
| Payment | One-time setup + monthly hosting/support | Setup fee + monthly subscription |
| Hosting | Client's choice after launch | Our infrastructure only |
| Free month | Yes, first month after launch | No |
| Content updates | Unlimited | Unlimited |
| Redesigns / new features | Quoted separately | Quoted separately |

---

## 2. Tier definitions (what's included)

**Part 1 — Website + CMS**

Marketing site, page builder (17 blocks), branding/colors/fonts, SEO settings, contact info, homepage settings, services/testimonials content, admin CMS.

*Not included:* quote/estimate funnel, lead capture, booking, ops tools.

**Part 2 — Website + CMS + Booking**

Everything in Part 1, plus: multi-step `/estimate` wizard, lead capture (including partial/abandoned leads), pricing engine, online booking/slots, leads admin, pricing settings, estimate CTAs across the site (hero, services, banners).

*Not included:* projects, proposals, invoices, crew scheduling, member portal, equipment tracking.

**Part 3 — Website + CMS + Booking + Ops** *(Track A only)*

Everything in Part 2, plus: projects/milestones/progress photos, client dashboard (shareable homeowner link), proposals (send/accept/decline online), invoices (create/send/print), crew management and schedule board, time tracking and equipment maintenance, member portal with add-on orders, ops alerts and webhook integrations.

---

## 3. Track A — Own It (One-Time Purchase)

| | Tier 1 | Tier 2 | Tier 3 |
|---|---|---|---|
| One-time setup | $987 | $1,777 | $4,798 |
| Monthly hosting & support | $50/mo | $125/mo | $250/mo |
| First month | Free after launch | Free after launch | Free after launch |

**Payment schedule:** 3 milestones — **20% at start / 40% at design approval / 40% at "handover."**

Note: the 40% "handover" milestone is a **payment-schedule label only** — it does **not** mean source code is handed over at that point (see §7).

**Included in the monthly fee (all tiers):** hosting, SSL, platform updates, security patches/bug fixes, unlimited content updates.

**Hosting after launch:** client's choice — stay on our hosting at the monthly rate above, or migrate elsewhere (migration may be quoted separately).

**All plans non-refundable once the site is built.**

**Data export:** Track A admins get **self-serve full data export** (download their leads, jobs, invoices, content, uploads, etc.) — required for V2 client installs. **Built:** Admin → Data Export downloads one ZIP (a CSV per table plus every uploaded file); switched on per install with `APP_LICENSE_TRACK=a`. See `product-stages.md` / suggestions **X-01**.

---

## 4. Track B — Rent It (Monthly Subscription)

| | Tier 1 | Tier 2 | Tier 3 |
|---|---|---|---|
| Setup fee | $200 | $200 | Not available |
| Monthly subscription | $117/mo | $227/mo | Not available |

**Setup fee:** bank transfer only, non-refundable, due before build begins (isolates chargeback exposure to the monthly charge only).

**Monthly fee:** billed in advance each month (pay first), card or bank. No trial, no free month. **Monthly billing only** — no quarterly/biyearly option.

**Hosting:** our infrastructure only, never handed over on this track.

**Payment failure:** access suspended after **3 days** of non-payment.

**Dispute/chargeback filed:** access is **suspended immediately** on filing (not waiting for the dispute to resolve). Client data is retained either way — a dispute blocks access, it does not delete anything.

**Data export:** **not** self-serve on Track B — not available at any time without Getwebfield super-admin consent. After buy-out to Track A (§5), self-serve export applies like any Track A client.

**Redesigns / new features:** always billed separately, never included in the monthly fee.

**Operating model:** billing and account management are handled manually, not as an automated self-serve SaaS platform.

---

## 5. Buy-Out: Subscription → One-Time

A Track B client can convert to Track A at any time.

| Current subscription | Buy-out fee (pay in full) | Monthly after buy-out |
|---|---|---|
| Tier 1 | $987 | $50/mo |
| Tier 2 | $1,777 | $125/mo |

- The buy-out fee is the **full published Track A setup price** for that tier — not a discounted or prorated amount.
- The original **$200 subscription setup fee is not credited** toward the buy-out.
- After buy-out, hosting becomes the client's choice (stay on our hosting at the lower rate, or migrate — may be quoted separately).
- Tier 3 is never available via subscription or buy-out onto a subscription — it's purchase-only from the start.

---

## 6. Support & Content Updates

**Unlimited content updates** are included for any client currently paying the ongoing monthly charge (hosting & support on Track A, or the subscription on Track B) — no per-edit cap, no per-tier limit.

New pages, redesigns, rebrands, and new features are always a separate quote, on both tracks.

---

## 7. Code / CMS Handover Policy (Track A)

Source code is **not** handed over — the build stays on our hosting — until **month 2**, i.e. until the **final payment installment clears**, regardless of what the payment schedule's milestone is labeled.

If a client insists on getting the code files early, they can have them — but we're **no longer responsible** for anything that breaks once it's off our hosting (no support liability).

---

## 8. Marketing Upsells — Separate Contracts

Not included in any tier or track. Same fixed pricing across Tier 1, 2, and 3 — **no percentage of ad spend.**

| Upsell | Setup | Monthly |
|---|---|---|
| Local SEO | $497 | $297 |
| Google Ads | $497 | $497 |
| Meta Ads | $397 | $397 |
| Email marketing | $397 | $397 |

- **Ad spend:** client pays Google/Meta directly. We only charge the fixed management fee — no ad budget passes through us.
- **Minimum term:** 3 months on any monthly marketing add-on.
- **No per-send email fee** — email marketing is flat-rate, same structure as the other three.
- **No bundled SEO+Email package** — clients buying both simply pay both prices.
- *Deferred, not decided:* if ads/email upsells show real traction, may add a separate design charge on top later.

Public detail page: `docs/pricing-upsells-public.md`.

---

## 9. Tier 3–Only Ops Add-Ons

Available only to Tier 3 clients (Track A), on top of the marketing menu everyone else can also buy.

| Add-on | Setup | Monthly |
|---|---|---|
| Data migration (from Jobber, spreadsheet, etc.) | $697 | — |
| Staff training (1 hr, up to 4 people) | $197 | — |
| Custom integration / webhook | from $497 | — |

- *Deferred, not decided:* if the custom integration/webhook add-on shows traction, may raise its price later.
- **Priority support was considered and dropped** — not offered as an add-on.

**Sales framing:** Tier 3 buyers are typically bigger businesses with more budget. Natural upsell path — Google Ads after go-live (they need leads), data migration if they're switching from another tool, staff training since they have staff to onboard. Tier 3 isn't a separate marketing price list — same marketing menu as everyone else, plus ops add-ons nobody else can buy.

---

## 10. What's Public vs. Internal

**On the website:**

- Pricing page/section stays **simple** — tracks, tiers, and prices only → `docs/pricing-public.md`.
- Upsells (marketing + Tier 3 ops add-ons) get their own dedicated detail page → `docs/pricing-upsells-public.md`.
- **No mention** anywhere of price increases, founding pricing, or limited availability — that mechanism is internal only (see §11).
- **Industry packs** (site content skinned per trade — lawn care, cleaning, roofing, etc.) are **not** a client-facing feature. They're an internal superadmin tool used only to generate the free per-lead demo quickly across different trades.
- Pricing is **public and unqualified** — no "contact us for a quote" gating on the core tiers.

---

## 11. Internal-Only Pricing Mechanics (never published)

- **Escalator:** price rises **25% every time 3 new clients close**, tracked separately per tier. Doesn't need to be precise.
- Whether Track B's own numbers ($200 setup, $117/$227 monthly) escalate on the same schedule is **undecided** — to be resolved later.
- **Non-payment policy:** if a client stops paying (subscription or hosting/support), simply stop serving them — **no collections chasing**.
- No company/legal entity is formed at this stage; payment structure is designed to work without one and to minimize chargeback exposure (bank-only on setup fees is the main lever currently in place).

---

## 12. Engineering mapping

| Sales tier | `product_part` setting |
|------------|------------------------|
| Tier 1 | 1 (Website) |
| Tier 2 | 2 (Booking) |
| Tier 3 | 3 (Ops) |

Disable `APP_DEMO_HUB` on all production client installs.

---

## 13. Production checklist (per paid client)

- [ ] Demo hub off (`APP_DEMO_HUB=false`)
- [ ] Correct `product_part` for tier
- [ ] Industry pack loaded internally; demo content replaced with client content
- [ ] Admin password changed from seed default
- [ ] Real mail driver configured (when implemented)
- [ ] Track and tier recorded for billing
- [ ] Agreement signed; setup fee collected (bank for Track B setup)
- [ ] First month collected before go-live (Track B)
- [ ] Track A: milestone payments logged (20 / 40 / 40)

---

## 14. Open Items

- Track B escalation rules — deferred.
- Ad/email design charge — deferred, pending traction.
- Custom integration/webhook price increase — deferred, pending traction.
