# Phase 3: Premium UI Overhaul, Interactive Dashboards & Access Control - Context

**Gathered:** 2026-06-17
**Status:** Ready for planning

<domain>
## Phase Boundary

Transform the current functional but "bland" admin and public interfaces into a premium, high-end visual experience. This includes a custom-themed admin panel, React-powered interactive dashboard widgets (Bento Grid layout), a polished public survey-taking portal with smooth vertical transitions, and robust role-based access control.

</domain>

<decisions>
## Implementation Decisions

### Admin UI & Aesthetics
- **D-13: Typography & Branding:** Replace default fonts with **Hanken Grotesk**. Set the primary accent color to **High-Contrast Mono (Black)**.
- **D-14: Layout Differentiation:** Sidebar will use a deep, professional background (e.g., Slate 900) while the main area remains clean and airy (e.g., Zinc 50).
- **D-15: Resource Layouts:** Use "Grid" and "Split" layouts for resources (Surveys, Responses) to move away from a traditional database table feel.

### Interactive Dashboards
- **D-16: Visual Style:** Implement an **Advanced Aesthetic Dashboard** using a **Bento Grid** layout.
- **D-17: React Integration:** Mount React components manually into Blade-based Filament widgets for maximum control and performance, bypassing the complexity of a full Inertia-in-Filament bridge.

### Public Portal
- **D-18: Animation Strategy:** Use **Framer Motion** for survey transitions.
- **D-19: Transitions:** Implement **Slide-Up (Vertical)** transitions between survey questions.
- **D-20: Progress Visualization:** Use a **Liquid (Continuous)** progress bar with a subtle pulse to indicate survey completion status.

### Access Control
- **D-21: Engine:** Restore and configure **Filament Shield** (`bezhansalleh/filament-shield`).
- **D-22: Management UI:** Provide the full **Shield Permission Grid** for granular control.
- **D-23: Scoping Logic:** Implement "Own Only" scoping for the **Manager** role, ensuring they only see surveys and responses they have created.

### Folded Todos
- **Filament Spotlight Check:** Confirmed that `pxlrbt/filament-spotlight` is the correct package and is already in `composer.json`.
- **AdminPanelProvider Audit:** Identified that `AdminPanelProvider.php` needs updates to font and color definitions to match the new Hanken Grotesk + Black Mono theme.

</decisions>

<canonical_refs>
## Canonical References

**Downstream agents MUST read these before planning or implementing.**

### Core Requirements
- `ROADMAP.md` — Phase 3 goals and detailed requirements.
- `.planning/STATE.md` — Current project progress and previous phase outcomes.

### UI & Styling
- `docs/planning/3-CONTEXT.md` — (Self-reference) Current implementation decisions.
- `docs/codebase/CONVENTIONS.md` — Line limit (120 chars) and coding style.
- `package.json` — Verified dependencies for Framer Motion and Tailwind 4.

### Access Control
- `app/Providers/Filament/AdminPanelProvider.php` — Current plugin registrations for Shield and Spotlight.

</canonical_refs>

<code_context>
## Existing Code Insights

### Reusable Assets
- **Framer Motion (React):** Already present in `package.json`. Can be used for vertical slide-up transitions in `resources/js/pages/surveys/*.tsx`.
- **Filament Apex Charts:** Already installed and registered. Can be used as a fallback or wrapper for data visualization.

### Established Patterns
- **Inertia.js:** Used for the public portal. All survey-taking animations should be implemented within the React components.
- **Filament Plugins:** Shield and Spotlight patterns are established; new resource-specific permissions should follow Shield conventions.

### Integration Points
- **Admin Dashboard:** `app/Filament/Admin/Pages/Dashboard.php` and associated widgets.
- **Filament Theme:** `resources/css/filament/admin/theme.css` — central place for custom styling.
- **Public Survey Views:** `resources/js/pages/surveys/show.tsx` for transition logic.

</code_context>

<specifics>
## Specific Ideas

- **"Swift & Minimal":** The goal for transitions is to feel fast but high-end (e.g., `duration: 0.3` for vertical slides).
- **"Bento Feel":** Dashboard cards should have varied sizes and clear, card-based borders to mimic a modern, modular interface.

</specifics>

<deferred>
## Deferred Ideas

- **Complex Analytics:** Deeper metric breakdowns or machine learning insights (belongs in a future analytics phase).
- **Multi-language Surveys:** Internationalization of the survey-taking form.

### Reviewed Todos (not folded)
- `tests/Feature/ResponseTest.php` ->todo() and `SurveyExportQueuedTest.php` ->todo() — testing gaps to be addressed in a future quality/testing-focused phase.

</deferred>

---

*Phase: 3-Premium UI Overhaul, Interactive Dashboards & Access Control*
*Context gathered: 2026-06-17*
