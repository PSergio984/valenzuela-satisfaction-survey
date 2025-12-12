<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Response extends Model
{
    /** @use HasFactory<\Database\Factories\ResponseFactory> */
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'user_id',
        'respondent_name',
        'respondent_email',
        'respondent_phone',
        'user_agent',
        'started_at',
        'submitted_at',
        'time_to_complete',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'time_to_complete' => 'integer',
        ];
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Mark the response as started and record the start time.
     */
    public function markAsStarted(): void
    {
        if ($this->started_at === null) {
            $this->update(['started_at' => now()]);
        }
    }

    /**
     * Mark the response as submitted and calculate time to complete.
     */
    public function markAsSubmitted(): void
    {
        $now = now();
        $timeToComplete = null;

        if ($this->started_at) {
            $timeToComplete = $this->started_at->diffInSeconds($now);
        }

        $this->update([
            'submitted_at' => $now,
            'time_to_complete' => $timeToComplete,
        ]);
    }

    /**
     * Get the formatted time to complete.
     */
    public function getFormattedTimeToCompleteAttribute(): string
    {
        $seconds = $this->time_to_complete;

        if ($seconds === null) {
            return 'N/A';
        }

        if ($seconds < 60) {
            return $seconds . ' sec';
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        if ($remainingSeconds > 0) {
            return $minutes . ' min ' . $remainingSeconds . ' sec';
        }

        return $minutes . ' min';
    }
}
