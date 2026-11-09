<?php

namespace App\Models\Submission;

use App\Enums\CompetitionLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Competition extends Model
{
    protected $connection = 'sipres_submission';

    protected $table = 'competitions';

    protected $fillable = [
        'uuid',
        'name',
        'organizer',
        'level',
        'category',
        'start_date',
        'end_date',
        'location_type',
        'venue',
        'website_url',
    ];

    protected static function booted(): void
    {
        static::creating(function (Competition $competition) {
            if (empty($competition->uuid)) {
                $competition->uuid = (string) Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'competition_id');
    }

    public function getCompetitionNameAttribute(): string
    {
        return $this->name ?? '';
    }

    public function getLocationAttribute(): string
    {
        return $this->venue ?? $this->location_type ?? 'Online';
    }

    public function getCompetitionUrlAttribute(): ?string
    {
        return $this->website_url;
    }

    public function getLevelAttribute($value)
    {
        if ($value instanceof CompetitionLevel) {
            return $value;
        }

        return match (strtolower((string) $value)) {
            'regional' => CompetitionLevel::REGIONAL,
            'national', 'nasional' => CompetitionLevel::NASIONAL,
            'international', 'internasional' => CompetitionLevel::INTERNASIONAL,
            default => CompetitionLevel::NASIONAL,
        };
    }
}
