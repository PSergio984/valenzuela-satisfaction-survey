# Codebase Concerns

## Core Sections (Required)

### 1) Top Risks (Prioritized)

| Severity | Concern | Evidence | Impact | Suggested action |
|----------|---------|----------|--------|------------------|
| Med | Lack of Frontend Tests | No testing framework for React components in `package.json`. | Potential UI regressions. | Install Vitest or Jest/RTL. |
| Low | Graphify Management | `graphify-out/` was missing but successfully generated. | Potential for stale architectural docs if not updated. | Run `graphify update .` regularly. |

### 2) Technical Debt

| Debt item | Why it exists | Where | Risk if ignored | Suggested fix |
|-----------|---------------|-------|-----------------|---------------|
| Empty README | Initial setup | `README.md` | Poor onboarding experience. | Populate README with project overview. |
| [TODO] | Additional debt items to be identified during feature work. | N/A | N/A | N/A |

### 3) Security Concerns

| Risk | OWASP category | Evidence | Current mitigation | Gap |
|------|--------------------------------|----------|--------------------|-----|
| Insecure File Uploads | A01:2021 | [TODO] | Laravel validation | Need to check file upload handling in `SurveyController`. |
| Exposure of Sensitive Data | A03:2021 | [TODO] | `.env` encryption | Confirm logging doesn't capture PII from surveys. |

### 4) Performance and Scaling Concerns

| Concern | Evidence | Current symptom | Scaling risk | Suggested improvement |
|---------|----------|-----------------|-------------|-----------------------|
| PDF Generation | Snappy/wkhtmltopdf | [TODO] | High CPU/Memory usage. | Use a queue for PDF generation. |

### 5) Fragile/High-Churn Areas

| Area | Why fragile | Churn signal | Safe change strategy |
|------|-------------|-------------|----------------------|
| `database/migrations` | High impact on data | Frequent additions | Always run `migrate:rollback` and `migrate` in dev. |

### 6) `[ASK USER]` Questions

1. [ASK USER] Should we add frontend unit testing (Vitest/Jest)?
2. [ASK USER] Confirm if PostgreSQL is the intended production database as indicated in `config/database.php`.

### 7) Evidence

- `docs/codebase/.codebase-scan.txt`
- `GEMINI.md`
- `composer.json`
- `package.json`
