# Phase 3 Context: Engaging UI Overhaul & Access Control

This document captures the implementation decisions for Phase 3, focusing on transforming the UI into a premium experience and restoring role-based access control.

## 1. Decisions & Strategy

### A. Admin UI Overhaul (Plugins & Power-Ups)
- **Theme**: Build a custom Filament Tailwind theme. We will replace the default fonts with a modern sans-serif (e.g., Inter) and refine the color palette.
- **Layout Differentiation**: 
  - **Sidebar**: Deep, professional background (Slate 900 or similar) with high-contrast text and refined icons.
  - **Main Area**: Clean, neutral background (Gray 50/Zinc 50) to create a clear visual hierarchy and "airy" feel.
- **Interactive Widgets (The "Taste" Factor)**: 
  - Leverage Filament's custom view widgets to embed **React components** (via Inertia or custom JS entry points).
  - Use these for core dashboard metrics to provide a more dynamic, "appealing" visual experience than standard static widgets.
- **Layouts**: Use "Grid" and "Split" layouts for resources (like Surveys and Responses) to make them look more like a dashboard than a raw database table.
- **Power-Ups**: 
  - Install `pxlrbt/filament-spotlight` for a `Cmd+K` global command palette (Completed in previous attempts but verified).
  - Install `awcodes/overlook` for quick-stat overview widgets.

### B. Access Control (Roles & Permissions)
- **Restoration**: The user noted that `filament-shield` was previously used but its files are missing. We will do a fresh installation of `bezhansalleh/filament-shield`.
- **Integration**: We will run the shield install command, which automatically generates the robust `RoleResource` with the checkbox grid UI. We will ensure this is properly linked in the admin sidebar.
- **Current State**: `spatie/laravel-permission` is already installed and seeded; Shield will sit perfectly on top of it.

### C. Public Portal Visual Polish (Swift & Minimal)
- **Animation Strategy**: Keep it simple. We will add lightweight, "swift" animations (e.g., using Framer Motion) to the survey-taking form. 
- **Transitions**: Questions will slide/fade in smoothly, rather than jarringly appearing.
- **Aesthetics**: Ensure the gradient and shadows are subtle and professional, matching the "Feedback Management System" generic branding established in Phase 2.

## 2. Reusable Assets & Patterns
- **Framer Motion**: Standardize on a single set of quick transition variants (e.g., `duration: 0.2`, `ease: "easeOut"`) to maintain the "swift" feel.
- **Filament Spotlight**: All new resources must be registered to appear in the Spotlight search.

## 3. Next Steps
- **Planning**: Create `3-01-PLAN.md` (Admin UI & Roles) and `3-02-PLAN.md` (Public UI Polish) detailing the exact packages to install and files to modify.