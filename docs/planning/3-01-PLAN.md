# Phase 3-01 Plan: Admin UI Overhaul & Role Management

Goal: Transform the admin experience into a premium, engaging interface and restore Role Management.

## 1. Role Management (Filament Shield)
- **Task 1.1**: Install Filament Shield.
  - Run `composer require bezhansalleh/filament-shield`.
  - Run `php artisan shield:install`.
- **Task 1.2**: Register Shield in `AdminPanelProvider.php`.
  - Add `->plugin(FilamentShieldPlugin::make())`.
- **Task 1.3**: Assign Permissions.
  - Run `php artisan shield:generate --all` and assign existing users to the 'super_admin' role.

## 2. Admin UI Overhaul (Theme & Plugins)
- **Task 2.1**: Install Power-up Plugins.
  - `composer require awcodes/filament-overlook`
  - `composer require pxlrbt/filament-spotlight`
- **Task 2.2**: Register Plugins in `AdminPanelProvider.php`.
  - Register `OverlookPlugin` and `SpotlightPlugin`.
- **Task 2.3**: Custom Theme Configuration.
  - Update `resources/css/filament/admin/theme.css` with custom branding (colors, spacing).
  - Update `AdminPanelProvider.php` to define the new typography (via theme/CSS).

## 3. Verification
- **Test 1**: Verify Shield roles/permissions work (sidebar check).
- **Test 2**: Verify Spotlight (`Cmd+K`) functionality.
- **Test 3**: Verify Overlook widgets are visible.
