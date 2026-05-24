# RESEARCH.md

## 1. Filament Tailwind Theme Customization
- **Mechanism:** Filament v4 uses `filament/filament` and its panels are configured in `app/Providers/Filament/AdminPanelProvider.php`.
- **Customization:** Filament v4 has moved to Tailwind CSS v4. Theme customization is handled via the `tailwind.config.js` or directly via Tailwind CSS v4's CSS-based configuration if available in `resources/css/filament/admin/theme.css`.
- **Evidence:** `composer.json` shows `"filament/filament": "^4.0"`. `AdminPanelProvider.php` is the entry point.

## 2. Filament Shield Implementation
- **Goal:** Manage roles and permissions.
- **Implementation:**
  1. Install via `composer require bezhansalleh/filament-shield`.
  2. Publish config: `php artisan vendor:publish --tag=filament-shield-config`.
  3. Run install: `php artisan shield:install`.
  4. Register plugin in `AdminPanelProvider.php`.
- **Evidence:** Documentation and `spatie/laravel-permission` already exists in `composer.json`.

## 3. Framer Motion Integration
- **Goal:** Minimal animations in `SurveyShow.tsx`.
- **Implementation:**
  1. Install `framer-motion`: `npm install framer-motion`.
  2. Wrap survey components (e.g., questions) in `motion.div`.
  3. Use `initial`, `animate`, `exit` for transition effects.
- **Evidence:** `resources/js/pages/surveys/show.tsx` is the identified file. `npm install` and standard React patterns apply.

## 4. Plugin Implementation
- **Overlook:** Quick stats widget for Filament. Requires installation and registration in `AdminPanelProvider.php`.
- **Spotlight:** Cmd+K functionality for Filament. Requires `filament/spotlight` or similar package.
- **Evidence:** Standard Filament ecosystem plugins.

[TODO]: Verify if Spotlight is a separate package for Filament v4 or if it's native.
[TODO]: Check `AdminPanelProvider.php` current plugins.
