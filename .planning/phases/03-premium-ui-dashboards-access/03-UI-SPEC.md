---
status: draft
phase: 03
name: premium-ui-dashboards-access
---

# UI Design Contract: Phase 03

This document defines the visual and interaction contract for Phase 03. It is the source of truth for implementation.

## 1. Visual Tokens

### Spacing
Scale: 8-point (multiples of 4px)
- **4px**: Micro-spacing (icon to text)
- **8px**: Small gaps, padding (compact)
- **16px**: Standard padding, gutters (default)
- **24px**: Section spacing, large padding
- **32px**: Large gaps between major components
- **48px**: Page header margins
- **64px**: Hero section margins

### Typography
- **Primary Font**: `Hanken Grotesk` (Replacing Source Sans 3)
- **Heading Font**: `Hanken Grotesk` (Replacing Lexend)
- **Monospace Font**: `Black Mono` (For Admin Branding/UI accents)
- **Line Heights**:
  - Body: 1.5
  - Headings: 1.2

| Token | Size | Weight | Usage |
|-------|------|--------|-------|
| `text-sm` | 14px | 400 | Standard body text, meta data |
| `text-base`| 16px | 400 | UI labels, secondary text |
| `text-lg` | 20px | 700 | Component headings |
| `text-2xl`| 28px | 700 | Page titles |

### Color Palette (60/30/10)
- **Dominant (60%)**: `Zinc 50` (Main area background - "Airy")
- **Secondary (30%)**: `Slate 900` (Sidebar background - "Deep Professional")
- **Accent (10%)**: `High-Contrast Mono (Black)` (Buttons, Active states, Branding)

| Element | Light Mode | Dark Mode | Source |
|---------|------------|-----------|--------|
| Main Background | `#fafafa` (Zinc 50) | `#020617` (Slate 950) | D-14 |
| Sidebar Background | `#0f172a` (Slate 900) | `#020617` (Slate 950) | D-14 |
| Sidebar Border | `#1e293b` (Slate 800) | `#0f172a` (Slate 900) | Phase 3 Spec |
| Primary Action | `#000000` (Black) | `#ffffff` (White) | D-13 |
| Surface/Card | `#ffffff` (White) | `#0f172a` (Slate 900) | Standard |
| Destructive | `#dc2626` (Red 600) | `#ef4444` (Red 500) | Recommendation |

## 2. Component Inventory

### Layout Patterns
- **Bento Grid**: Used for the Advanced Aesthetic Dashboard. Cards with varied spans (1x1, 2x1, 2x2).
- **Split Layout**: Used for Resource views (Surveys, Responses). Sidebar for list, Main for detail.
- **Vertical Slide-Up**: Used for Public Survey transitions via Framer Motion.

### New Components
- **Liquid Progress Bar**: Continuous pulse, smooth width transitions.
- **Premium Chart Widgets**: React-powered interactive charts (e.g., using Recharts or Tremor).
- **Shield Permission Grid**: Restoration of the standard Shield management UI.

## 3. Interaction & Motion

| Trigger | Transition | Duration | Easing |
|---------|------------|----------|--------|
| Next Question | Slide-Up (Vertical) | 300ms | `easeOut` |
| Hover Card | Subtle Lift + Border | 200ms | `easeInOut` |
| Sidebar Toggle| Slide + Fade | 250ms | `cubic-bezier`|
| Page Load | Skeleton Reveal | 400ms | `linear` |

## 4. Copywriting Contract

| Interaction | Copy |
|-------------|------|
| **Primary CTA (Public)** | "Submit Response" |
| **Primary CTA (Admin)** | "Create Survey" |
| **Empty State** | "No surveys found. Start by creating one to begin gathering insights." |
| **Error State** | "Oops! Something went wrong. Please try again or contact support if the issue persists." |
| **Destructive Action** | "Delete Survey? This action cannot be undone and all response data will be lost." |
| **Success Alert** | "Survey published successfully. Copy the link below to share." |

## 5. Registry & Safety

- **Registries**: `shadcn/ui` (Official)
- **Third-party Blocks**: `none`
- **Safety Gate**: No external registries declared.

## 6. Verification Checklist

- [ ] Typography updated to Hanken Grotesk in `app.css`.
- [ ] Sidebar background set to Slate 900.
- [ ] Main background set to Zinc 50.
- [ ] Framer Motion vertical transitions implemented for survey steps.
- [ ] Dashboard uses Bento Grid layout.
- [ ] Manager role restricted to "Own Only" surveys.
