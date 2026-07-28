# Bug History

## 2026-07-28

<bug>
 <category>CRO</category>
 <symptom>ClientCroEnhancementsTest and CtaPopupModalTest failed — hero neighborhood checker, price preview widget, and global estimate modal were missing from rendered HTML.</symptom>
 <root_cause>Product-parts and niche-pack refactors replaced the hero primary CTA with a plain link and dropped the layout modal + service_matrix calculator without updating tests.</root_cause>
 <prevention_rule>When gating booking CTAs behind product_part_at_least(2), keep the CRO widgets (neighborhood form, modal dispatch, price preview) inside that same gate — never swap them for a dumb link.</prevention_rule>
</bug>

<bug>
 <category>UI/UX</category>
 <symptom>Changing Product Part and clicking Save left the admin sidebar showing the old menu until a full browser refresh.</symptom>
 <root_cause>Save ran as a Livewire AJAX update that only re-rendered the settings form; Filament builds nav visibility from canAccess()/ProductFeatures once per full page load.</root_cause>
 <prevention_rule>After saving product_part (or any setting that gates Filament navigation), redirect with a full page load so the admin shell rebuilds.</prevention_rule>
</bug>

<bug>
 <category>UI/UX</category>
 <symptom>After contrast tokens shipped, primary CTAs looked white/empty and primary eyebrows had dark-on-dark unreadable text.</symptom>
 <root_cause>New .btn-primary / text-on-* / text-accent-ink rules lived in @layer components or relied on Tailwind generating theme utilities; soft theme + missing utilities dropped accent fills and left inherited near-black on emerald badges.</root_cause>
 <prevention_rule>Keep accent fills on bg-yellow-400 (CMS-remapped), define brand ink utilities outside @layer, and never replace accent-on-primary badge text (text-yellow-400) with an unguaranteed utility.</prevention_rule>
</bug>

<bug>
 <category>UI/UX</category>
 <symptom>After typography tokens shipped, Care Suite still looked like old Tailwind sizing — fonts/spacing didn’t “show up” and felt non-responsive on laptops.</symptom>
 <root_cause>Token media queries used Figma frame widths 744/1440, so desktop type never applied under 1440px; cards still used Tailwind p/mt/gap and sm/lg grids instead of token classes.</root_cause>
 <prevention_rule>Always drive public design tokens and page-builder tiers at 0 / 640 (tab:) / 1200 (desk:), and put section rhythm on .space-* / .grid-* / .type-* — never leave Tailwind text-*, py-*, or lg: as the source of truth for a tokenized section.</prevention_rule>
</bug>

<bug>
 <category>Code</category>
 <symptom>Site rendered with no fonts, no spacing, and no responsive behaviour at all — looked like the new typography tokens had never shipped.</symptom>
 <root_cause>A leftover public/hot file from a killed `npm run dev` made the layout point every stylesheet at http://[::1]:5173, a dev server that was no longer running, so the page loaded zero CSS instead of the built public/build assets.</root_cause>
 <prevention_rule>When styles appear completely missing, check public/hot first and delete it if no Vite dev server is listening — verify the rendered HTML links /build/assets/app-*.css before debugging any CSS.</prevention_rule>
</bug>

<bug>
 <category>CRO</category>
 <symptom>Hero ZIP field placeholder was cut off mid-word (“ENTER ZIP CODE OR NEIGHBORHOO”), hiding what the visitor is supposed to type into the primary estimate CTA.</symptom>
 <root_cause>The field used Tagline Large (18px IBM Plex Mono, uppercase); monospace is much wider per character than the old sans placeholder, so the text overflowed the max-w-xl input.</root_cause>
 <prevention_rule>Any mono/Tagline text inside a fixed-width control must be checked at the desktop token size — drop to .type-tagline and widen the field rather than letting a conversion field truncate its prompt.</prevention_rule>
</bug>
