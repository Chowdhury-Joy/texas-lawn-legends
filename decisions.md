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
