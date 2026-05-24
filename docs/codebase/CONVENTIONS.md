# Coding Conventions

## Core Sections (Required)

### 1) Naming Rules

| Item | Rule | Example | Evidence |
|------|------|---------|----------|
| Files | PascalCase for classes/components, snake_case for migrations | `SurveyController.php`, `create_surveys_table.php` | Directory scan |
| Functions/methods | camelCase | `exportExcel()`, `storeResponse()` | `app/Http/Controllers/` |
| Types/interfaces | PascalCase (prefixed with `T` or not, usually not in this repo) | `SurveyProps` | `resources/js/types/` |
| Constants/env vars | SCREAMING_SNAKE_CASE | `APP_URL`, `DB_CONNECTION` | `.env.example` |

### 2) Formatting and Linting

- Formatter: Prettier (Frontend), Laravel Pint (Backend).
- Linter: ESLint (Frontend).
- Most relevant enforced rules: TypeScript strict mode, React hooks rules, PSR-12 (via Pint).
- **Line Length**: Maximum 120 characters per line for all source and documentation files.
- Run commands: `npm run lint`, `npm run format`, `vendor/bin/pint`.

### 3) Import and Module Conventions

- Import grouping/order: Prettier plugin `prettier-plugin-organize-imports` is used.
- Alias vs relative import policy: `@/*` alias for `resources/js/*`.
- Public exports/barrel policy: Not explicitly seen, mostly direct imports.

### 4) Error and Logging Conventions

- Error strategy by layer: Laravel default (Exceptions caught by handler in `bootstrap/app.php`).
- Logging style: Laravel `Log` facade, logs to `storage/logs/laravel.log`.
- Sensitive-data redaction: `PiiScrubberProcessor` redacts `answers` and secrets from all system logs.

### 5) Testing Conventions

- Test file naming/location rule: `tests/Feature` and `tests/Unit`, suffix `Test.php`.
- Mocking strategy norm: Pest's mocking or Laravel's built-in mocks/fakes.
- Coverage expectation: Aim for 80%+ coverage on core business logic (Controllers, Services, Observers).

### 6) Shared Components and Foundational Patterns

- **Data Exports**: Use `app/Exports/SurveyResponsesExport.php` as the foundational pattern
  for all survey-related data exports (Queued, Excel, etc.).

### 7) Evidence

- `.prettierrc`
- `eslint.config.js`
- `composer.json` (Laravel Pint, Pest)
- `package.json` (Prettier, ESLint)
- `tsconfig.json` (Strict mode)
