# Decisions

## 2026-07-28

<decision>
 <category>UI/UX</category>
 <context>Admin top-left brand label always showed the site name ("Texas Lawn Legends"), which made it harder to know which page you were on without looking at the main heading.</context>
 <action>Override Filament’s logo Blade view to show the current page heading (e.g. "Edit About Us"). A Livewire render hook captures the heading from the active Filament page before the layout renders the topbar. Login/simple pages still show the site name. Browser tab title still ends with the site brand via brandName().</action>
 <reason>Matches how people scan admin tools (page name in the chrome), without changing document titles or the public site brand.</reason>
</decision>
