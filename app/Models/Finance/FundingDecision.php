<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FundingDecision extends Model
{
    use HasUlids;

    protected $connection = 'sipres_finance';

    protected $table = 'funding_decisions';

    protected $fillable = [
        'budget_request_id',
        'approved_amount',
        'decided_by_nidn',
        'decided_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'approved_amount' => 'decimal:2',
            'decided_at' => 'datetime',
        ];
    }

    public function budgetRequest(): BelongsTo
    {
        return $this->belongsTo(BudgetRequest::class);
    }

    public function disbursementTicket(): HasOne
    {
        return $this->hasOne(DisbursementTicket::class);
    }
}
