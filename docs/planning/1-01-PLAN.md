# Plan 01-01: Backend Performance & Admin Optimization

Objective: Update dependencies, optimize backend performance (N+1 queries), and implement queued exports.

## Tasks

### Task 0: Dependency Update & Audit
- **Objective**: Bring core dependencies up to date to address technical debt.
- **Action**: 
    - Run `composer update` to update PHP dependencies (minor/patch).
    - Run `npm update` to update JS dependencies (minor/patch).
    - Verify application functionality and address any immediate breakage.
- **Verification**:
    - `php artisan about` (Check versions)
    - `npm list --depth=0` (Check versions)

### Task 1: Wave 0 Test Scaffolding
- **Objective**: Create failing Pest tests for Phase 1 requirements.
- **Action**:
    - Create `tests/Feature/SurveyControllerTest.php` with failing tests for pagination and search.
    - Create `tests/Unit/ExportJobTest.php` for queued export verification.
    - Create `tests/Feature/ResponseTest.php` for duration tracking validation.
- **Verification**:
    - `php artisan test` (Tests should fail/skip)

### Task 2: Performance Optimization & Admin Filters
- **Objective**: Eliminate N+1 queries and enhance admin table functionality.
- **Action**:
    - Update `Survey` and `Response` queries in Filament resources and controllers to use `withCount()` and `withAvg()`.
    - Implement caching for expensive metrics in the `Survey` model.
    - Add advanced range and date filters to Filament `ResponsesTable`.
- **Verification**:
    - `php artisan test --filter=SurveyControllerTest`
    - Manual check of Filament Admin UI.

### Task 3: Queued Exports Implementation
- **Objective**: Implement scalable, background Excel exports.
- **Action**:
    - Refactor `SurveyResponsesExport` to use `FromQuery` and `ShouldQueue`.
    - Update `SurveyExportController` to trigger queued exports and save to `private` disk.
    - Implement Filament `Notifications` to alert admins when the export is ready.
- **Verification**:
    - `php artisan test --filter=ExportJobTest`
    - `php artisan queue:listen` (Verify job execution)
