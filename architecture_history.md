## 2026-07-30
- Added Filament Vite theme (`resources/css/filament/admin/theme.css` + `->viteTheme()`) so custom admin Blade Tailwind utilities compile; see decisions.md / bug_history.md 2026-07-30 (filament theme).
- Efficiency audit (P-01–P-10): two-layer Setting cache with cacheable misses, CACHE_STORE=file, new App\Support\SiteVersion owning site_version + bulk suppression, widget queries consolidated to grouped aggregates with lazy charts/tables, and an ops index migration; see decisions.md 2026-07-30 (efficiency audit).
- Fixed audit bugs F-09–F-14: estimator sqft mount/slider, multi-service custom threshold, invoice number collision after soft delete, booked lead status preservation, book-without-lead guard, niche pack custom thresholds; see bug_history.md 2026-07-30 (audit fixes).
- Added five industry niche packs (pressure washing, window cleaning, gutters, fence/deck, pest) with demo content seeders; generalized DatabaseSeeder to seed any active APP_NICHE pack; see decisions.md.
- Created architecture.md as the live codebase blueprint documenting stack, folders, components, data flow, and integrations.
- Added suggestions.md backlog and suggestions-backlog.mdc Cursor rule for pre-decision agent checks; see decisions.md 2026-07-30.
- Implemented full model-home restore in NicheLoader (D-01): wipe pitch data, reseed pack, gate on APP_DEMO_HUB; see decisions.md.
- Fixed F-01–F-08 functional bugs: Lead referral fillable, homepage publish guard, proposal decline guard, booking slot validation + lock, ReservedPageSlugs, BookingMatrix cursor copy; see bug_history.md 2026-07-30.
- Added shared SeedsDemoOps seeder concern and wired it into all eight niche SampleProjectSeeders so Load / Restore model home stocks admin Operations (crew, proposal, invoice, equipment, time entries); see decisions.md.
