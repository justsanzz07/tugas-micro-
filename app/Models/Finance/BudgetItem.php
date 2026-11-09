<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetItem extends Model
{
    protected $connection = 'sipres_finance';

    protected $table = 'budget_items';

    protected $fillable = [
        'budget_request_id',
        'category',
        'item_name',
        'quantity',
        'unit',
        'unit_price',
        'total_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function budgetRequest(): BelongsTo
    {
        return $this->belongsTo(BudgetRequest::class, 'budget_request_id');
    }

    public function getSubtotalAttribute(): float
    {
        return (float) ($this->total_price ?? ($this->quantity * $this->unit_price));
    }

    protected static function booted(): void
    {
        static::saving(function (BudgetItem $item) {
            $item->total_price = $item->quantity * $item->unit_price;
        });

        static::saved(function (BudgetItem $item) {
            $item->budgetRequest?->recalculateTotal();
        });

        static::deleted(function (BudgetItem $item) {
            $item->budgetRequest?->recalculateTotal();
        });
    }
}
