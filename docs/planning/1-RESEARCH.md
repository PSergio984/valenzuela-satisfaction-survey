# Phase 1: Maintenance, QOL, and Performance - Research

**Researched:** 2026-05-16
**Domain:** Maintenance, Performance, and UX Optimization
**Confidence:** HIGH

## Summary

This research phase focuses on optimizing the survey platform's performance, enhancing the admin filtering capabilities, and improving the user experience for both public respondents and administrators. Key areas investigated include Filament resource customization, queued export implementation using `maatwebsite/excel`, and frontend duration tracking strategies.

**Primary recommendation:** Use `Maatwebsite\Excel` with the `FromQuery` concern and `ShouldQueue` trait for scalable exports, while optimizing database queries using aggregates (`withCount`, `withAvg`) to eliminate N+1 issues in lists and widgets.

## Architectural Responsibility Map

| Capability | Primary Tier | Secondary Tier | Rationale |
|------------|-------------|----------------|-----------|
| Survey Pagination | API / Backend | Browser / Client | Data fetching logic is handled by Laravel's `paginate()`, while the UI renders controls. |
| Queued Exports | API / Backend | — | Heavy processing belongs in background jobs to prevent HTTP timeouts. |
| Duration Tracking | Browser / Client | API / Backend | Frontend captures the mount time (`started_at`) to ensure accuracy regardless of session state. |
| Advanced Filters | API / Backend | Browser / Client | Query building happens on the server; UI (Filament) provides the interface. |
| Metric Optimization| Database / Storage | API / Backend | Moving logic to DB aggregates (`withCount`) reduces memory usage and query volume. |

## Dependency Audit Findings

The project's dependencies are approximately 1 year old (relative to the current session date of May 2026). Several core packages have available updates:

| Package | Current | Latest | Type | Impact |
|---------|---------|--------|------|--------|
| `filament/filament` | 4.0.0 | 5.6.3 | Major | High (Breaking changes in UI/API) |
| `inertiajs/inertia-laravel` | 2.0.10 | 3.1.0 | Major | Med (New features, some deprecations) |
| `laravel/framework` | 12.38.1 | 12.59.0 | Minor | Low (Stability & Performance) |
| `@inertiajs/react` | 2.2.19 | 3.1.1 | Major | Med (Sync with backend) |
| `vite` | 7.2.6 | 8.0.13 | Major | Low (Build speed/tooling) |
| `shadcn` | 3.5.1 | 4.7.0 | Major | Med (Component updates) |

**Recommendation**: 
1. Run `composer update` and `npm update` to pull in minor/patch updates first.
2. Evaluate and apply major updates (specifically Laravel 12 minor updates and Filament 5) sequentially.
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| `filament/filament` | 4.0 | Admin Panel | Robust, extensible admin framework for Laravel. [VERIFIED: composer] |
| `maatwebsite/excel` | 3.1.67 | Excel Exports | Industry standard for Laravel Excel handling with queue support. [VERIFIED: composer] |
| `inertiajs/inertia-laravel` | 2.0 | Frontend Bridge | Seamlessly connects Laravel backend with React frontend. [VERIFIED: composer] |
| `react` | 18+ | UI Library | Modern component-based UI for the survey platform. [ASSUMED] |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|--------------|
| `spatie/simple-excel` | 3.7.3 | Simple CSV/Excel | Used for lightweight, non-queued stream exports. [VERIFIED: composer] |
| `lucide-react` | Latest | Icons | Standard icon set for UI consistency. [ASSUMED] |
| `shadcn/ui` | Latest | UI Components | Provides the base for Card, Button, and Input components. [CITED: 1-CONTEXT.md] |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| `maatwebsite/excel` | `spatie/simple-excel` | Simple-excel is faster for small files but lacks complex formatting and robust queue support for massive datasets. |
| Frontend `started_at` | Session tracking | Session tracking is prone to expiration and can be inaccurate if multiple tabs are used. |

**Installation:**
```bash
# Core packages already present in composer.json
composer install
```

## Package Legitimacy Audit

| Package | Registry | Age | Downloads | Source Repo | slopcheck | Disposition |
|---------|----------|-----|-----------|-------------|-----------|-------------|
| `maatwebsite/excel` | Packagist | 10+ yrs | 50M+ | github.com/SpartnerNL/Laravel-Excel | [OK] | Approved |
| `spatie/simple-excel` | Packagist | 5+ yrs | 5M+ | github.com/spatie/simple-excel | [OK] | Approved |
| `filament/filament` | Packagist | 3+ yrs | 10M+ | github.com/filamentphp/filament | [OK] | Approved |

## Architecture Patterns

### Recommended Project Structure
```
app/
├── Exports/             # Refactored for FromQuery and ShouldQueue
├── Filament/Admin/
│   ├── Resources/
│   │   ├── Responses/
│   │   │   └── Tables/  # Inject advanced rating filters
│   │   └── Surveys/
│   │       ├── Tables/  # Optimize lists with aggregates
│   │       └── Widgets/ # Use database-level metrics
resources/js/
├── components/
│   └── pagination.tsx   # New reusable Shadcn component
└── pages/
    └── surveys/
        ├── index.tsx    # Add search and pagination
        └── show.tsx     # Add progress bar and startedAt tracking
```

### Pattern 1: Queued Export with `FromQuery`
**What:** Offload large Excel generation to the queue using chunks.
**When to use:** Any export likely to exceed 1,000 rows or with complex relationships.
**Example:**
```php
// Source: https://docs.laravel-excel.com/3.1/exports/queued.html
class ResponsesExport implements FromQuery, ShouldQueue, WithMapping 
{
    use Exportable;
    
    public function query() {
        return Response::query()->with(['survey', 'answers']);
    }
}
```

### Pattern 2: Database Aggregates in Filament
**What:** Use `withCount()` and `withAvg()` to avoid N+1 queries.
**When to use:** Displaying relationship-based metrics in tables or dashboards.
**Example:**
```php
// In SurveysTable.php
$table->columns([
    TextColumn::make('responses_count')->counts('responses'),
    TextColumn::make('avg_rating')->avg('answers', 'value'), // Simplified
]);
```

## Don't Hand-Roll

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Excel Queuing | Custom Job | `maatwebsite/excel` | Handles chunking, temporary storage, and serialization automatically. |
| Pagination UI | Custom logic | `shadcn/ui` + `laravel/paginate` | Ensures accessibility and consistent styling. |
| Date Ranges | Custom regex | Filament `DatePicker` | Built-in validation and localized UI. |

## Common Pitfalls

### Pitfall 1: Large Export Timeouts
**What goes wrong:** Exporting 10k+ rows synchronously hangs the browser.
**How to avoid:** Always use `ShouldQueue` for admin exports and notify the user via a background notification or "Download Ready" UI.

### Pitfall 2: N+1 in Model Accessors
**What goes wrong:** Accessing `$survey->completion_rate` in a list view triggers a query for every row.
**How to avoid:** Use `withCount` and calculate the rate in the query or use a cached attribute.

### Pitfall 3: Inaccurate Duration Tracking
**What goes wrong:** Server `now()` - session `started_at` fails if the user takes 2 days (session expires).
**How to avoid:** Capture `started_at` on the frontend when the form mounts and pass it during submission.

## Code Examples

### Frontend `started_at` Capture
```typescript
// resources/js/pages/surveys/show.tsx
import { useState, useEffect } from 'react';

export default function SurveyShow({ survey }) {
    const { data, setData, post } = useForm({
        started_at: '',
        // ...
    });

    useEffect(() => {
        setData('started_at', new Date().toISOString());
    }, []);
}
```

## Assumptions Log

| # | Claim | Section | Risk if Wrong |
|---|-------|---------|---------------|
| A1 | `maatwebsite/excel` is the preferred tool for queued exports. | Standard Stack | Low - already present in project and standard for Laravel. |
| A2 | `started_at` is better handled by frontend. | Duration Tracking | Low - resolves session expiration issues. |

## Open Questions (RESOLVED)

1. **Storage Policy:** Should we use the `private` disk for queued exports? (RESOLVED)
   - Decision: Yes, use `private` to prevent unauthorized access.
2. **Notification Channel:** How should administrators be notified when a queued export is ready? (RESOLVED)
   - Decision: Use Filament's `Notification` system to alert admins when background jobs finish.

## Environment Availability

| Dependency | Required By | Available | Version | Fallback |
|------------|------------|-----------|---------|----------|
| PHP | Core | ✓ | 8.2 | — |
| Laravel | Core | ✓ | 12.0 | — |
| Filament | Admin | ✓ | 4.0 | — |
| Node/npm | Frontend | ✓ | 20+ | — |

## Validation Architecture

### Test Framework
| Property | Value |
|----------|-------|
| Framework | Pest 3.8 |
| Config file | `phpunit.xml` |
| Quick run command | `php artisan test` |

### Phase Requirements → Test Map
| Req ID | Behavior | Test Type | Automated Command | File Exists? |
|--------|----------|-----------|-------------------|-------------|
| PERF-01| Pagination in Survey List | Feature | `php artisan test --filter=SurveyControllerTest` | ❌ Wave 0 |
| PERF-02| Queued Export Job | Unit/Feature | `php artisan test --filter=ExportJobTest` | ❌ Wave 0 |
| BUG-01 | Duration Tracking Validation | Feature | `php artisan test --filter=ResponseTest` | ❌ Wave 0 |

## Security Domain

### Applicable ASVS Categories

| ASVS Category | Applies | Standard Control |
|---------------|---------|-----------------|
| V5 Input Validation | yes | Laravel Request Validation (Pydantic-like behavior) |
| V12 File Upload/Download | yes | Private disk storage for exports |

### Known Threat Patterns for Laravel/React

| Pattern | STRIDE | Standard Mitigation |
|---------|--------|---------------------|
| Insecure Direct Object Reference | Elevation of Privilege | Policy-based access in Filament |
| Mass Assignment | Tampering | `$fillable` protection in Models |

## Sources

### Primary (HIGH confidence)
- `maatwebsite/excel` docs - Queued Exports
- Filament PHP documentation - Table Filters & Widgets
- `1-CONTEXT.md` - Phase decisions

### Secondary (MEDIUM confidence)
- Laracasts - Performance optimization patterns (aggregates)

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - Directly verified in `composer.json`.
- Architecture: HIGH - Matches established Laravel/Filament patterns.
- Pitfalls: HIGH - Common documented issues in the ecosystem.

**Research date:** 2026-05-16
**Valid until:** 2026-06-16
