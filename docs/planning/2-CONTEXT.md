# Phase 2 Context: Reliability, Security, and Advanced Insights

This document captures the implementation decisions for Phase 2, focusing on 
system hardening, security, and "Heuristic UX" (perceived performance).

## 1. Decisions & Strategy

### A. Reliability & Scale (Queued PDFs)
- **Queued Generation**: Move all PDF exports to background jobs using Laravel 
  Queues.
- **UI Flow**: When an export is initiated, show a "Silent" success toast. The 
  file will be stored on the `private` disk, and the user will receive a 
  database notification (Filament bell) once the download link is ready.

### B. Security & Compliance
- **File Upload Audit**: Hardened validation for all file uploads in 
  `SurveyController` and admin resources to prevent RCE/insecure access.
- **PII Protection**: Implement log scrubbing to ensure sensitive survey 
  answers are not captured in `laravel.log`.

### C. Advanced Insights (Detractor Alerts)
- **Threshold**: Automated notifications will trigger for any survey response 
  with a rating < 3.
- **Delivery**: Alerts are "Silent-only" (Database notifications). No immediate 
  emails or external pings to minimize noise.

### D. Heuristic UX & Performance
- **Skeletons**: Prioritize Skeleton loaders over generic spinners or loaders 
  during Inertia page transitions and data fetching. Use Shadcn UI's 
  `Skeleton` component.
- **Prefetching**: Expand Inertia `prefetch` usage for all primary sidebar and 
  navigation links to ensure instant-feeling tab switching.

### E. Quality Assurance
- **Testing Framework**: Standardize on **Vitest + React Testing Library** for 
  frontend component testing.
- **Requirement**: New Phase 2 features must include regression tests before 
  implementation.

## 2. Reusable Assets & Patterns
- **Inertia Prefetch**: Standardize `<Link prefetch ...>` for all high-traffic 
  internal transitions.
- **Shadcn Skeleton**: Use for all "Heuristic UX" loading states.
- **Laravel Notifications**: Use for all background job completions.

## 3. Evidence Traceability
- **Line Length**: Mandated 120-character limit added to `CONVENTIONS.md`.
- **Export Pattern**: `app/Exports/SurveyResponsesExport.php` locked as the 
  foundational pattern.

## 4. Next Steps
- **Research**: Map the existing Inertia navigation to identify "blind spots" 
  lacking prefetching or skeletons.
- **Planning**: Create `2-PLAN.md` with tasks broken down by Reliability, 
  Security, and UX tracks.
