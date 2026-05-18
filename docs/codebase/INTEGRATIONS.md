# Integrations

## Core Sections (Required)

### 1) Integration Inventory

| System | Type (API/DB/Queue/etc) | Purpose | Auth model | Criticality | Evidence |
|--------|---------------------------|---------|------------|-------------|----------|
| Database | PostgreSQL (Prod) / SQLite (Local) | Persistent storage | DB User/Pass | High | `config/database.php` |
| Filament | Admin UI | Survey & User management | Fortify/Session | High | `app/Filament/` |
| Fortify | Auth Service | Registration, Login, 2FA | Session/Cookie | High | `config/fortify.php` |
| Spatie Permission | Auth Library | RBAC (Roles/Permissions) | DB-backed | High | `config/permission.php` |
| Laravel Snappy | PDF Tool | Survey result exports | Local Binary (wkhtmltopdf) | Medium | `config/snappy.php` |
| Simple Excel | Export Tool | Data analysis exports | N/A | Medium | `composer.json` |

### 2) Data Stores

| Store | Role | Access layer | Key risk | Evidence |
|-------|------|--------------|----------|----------|
| PostgreSQL | Primary Production Database | Eloquent Models | Connection security | `config/database.php` |
| SQLite | Local Development Database | Eloquent Models | Data persistence | `database/database.sqlite` |
| Cache | Performance | Laravel Cache Facade | Stale data | `config/cache.php` |
| Session | User State | Middleware | Session hijacking | `config/session.php` |

### 3) Secrets and Credentials Handling

- Credential sources: `.env` file.
- Hardcoding checks: No secrets found in source scan.
- Rotation or lifecycle notes: [TODO]

### 4) Reliability and Failure Behavior

- Retry/backoff behavior: Not explicitly configured for third-party APIs (since few are used yet).
- Timeout policy: Standard PHP/Vite timeouts.
- Circuit-breaker or fallback behavior: None seen.

### 5) Observability for Integrations

- Logging around external calls: Laravel standard logging.
- Metrics/tracing coverage: None seen.
- Missing visibility gaps: No dedicated monitoring (Sentry/Honeybadger) seen yet.

### 6) Evidence

- `config/` directory
- `.env.example`
- `composer.json`
