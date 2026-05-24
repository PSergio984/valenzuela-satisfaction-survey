# Phase 3-02 Plan: Public UI Visual Polish

Goal: Enhance the public survey portal with subtle, professional animations and styling.

## 1. Public Portal Overhaul
- **Task 1.1**: Install Framer Motion.
  - Run `npm install framer-motion`.
- **Task 1.2**: Implement Swift Transitions.
  - Refactor `SurveyShow.tsx` to wrap questions in `motion.div`.
  - Add transition variants (e.g., `initial={{ opacity: 0, y: 10 }}`, `animate={{ opacity: 1, y: 0 }}`).
- **Task 1.3**: Polish Visuals.
  - Review gradient usage and shadow aesthetics. 
  - Ensure dark mode consistency with the new slate/blue color palette.

## 2. Verification
- **Test 1**: Verify animations are "swift and minimal" (no jank).
- **Test 2**: Verify survey taking remains functional during transitions.
