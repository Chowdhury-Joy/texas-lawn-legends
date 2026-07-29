## 2026-07-30
- Added five industry niche packs (pressure washing, window cleaning, gutters, fence/deck, pest) with demo content seeders; generalized DatabaseSeeder to seed any active APP_NICHE pack; see decisions.md.
- Created architecture.md as the live codebase blueprint documenting stack, folders, components, data flow, and integrations.
- Added suggestions.md backlog and suggestions-backlog.mdc Cursor rule for pre-decision agent checks; see decisions.md 2026-07-30.
- Implemented full model-home restore in NicheLoader (D-01): wipe pitch data, reseed pack, gate on APP_DEMO_HUB; see decisions.md.
- Fixed F-01–F-08 functional bugs: Lead referral fillable, homepage publish guard, proposal decline guard, booking slot validation + lock, ReservedPageSlugs, BookingMatrix cursor copy; see bug_history.md 2026-07-30.
