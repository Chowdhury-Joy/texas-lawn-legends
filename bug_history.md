# Bug History

## 2026-08-01 (unreadable structural slate captions)

<bug>
 <category>UI/UX</category>
 <symptom>Client dashboard progress-photo captions (e.g. "Primary bath in progress" / "JUL 27, 2026") were nearly invisible light gray on white.</symptom>
 <root_cause>Layout remaps `--color-slate-800` from CMS `color_slate`. Live branding had `color_slate=#d6d6d6`, so every `text-slate-800` washed out. Branding UI incorrectly described all color tokens as "background only," and niche restore did not clear a bad slate value.</root_cause>
 <prevention_rule>Treat `color_slate` as body ink on white: clamp with `ColorContrast::bodyInk()` / `body_ink()` before publishing `--color-slate-800`, document it as text (not fill) in Manage Branding, clear `color_slate` on niche restore, and assert layout falls back when the setting is too light.</prevention_rule>
</bug>


## 2026-08-01 (dashboard progress photo URLs)

<bug>
 <category>UI/UX</category>
 <symptom>Client dashboard progress photos showed broken images (alt text only, e.g. "Scope checklist signed") even though Unsplash demo JPEGs existed under `storage/app/public/progress-photos`.</symptom>
 <root_cause>`resources/views/dashboard.blade.php` built `<img src>` with `Storage::disk('public')->url()`, which prefixes `APP_URL` (`http://localhost`). The local app runs on `http://localhost:8123`, so the browser requested port 80 and got nothing. Same pattern in `proposals/show.blade.php` image showcase blocks. Other public views already used `public_url()`.</root_cause>
 <prevention_rule>Never use `Storage::url()` / `Storage::disk('public')->url()` in public Blade for visitor-facing assets — use `public_url()` (root-relative `/storage/...`) so host/port mismatches with `APP_URL` cannot break images. Cover with an assert that dashboard HTML contains `/storage/...` and not `http://localhost/storage/...`.</prevention_rule>
</bug>


## 2026-07-31 (estimate modal scope price)

<bug>
 <category>CRO</category>
 <symptom>In the homepage Instant Valuation popup, toggling Projects vs Maintain changed the selected button but the dollar range stayed the same.</symptom>
 <root_cause>`serviceScope` only drove button CSS classes; `low()` / `high()` used a flat `sqft × rate` and never read the selected suite.</root_cause>
 <prevention_rule>Any control in the estimate teaser that looks like it affects price must multiply into `low()` / `high()` (or remove the control). Cover with an assert on the scope rate in `CtaPopupModalTest`.</prevention_rule>
</bug>

## 2026-07-30 (filament theme)

<bug>
 <category>UI/UX</category>
 <symptom>Crew Schedule (and other custom Filament Blade pages) rendered as bare white text on a black background — filter tabs looked like a sentence, amber alert had no box, cards had no borders.</symptom>
 <root_cause>Custom admin views use Tailwind utilities that are not in Filament's default stylesheet. The panel had no `->viteTheme()`, so those classes existed in HTML but never compiled to CSS.</root_cause>
 <prevention_rule>Any custom Filament Blade that uses Tailwind utilities must be covered by a panel Vite theme (`resources/css/filament/{panel}/theme.css` with `@source` for those views) registered via `->viteTheme()`, and covered by a compile-presence test.</prevention_rule>
</bug>

## 2026-07-30 (efficiency audit)

<bug>
 <category>Code</category>
 <symptom>Every absent setting key re-queried the database on each call, forever — no amount of cache warming helped.</symptom>
 <root_cause>`Setting::get()` stored `null` via `Cache::rememberForever`, and Laravel treats a cached `null` as a cache miss, so the closure re-ran on every call.</root_cause>
 <prevention_rule>Cache a payload that records existence (`['hit' => bool, 'value' => mixed]`) rather than the bare value, so a legitimately null value and a missing row stay distinguishable.</prevention_rule>
</bug>

<bug>
 <category>Code</category>
 <symptom>RecentActivity dashboard widget issued one extra query per visible row — the only genuine N+1 in the codebase, growing with data volume.</symptom>
 <root_cause>The table column renders `causer.name` but the underlying `Activity::query()` had no eager load.</root_cause>
 <prevention_rule>Any Filament table column using dot-notation relationship access must have a matching `->with()` on the widget/resource query.</prevention_rule>
</bug>

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

## 2026-07-30

<bug>
 <category>Code</category>
 <symptom>Filament Invoices list crashed with TypeError: InvoiceStatus::getIcon() must return string but returned Heroicon enum.</symptom>
 <root_cause>Filament v4 HasIcon expects string | BackedEnum | Htmlable | null; InvoiceStatus declared getIcon(): string while returning Heroicon cases.</root_cause>
 <prevention_rule>Enum getIcon() return types must match Filament's HasIcon contract — use Heroicon (or the full union), not string, when returning Heroicon cases.</prevention_rule>
</bug>

## 2026-08-01

<bug>
 <category>Code</category>
 <symptom>Filament Invoices list rows could appear out of order: the table sorted by `created_at` desc while displaying the `issue_date` column, so any back-dated or forward-dated invoice sat in a position its visible date did not justify.</symptom>
 <root_cause>`InvoicesTable` set `defaultSort('created_at', 'desc')` but exposed no `created_at` column — `issue_date` is the date staff see, and the two diverge as soon as an invoice is entered on a day other than its issue date.</root_cause>
 <prevention_rule>A table's defaultSort column must be one the table actually displays — never sort on a hidden timestamp while showing a different date column.</prevention_rule>
</bug>

<bug>
 <category>Code</category>
 <symptom>Every admin Edit screen that renders the footer danger-zone Delete crashed with 500: "DeleteAction::{closure}(): Argument #1 ($record) must be of type Model, null given". Hit `/admin/pages/{slug}/edit` and the Users edit screen.</symptom>
 <root_cause>Filament's `DeleteAction::setUp()` registers `hidden(static fn (Model $record) => $record->trashed())`. Filament normally injects the record when an action is rendered through its `Actions` schema component, but the custom Blade views for these pages loop `$this->getFormActions()` and echo each action directly, bypassing that injection — so the closure received null.</root_cause>
 <prevention_rule>An Action rendered outside Filament's own Actions schema component must be given its record explicitly via `->record(...)` — never assume Filament will inject it when a custom Blade view echoes the action.</prevention_rule>
</bug>

<bug>
 <category>Code</category>
 <symptom>The Users edit screen crashed with "Method App\Filament\Resources\Users\Pages\EditUser::getDangerDeleteAction does not exist", taking all five AccessPermissionSyncTest cases with it.</symptom>
 <root_cause>`EditUser::getDangerDeleteAction()` called `parent::getDangerDeleteAction()` to extend the base action, but that method is defined in the `HasPrimarySaveAndDangerDelete` **trait**, not in a parent class. PHP flattens traits into the using class, so `parent::` resolved to Filament's `EditRecord` — which has no such method — and fell through to Livewire's `__call`.</root_cause>
 <prevention_rule>Never use `parent::` to reach a trait method you are overriding — alias it in the `use` statement (`use T { m as protected baseM; }`) and call the alias.</prevention_rule>
</bug>
