# Phase 2-01 Plan: Reliability & Security

Goal: Harden the platform through background processing and security best practices.

## 1. Reliability: Queued PDF Exports
- **Task 1.1**: Create `app/Jobs/GenerateSurveyPdfReport.php`.
  - Logic: Move `SurveyExportController@calculateStatistics` and PDF generation 
    into this job.
  - Notification: Use `Filament\Notifications\Notification` to send a "Silent" 
    database notification to the user when complete.
- **Task 1.2**: Refactor `SurveyExportController@exportPdf`.
  - Action: Dispatch the job instead of generating the PDF synchronously.
  - UI: Return a success notification to the dashboard.
- **Task 1.3**: Update `downloadExport` to handle PDF paths.

## 2. Security: Log Scrubbing & Auditing
- **Task 2.1**: Implement Log Scrubbing.
  - Strategy: Perform redaction in the logging stack using Monolog processors and/or custom handlers/channels. 
  - Note: `TrustProxies` only configures trusted reverse proxies / X-Forwarded-* handling (e.g., client IP/scheme) and does not scrub request or log payloads.
  - Implementation: Add custom processors to the Laravel log channel for sensitive-data scrubbing (see `app/Logging/PiiScrubberProcessor.php`).
- **Task 2.2**: Audit `ProfileController` and `SurveyController`.
  - Action: Ensure all inputs are strictly validated and no mass-assignment 
    risks exist.
- **Task 2.3**: .env Encryption.
  - Action: Document or implement `php artisan env:encrypt` if applicable for 
    the environment.

## 3. Verification
- **Test 1**: Verify PDF export queues correctly and triggers a notification.
- **Test 2**: Verify logs do not contain sensitive answer data.
- **Test 3**: Verify file download security (cannot access unauthorized files).
