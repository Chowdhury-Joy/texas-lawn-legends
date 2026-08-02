# Decisions

## 2026-07-30 (filament theme)

<decision>
 <category>UI/UX</category>
 <context>Custom Filament pages like Crew Schedule used raw Tailwind in Blade, but Filament's default CSS only styles its own components — so the schedule page shipped unstyled.</context>
 <action>Add a Filament Vite theme at `resources/css/filament/admin/theme.css` that imports Filament's base theme and `@source`s `app/Filament` + `resources/views/filament`. Register it with `->viteTheme()` and include it in `vite.config.js`. Guard with `FilamentThemeCompilesCustomAdminClassesTest`.</action>
 <reason>One theme fixes every custom admin Blade (Crew Schedule, Industry Packs tip banner, settings forms) without rewriting pages to Filament primitives; matches Filament v4's documented Tailwind v4 workflow.</reason>
</decision>

## 2026-07-30 (efficiency audit)

<decision>
 <category>Code</category>
 <context>Settings drive nearly all copy/pricing/branding, so a page render read 40-50 keys. With CACHE_STORE=database every read was a DB round trip, and Setting::get() cached null for missing keys — indistinguishable from a miss, so absent keys re-queried forever.</context>
 <action>Cache a `['hit' => bool, 'value' => mixed]` payload so misses are cacheable, and add a per-request static memo in `Setting` (flushed in `TestCase::setUp()` and via `Setting::flushRequestCache()`). Legacy payload shapes are upgraded on read. Switch `CACHE_STORE` to `file`; session and queue stay on `database`.</action>
 <reason>Measured `GET /` 50 → 1 query, `GET /estimate` 44 → 4, estimator steps 1→3 48 → 26. File cache avoids routing settings reads through the same SQLite file as app data; session stays on database because file sessions are riskier under concurrent Livewire requests, and queue stays put since there is no Redis on cPanel hosting.</reason>
</decision>

<decision>
 <category>Code</category>
 <context>Dashboard widgets each looped one query per day/status/month — 38 queries on /admin — and RecentActivity rendered `causer.name` without eager loading (a true N+1 that scales with data).</context>
 <action>Replace the loops with single grouped/conditional-aggregate queries across LeadFunnel, WeeklyLeadTrend, RevenueChart, LeadConversionStats, BusinessSnapshot, and FinancialOverview; eager-load `causer` on RecentActivity. RevenueChart resolves its month bucket expression per driver (sqlite/pgsql/mysql) so grouping is not SQLite-only.</action>
 <reason>Measured /admin 38 → 6 queries with identical widget output. Chart and table widgets now lazy-load; the two stats overview widgets stay inline because they are above-the-fold headline numbers and cost two queries each after consolidation.</reason>
</decision>

<decision>
 <category>UI/UX</category>
 <context>Authenticated pages polled /api/site-version every 2 seconds (1,800 requests/hour per open tab), and every CMS-ish model save bumped the version — so a niche pack restore fired one bump per seeded row and reloaded every open admin tab repeatedly mid-restore.</context>
 <action>Move version state into `App\Support\SiteVersion` and poll every 10 seconds. `NicheLoader` wraps restore in `SiteVersion::withoutBumping()` and fires a single `bumpNow()` at the end.</action>
 <reason>Auto-reload still feels immediate for an admin editing content, at a fifth of the request volume, and a restore now triggers exactly one reload instead of hundreds.</reason>
</decision>

## 2026-07-30 (industry packs expansion)

<decision>
 <category>Business_Logic</category>
 <context>Audit found fence/windows/gutters packs had estimate_custom_threshold values that read like size units (linear ft / panes) not dollars, breaking instant-estimate demos.</context>
 <action>Raise pack defaults to dollar thresholds validated against cheapest realistic quote: fence $2500, windows $500, gutters $1500. Lawn/roofing/cleaning/pressure/pest unchanged.</action>
 <reason>Threshold must exceed minimum-size high estimate for the cheapest service so prospects on a live call see a price before custom-quote routing.</reason>
</decision>

## 2026-07-30 (industry packs expansion)

<decision>
 <category>Business_Logic</category>
 <context>Sales demos needed more home-service verticals beyond lawn, cleaning, and roofing — without rebuilding the product or estimator engine.</context>
 <action>Add five niche packs as skins only: pressure (`ClearPath Wash`), windows (`PanePerfect`), gutters (`FlowGuard Gutters`), fence/deck (`TimberLine`), pest (`ShieldBug Pest`). Each ships Pack class + five content seeders (services, testimonials, add-ons, sample project, pages). All reuse `sqft_neighborhood` pricing strategy with industry-specific size labels (sq ft, panes, linear ft). Register in `config/niche.php`; demo hub and Admin Industry Packs pick them up automatically. `DatabaseSeeder` now runs `contentSeeders()` for any active `APP_NICHE`, not lawn-only.</action>
 <reason>Expands pitch surface for owner-led trades that quote by size/scope and run jobs as projects — same Tier 2/3 story, minimal code risk.</reason>
</decision>

## 2026-07-30

<decision>
 <category>Code</category>
 <context>Security/bug audit surfaced many hardening items; most are not urgent for local prototype but easy to lose track of before launch.</context>
 <action>Ship `suggestions.md` as the living backlog (not decided work). Add `.cursor/rules/suggestions-backlog.mdc` so agents read and flag relevant items before security, demo, onboarding, or production-readiness changes. Precedence: decisions.md &gt; bug_history.md &gt; suggestions.md.</action>
 <reason>Separates “ideas for later” from locked decisions; agents surface overlap without silently implementing or contradicting logged choices.</reason>
</decision>

<decision>
 <category>Business_Logic</category>
 <context>Public demo reset/load was built for Getwebfield’s shared sales pitch install, not for paying client sites (SaaS or one-install-per-client).</context>
 <action>Do not ship public demo reset on client production. Paying customers start with empty/fresh ops data; onboarding is one-time industry pack + seed. Optional: keep load/reset admin-only on a dedicated internal demo/staging URL. Document full rationale in suggestions.md § Demo hub & reset.</action>
 <reason>Reset wipes catalog content (services, add-ons, testimonials) and solves an internal demo problem clients do not have.</reason>
</decision>

<decision>
 <category>Business_Logic</category>
 <context>Clarifies the entry above, which read as "reset is optional." Each niche has a pre-built model home (logo, homepage copy, sample projects, one in-progress job with a crew) so a demo never opens empty. Running back-to-back calls in the same niche — roofer A, then roofer B — stacks each prospect's mid-pitch edits and bloats the showroom.</context>
 <action>Reset is required tooling on the Getwebfield sales install, not optional: treat it as "restore this niche's model home" (snapshot restore) run between meetings, with Load pack switching niche and restoring that pack's model home. Still never shipped to paying clients. Scope of a true restore and demo-install gating tracked as D-01 / D-02 in suggestions.md § Demo hub & model-home restore.</action>
 <reason>Prevents agents reading "no reset for clients" as "remove reset," and names the real requirement (per-niche snapshot restore) rather than a catalog-wide wipe.</reason>
</decision>

<decision>
 <category>Code</category>
 <context>D-01 — mid-pitch edits (extra projects, logo, crews, staff, custom pages) survived partial wipe, bloating back-to-back sales demos in the same niche.</context>
 <action>`NicheLoader` restore now wipes all pitch-mutable showcase tables, clears upload branding keys, re-applies pack settings, re-runs content seeders + AccessCodesSeeder, preserves `admin@admin.com` only. Block load/restore when `APP_DEMO_HUB=false`. UI copy: Restore model home. Covered by `NicheModelHomeRestoreTest`.</action>
 <reason>Matches the video-game save-slot workflow — each meeting in a niche starts from the seeded model home, not the last prospect's edits.</reason>
</decision>

<decision>
 <category>Business_Logic</category>
 <context>Need a self-serve 7-day trial so curious prospects can try the product without a Zoom for every inquiry. Must not look like their live customer-facing website.</context>
 <action>Offer a Getwebfield-hosted trial sandbox (not a white-label client site): templated niche website on Getwebfield hosting/branding; always-on "Demo purpose only" public banner; logo/branding locked. Admin: Site Settings, Site Content (Pages, Services, Testimonials, Add-ons, Homepage), and Configuration (Users, raw Settings) are view-only — can open screens, cannot save/rewrite/create. Operations (Leads, Projects, Invoices, crews, etc. per Product Part) stay fully usable. No demo hub / Restore model home for trialists. After 7 days, lock or upgrade gate. Track B "no free month" still applies only after they become a paying renter on a real client install — the trial is a product demo front door, not Track B hosting. Build items tracked as T-01+ in suggestions.md.</action>
 <reason>Self-serve cuts meeting load; view-only content/settings/configuration keeps the template and staff setup intact and stops them treating the sandbox as their shopfront; open Operations lets them feel the booking/ops product story.</reason>
</decision>

<decision>
 <category>Business_Logic</category>
 <context>Open product choices for the 7-day trial needed locking before build: part level, niche, expiry UX, signup, public estimate limits, conversion, URL shape.</context>
 <action>Trial workspaces run at Product Part 3 (full Website + Booking + Ops). Signup: prospect picks niche themselves; auth = email + password (password field visible/plain at start, no confirm-password on trial signup) and Google OAuth. URL shape: getwebfield.com/trial/{slug} (e.g. /trial/acme). Public estimate/booking funnel allows up to 3 submissions per trial; each submit shows a reminder banner that this is a demo. After day 7: admin login blocked + public demo banner remains (no further authenticated use). Converting to a paying client starts fresh on a new install — trial ops data is not migrated. No billing and no card required for the trial/demo — payment only when they become a paying Track A/B client. Timing of when to build the trial vs keep selling with meetings is still undecided. Details mirrored in suggestions.md § 7-day trial.</action>
 <reason>Part 3 lets them feel the full product story; self-serve niche + Google lowers signup friction; 3-lead cap + per-submit banner stops spam and reinforces demo; expire = no login keeps the sandbox from becoming free forever; start-fresh conversion avoids messy half-migrated demo data onto real client sites; no card at signup keeps the trial low-friction and matches manual billing until later SaaS.</reason>
</decision>

## 2026-07-29

<decision>
 <category>UI/UX</category>
 <context>Admin settings and page editors used a sticky bottom save bar with an "Site Settings Editor" label that floated over content and added visual noise.</context>
 <action>Replace the sticky save bar with a static inline Save changes button (24px top margin via inline style, right-aligned) and remove the editor label text across settings, homepage, and page create/edit forms.</action>
 <reason>Save sits naturally at the end of the form scroll instead of covering fields; inline margin is required because Filament admin CSS does not ship project Tailwind utilities like mt-6.</reason>
</decision>

<decision>
 <category>Business_Logic</category>
 <context>Getwebfield pricing needed one internal master, a minimal public price list, and a separate upsells page — US/UK/CA/AU in USD only; Bangladesh is a separate venture.</context>
 <action>docs/pricing-master.md holds full terms (20/40/40 Track A milestones, unlimited content updates, chargeback immediate suspend, code handover at month 2, 25% escalator every 3 clients internal-only, email upsell $397/mo, no priority support, no SEO+email bundle). docs/pricing-public.md and docs/pricing-upsells-public.md are website-ready. Industry packs remain internal demo tooling only.</action>
 <reason>Public pricing stays simple and unqualified; operational and pricing mechanics stay out of client view; one master avoids drift between proposal copies.</reason>
</decision>

<decision>
 <category>Business_Logic</category>
 <context>Need two sales tracks alongside demos: clients who want to buy the build vs. clients who want low upfront cost on our hosting, without building SaaS or auto-billing yet.</context>
 <action>Track A (Own it): founding setup $987 / $1,777 / $4,798 + monthly $50 / $125 / $250, first month free after launch, tiers 1–3. Track B (Rent it): $200 bank setup + monthly $117 / $227 pay-first, tiers 1–2 only, our hosting, no trial/free month, suspend after 3 days non-payment, buy-out anytime at full Track A setup ($987 / $1,777) with no credit for $200; hosting after buy-out client’s choice, $50 / $125 if they stay. Redesign and upsells (SEO, ads, email) always separate. Client-facing copy in docs/pricing-proposal.md; ops notes in docs/pricing-internal.md.</action>
 <reason>Lower-friction entry ($200 + month 1) without undermining Tier 3 ops sales or training the market on unsustainably cheap monthly; manual billing until client 5+ and SaaS phase.</reason>
</decision>

## 2026-07-28

<decision>
 <category>CRO</category>
 <context>Sales demos need to flip between lawn / cleaning / roofing without editing branding live or hosting three apps.</context>
 <action>Ship public /demo hub (APP_DEMO_HUB) plus admin Industry Packs page. NicheLoader sets active_niche + demo_mode, wipes showcase content, reseeds pack. Demo banner on public site; ops webhooks muted and new estimator leads tagged is_demo while demo_mode is on. Showcase brands: BrightSide Cleaning, Summit Roof Co.</action>
 <reason>Model-home pitch (hub → quote → admin → reset) is faster and safer than rearranging one site mid-call.</reason>
</decision>

<decision>
 <category>Business_Logic</category>
 <context>Need to reuse one codebase across home-service industries without baking Dallas landscaping into the product shell.</context>
 <action>Introduce APP_NICHE + NicheResolver + LawnPack (vocabulary, SEO schema type, settings defaults, content seeders under database/seeders/Niches/Lawn). Public/admin UI reads labels via niche_label(); empty brand settings use neutral fallbacks, never hardcoded Texas Lawn Legends/Dallas. Product Parts remain the feature tier; packs are the industry skin.</action>
 <reason>Separates “what features are on” from “what industry am I” so Phase 3 can add showcase packs without rewriting the funnel.</reason>
</decision>

<decision>
 <category>Business_Logic</category>
 <context>Need to sell and demo the same codebase as Website+CMS only, +Booking, or full Ops — without rebuilding per client.</context>
 <action>Introduce product_part setting (1/2/3) with ProductFeatures map, admin page “Product Parts”, route middleware, and admin/nav gating. Default remains Part 3 so Texas Lawn Legends behavior is unchanged.</action>
 <reason>Clear packaging for sales (three product levels) while keeping industry packs as a separate skin/content layer next.</reason>
</decision>


## 2026-07-30 (niche estimator vocabulary)

<decision>
 <category>UI/UX</category>
 <context>Switching industry packs still left the estimator sounding like lawn care — hardcoded “What are we building?” / “Project dimensions” / yard complexity copy, and packs like pest reused the bare “sq ft” unit.</context>
 <action>Expand each niche pack’s `labels()` with estimator progress titles, step titles/bodies, and complexity blurbs. Wire `estimator-wizard` to `niche_label()`. Differentiate size units (e.g. pest `home sq ft`, pressure `surface sq ft`, cleaning `interior sq ft`; roofing/windows/fence/gutters keep squares/panes/linear ft).</action>
 <reason>Pack switch should change the quote funnel’s language, not only the logo — prospects must hear their trade’s words on a live demo.</reason>
</decision>

## 2026-07-30 (admin figma typography)

<decision>
 <category>UI/UX</category>
 <context>Admin shell still used Filament’s default Inter-like heading weight/size instead of the Lux Figma text styles.</context>
 <action>Load Geist Sans + Geist Mono via Fontsource in the Filament Vite theme. Apply Figma Text/sm (14/22/-2%), Text/xs (12/16/-3%), and page title/brand mono (18/22/-2%, title uppercase). Remove the previous header `text-lg font-medium tracking-wide` overrides.</action>
 <reason>Matches the Figma type styles named in the frame so admin chrome reads as the same product as the design reference.</reason>
</decision>

## 2026-07-30 (admin main CTA vs danger delete)

<decision>
 <category>UI/UX</category>
 <context>On edit pages, Delete was the only header action, so it read as the page’s main CTA (Figma’s “Main Button of the Page”). Create / Add / Save should own that slot; Delete is a destructive secondary action.</context>
 <action>Shared trait `HasPrimarySaveAndDangerDelete` on all resource Edit pages: header shows Save as the primary CTA; form footer shows Cancel plus outlined danger Delete (visually separated). Create/Add stay as list-page header CTAs.</action>
 <reason>Matches the Figma header contract — primary action advances the job (create/save); delete is danger-zoned so operators don’t treat it as the default next step.</reason>
</decision>

## 2026-07-30 (filament content header)

<decision>
 <category>UI/UX</category>
 <context>The prior fix put the page title in the sidebar logo slot, which felt like a back button and did not match the Figma admin shell (brand in sidebar, title in the main content header, back arrow only on drill-down pages).</context>
 <action>Disable Filament’s global topbar so brand stays in the sidebar only. Override the page header Blade into a Figma-style 64px dark content shell (`fi-content-shell-header`): uppercase page title left, ← back on Create/Edit only, primary actions right. `FilamentContentHeader` resolves title/back. Rebuild admin Vite theme so styles ship.</action>
 <reason>Matches the Lux dashboard shell — sidebar = app identity + nav; main column header = where you are + what you can do — without a second brand bar that reads as a back button.</reason>
</decision>

## 2026-07-30 (invoice public page)

<decision>
 <category>UI/UX</category>
 <context>Public invoice page (`/invoices/{token}`) used nested bordered boxes that made the layout feel heavy; notes sat mid-page; Print felt like a secondary action.</context>
 <action>Flatten the invoice layout (no outer card shadow/border stack), right-align totals, move notes to a centered bottom panel with subtle `bg-slate-50` + border, and style Print / Save as PDF as the primary button.</action>
 <reason>Cleaner client-facing document that reads like an invoice, not an admin form — better for email-link opens and print/PDF.</reason>
</decision>

<decision>
 <category>UI/UX</category>
 <context>During a sales call, switching packs is easy, but resetting back to a clean demo state was too easy to miss unless you remember to use the admin page.</context>
 <action>Add a public “Reset current pack” action to the `/demo` hub (`POST /demo/reset`) so you can restore the model-home starter content without leaving the pitch flow.</action>
 <reason>Reduces demo friction and prevents the next meeting from starting with a prospect-edited/dirty model home.</reason>
</decision>

<decision>
 <category>UI/UX</category>
 <context>Admin users don’t need the “Theme Preview” live iframe block while editing branding settings.</context>
 <action>Remove the “Theme Preview” section from the Filament “Branding & Theme” settings page and delete its backing Blade view.</action>
 <reason>Reduces admin panel clutter and removes an unused preview surface from the settings flow.</reason>
</decision>

<decision>
 <category>UI/UX</category>
 <context>After saving a new Product Part, the admin sidebar still showed Booking/Ops items until a hard refresh.</context>
 <action>On ManageProductParts save, persist the setting then full-page redirect back to the Product Parts URL so Filament rebuilds navigation from the new product_part.</action>
 <reason>Livewire form saves don’t re-evaluate sidebar canAccess(); a redirect is the smallest fix that matches how people expect “Save” to apply packaging changes.</reason>
</decision>

<decision>
 <category>UI/UX</category>
 <context>Admin Accent/Primary backgrounds could be set dark while button and badge labels stayed hardcoded near-black, and nested section/card layers had no paired ink tokens.</context>
 <action>Publish on-primary, on-primary-light, on-accent, and on-accent-inverse CSS vars (auto near-black #1a1a1a or near-white #f2f2f2 from luminance). Backgrounds stay CMS colors; labels on those fills never use accent-as-text. Primary CTAs use accent fill + on-accent label; primary badges use primary fill + on-primary label.</action>
 <reason>Background-only admin controls stay simple while stacked surfaces stay readable — colorful paints, B/W type only.</reason>
</decision>

<decision>
 <category>UI/UX</category>
 <context>3-step process numbers used yellow fill + black text-stroke and sometimes showed a decorative star inside “02”.</context>
 <action>Render step numbers as plain near-black type with no stroke/fill color, and strip non-alphanumeric characters from the number field.</action>
 <reason>Step indices are structure, not accent highlights — colorful outlined numerals compete with real CTAs.</reason>
</decision>

<decision>
 <category>UI/UX</category>
 <context>Site typography and section spacing drifted from the Figma token tables; admin still offered a single brand font and an Editorial serif theme that conflicted with the fixed Heading/Body/Tagline system.</context>
 <action>Lock fonts to Hanken Grotesk (Heading Medium / Body Regular) and IBM Plex Mono (Tagline Regular). Encode Figma type scale and device spacing as plain CSS classes in design-tokens.css (letter-spacing as % of font-size). Remove brand_font admin control and Editorial theme (legacy editorial → clean). Wire hero, three_step, service_matrix, cta_banner, and layout chrome to those roles.</action>
 <reason>One global CSS source of truth matches Figma; proportional tracking survives breakpoint size changes; no admin font picker means demos can’t accidentally break the brand type system.</reason>
</decision>

<decision>
 <category>UI/UX</category>
 <context>Token media queries initially used Figma frame widths (744 / 1440), so laptop widths never hit “desktop” type/spacing and the Care Suite still mixed Tailwind gaps.</context>
 <action>Device tiers are 0 / 640 / 1200 everywhere: type scale and spacing vars in design-tokens.css, plain-CSS grids (.grid-services 1→2→3, .grid-steps 1→3, .grid-hero stacked→12-col), and the page-builder layout picker (tab: 640px, new desk: 1200px replacing lg: in PageBlocks::layoutClasses). Section/card rhythm comes from .space-section / .space-card / .stack-* instead of ad-hoc Tailwind py/mt/gap.</action>
 <reason>One tier system means type, spacing, and column counts flip at the same widths on real phones/tablets/laptops. Tailwind's own sm/md/lg stay available for one-off chrome (e.g. the header nav still swaps to the hamburger at lg, since that depends on whether the links fit, not on the type scale).</reason>
</decision>

<decision>
 <category>UI/UX</category>
 <context>Only hero / three_step / service_matrix / cta_banner were moved onto Figma type+spacing tokens; the other pre-made blocks still used Tailwind text-* / py-* / gap-* so About, FAQ, proof, stats, etc. looked like a different design system.</context>
 <action>Wire every public block (about, faq, gallery, image_text_split, rich_text, icon_feature, logo_cloud, neighborhood_proof, review_spotlight, stat_band, testimonial_grid, testimonial_quote, trust_bar) to .type-* / .space-section / .space-card / .stack-* / .grid-split|services|stats using the same Heading / Body / Tagline role map.</action>
 <reason>Pre-made blocks are the product’s “model home rooms” — they must share one type and spacing language or demos look half-finished.</reason>
</decision>

## 2026-07-29 (anti-slop design rule)

<decision>
 <category>UI/UX</category>
 <context>AI-generated UI drifted toward generic SaaS patterns (gradients, brand-colored text, decorative backgrounds, mixed heading weights). Needed a persistent rule so every new page/block starts from a defined system.</context>
 <action>Ship `.cursor/rules/anti-slop-design.mdc` (always apply). Before UI work: AskQuestion for fonts (heading / paragraph / button / tagline) and typography scale (Large → Figma 28305-260, Regular → 28334-265). Lock text to Content/Base and Content/Inverted grayscale tokens in `design-tokens.css`; section backgrounds `#FFFFFF` or `#000000` only unless user says otherwise; spacing from Figma 28297-253 via existing token utilities; WCAG 2.1 AA; no checker/dot/grid decorative backgrounds; single weight per heading element.</action>
 <reason>Turns anti-slop from advice into enforced workflow — fonts and scale are chosen per project, but color/spacing/type structure stay predictable and non-generic.</reason>
</decision>

<decision>
 <category>UI/UX</category>
 <context>AI defaults often add soft card shadows and layered elevation on every block, which reads as generic SaaS slop and fights the flat B/W section system.</context>
 <action>Add anti-slop rule §9: shadows minimal by default — no box-shadow on cards/sections unless functionally needed (dropdown, focus) or user explicitly requests; prefer flat surfaces, spacing, and 1px borders over elevation.</action>
 <reason>Flat black/white sections already create hierarchy; decorative shadows are a common AI tell and add visual noise without improving conversion.</reason>
</decision>

<decision>
 <category>UI/UX</category>
 <context>Public pages used Tailwind max-w-7xl (1280px) instead of the intended desktop content cap; horizontal padding needed to live inside that shell.</context>
 <action>Add `--layout-max-width: 1400px` and `.layout-container` in design-tokens.css (centered, full width below cap). Pair with `.space-inline` / `.space-section` for Figma horizontal padding inside the box. Replace public `max-w-7xl` shells with `.layout-container`; document in anti-slop rule §5.</action>
 <reason>1400px matches the design spec; token-based inner padding keeps content off the edges on wide screens without ad-hoc Tailwind px-*.</reason>
</decision>

<decision>
 <category>UI/UX</category>
 <context>Card grids used inconsistent column counts (3-col desktop always, stats with 4-col) instead of a simple count-based rule.</context>
 <action>Horizontal card grids: mobile 1col, tablet 2col, desktop 2col when card count is 1/2/4 else 3col. Ship `.grid-cards` + `data-card-count` in design-tokens.css; wire icon_feature, service_matrix, testimonial_grid, neighborhood_proof, gallery, three_step; document as anti-slop rule §6.</action>
 <reason>Predictable card rhythm — 4 cards become a clean 2×2 on desktop, 6 cards become 3×2, no AI-style auto-fit mush.</reason>
</decision>

<decision>
 <category>UI/UX</category>
 <context>Card grid gaps used a single `gap` token that did not match the Figma horizontal/vertical split.</context>
 <action>Card grids (`.grid-cards`, `.grid-steps`): `column-gap: var(--space-lg)` always; `row-gap: var(--space-lg)` on mobile, `row-gap: var(--space-xl)` from tablet (640px+) up; document in anti-slop rule §6.</action>
 <reason>Side-by-side cards breathe on lg; stacked rows separate on xl — matches the spacing token intent.</reason>
</decision>

## 2026-07-30 (demo ops seed data)

<decision>
 <category>Business_Logic</category>
 <context>After Load / Restore model home, admin Operations looked empty on sales calls — each niche only seeded a lead, project, milestones, and photos, so Crews, Proposals, Invoices, Equipment, and Time Entries were blank screens mid-pitch.</context>
 <action>Added shared `Database\Seeders\Niches\Concerns\SeedsDemoOps` trait, called from all eight niche `SampleProjectSeeder`s. Each model home now seeds one crew (assigned to the sample project), one sent proposal, one sent invoice with line items, two equipment rows, and two to three time entries. Amounts derive from the pack's existing `contract_value` (proposal total and invoice total both equal it); time entries use a per-niche labour rate capped so labour stays roughly a third of contract and no shift exceeds ~8 hours. Because time entries populate `labor_cost`, the helper also sets `material_cost` from a per-niche `material_share` — otherwise the projects table would report a ~90% profit margin on the bigger jobs. Seeding is idempotent via `updateOrCreate` on stable keys. Covered by `NicheModelHomeRestoreTest` across all eight packs.</action>
 <reason>One shared helper instead of eight copies keeps the ops story consistent and cheap to extend; deriving money from `contract_value` keeps every niche's numbers believable without hard-coding amounts per pack.</reason>
</decision>

## 2026-07-31 (demo funnel leads, photos, staff)

<decision>
 <category>Business_Logic</category>
 <context>Admin video walkthrough looked empty: funnel/charts had one booked lead, Progress Photos had caption-only placeholders, and Users showed only the admin account.</context>
 <action>Extended `SeedsDemoOps` for all eight niches: seed 3 Partial + 3 Qualified + 3 Contacted + 1 Lost leads (staggered over 7 days, niche service/neighborhoods); copy compressed Unsplash JPEGs from `database/seeders/assets/progress-photos` into `storage/app/public/progress-photos` via `seedDemoProgressPhotos()`; seed three demo staff users (Sales, Operations, Bookkeeper) at `sales@demo.local` / `ops@demo.local` / `books@demo.local` with password `pass`. Covered by `NicheModelHomeRestoreTest`.</action>
 <reason>Model-home restore already wipes and reseeds — putting chart filler, real photo paths, and role examples in the shared trait keeps every niche camera-ready without hand-editing eight packs.</reason>
</decision>

## 2026-07-31 (admin shell padding)

<decision>
 <category>UI/UX</category>
 <context>Admin content shell used 160px horizontal padding on desktop, leaving too much empty space around list pages like Projects.</context>
 <action>Set `--fi-shell-inline` to 16px (mobile), 40px (tablet 768px+), and 80px (desktop 1200px+) in `resources/css/filament/admin/theme.css`.</action>
 <reason>Gives the table more usable width while keeping breathing room off the edges on each breakpoint.</reason>
</decision>

## 2026-07-31 (admin row action buttons)

<decision>
 <category>UI/UX</category>
 <context>Admin table row actions (View, Edit, etc.) rendered as plain text links with no padding or border, so they did not read as clickable buttons.</context>
 <action>In AdminPanelProvider, configure all Filament tables via `modifyUngroupedRecordActionsUsing` so ungrouped record actions use `->button()->outlined()` (padding + stroke). Header primary CTAs stay filled.</action>
 <reason>One panel-wide default matches the Invoices table treatment without repeating button/outlined on every resource; row actions stay secondary to the main page CTA.</reason>
</decision>

## 2026-07-31 (estimate modal Projects vs Maintain pricing)

<decision>
 <category>CRO</category>
 <context>The Instant Valuation popup let visitors pick Projects or Maintain, but both showed the same teaser range — so the toggle felt broken and undercut trust before the full estimator.</context>
 <action>Apply a suite rate in the modal Alpine math so Maintain discounts vs Projects. Pass `scope` on the “Lock In Free Site Visit” link. Full `/estimate` wizard still uses real per-service multipliers.</action>
 <reason>Matches the product story (one-time work costs more than recurring plans) without pretending the teaser is the final quote.</reason>
</decision>

## 2026-08-01 (V2: data export Track A only)

<decision>
 <category>Business_Logic</category>
 <context>Needed to resolve whether self-serve full data export applies to Track A (Own it), Track B (Rent it), or both.</context>
 <action>Self-serve full data export is **Track A only** (V2 **X-01**). Track B stays “no export without Getwebfield super-admin consent.” After Track B → Track A buy-out, self-serve export applies. Updated `docs/pricing-master.md`, `product-stages.md`, and `suggestions.md`.</action>
 <reason>Owning the product includes owning the data dump; renting keeps leverage and chargeback protection until they buy out.</reason>
</decision>

## 2026-08-01 (V2: full client data export required)

<decision>
 <category>Business_Logic</category>
 <context>V2 client installs must let the business take their records with them (backup, leave hosting, own their CRM data) without waiting on Getwebfield.</context>
 <action>Require a self-serve admin **full data export** as a V2 exit criterion (`product-stages.md`, suggestions **X-01**). Default direction: one download package with business records (leads, projects, invoices, proposals, CMS/catalog, settings snapshot) plus uploaded files. Exact format (CSV ZIP vs JSON) and who can click Export still TBD at build time.</action>
 <reason>Matches “upload to their hosting / own their business” — data portability is part of a real production handoff, not a SaaS-only luxury.</reason>
</decision>

## 2026-08-01 (V2: defer estimate/portal rate limits)

<decision>
 <category>Business_Logic</category>
 <context>V2 client-install hardening list included rate limits on `/estimate` and `/portal` (S-03/S-04).</context>
 <action>Do not require estimate or portal rate limits for V2 exit. Mark S-03/S-04 deferred in `suggestions.md` and `product-stages.md`.</action>
 <reason>Owner priority is low-work handoff; spam throttles are optional later, not a go-live blocker for base client installs.</reason>
</decision>

## 2026-08-01 (product stages V1–V4)

<decision>
 <category>Business_Logic</category>
 <context>Need one clear scoreboard for demo vs client install vs trial vs SaaS so agents and humans do not mix “video showcase,” “upload to their hosting,” and “self-serve trial.”</context>
 <action>Lock four stages in `product-stages.md`: V1 base demo (our site), V2 base client installment (their site), V3 SaaS demo/trial (our site), V4 SaaS client installment (their site). Current position: V1 nearly done, V2 next, V3 rules-only, V4 future. Agents must update that file whenever stage status or exit criteria change; enforced by `.cursor/rules/product-stages.mdc`.</action>
 <reason>Separates sales-demo work from production handoff and from later SaaS, matching the low-work client-upload business model before trial/multi-tenant build.</reason>
</decision>

## 2026-08-01 (settings forms full width)

<decision>
 <category>UI/UX</category>
 <context>Site Settings pages (e.g. Estimator & Pricing) sat in a `max-w-5xl` column inside an already full-width admin shell, leaving a large empty band on the right.</context>
 <action>Remove the `max-w-5xl` wrapper from `settings-form.blade.php` and `manage-homepage.blade.php` so Site Settings + Homepage Content use the full content shell width. Page builder Create/Edit keep their own `max-w-5xl` (longer reading line for block editors).</action>
 <reason>Pricing, branding, and homepage section lists are multi-column / wide UI — they need horizontal room, not a prose-style max width.</reason>
</decision>

## 2026-08-01 (admin-editable popup teaser rates)

<decision>
 <category>CRO</category>
 <context>Homepage Instant Valuation low/high $/unit rates and the Maintain-vs-Projects ratio were hard-coded in the layout Blade, so owners could not tune the sticker price without a developer.</context>
 <action>Add three `pricing` settings — `estimate_teaser_low_per_unit`, `estimate_teaser_high_per_unit`, `estimate_teaser_maintain_multiplier` — edited under Admin → Estimator & Pricing → “Homepage popup teaser”. Layout modal reads them via `setting()` (defaults 0.85 / 1.45 / 0.62). Seed the same defaults in all eight niche packs. Covered by `CtaPopupModalTest`.</action>
 <reason>Lets the business owner set the window-sticker range in admin; full estimator math stays on the real service multipliers.</reason>
</decision>

## 2026-07-31 (per-industry favicons)

<decision>
 <category>UI/UX</category>
 <context>Every industry pack shared one hard-coded fallback favicon (an inline green "L" data-URI in `partials/seo.blade.php`), so a Summit Roof Co or ShieldBug Pest demo tab still showed the lawn mark — and the admin panel had no favicon at all. On a multi-tab sales pitch across `/demo` packs, every tab looked identical.</context>
 <action>Shipped eight brand-matched SVG icons at `public/images/favicons/{lawn,cleaning,roofing,pressure,windows,gutters,fence,pest}.svg` — rounded-square badge in the pack's `color_primary` with a bold industry glyph in its `color_accent` (grass blades, sparkle, roof + house, spray wand, pane grid, gutter trough + droplet, pickets, shield + bug). New `niche_favicon()` helper in `app/helpers.php` returns the uploaded `favicon` branding setting when one exists, else the active pack's icon (falling back to `lawn.svg` if a pack ships without one). `partials/seo.blade.php` emits it as `rel="icon" type="image/svg+xml"` plus `/favicon.ico` as the legacy alternate, and `AdminPanelProvider` uses `->favicon(fn () => niche_favicon())`. Branding page helper text now says an empty upload falls back to the industry icon. Covered by `NichePackTest`.</action>
 <reason>Icons live as static files under `public/`, not as seeded uploads, so switching packs needs no storage copy, no reseed, and no `storage:link` dependency — the tab icon follows `active_niche` instantly, while an uploaded client favicon still wins for real installs.</reason>
</decision>

## 2026-08-01 (invoice date-range search)

<decision>
 <category>UI/UX</category>
 <context>Staff had no way to answer "which invoices went out today / this week / between these two dates" — the Invoices list only offered a status dropdown and a free-text search on invoice number and client name. The original request was for the page to open showing only today's invoices, with From/To as plain text inputs, plus a new "invoice type" dropdown next to them.</context>
 <action>Added a `Filter::make('issue_date')` range filter (`issued_from` / `issued_until`) to `InvoicesTable`, laid out on one row beside the existing status filter via `filtersFormColumns(3)` (range spans 2 of 3 columns). Added `ListInvoices::getTabs()` presets — All / Today / This Week / This Month / Overdue — each with a live count badge. Renamed the `issue_date` column label from "Issued" to "Invoice Date". Covered by `InvoiceListFilterTest`.</action>
 <reason>Three deliberate departures from the request. (1) **All is the default tab, not Today** — this business does not invoice every day, so a Today-first page would open empty on a quiet day and read as broken or as data loss, especially in a sales demo; Today is still one click. (2) **Date pickers, not text inputs** — typed dates are ambiguous between DD/MM and MM/DD, and a misread date fails silently as an empty result set with nothing to explain it; the picker still allows typing, so nothing is lost. From/To are cross-bounded (`maxDate`/`minDate`) so an inverted range cannot be entered at all. (3) **No new "invoice type" column** — confirmed with the owner that "type" meant the existing Draft/Sent/Paid/Overdue/Cancelled status, so status was moved beside the dates instead of adding a redundant field and migration. Filters are independent and AND-ed, so status alone, dates alone, or both together all work.</reason>
</decision>

## 2026-08-01 (client dashboard milestone redesign)

<decision>
 <category>UI/UX</category>
 <context>The client project dashboard did not read as a milestone page. Four structural problems: the referral banner sat between the overall-progress bar and the step track, splitting the status story in half; progress was stated twice in two visual languages; the "timeline" was `space-y-6` stacked cards with no connecting rail, so each step was an isolated island; and nothing marked which step was current, even though that is the only thing a client opens the page to check.</context>
 <action>Merged the progress bar and step track into one uninterrupted block inside the header card. Added a "Happening now / Then" callout naming the current and next step, with the current step's marker enlarged and ringed in both the track and the timeline. Rebuilt the timeline with a real per-row vertical rail (node + connecting line drawn per row rather than one absolute element, so it survives cards of very different heights). Added a `completed_at` column so completed steps carry real dates. Moved the referral banner below the timeline. Every step marker sits in a fixed `h-14` slot and every title in a fixed two-line box so titles, dates, and badges align straight across the track. Covered by `ClientDashboardTimelineTest`.</action>
 <reason>`updated_at` was rejected as a completion date because any later edit to a milestone would silently move the date shown to the client. The rail is drawn per row rather than as a single absolute line so a step with six photos next to one with none cannot break it. `space-y-6` was removed from the timeline container because sibling margins would cut visible gaps into the rail.</reason>
</decision>

<decision>
 <category>Business_Logic</category>
 <context>Staff should not have to hand-maintain a completion date on top of setting a milestone's status.</context>
 <action>`Milestone::booted()` stamps `completed_at` when status becomes Completed and clears it when a step is reopened. An explicitly supplied date always wins, which is how the demo seeders stagger dates off `started_at`. The Filament form exposes `completed_at` only while status is Completed, as a correction field.</action>
 <reason>Two sources of truth for "when was this done" drift immediately. Clearing on reopen matters most: a stale completion date left showing on a reopened step actively misinforms the client.</reason>
</decision>

## 2026-08-01 (X-01 full data export)

<decision>
 <category>Business_Logic</category>
 <context>X-01 left two things TBD at build time: the export format (CSV ZIP vs JSON) and who may click Export.</context>
 <action>Ship one ZIP: `data/<table>.csv` per business table, `uploads/` mirroring the public disk, `manifest.json` (row counts, upload totals, site snapshot), and a plain-text `README.txt`. Built synchronously by `DataExportService` and streamed with `deleteFileAfterSend()`. Clicking Export needs the new Admin-only permission key `settings.data_export`, so a Bookkeeper or Ops manager only gets it if an admin grants it deliberately.</action>
 <reason>CSV is what the promise actually means — the owner can open it in Excel, hand it to an accountant, or import it elsewhere; a SQL dump only helps a developer. Uploads ride in the same archive so photo/logo paths in the CSVs still resolve. Synchronous keeps it working on cPanel hosting with no persistent queue worker, matching how escalations already run. Export is the whole business in one file, so it sits at the same trust level as Users and Settings.</reason>
</decision>

<decision>
 <category>Business_Logic</category>
 <context>Self-serve export is Track A only, but the codebase had no notion of which track an install was sold on.</context>
 <action>Add `App\Enums\LicenseTrack` + `config/license.php` + `APP_LICENSE_TRACK` (documented in `.env.example`). Default is Track A in local (so demos and dev can walk the feature) and Track B everywhere else, so a production install must opt in at handoff. Deliberately environment-driven, never a `settings` row.</action>
 <reason>If the track lived in the CMS settings table, a Track B client could switch their own export on from the admin panel — that is exactly the leverage the rent-it pricing protects. Failing closed on non-local environments makes a forgotten env var a support call, not a data walkout.</reason>
</decision>

<decision>
 <category>UI/UX</category>
 <context>Track B installs have no self-serve export. The options were to hide the admin page entirely or show it in a locked state.</context>
 <action>Show Admin → Data Export on both tracks. Track A gets the Download button; Track B gets an amber panel saying the records are still theirs, that Getwebfield will run the export on request, and that buying out to Track A turns it into a one-click download. The contents list renders either way. `DataExportService::generate()` throws on Track B regardless of the UI.</action>
 <reason>A missing menu item reads as "the product cannot do this"; a locked one reads as "this is what buying out unlocks" — an honest answer to a real question and a buy-out prompt at the exact moment it is being asked. Hiding the button is presentation; the server-side guard is the actual boundary.</reason>
</decision>

## 2026-08-01 (scoped resource exports)

<decision>
 <category>Business_Logic</category>
 <context>Staff asked for a way to pull just the data on the page they are looking at — e.g. this week's invoices for the bookkeeper — without downloading the whole business.</context>
 <action>Add `DataExportService::generateScoped()` and an `ExportsResourceData` Filament concern. Resource list pages for Operations and Site Content get an **Export CSV** header action (ZIP when related tables ship together, e.g. invoices + line items). The export uses Filament's `getTableQueryForExport()` so tabs, filters, search, and sort carry through. Track A only, same as X-01; permission is the resource key (`resource.invoices`), not `settings.data_export`. Users, Settings, and Access Codes are excluded.</action>
 <reason>Day-to-day exports are a different job from a full backup — a Bookkeeper with invoice access should not need Admin-level full-export permission to hand a CSV to an accountant. Respecting the active filters avoids the "I exported leads but got everyone including deleted" surprise. Sensitive configuration tables stay on the full-export path only.</reason>
</decision>

## 2026-08-01 (milestones filters always visible)

<decision>
 <category>UI/UX</category>
 <context>On /admin/milestones, Status and Project filters were tucked behind the funnel icon, so staff had an extra click before they could narrow a growing multi-project list.</context>
 <action>Set `MilestonesTable` filters to `FiltersLayout::AboveContent` with `filtersFormColumns(2)` and `deferFilters(false)`, so both dropdowns sit above the table and apply as soon as they change.</action>
 <reason>Two filters do not need a modal. Showing them up front matches how staff actually use this screen — pick a project or status first, then scan the rows.</reason>
</decision>

## 2026-08-02 (admin light mode shell)

<decision>
 <category>UI/UX</category>
 <context>Filament's theme switcher offers light mode, but the custom admin shell hardcoded dark Figma colors on `:root` — so light mode kept a charcoal sidebar and near-black page background while Filament rendered light-mode nav labels (dark gray text), producing unreadable contrast.</context>
 <action>Split shell CSS variables: default `:root` uses light surfaces (white sidebar/header, gray-50 content, dark ink for logo/title/back); `html.dark` keeps the Lux charcoal shell. Logo, page title, subheading, back arrow, and danger-zone border all read from mode-aware `--fi-shell-*` tokens.</action>
 <reason>Respects Filament's built-in light/dark toggle without rewriting every component — dark mode keeps the Figma look; light mode matches Filament's native surfaces so nav, tables, and forms stay legible.</reason>
</decision>
