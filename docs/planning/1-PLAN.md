---
phase: 01-maintenance
plan: 01
type: execute
wave: 1
depends_on: []
files_modified: []
autonomous: true
requirements: [PERF-01, PERF-02, PERF-03, QOL-01, BUG-01, UX-01, UX-02]
must_haves:
  truths:
    - "Users can page through surveys and search by title."
    - "Surveys created within 48h show a 'New' badge."
    - "Taking a survey shows a progress bar and tracks exact start time."
    - "Admin can filter responses by rating and date."
    - "Exporting responses queues the job and doesn't time out."
    - "Admin dashboard and lists load fast (no N+1)."
  artifacts:
    - path: "app/Http/Controllers/SurveyController.php"
      provides: "Paginated and searchable surveys."
    - path: "app/Exports/ResponsesExport.php"
      provides: "Queued Excel export functionality."
    - path: "resources/js/pages/surveys/show.tsx"
      provides: "Frontend duration tracking and progress bar."
  key_links:
    - from: "resources/js/pages/surveys/show.tsx"
      to: "/api/surveys"
      via: "Post with started_at"
---

<objective>
Implement maintenance, QOL, and performance improvements for Phase 1.

Purpose: Improve platform stability, fix N+1 issues, enable scalable queued exports, and enhance the survey-taking UX.
Output: Optimized survey queries, queued exports, robust duration tracking, and a polished survey UI.
</objective>

<execution_context>
@$HOME/.gemini/get-shit-done/workflows/execute-plan.md
@$HOME/.gemini/get-shit-done/templates/summary.md
</execution_context>

<context>
@docs/planning/1-CONTEXT.md
@docs/planning/1-RESEARCH.md
</context>

<tasks>

<task type="auto">
  <name>Task 1: Backend Performance & Admin Filters</name>
  <files>app/Http/Controllers/SurveyController.php, app/Models/Survey.php, app/Filament/Admin/Resources/ResponseResource.php, app/Filament/Admin/Resources/SurveyResource.php</files>
  <action>
    Implement N+1 optimizations and admin filters per D-01, D-03, and D-04.
    - In `SurveyController@index`, replace `get()` with `paginate(12)` and add simple text search.
    - In `Survey.php`, implement 5-minute caching for expensive metrics (completion rate).
    - Update `SurveyController` and Filament resources to eager load metrics using `withCount('responses')` and `withAvg('answers', 'value')`.
    - In `ResponseResource.php`, add Filament range filters for ratings and date filters.
  </action>
  <verify>
    <automated>php artisan test --filter=SurveyControllerTest</automated>
  </verify>
  <done>Lists are paginated, search works, and Filament queries are free of N+1 issues.</done>
</task>

<task type="auto">
  <name>Task 2: Queued Exports implementation</name>
  <files>app/Exports/ResponsesExport.php, app/Filament/Admin/Resources/ResponseResource.php</files>
  <action>
    Transition Excel exports to use Maatwebsite's queue per D-02.
    - Refactor `ResponsesExport` to implement `FromQuery`, `ShouldQueue`, and `WithMapping`.
    - Update Filament action to dispatch the queued export.
    - Ensure temporary files use the `private` disk to protect sensitive data.
  </action>
  <verify>
    <automated>php artisan test --filter=ExportTest</automated>
  </verify>
  <done>Exporting large datasets queues a job instead of timing out.</done>
</task>

<task type="auto" tdd="true">
  <name>Task 3: Frontend UX & Duration Tracking</name>
  <files>resources/js/pages/surveys/show.tsx, resources/js/pages/surveys/index.tsx, resources/js/components/pagination.tsx, app/Http/Requests/StoreResponseRequest.php</files>
  <behavior>
    - Test 1: `started_at` is captured on mount and sent on submit.
    - Test 2: Progress bar updates as questions are answered.
    - Test 3: Surveys < 48 hours old render a "New" badge.
  </behavior>
  <action>
    Implement UI/UX overhaul and duration tracking per D-05 and user hints.
    - In `show.tsx`, capture `started_at` in `useEffect` and include in form payload. Add a progress bar component. Improve spacing and validation layout.
    - In `StoreResponseRequest.php`, validate `started_at` (before now, max 24h old).
    - In `index.tsx`, use a new `pagination.tsx` Shadcn component for navigation. Add "New" badges to recent surveys.
  </action>
  <verify>
    <automated>npm run test</automated>
  </verify>
  <done>Survey form has progress bar, records accurate duration, and index displays badges.</done>
</task>

</tasks>

<threat_model>
## Trust Boundaries

| Boundary | Description |
|----------|-------------|
| Client -> API | Untrusted input (started_at timestamp) crosses into the backend |
| API -> Storage | Exported data written to filesystem |

## STRIDE Threat Register

| Threat ID | Category | Component | Disposition | Mitigation Plan |
|-----------|----------|-----------|-------------|-----------------|
| T-01-01 | Tampering | `StoreResponseRequest` | mitigate | Validate `started_at` is a valid ISO date, <= now(), and within 24h. |
| T-01-02 | Info Disclosure | Queued Exports | mitigate | Store temporary and final export files on the `private` disk. |
</threat_model>

<verification>
- Check N+1 queries using Laravel Debugbar or Telescope.
- Verify queued job dispatches and completes successfully.
- Manually test survey flow to ensure `started_at` is recorded and progress bar visually updates.
</verification>

<success_criteria>
- Metrics use DB aggregates instead of collection loops.
- Public list has working pagination and search.
- Excel exports are offloaded to queue.
- Survey taking form tracks exact duration and looks polished.
</success_criteria>

<output>
Create `.planning/phases/01-maintenance/01-01-SUMMARY.md` when done
</output>