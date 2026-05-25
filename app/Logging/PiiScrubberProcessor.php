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
        'email',
        'email_address',
        'name',
        'first_name',
        'last_name',
        'ip',
        'ip_address',
        'phone',
        'phone_number',
        'address',
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
    protected function scrubArray(array $data, array &$visited = [], int $depth = 0): array
    {
        // Prevent excessive recursion or circular references
        if ($depth > 10) {
            return ['[DEPTH_LIMIT_REACHED]'];
        }

        foreach ($data as $key => $value) {
            if (in_array($key, $this->sensitiveKeys)) {
                $data[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                // Track visited arrays to prevent circular references
                $hash = spl_object_hash((object) $value);
                if (isset($visited[$hash])) {
                    $data[$key] = '[CIRCULAR_REFERENCE]';
                    continue;
                }
                $visited[$hash] = true;

                $data[$key] = $this->scrubArray($value, $visited, $depth + 1);
            }
        }

        return $data;
    }
}
