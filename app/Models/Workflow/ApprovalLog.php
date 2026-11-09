<?php

namespace App\Models\Workflow;

use App\Enums\ApprovalAction;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalLog extends Model
{
    use HasUlids;

    protected $connection = 'sipres_workflow';

    protected $table = 'approval_logs';

    public $timestamps = false;

    protected $fillable = [
        'approval_step_id',
        'action',
        'actor_identifier',
        'actor_role',
        'notes',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'action' => ApprovalAction::class,
            'actor_role' => UserRole::class,
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(ApprovalStep::class, 'approval_step_id');
    }
}
