<?php

namespace App\Models\Submission;

use App\Enums\CompetitionLevel;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionDetail extends Model
{
    use HasUlids;

    protected $connection = 'sipres_submission';

    protected $table = 'competition_details';

    protected $fillable = [
        'submission_id',
        'competition_name',
        'organizer',
        'level',
        'category',
        'competition_url',
        'start_date',
        'end_date',
        'location',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'level' => CompetitionLevel::class,
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
