<?php

namespace App\Models;

use App\Enums\SurveyMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Survey extends Model
{
    /** @use HasFactory<\Database\Factories\SurveyFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'mode',
        'is_active',
        'is_public',
        'requires_authentication',
        'collect_respondent_info',
        'starts_at',
        'ends_at',
        'thank_you_message',
        'views_count',
        'starts_count',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'mode' => SurveyMode::class,
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'requires_authentication' => 'boolean',
            'collect_respondent_info' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'views_count' => 'integer',
            'starts_count' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Survey $survey) {
            if (empty($survey->slug)) {
                $survey->slug = Str::slug($survey->title).'-'.Str::random(6);
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    public function isOpen(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $now->isBefore($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->isAfter($this->ends_at)) {
            return false;
        }

        return true;
    }

    /**
     * Get the completion rate as a percentage.
     */
    public function getCompletionRateAttribute(): float
    {
        if ($this->starts_count === 0 || $this->starts_count === null) {
            return 0;
        }

        $completedCount = $this->responses()->whereNotNull('submitted_at')->count();

        return round(($completedCount / $this->starts_count) * 100, 1);
    }

    /**
     * Get the average time to complete in seconds.
     */
    public function getAverageCompletionTimeAttribute(): ?int
    {
        $avgTime = $this->responses()
            ->whereNotNull('time_to_complete')
            ->avg('time_to_complete');

        return $avgTime ? (int) round($avgTime) : null;
    }

    /**
     * Get a formatted average completion time string.
     */
    public function getFormattedAverageCompletionTimeAttribute(): string
    {
        $seconds = $this->average_completion_time;

        if ($seconds === null) {
            return 'N/A';
        }

        if ($seconds < 60) {
            return $seconds.' sec';
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        if ($remainingSeconds > 0) {
            return $minutes.' min '.$remainingSeconds.' sec';
        }

        return $minutes.' min';
    }

    /**
     * Increment the views count.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment the starts count.
     */
    public function incrementStarts(): void
    {
        $this->increment('starts_count');
    }
}
