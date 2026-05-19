# Architecture

## Core Sections (Required)

### 1) Architectural Style

- Primary style: Layered MVC with a SPA Frontend (Inertia.js).
- Why this classification: Backend uses Laravel's MVC pattern; Frontend is a React SPA managed by Inertia.js without a separate REST API.
- Primary constraints: Data compliance, role-based access control, extensibility for survey types.

### 2) System Flow

```text
[HTTP Request] -> [public/index.php] -> [routes/web.php] -> [Controller] -> [Model/Service] -> [Inertia Response] -> [React UI]
```

1. **Entry**: Request enters through `public/index.php`.
2. **Routing**: Laravel router matches the request in `routes/web.php`.
3. **Controller**: Controller handles the request, often utilizing **Services** or **Actions** (e.g., Fortify Actions).
4. **Domain Logic**: Logic is processed in Models or dedicated Service classes (e.g., `QrCodeService`).
5. **Data**: Persistence via Eloquent Models to the database (SQLite by default in local).
6. **Response**: Inertia renders a React component from `resources/js/pages`.

### 3) Layer/Module Responsibilities

| Layer or module | Owns | Must not own | Evidence |
|-----------------|------|--------------|----------|
| Models | Data schema, relationships, basic validation | HTTP logic, complex business flows | `app/Models/` |
| Controllers | Request parsing, calling logic, returning responses | Deep business logic | `app/Http/Controllers/` |
| Services | Reusable business logic, third-party integrations | HTTP state, global session state | `app/Services/` |
| Actions | Single-purpose, repeatable tasks (especially Auth) | Multi-step navigation flows | `app/Actions/` |
| Filament | Admin UI, resource management | Public survey taking logic | `app/Filament/` |

### 4) Reused Patterns

| Pattern | Where found | Why it exists |
|---------|-------------|---------------|
| Service Layer | `app/Services/` | Encapsulate complex logic like QR code generation or exports. |
| Action Pattern | `app/Actions/Fortify/` | Standardized approach for auth-related tasks. |
| Enum | `app/Enums/` | Type-safe constants for things like Survey Modes. |
| Policy | `app/Policies/` | Fine-grained authorization logic. |

### 5) Known Architectural Risks

- **Missing Knowledge Graph**: `GEMINI.md` references `graphify-out/` which is currently missing from the root.
- **Dependency on Local Storage**: PDF and Excel generation may rely on local temp storage which needs monitoring in distributed environments.

### 6) Evidence

- `bootstrap/app.php`
- `app/Providers/AppServiceProvider.php`
- `app/Http/Middleware/HandleInertiaRequests.php`
- `app/Services/QrCodeService.php`
