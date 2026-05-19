# Codebase Structure

## Core Sections (Required)

### 1) Top-Level Map

| Path | Purpose | Evidence |
|------|---------|----------|
| `app/` | Core application logic (Models, Controllers, Services) | Directory scan |
| `bootstrap/` | Framework bootstrapping and configuration | Directory scan |
| `config/` | Application configuration files | Directory scan |
| `database/` | Migrations, factories, and seeders | Directory scan |
| `docs/` | Project documentation (including this folder) | Directory scan |
| `public/` | Publicly accessible assets and entry point | Directory scan |
| `resources/` | Frontend source files (React, CSS, views) | Directory scan |
| `routes/` | Web and console route definitions | Directory scan |
| `storage/` | Logs, cache, and uploaded files | Directory scan |
| `tests/` | Automated tests (Pest) | Directory scan |

### 2) Entry Points

- Main runtime entry: `public/index.php`
- Secondary entry points: `artisan` (CLI)
- How entry is selected: HTTP server points to `public/index.php`; Artisan used for CLI commands.

### 3) Module Boundaries

| Boundary | What belongs here | What must not be here | Evidence |
|----------|-------------------|------------------------|----------|
| `app/Models` | Eloquent models and data structure | Business logic, view rendering | `app/Models/` |
| `app/Http/Controllers` | Request handling and response coordination | Complex business logic (delegate to Services) | `app/Http/Controllers/` |
| `app/Services` | Complex business logic and integrations | Direct HTTP request/response handling | `app/Services/` |
| `app/Filament` | Admin panel specific configuration and resources | Public-facing survey logic | `app/Filament/` |
| `resources/js/pages` | Inertia/React page components | Backend database logic | `resources/js/pages/` |

### 4) Naming and Organization Rules

- File naming pattern: PascalCase for Classes and Components; snake_case for migrations.
- Directory organization pattern: Layered/Functional (Controllers, Models, Services).
- Import aliasing: `@/*` maps to `./resources/js/*`.

### 5) Evidence

- `docs/codebase/.codebase-scan.txt`
- `tsconfig.json`
- `composer.json`
- `resources/js/app.tsx`
