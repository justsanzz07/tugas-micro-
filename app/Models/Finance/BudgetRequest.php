<?php

namespace App\Models\Finance;

use App\Models\Submission\Submission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BudgetRequest extends Model
{
    protected $connection = 'sipres_finance';

    protected $table = 'budget_requests';

    protected $fillable = [
        'uuid',
        'reference_id',
        'total_proposed',
        'total_approved',
        'status',
        'verified_by_user_id',
        'verified_by_name',
        'verification_notes',
        'verified_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (BudgetRequest $budget) {
            if (empty($budget->uuid)) {
                $budget->uuid = (string) Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'total_proposed' => 'decimal:2',
            'total_approved' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(BudgetItem::class, 'budget_request_id');
    }

    public function getFundingDecisionAttribute()
    {
        if ($this->total_approved > 0 || $this->status === 'approved') {
            $disbursement = DB::connection('sipres_finance')->table('disbursements')
                ->where('reference_id', $this->reference_id)
                ->first();

            $statusStr = $disbursement->status ?? 'pending';

            return (object) [
                'approved_amount' => (float) $this->total_approved,
                'notes' => $this->verification_notes,
                'disbursementTicket' => $disbursement ? (object) [
                    'ticket_code' => $disbursement->disbursement_code,
                    'simpay_status' => new class($statusStr)
                    {
                        public function __construct(protected string $st) {}

                        public function label(): string
                        {
                            return match ($this->st) {
                                'disbursed' => 'Dana Telah Dicairkan',
                                'pending' => 'Menunggu Pencairan SIMPAY',
                                'cancelled' => 'Dibatalkan',
                                default => 'Menunggu Pencairan',
                            };
                        }
                    },
                ] : null,
            ];
        }

        return null;
    }

    public function submission(): ?Submission
    {
        return Submission::find($this->reference_id);
    }

    public function getTotalRequestedAttribute(): float
    {
        return (float) ($this->total_proposed ?? 0);
    }

    public function getSubmissionIdAttribute(): ?int
    {
        return $this->reference_id;
    }

    public function recalculateTotal(): void
    {
        $this->update([
            'total_proposed' => $this->items()->sum('total_price'),
        ]);
    }
}
