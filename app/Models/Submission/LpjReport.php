<?php

namespace App\Models\Submission;

use App\Enums\LpjValidationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LpjReport extends Model
{
    protected $connection = 'sipres_submission';

    protected $table = 'activity_reports';

    protected $fillable = [
        'uuid',
        'submission_id',
        'submission_uuid',
        'summary',
        'report_file_path',
        'financial_report_path',
        'documentation_paths',
        'submission_date',
        'status',
        'review_notes',
        'verified_by',
        'verified_by_name',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'submission_date' => 'date',
            'verified_at' => 'datetime',
            'documentation_paths' => 'array',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(LpjAttachment::class, 'lpj_report_id');
    }

    public function getReportTextAttribute(): ?string
    {
        return $this->summary;
    }

    public function getValidationStatusAttribute(): LpjValidationStatus
    {
        return match (strtolower((string) $this->status)) {
            'verified', 'valid', 'approved' => LpjValidationStatus::VALID,
            'revision', 'revision_required' => LpjValidationStatus::REVISION,
            default => LpjValidationStatus::PENDING,
        };
    }
}
