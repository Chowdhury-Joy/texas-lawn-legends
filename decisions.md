# Decisions

## 2026-07-28

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
