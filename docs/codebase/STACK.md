# Technology Stack

## Core Sections (Required)

### 1) Runtime Summary

| Area | Value | Evidence |
|------|-------|----------|
| Primary language | PHP (Backend), TypeScript (Frontend) | `composer.json`, `package.json` |
| Runtime + version | PHP ^8.2, Node.js (Vite) | `composer.json`, `package.json` |
| Package manager | Composer, npm | `composer.lock`, `package-lock.json` |
| Module/build system | PSR-4 (PHP), ESM/Vite (Frontend) | `composer.json`, `package.json`, `vite.config.ts` |

### 2) Production Frameworks and Dependencies

| Dependency | Version | Role in system | Evidence |
|------------|---------|----------------|----------|
| Laravel | ^12.0 | Backend Framework | `composer.json` |
| React | ^19.2.0 | Frontend UI Library | `package.json` |
| Inertia.js | ^2.2.17 (JS) / ^2.0 (PHP) | SPA Bridge | `package.json`, `composer.json` |
| Tailwind CSS | ^4.1.17 | Styling | `package.json` |
| Filament | 4.0 | Admin Panel | `composer.json` |
| Laravel Fortify | ^1.30 | Authentication Backend | `composer.json` |
| Spatie Laravel Permission | ^6.23 | Authorization/Roles | `composer.json` |
| Spatie Simple Excel | ^3.7 | Excel Exports | `composer.json` |
| Laravel Snappy | ^1.0 | PDF Generation | `composer.json` |

### 3) Development Toolchain

| Tool | Purpose | Evidence |
|------|---------|----------|
| Pest | Testing (PHP) | `composer.json` |
| Laravel Pint | Code Formatting (PHP) | `composer.json` |
| Prettier | Code Formatting (JS/TS) | `package.json` |
| ESLint | Linting (JS/TS) | `package.json` |
| TypeScript | Type Checking | `package.json`, `tsconfig.json` |
| Vite | Frontend Build Tool | `package.json`, `vite.config.ts` |
| Laravel Boost | Development Productivity | `composer.json` |

### 4) Key Commands

```bash
composer install
npm install
npm run dev
php artisan test
npm run lint
npm run format
```

### 5) Environment and Config

- Config sources: `config/*.php`, `.env`
- Required env vars: `APP_KEY`, `DB_CONNECTION`, `APP_URL`, [TODO]
- Deployment/runtime constraints: PHP 8.2+ required.

### 6) Evidence

- `composer.json`
- `package.json`
- `vite.config.ts`
- `tsconfig.json`
