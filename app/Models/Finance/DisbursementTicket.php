<?php

namespace App\Models\Finance;

use App\Enums\DisbursementStatus;
use Illuminate\Database\Eloquent\Model;

class DisbursementTicket extends Model
{
    protected $connection = 'sipres_finance';

    protected $table = 'disbursements';

    protected $fillable = [
        'uuid',
        'reference_id',
        'disbursement_code',
        'stage',
        'amount',
        'payment_method',
        'bank_name',
        'account_number',
        'recipient_name',
        'transaction_reference',
        'processed_by_user_id',
        'processed_by_name',
        'disbursed_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'disbursed_at' => 'datetime',
        ];
    }

    public function getTicketCodeAttribute(): string
    {
        return $this->disbursement_code ?? 'DSB-'.$this->id;
    }

    public function getRecipientNimAttribute(): ?string
    {
        return $this->recipient_name;
    }

    public function getSimpayStatusAttribute(): DisbursementStatus
    {
        return match (strtolower((string) $this->status)) {
            'disbursed', 'completed', 'paid' => DisbursementStatus::DISBURSED,
            'processing' => DisbursementStatus::PROCESSING,
            'failed' => DisbursementStatus::FAILED,
            default => DisbursementStatus::PENDING,
        };
    }

    public function getSyncedAtAttribute()
    {
        return $this->created_at;
    }
}
