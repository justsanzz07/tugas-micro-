<?php

namespace App\Models\Workflow;

use App\Enums\SubmissionStatus;
use App\Models\Submission\Submission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ApprovalFlow extends Model
{
    protected $connection = 'sipres_workflow';

    protected $table = 'workflow_instances';

    protected $fillable = [
        'uuid',
        'reference_id',
        'template_code',
        'current_step',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (ApprovalFlow $flow) {
            if (empty($flow->uuid)) {
                $flow->uuid = (string) Str::uuid();
            }
        });
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ApprovalStep::class, 'workflow_instance_id')->orderBy('step_order');
    }

    public function currentStep(): ?ApprovalStep
    {
        if ($this->current_step) {
            $step = $this->steps()
                ->where(function ($q) {
                    $q->where('role_required', $this->current_step)
                        ->orWhere('role_required', 'like', "%{$this->current_step}%")
                        ->orWhere('step_order', $this->current_step);
                })
                ->where('status', 'pending')
                ->first();

            if ($step) {
                return $step;
            }
        }

        return $this->steps()->where('status', 'pending')->first();
    }

    public function submission(): ?Submission
    {
        return Submission::where('uuid', $this->reference_id)
            ->orWhere('id', $this->reference_id)
            ->first();
    }

    public function getCurrentStatusAttribute(): SubmissionStatus
    {
        return match (strtolower((string) $this->status)) {
            'submitted', 'dosen_review', 'kaprodi_review', 'in_progress' => ($this->current_step === 'dekan' ? SubmissionStatus::REVIEW_DEKAN : SubmissionStatus::REVIEW_KAPRODI),
            'revision_required', 'revision_requested' => SubmissionStatus::REVISION_KAPRODI,
            'approved' => SubmissionStatus::APPROVED_FINAL,
            'waiting_lpj' => SubmissionStatus::WAITING_LPJ,
            'lpj_review' => SubmissionStatus::REVIEW_LPJ,
            'completed' => SubmissionStatus::COMPLETED,
            'rejected' => SubmissionStatus::REJECTED,
            default => SubmissionStatus::REVIEW_KAPRODI,
        };
    }
}
