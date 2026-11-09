<?php

namespace App\Models\Workflow;

use App\Enums\ApproverRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalStep extends Model
{
    protected $connection = 'sipres_workflow';

    protected $table = 'approval_steps';

    protected $fillable = [
        'workflow_instance_id',
        'reference_id',
        'step_order',
        'step_name',
        'role_required',
        'assigned_user_id',
        'status',
        'decision_notes',
        'acted_by_user_id',
        'acted_by_user_name',
        'acted_at',
        'idempotency_key',
    ];

    protected function casts(): array
    {
        return [
            'step_order' => 'integer',
            'acted_at' => 'datetime',
        ];
    }

    public function flow(): BelongsTo
    {
        return $this->belongsTo(ApprovalFlow::class, 'workflow_instance_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ApprovalLog::class, 'approval_step_id');
    }

    public function getApproverRoleAttribute(): ApproverRole
    {
        $roleStr = strtolower((string) ($this->role_required ?? $this->step_name));

        return match (true) {
            str_contains($roleStr, 'dekan') => ApproverRole::DEKAN,
            default => ApproverRole::KAPRODI,
        };
    }

    public function getNotesAttribute(): ?string
    {
        return $this->decision_notes;
    }

    public function getDecidedAtAttribute()
    {
        return $this->acted_at;
    }

    public function getApproverIdentifierAttribute(): ?string
    {
        return $this->acted_by_user_name ?? (string) $this->acted_by_user_id;
    }
}
