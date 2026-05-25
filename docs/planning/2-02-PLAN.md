# Phase 2-02 Plan: Testing, Alerts, and UX Performance

Goal: Enhance perceived performance and implement automated monitoring.

## 1. Testing Setup
- **Task 1.1**: Install and configure Vitest and React Testing Library.
  - Dependencies: `vitest`, `@testing-library/react`, `@testing-library/jest-dom`, 
    `jsdom`.
  - Config: Update `vite.config.ts` to support Vitest.
- **Task 1.2**: Create a base test suite for `SurveyIndex.tsx`.

## 2. Advanced Insights: Detractor Alerts
- **Task 2.1**: Create `app/Observers/AnswerObserver.php`.
  - Logic: If `question->type === 'rating'` and `value < 3`, trigger a 
    notification.
- **Task 2.2**: Register the observer in `AppServiceProvider.php`.
- **Task 2.3**: Verify "Silent" (DB only) delivery to survey owners.

## 3. Heuristic UX: Skeletons & Prefetching
- **Task 3.1**: Implement Skeleton loaders for `SurveyIndex`.
  - Strategy: Use `router.on('start', ...)` or local state to show skeletons 
    during search/pagination.
- **Task 3.2**: Implement Skeleton loaders for `SurveyShow`.
- **Task 3.3**: Navigation Audit.
  - Action: Ensure `prefetch` is used in `AppSidebar` (Logo), `AppHeader`, 
    and all primary links.

## 4. Documentation
- **Task 4.1**: Populate `README.md` with project setup and architecture overview.
- **Task 4.2**: Finalize any remaining [TODO] items in `docs/codebase/*.md`.

## 5. Verification
- **Test 1**: Run `npm test` and ensure Vitest is working.
- **Test 2**: Submit a 1-star rating and verify the silent notification appears 
  in Filament.
- **Test 3**: Verify page transitions feel instant or provide visual feedback 
  via skeletons.
