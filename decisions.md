# Decisions

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
