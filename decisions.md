# Decisions

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


<decision>
 <category>UI/UX</category>
 <context>Admin top-left brand label always showed the site name ("Texas Lawn Legends"), which made it harder to know which page you were on without looking at the main heading.</context>
 <action>Override Filament’s logo Blade view to show the current page heading (e.g. "Edit About Us"). A Livewire render hook captures the heading from the active Filament page before the layout renders the topbar. Login/simple pages still show the site name. Browser tab title still ends with the site brand via brandName().</action>
 <reason>Matches how people scan admin tools (page name in the chrome), without changing document titles or the public site brand.</reason>
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
