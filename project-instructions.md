# Project Instructions: Compliant Customer Satisfaction Survey System

This document provides detailed project instructions and architectural guidance for the development, maintenance, and extension of the Compliant Customer Satisfaction Survey System. It is tailored for a Laravel 12 + Inertia.js + React 19 + Tailwind CSS 4 stack, with a focus on compliance, extensibility, and robust data analysis.

---

## 1. System Overview

The system is a cross-platform digital survey solution for capturing, managing, and analyzing customer feedback. It supports web and desktop interfaces, QR code access, secure administration, and advanced analytics/reporting.

### Key Features

- Dynamic survey content with support for compliance and custom questions
- Admin interface for survey management (no code required)
- QR code-based survey access
- Secure, role-based authentication (Fortify)
- Automated scoring, segmentation, and export (Excel, PDF)
- Real-time dashboards and data visualization
- Data privacy, consent management, and encryption

---

## 2. Core Technology Stack

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Inertia.js (v2), React 19, Tailwind CSS 4
- **Authentication:** Laravel Fortify
- **Data Export:** spatie/simple-excel, barryvdh/laravel-snappy (or dompdf/dompdf if needed)
- **QR Code:** bacon/bacon-qr-code
- **Testing:** PestPHP, PHPUnit
- **Bundling/Tooling:** Vite, ESLint, Prettier, Laravel Pint

---

## 3. Recommended Packages & Utilities

### Already Installed

- **spatie/simple-excel:** Excel/CSV export
- **bacon/bacon-qr-code:** QR code generation
- **barryvdh/laravel-snappy:** PDF export (wkhtmltopdf)
- **dompdf/dompdf:** (if needed for alternative PDF export)

### Highly Recommended

- **spatie/laravel-permission:** Advanced role/permission management
- **spatie/laravel-activitylog:** Audit logging for compliance and traceability
- **spatie/laravel-analytics:** Google Analytics integration for advanced reporting
- **spatie/laravel-backup:** Automated backups for compliance and disaster recovery
- **spatie/laravel-cookie-consent:** Consent management for survey participants
- **laravel/sanctum:** API authentication (if mobile app or API endpoints needed)
- **laravel/horizon:** Queue monitoring (if using jobs/notifications)
- **filament/filament:** (Optional) For rapid admin panel development
- **livewire/livewire:** (Optional) For interactive admin features if needed

### Frontend/UX

- **react-chartjs-2** or **recharts:** For dashboard data visualization
- **@headlessui/react:** Accessible UI components
- **@heroicons/react:** Icon set for UI consistency

---

## 4. Architecture & Best Practices

### Survey Content

- Store survey questions in the database with support for required, optional, and custom fields.
- Use Eloquent models and relationships for survey/question/response management.
- Provide admin UI for CRUD operations on surveys/questions (Inertia + React).

### Platform Development

- Use Inertia.js for seamless SPA experience.
- Place React pages in `resources/js/pages` and components in `resources/js/components`.
- Use Tailwind CSS for all styling; follow existing utility class conventions.

### Data Collection

- Generate unique QR codes for each survey instance/location.
- Secure admin routes with Fortify and spatie/laravel-permission.
- Implement tiered permissions (admin, staff, viewer, etc.).

### Data Analysis & Reporting

- Use Eloquent queries for segmentation (location, service, time, etc.).
- Provide export options (Excel, PDF) using spatie/simple-excel and snappy/dompdf.
- Build dashboards with React charting libraries for real-time insights.

### Security & Compliance

- Enforce HTTPS and secure cookies.
- Encrypt sensitive data at rest and in transit.
- Use spatie/laravel-activitylog for audit trails.
- Implement consent management (spatie/laravel-cookie-consent).
- Regularly back up data (spatie/laravel-backup).

---

## 5. Extensibility & Customization

- All survey content, questions, and options should be database-driven and editable via the admin UI.
- Use Laravel policies and gates for fine-grained access control.
- Design the system to allow new question types and analytics modules with minimal code changes.
- Use Laravel’s event system for extensibility (e.g., trigger notifications or webhooks on survey submission).

---

## 6. Testing & Quality

- Write feature and unit tests using PestPHP and PHPUnit.
- Use factories for test data; cover all major user flows (survey creation, response, export, admin actions).
- Run `vendor/bin/pint --dirty` before committing to ensure code style.
- Use ESLint and Prettier for JS/React code.

---

## 7. Deployment & Operations

- Use Laravel Herd for local development.
- Use environment variables for all secrets and configuration.
- Set up automated backups and monitoring.
- Document all environment/configuration requirements in `.env.example`.

---

## 8. Compliance & Data Protection

- Ensure all data collection and storage complies with local privacy laws (e.g., GDPR).
- Provide clear consent forms and privacy policy links in the survey UI.
- Log all admin actions for auditability.
- Regularly review and update security dependencies.

---

## 9. Useful Artisan Commands

- `php artisan make:model Survey -m`
- `php artisan make:controller SurveyController --resource`
- `php artisan make:request StoreSurveyRequest`
- `php artisan permission:create-role admin`
- `php artisan backup:run`
- `php artisan activitylog:clean`

---

## 10. Further Recommendations

- Review and update dependencies regularly.
- Consider adding API endpoints for mobile app integration.
- Use Laravel’s scheduler for periodic tasks (e.g., report generation, data cleanup).
- Encourage contributions to documentation and tests.

---

# End of Instructions

For any new features or changes, always check the latest Laravel, Inertia, and React documentation, and follow the conventions and guidelines in this document and `.github/copilot-instructions.md`.
