# Roadmap

## Phase 1: Maintenance, QOL, and Performance
**Goal:** Improve platform stability, performance, and user experience through backend optimizations and frontend enhancements.

**Requirements:** [PERF-01, PERF-02, PERF-03, QOL-01, BUG-01, UX-01, UX-02]

**Plans:** 2 plans

### Plans
- [ ] 01-01-PLAN.md — Backend, Admin, and Performance (located at docs/planning/1-01-PLAN.md)
- [ ] 01-02-PLAN.md — Frontend, UX, and Bug Fixes (located at docs/planning/1-02-PLAN.md)

## Phase 2: Reliability, Security, and Advanced Insights
**Goal:** Harden the platform for production scale, improve security posture, and add proactive monitoring features with a focus on perceived performance.

**Requirements:** [RELI-01, SEC-01, SEC-02, TEST-01, ALERT-01, PERF-04, PERF-05, DOC-01]

### Plans
- [ ] 02-01-PLAN.md — Reliability & Security
- [ ] 02-02-PLAN.md — Testing, Alerts, and UX Performance

## Phase 3: Premium UI Overhaul, Interactive Dashboards & Access Control
**Goal:** Transform the bland admin and public interfaces into a premium, high-end visual experience with distinct layouts, React-powered interactive dashboards, and robust Role & Permission management.

**Requirements:** [UI-01, UI-02, AUTH-01, DASH-01, DASH-02, DASH-03]

### Plans
- [ ] 03-01-PLAN.md — Admin UI, Theme & Role Management
- [ ] 03-02-PLAN.md — Public Portal Visual Polish
- [ ] 03-03-PLAN.md — Advanced Aesthetic Dashboard (Draft)

### Requirements Detail (Phase 3)
- **UI-01**: Admin Panel Theme Overhaul (Custom Tailwind theme, custom fonts, advanced widgets, grid layouts).
- **UI-02**: Public Portal Visual Polish (Animations, micro-interactions, premium styling).
- **AUTH-01**: Restore and implement a working Role & Permission management resource in the admin sidebar.
- **DASH-01**: Custom Sidebar & Layout Differentiation (Distinct background colors, high-end borders, and refined typography).
- **DASH-02**: React-Powered Dashboard Widgets (Interactive, high-taste visual components for analytics).
- **DASH-03**: Aesthetic Polish (Motion effects, premium shadows, and micro-interactions via Framer Motion).

### Requirements Detail (Phase 2)
- **RELI-01**: Transition PDF generation to queued background jobs.
- **SEC-01**: Audit and harden file upload handling in controllers.
- **SEC-02**: Implement PII scrubbing for system logs and .env encryption.
- **TEST-01**: Install and configure Vitest/RTL for frontend component testing.
- **ALERT-01**: Implement "Silent" Detractor Alerts (database notifications for ratings < 3).
- **PERF-04**: Implement Skeleton loaders for all Inertia page transitions (Heuristic UX).
- **PERF-05**: Audit and expand Inertia prefetching for sidebar and primary navigation.
- **DOC-01**: Populate README and finalize codebase documentation.

### Requirements Detail (Phase 1)
- **PERF-01**: Implement N+1 optimizations for Survey metrics
  (use withCount, withAvg, and Caching).
- **PERF-02**: Implement Pagination and Search on the Public Survey List.
- **PERF-03**: Transition Excel exports to Queued Exports (Maatwebsite).
- **QOL-01**: Add advanced filters to Filament tables (Surveys, Responses).
- **BUG-01**: Implement robust duration tracking (Frontend-initiated started_at).
- **UX-01**: UI/UX Overhaul for the Survey Taking form (Progress Bar, spacing, validation).
- **UX-02**: Add "New" badges to recent surveys in the index.
