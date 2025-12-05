<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionFactory> */
    use HasFactory;

    public const TYPE_TEXT = 'text';

    public const TYPE_TEXTAREA = 'textarea';

    public const TYPE_RADIO = 'radio';

    public const TYPE_CHECKBOX = 'checkbox';

    public const TYPE_SELECT = 'select';

    public const TYPE_RATING = 'rating';

    public const TYPE_DATE = 'date';

    public const TYPE_NUMBER = 'number';

    public const TYPES = [
        self::TYPE_TEXT => 'Short Text',
        self::TYPE_TEXTAREA => 'Long Text',
        self::TYPE_RADIO => 'Single Choice (Radio)',
        self::TYPE_CHECKBOX => 'Multiple Choice (Checkbox)',
        self::TYPE_SELECT => 'Dropdown Select',
        self::TYPE_RATING => 'Rating Scale',
        self::TYPE_DATE => 'Date',
        self::TYPE_NUMBER => 'Number',
    ];

    protected $fillable = [
        'survey_id',
        'type',
        'question',
        'description',
        'is_required',
        'order',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class)->orderBy('order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function hasOptions(): bool
    {
        return in_array($this->type, [
            self::TYPE_RADIO,
            self::TYPE_CHECKBOX,
            self::TYPE_SELECT,
        ]);
    }
}
