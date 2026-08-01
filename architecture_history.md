## 2026-07-30
- Added Filament Vite theme (`resources/css/filament/admin/theme.css` + `->viteTheme()`) so custom admin Blade Tailwind utilities compile; see decisions.md / bug_history.md 2026-07-30 (filament theme).
- Efficiency audit (P-01–P-10): two-layer Setting cache with cacheable misses, CACHE_STORE=file, new App\Support\SiteVersion owning site_version + bulk suppression, widget queries consolidated to grouped aggregates with lazy charts/tables, and an ops index migration; see decisions.md 2026-07-30 (efficiency audit).
- Fixed audit bugs F-09–F-14: estimator sqft mount/slider, multi-service custom threshold, invoice number collision after soft delete, booked lead status preservation, book-without-lead guard, niche pack custom thresholds; see bug_history.md 2026-07-30 (audit fixes).
- Added five industry niche packs (pressure washing, window cleaning, gutters, fence/deck, pest) with demo content seeders; generalized DatabaseSeeder to seed any active APP_NICHE pack; see decisions.md.
- Created architecture.md as the live codebase blueprint documenting stack, folders, components, data flow, and integrations.
- Added suggestions.md backlog and suggestions-backlog.mdc Cursor rule for pre-decision agent checks; see decisions.md 2026-07-30.
- Implemented full model-home restore in NicheLoader (D-01): wipe pitch data, reseed pack, gate on APP_DEMO_HUB; see decisions.md.
- Fixed F-01–F-08 functional bugs: Lead referral fillable, homepage publish guard, proposal decline guard, booking slot validation + lock, ReservedPageSlugs, BookingMatrix cursor copy; see bug_history.md 2026-07-30.
- Admin Filament theme now self-hosts Geist Sans + Geist Mono (Fontsource) and applies Figma Text/sm, Text/xs, and mono page-title styles; see decisions.md 2026-07-30 (admin figma typography).
- Restyled public invoice page layout (flat document, notes at bottom, primary print CTA); see decisions.md 2026-07-30 (invoice public page).

## 2026-07-31
- Extended SeedsDemoOps with funnel filler leads, Unsplash progress-photo assets under database/seeders/assets/progress-photos, and three demo staff users; see decisions.md 2026-07-31 (demo funnel leads, photos, staff).
- Added per-industry favicons under public/images/favicons plus the niche_favicon() helper wired into the SEO partial and Filament panel; see decisions.md 2026-07-31 (per-industry favicons).
- Admin shell horizontal padding reduced via `--fi-shell-inline` (16/40/80px); see decisions.md 2026-07-31 (admin shell padding).
- Panel-wide Filament table row actions use button()->outlined(); see decisions.md 2026-07-31 (admin row action buttons).
- Homepage Instant Valuation Maintain vs Projects now multiplies into the teaser range; see decisions.md / bug_history.md 2026-07-31 (estimate modal).

## 2026-08-01
- Homepage Instant Valuation teaser rates moved to admin pricing settings (`estimate_teaser_*`) on ManagePricing; pack defaults seeded; see decisions.md 2026-08-01 (admin-editable popup teaser rates).
- Site Settings + Homepage Content forms use full content shell width (removed max-w-5xl); see decisions.md 2026-08-01 (settings forms full width).
- Structural slate (`color_slate` → `--color-slate-800`) is clamped with `body_ink()` so light CMS picks cannot wash out captions; niche restore clears `color_slate`; see bug_history.md 2026-08-01 (unreadable structural slate captions).
- Fixed public progress-photo / proposal showcase image URLs to use public_url() instead of Storage::url() so APP_URL port mismatches do not break images; see bug_history.md 2026-08-01 (dashboard progress photo URLs).
- Added `product-stages.md` (V1–V4 stage scoreboard) plus always-apply `.cursor/rules/product-stages.mdc`; see decisions.md 2026-08-01 (product stages).
- Invoices list gained an `issue_date` From/To range filter beside the status filter plus All/Today/Week/Month/Overdue tab presets (All default), and its defaultSort moved from `created_at` to the displayed `issue_date`; see decisions.md / bug_history.md 2026-08-01 (invoice date-range search).
- Fixed the footer danger-zone Delete action on all Edit screens: the record is now bound explicitly in `HasPrimarySaveAndDangerDelete` (custom Blade views bypass Filament's record injection), and `EditUser` reaches the base action via a trait alias instead of `parent::`; see bug_history.md 2026-08-01 (danger-zone delete).
