<?php

namespace App\Logging;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class PiiScrubberProcessor implements ProcessorInterface
{
    /**
     * Sensitive keys to redact.
     */
    protected array $sensitiveKeys = [
        'answers',
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * @param  LogRecord  $record
     * @return LogRecord
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        $context = $record->context;
        $extra = $record->extra;

        if (! empty($context)) {
            $record = $record->with(context: $this->scrubArray($context));
        }

        if (! empty($extra)) {
            $record = $record->with(extra: $this->scrubArray($extra));
        }

        return $record;
    }

    /**
     * Recursively scrub an array of sensitive keys.
     */
    protected function scrubArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->sensitiveKeys)) {
                $data[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $data[$key] = $this->scrubArray($value);
            }
        }

        return $data;
    }
}
