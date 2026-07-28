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
