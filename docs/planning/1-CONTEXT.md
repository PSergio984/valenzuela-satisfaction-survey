# Phase 1 Context: Maintenance, QOL, and Performance

This document captures the implementation decisions for Phase 1, focusing on bug fixes, quality-of-life (QOL) improvements, and performance optimizations.

## 1. Decisions & Strategy

### A. Public Survey List (QOL & UX)
- **Pagination**: Implement standard Laravel pagination (12 items per page) in `SurveyController@index`.
- **Search/Filter**: Add a simple text search for survey titles and descriptions.
- **Frontend**: Create a reusable `Pagination` component in React using Shadcn UI primitives. Add "New" badges for surveys created within the last 48 hours.

### B. Scalable Exports (Performance)
- **Queued Exports**: Transition Excel exports to use Maatwebsite's `FromQuery` with `Exportable` and `ShouldQueue`.
- **PDF Generation**: Keep PDF generation synchronous for now but optimize data fetching.
  If timeouts occur, transition to an asynchronous "Generation Started" UI flow.
- **Storage**: Use the `private` disk for temporary export files before download.

### C. N+1 & Metric Optimization (Performance)
- **Caching**: Implement a 5-minute cache for expensive survey metrics (completion rate, average time) using Laravel's Cache facade.
- **Eager Loading**: Update `SurveyController` and Filament resources to always eager load `questions` and `responses` when metrics are displayed in lists.
- **Database Aggregates**: Use `withCount()` and `withAvg()` in Eloquent queries instead of PHP-level collection manipulation.

### D. Admin Dashboard (Filament QOL)
- **Advanced Filters**: Add range filters for ratings and date filters for submissions in `ResponsesTable`.
- **Stats Widgets**: Update dashboard widgets to use the optimized database aggregates defined in step C.

### E. Robust Duration Tracking (Bug Fix)
- **Strategy**: Instead of relying solely on session-based start times, the frontend will now send a `started_at` timestamp (ISO format) captured when the survey form first mounts.
- **Backend Validation**: The backend will validate that `started_at` is before `now()` and within a reasonable timeframe (max 24 hours).

### F. Dependency Updates (Maintenance)
- **Strategy**: Update all dependencies to their latest compatible versions.
- **Backend**: Update `composer.json` dependencies, targeting minor/patch updates for Laravel, Filament, and Inertia. Evaluate major jumps (Filament 4->5, Inertia 2->3) if stability permits.
- **Frontend**: Update `package.json` dependencies via `npm update`. Address breaking changes from major jumps (Vite 7->8, React 19 minor updates).

## 2. Reusable Assets & Patterns
- **Shadcn UI**: Use existing `Card`, `Button`, and `Badge` components.
- **Inertia**: Follow the established `Inertia::render` pattern.
- **Pest**: New features must include feature tests reproducing the performance/UX gap before fixing.

## 3. Evidence Traceability
- **Pagination gap**: `SurveyController.php` lines 23-41 uses `->get()` instead of `->paginate()`.
- **N+1 Risk**: `Survey.php` lines 122-135 performs counts/avgs on relationships without pre-loading.
- **Duration Tracking**: `SurveyController.php` lines 96-105 relies on `session()->get($sessionKey)`.

## 4. Next Steps
- **Research**: Identify the specific Filament classes for filter injection.
- **Planning**: Create the detailed `1-PLAN.md` with atomic tasks.
- **Execution**: Start with N+1 optimizations as they provide the foundation for faster lists.
