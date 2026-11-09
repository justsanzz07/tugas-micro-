<?php

namespace App\Models\Submission;

use App\Enums\SubmissionStatus;
use App\Models\Finance\BudgetRequest;
use App\Models\Workflow\ApprovalFlow;
use App\Models\Workflow\ApprovalStep;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Submission extends Model
{
    use HasFactory;

    protected $connection = 'sipres_submission';

    protected $table = 'submissions';

    protected $fillable = [
        'uuid',
        'competition_id',
        'submitter_id',
        'submitter_name',
        'submitter_nim',
        'study_program_id',
        'study_program_name',
        'title',
        'delegation_type',
        'team_name',
        'supervisor_id',
        'supervisor_name',
        'supervisor_nidn',
        'status',
        'total_budget_requested',
        'total_budget_approved',
        'proposal_path',
        'invitation_letter_path',
        'supporting_doc_path',
        'assignment_letter_id',
        'submission_notes',
        // Backward-compatible aliases
        'code',
        'mahasiswa_nim',
        'prodi_id',
        'fakultas_id',
        'dosen_nidn',
        'current_status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Submission $submission) {
            if (empty($submission->uuid)) {
                $submission->uuid = (string) Str::uuid();
            }
        });
    }

    public function setMahasiswaNimAttribute($value): void
    {
        $this->attributes['submitter_nim'] = $value;
    }

    public function setProdiIdAttribute($value): void
    {
        $this->attributes['study_program_id'] = $value;
    }

    public function setDosenNidnAttribute($value): void
    {
        $this->attributes['supervisor_nidn'] = $value;
    }

    public function setCurrentStatusAttribute($value): void
    {
        if ($value instanceof SubmissionStatus) {
            $this->attributes['status'] = match ($value) {
                SubmissionStatus::DRAFT => 'draft',
                SubmissionStatus::SUBMITTED, SubmissionStatus::REVIEW_KAPRODI, SubmissionStatus::REVIEW_DOSEN => 'submitted',
                SubmissionStatus::REVISION_KAPRODI, SubmissionStatus::REVISION_DOSEN, SubmissionStatus::REVISION_DEKAN => 'revision_required',
                SubmissionStatus::REVIEW_DEKAN => 'dekan_review',
                SubmissionStatus::APPROVED_FINAL => 'approved',
                SubmissionStatus::WAITING_LPJ => 'waiting_lpj',
                SubmissionStatus::REVIEW_LPJ => 'lpj_review',
                SubmissionStatus::COMPLETED => 'completed',
                SubmissionStatus::REJECTED => 'rejected',
                default => 'draft',
            };
        }
    }

    // ── Relationships (same DB) ──

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }

    public function competitionDetail(): BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(SubmissionMember::class, 'submission_id');
    }

    public function documents()
    {
        return $this->hasMany(SubmissionMember::class, 'submission_id')->whereRaw('1 = 0');
    }

    public function getDocumentsAttribute()
    {
        return collect([]);
    }

    public function lpjReport(): HasOne
    {
        return $this->hasOne(LpjReport::class, 'submission_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(AchievementCertificate::class, 'submission_id');
    }

    // ── Cross-DB Relationships ──

    public function officialLetters()
    {
        return $this->hasMany(OfficialLetter::class, 'reference_id', 'uuid');
    }

    public function approvalFlow(): ?ApprovalFlow
    {
        $flow = ApprovalFlow::where('reference_id', $this->uuid)
            ->orWhere('reference_id', (string) $this->id)
            ->first();

        if (! $flow) {
            $refId = $this->uuid ?? (string) Str::uuid();
            if (! $this->uuid) {
                $this->update(['uuid' => $refId]);
            }

            $flow = ApprovalFlow::create([
                'uuid' => (string) Str::uuid(),
                'reference_id' => $refId,
                'template_code' => 'STANDARD_COMPETITION',
                'current_step' => 'kaprodi',
                'status' => 'in_progress',
            ]);

            ApprovalStep::create([
                'workflow_instance_id' => $flow->id,
                'reference_id' => $refId,
                'step_order' => 1,
                'step_name' => 'Persetujuan Kaprodi & Penunjukan Dosen',
                'role_required' => 'kaprodi',
                'status' => 'pending',
            ]);

            ApprovalStep::create([
                'workflow_instance_id' => $flow->id,
                'reference_id' => $refId,
                'step_order' => 2,
                'step_name' => 'Persetujuan Final Dekan Fakultas',
                'role_required' => 'dekan',
                'status' => 'pending',
            ]);
        }

        return $flow;
    }

    public function budgetRequest(): ?BudgetRequest
    {
        return BudgetRequest::where('reference_id', $this->uuid)
            ->orWhere('reference_id', (string) $this->id)
            ->first();
    }

    public function incentiveAward(): ?object
    {
        $disb = DB::connection('sipres_finance')
            ->table('disbursements')
            ->where(function ($q) {
                $q->where('reference_id', $this->uuid)
                    ->orWhere('reference_id', (string) $this->id);
            })
            ->where('stage', 'incentive')
            ->first();

        if (! $disb) {
            return null;
        }

        return (object) [
            'achievement_rank' => $disb->notes ?? 'Juara Prestasi',
            'incentive_amount' => (float) $disb->amount,
            'notes' => $disb->notes,
        ];
    }

    // ── Virtual / Backward-Compatible Accessors ──

    public function getCodeAttribute(): string
    {
        return 'SUB-'.str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getMahasiswaNimAttribute(): ?string
    {
        return $this->submitter_nim;
    }

    public function getDosenNidnAttribute(): ?string
    {
        return $this->supervisor_nidn;
    }

    public function getFakultasIdAttribute(): string
    {
        return in_array($this->study_program_id, ['TI', 'TE'], true) ? 'FTI' : 'FTI';
    }

    public function getProdiIdAttribute(): ?string
    {
        return $this->study_program_id;
    }

    public function getCurrentStatusAttribute(): SubmissionStatus
    {
        $st = strtolower((string) ($this->status ?? 'draft'));

        return match ($st) {
            'submitted', 'kaprodi_review', 'dosen_review', 'review_kaprodi', 'review_dosen' => SubmissionStatus::REVIEW_KAPRODI,
            'revision_required', 'revision_kaprodi', 'revision_dosen' => SubmissionStatus::REVISION_KAPRODI,
            'approved_kaprodi', 'approved_dosen', 'dekan_review', 'review_dekan' => SubmissionStatus::REVIEW_DEKAN,
            'kemahasiswaan_review', 'warek_review', 'review_warek' => SubmissionStatus::REVIEW_WAREK,
            'approved', 'approved_final' => SubmissionStatus::APPROVED_FINAL,
            'waiting_lpj' => SubmissionStatus::WAITING_LPJ,
            'lpj_review' => SubmissionStatus::REVIEW_LPJ,
            'completed' => SubmissionStatus::COMPLETED,
            'rejected' => SubmissionStatus::REJECTED,
            default => SubmissionStatus::tryFrom(strtoupper($st)) ?? SubmissionStatus::DRAFT,
        };
    }

    public function isEditable(): bool
    {
        return $this->current_status->isEditable();
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'rejected']);
    }

    public static function generateCode(): string
    {
        $lastId = static::max('id') ?? 0;

        return 'SUB-'.str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
    }
}
