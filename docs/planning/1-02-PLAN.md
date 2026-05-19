---
phase: 01-maintenance-qol-perf
plan: 02
type: execute
wave: 2
depends_on: ["01-01"]
files_modified:
  - app/Http/Controllers/SurveyController.php
  - resources/js/pages/surveys/show.tsx
  - app/Http/Requests/StoreResponseRequest.php
  - app/Models/Response.php
  - resources/js/pages/surveys/index.tsx
  - resources/js/components/pagination.tsx
  - docs/planning/1-VALIDATION.md
autonomous: true
requirements: [PERF-02, BUG-01, UX-01, UX-02]
user_setup: []

must_haves:
  truths:
    - "Public survey list displays 12 items per page with working search"
    - "Surveys created within 48 hours display a 'New' badge"
    - "Survey duration is accurately tracked via frontend-captured 'started_at'"
    - "The survey taking form has a functional progress bar and polished UI"
  artifacts:
    - path: "resources/js/components/pagination.tsx"
      provides: "Reusable Shadcn-based pagination"
    - path: "docs/planning/1-VALIDATION.md"
      provides: "Phase completion evidence"
  key_links:
    - from: "resources/js/pages/surveys/show.tsx"
      to: "app/Http/Requests/StoreResponseRequest.php"
      via: "started_at payload"
---

<objective>
Enhance the frontend user experience with improved survey lists, robust duration tracking, and a polished survey-taking interface.

Purpose: Improve respondent engagement and data accuracy.
Output: Searchable/paginated survey index, fixed duration tracking bug, and an overhauled survey form.
</objective>

<execution_context>
@$HOME/.gemini/get-shit-done/workflows/execute-plan.md
@$HOME/.gemini/get-shit-done/templates/summary.md
</execution_context>

<context>
@.planning/ROADMAP.md
@.planning/STATE.md
@docs/planning/1-CONTEXT.md
@docs/planning/1-RESEARCH.md
@docs/planning/1-01-PLAN.md
</context>

<tasks>

<task type="auto">
  <name>Task 3: Robust Duration Tracking Fix</name>
  <files>resources/js/pages/surveys/show.tsx, app/Http/Requests/StoreResponseRequest.php, app/Models/Response.php</files>
  <action>
    1. Update the survey show page (React) to capture the `started_at` timestamp in `useEffect` when the component first mounts (per D-E in 1-CONTEXT.md).
    2. Pass `started_at` in the Inertia post payload.
    3. Add validation for `started_at` in `StoreResponseRequest` (required, date, before:now).
    4. Update the `Response` model to calculate and store the duration based on `started_at` and the current timestamp.
  </action>
  <verify>
    <automated>php artisan test --filter=ResponseTest</automated>
  </verify>
  <done>Response duration is accurately tracked based on the actual start time captured on the client.</done>
</task>

<task type="auto">
  <name>Task 4: Public Survey List Improvements</name>
  <files>app/Http/Controllers/SurveyController.php, resources/js/pages/surveys/index.tsx, resources/js/components/pagination.tsx</files>
  <action>
    1. Update `SurveyController@index` to use `paginate(12)` and filter by a `search` query parameter.
    2. Create a reusable `Pagination` component in React using Shadcn UI.
    3. Update the index page to display the `Pagination` component and a search input.
    4. Implement "New" badges for surveys created within the last 48 hours (per UX-02).
  </action>
  <verify>
    <automated>php artisan test --filter=SurveyControllerTest</automated>
  </verify>
  <done>The public survey list is searchable, paginated, and highlights recent surveys.</done>
</task>

<task type="auto">
  <name>Task 5: Survey Form UI/UX Overhaul</name>
  <files>resources/js/pages/surveys/show.tsx</files>
  <action>
    1. Implement a sticky progress bar at the top of the survey form that updates as required questions are answered.
    2. Refine spacing and typography for better readability.
    3. Improve the display of validation errors using Shadcn components.
    4. Exercise discretion to modernize the form layout while maintaining Shadcn consistency (per user hint).
  </action>
  <verify>
    <human-check>Visit a survey page, verify progress bar movement, and test responsiveness.</human-check>
  </verify>
  <done>The survey taking experience is visually polished and provides clear feedback on progress.</done>
</task>

<task type="auto">
  <name>Task 6: Final Validation</name>
  <files>docs/planning/1-VALIDATION.md</files>
  <action>
    Create the `docs/planning/1-VALIDATION.md` file summarizing:
    - Requirements covered (PERF-01 to UX-02).
    - Summary of test results (Pass/Fail).
    - Visual confirmation of UI changes.
    - Performance improvements (query reduction).
  </action>
  <verify>
    <automated>ls docs/planning/1-VALIDATION.md</automated>
  </verify>
  <done>Validation document is generated and provides a clear record of phase success.</done>
</task>

</tasks>

<threat_model>
## Trust Boundaries

| Boundary | Description |
|----------|-------------|
| Public Respondent → API | Untrusted input (answers, started_at) crosses into the system. |

## STRIDE Threat Register

| Threat ID | Category | Component | Disposition | Mitigation Plan |
|-----------|----------|-----------|-------------|-----------------|
| T-01-03 | Tampering | `started_at` payload | mitigate | Validate `started_at` is a valid date and not in the future. |
| T-01-04 | Tampering | Pagination/Search Params | mitigate | Use standard Laravel validation and Eloquent parameter binding. |
</threat_model>

<verification>
- Public survey list works with pagination and search.
- "New" badges appear correctly.
- Survey UI improvements verified via human check.
- Final validation document exists.
</verification>

<success_criteria>
- PERF-02, BUG-01, UX-01, and UX-02 are fully implemented.
- All Phase 1 tests pass.
- `docs/planning/1-VALIDATION.md` is complete.
</success_criteria>

<output>
Create `.planning/phases/01-maintenance-qol-perf/01-02-SUMMARY.md` when done
</output>
