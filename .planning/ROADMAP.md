# Roadmap

## Phase 1: Maintenance, QOL, and Performance
**Goal:** Improve platform stability, performance, and user experience through backend optimizations and frontend enhancements.

**Requirements:** [PERF-01, PERF-02, PERF-03, QOL-01, BUG-01, UX-01, UX-02]

**Plans:** 2 plans

### Plans
- [ ] 01-01-PLAN.md — Backend, Admin, and Performance (located at docs/planning/1-01-PLAN.md)
- [ ] 01-02-PLAN.md — Frontend, UX, and Bug Fixes (located at docs/planning/1-02-PLAN.md)

### Requirements Detail
- **PERF-01**: Implement N+1 optimizations for Survey metrics (use withCount, withAvg, and Caching).
- **PERF-02**: Implement Pagination and Search on the Public Survey List.
- **PERF-03**: Transition Excel exports to Queued Exports (Maatwebsite).
- **QOL-01**: Add advanced filters to Filament tables (Surveys, Responses).
- **BUG-01**: Implement robust duration tracking (Frontend-initiated started_at).
- **UX-01**: UI/UX Overhaul for the Survey Taking form (Progress Bar, spacing, validation).
- **UX-02**: Add "New" badges to recent surveys in the index.
