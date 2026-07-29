# Bug History

## 2026-07-30 (audit fixes)

<bug>
 <category>CRO</category>
 <symptom>Four niche packs (roofing, windows, gutters, fence) opened the estimator at 500 sqft even when pack max was lower — first Continue failed validation before the prospect touched anything.</symptom>
 <root_cause>EstimatorWizard::mount() used max(setting min, hardcoded 500) instead of the pack minimum; slider step was also hardcoded to 50.</root_cause>
 <prevention_rule>Mount sqft from sqftBounds min only; derive slider step from pack range via sqftStep computed property.</prevention_rule>
</bug>

<bug>
 <category>Business_Logic</category>
 <symptom>Multi-service Full Estimate mode never flagged custom quote when summed total exceeded estimate_custom_threshold.</symptom>
 <root_cause>EstimatePricingEngine::calculateMany() OR'd per-service is_custom only and never compared totalHigh to the threshold.</root_cause>
 <prevention_rule>calculateMany() must apply the same threshold and max-sqft checks against the combined total.</prevention_rule>
</bug>

<bug>
 <category>Business_Logic</category>
 <symptom>Soft-deleting the newest invoice caused UniqueConstraintViolationException on the next create — admin 500 on the money path.</symptom>
 <root_cause>Invoice::generateNextNumber() used static::query() which excludes soft-deleted rows while invoice_number has a UNIQUE index.</root_cause>
 <prevention_rule>Number generation must use withTrashed() when finding the last invoice number for a year prefix.</prevention_rule>
</bug>

<bug>
 <category>Business_Logic</category>
 <symptom>Booked leads silently downgraded to Qualified when the user went Back then Continue; scheduled_at stayed set so scopeStalled could not catch them.</symptom>
 <root_cause>persistLead() always recomputed status from estimateLow on every step advance, overwriting Booked.</root_cause>
 <prevention_rule>persistLead() must preserve Booked status when booked flag or existing lead status is Booked.</prevention_rule>
</bug>

<bug>
 <category>CRO</category>
 <symptom>Booking confirmation screen appeared even when no lead row existed (leadUuid null) — silently lost customer.</symptom>
 <root_cause>book() returned success from the transaction without requiring leadUuid or creating a lead.</root_cause>
 <prevention_rule>book() must reject when leadUuid is null before confirming the slot.</prevention_rule>
</bug>

<bug>
 <category>Business_Logic</category>
 <symptom>Fence pack never showed an instant price; windows pack showed prices on only a tiny pane range — instant-estimate demo broken on sales calls.</symptom>
 <root_cause>estimate_custom_threshold in FencePack (180) and WindowsPack (80) were authored as size units not dollars; fence cheapest high (~867) always exceeded 180.</root_cause>
 <prevention_rule>Niche pack custom thresholds must be dollar amounts validated against cheapest realistic high estimate at pack minimum size.</prevention_rule>
</bug>

## 2026-07-30

<bug>
 <category>CRO</category>
 <symptom>Referral codes from ?ref= on /estimate were dropped — leads saved without referred_by_code even though EstimatorWizard passed it.</symptom>
 <root_cause>Lead model $fillable omitted referred_by_code, so mass assignment silently stripped the field on create/update.</root_cause>
 <prevention_rule>When adding a DB column used in persistLead() or similar mass-assignment paths, add it to $fillable in the same change and cover with a feature test.</prevention_rule>
</bug>

<bug>
 <category>UI/UX</category>
 <symptom>Unpublished homepage still rendered at / while other CMS pages correctly 404'd.</symptom>
 <root_cause>PageController::home() used Page::home() without checking is_published; show() already enforced publish state.</root_cause>
 <prevention_rule>Home and CMS show actions must share the same is_published guard when a homepage record exists.</prevention_rule>
</bug>

<bug>
 <category>Business_Logic</category>
 <symptom>Declining an already-accepted proposal overwrote status to declined.</symptom>
 <root_cause>ProposalController::decline() updated status unconditionally; accept() had an idempotency guard but decline() did not.</root_cause>
 <prevention_rule>Public proposal mutations must guard on current status — never downgrade accepted to declined.</prevention_rule>
</bug>

<bug>
 <category>Business_Logic</category>
 <symptom>Estimator book() accepted arbitrary date/time strings outside the offered BookingMatrix grid.</symptom>
 <root_cause>book() parsed and saved any date/time without validating against BookingMatrix::slots().</root_cause>
 <prevention_rule>book() must call BookingMatrix::isOfferedSlot() before persisting scheduled_at.</prevention_rule>
</bug>

<bug>
 <category>Business_Logic</category>
 <symptom>Two concurrent book() calls could double-book the same slot.</symptom>
 <root_cause>Slot availability used a plain exists() check outside a transaction with no row lock.</root_cause>
 <prevention_rule>Booking confirmation must run inside DB::transaction with lockForUpdate on conflicting booked leads for that scheduled_at.</prevention_rule>
</bug>

<bug>
 <category>Code</category>
 <symptom>Admin could create CMS pages with slugs that collide with static routes (demo, proposals, invoices, api).</symptom>
 <root_cause>PageForm slug notIn list was incomplete vs routes/web.php.</root_cause>
 <prevention_rule>Keep reserved CMS slugs in App\Support\ReservedPageSlugs synced with static routes in web.php.</prevention_rule>
</bug>

<bug>
 <category>Code</category>
 <symptom>BookingMatrix loop mutated Carbon::today via addDay() on a shared cursor reference.</symptom>
 <root_cause>$cursor->addDay() mutates in place; without copy() the date cursor could drift or affect Carbon::today().</root_cause>
 <prevention_rule>In BookingMatrix day iteration, always advance with $cursor->copy()->addDay().</prevention_rule>
</bug>

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

<bug>
 <category>UI/UX</category>
 <symptom>Homepage sections below the hero (About, FAQ, proof, stats, testimonials) still looked like the old Tailwind kit after the typography system shipped.</symptom>
 <root_cause>Token wiring stopped at four blocks; the remaining pre-made Blade blocks kept text-3xl / tracking-* / px-6 py-section as their type and spacing source of truth.</root_cause>
 <prevention_rule>When shipping a public design-token system, update every resources/views/blocks/*.blade.php in the same pass — never leave a subset of pre-made blocks on Tailwind type/spacing.</prevention_rule>
</bug>
