# Testing Patterns

## Core Sections (Required)

### 1) Test Stack and Commands

- Primary test framework: Pest ^3.8
- Assertion/mocking tools: Pest, Mockery, Laravel Fakes
- Commands:

```bash
php artisan test
php artisan test --parallel
php artisan test --coverage
```

### 2) Test Layout

- Test file placement pattern: `tests/Feature` and `tests/Unit`
- Naming convention: `*Test.php`
- Setup files: `tests/TestCase.php`, `tests/Pest.php`

### 3) Test Scope Matrix

| Scope | Covered? | Typical target | Notes |
|-------|----------|----------------|-------|
| Unit | Yes | Utility classes, simple model methods | `tests/Unit` |
| Integration | Yes | Controllers, Database interactions | `tests/Feature` |
| E2E | No | Full browser flows | No Playwright/Cypress seen |

### 4) Mocking and Isolation Strategy

- Main mocking approach: Laravel Fakes (Mail, Queue, Storage).
- Isolation guarantees: Database migrations run before tests (RefreshDatabase trait likely used).
- Common failure mode: Database state contamination if traits are missing.

### 5) Coverage and Quality Signals

- Coverage tool: PCOV or Xdebug via Pest.
- Current reported coverage: [TODO]
- Known gaps: Frontend (React) components do not seem to have dedicated Vitest/Jest tests.

### 6) Evidence

- `composer.json`
- `tests/Pest.php`
- `phpunit.xml`
