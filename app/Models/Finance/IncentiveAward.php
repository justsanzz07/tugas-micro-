<?php

namespace App\Models\Finance;

use App\Models\Submission\Submission;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncentiveAward extends Model
{
    use HasUlids;

    protected $connection = 'sipres_finance';

    protected $table = 'incentive_awards';

    protected $fillable = [
        'submission_id',
        'achievement_rank',
        'incentive_amount',
        'decided_by_nidn',
        'decided_at',
        'disbursement_ticket_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'incentive_amount' => 'decimal:2',
            'decided_at' => 'datetime',
        ];
    }

    public function disbursementTicket(): BelongsTo
    {
        return $this->belongsTo(DisbursementTicket::class);
    }

    // Cross-DB accessor
    public function submission(): ?Submission
    {
        return Submission::find($this->submission_id);
    }
}
