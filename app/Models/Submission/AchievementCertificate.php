<?php

namespace App\Models\Submission;

use App\Enums\CertificateValidationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AchievementCertificate extends Model
{
    protected $connection = 'sipres_submission';

    protected $table = 'achievements';

    protected $fillable = [
        'uuid',
        'submission_id',
        'submission_uuid',
        'ranking',
        'level',
        'awarded_date',
        'certificate_path',
        'photo_path',
        'status',
        'skpi_points_awarded',
        'verification_notes',
        'verified_by',
        'verified_by_name',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'awarded_date' => 'date',
            'verified_at' => 'datetime',
            'skpi_points_awarded' => 'decimal:2',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    public function getAchievementRankAttribute(): ?string
    {
        return $this->ranking ?: null;
    }

    public function setAchievementRankAttribute(?string $value): void
    {
        $this->attributes['ranking'] = $value;
    }

    public function getCertificateFilePathAttribute(): ?string
    {
        return $this->certificate_path;
    }

    public function setCertificateFilePathAttribute(?string $value): void
    {
        $this->attributes['certificate_path'] = $value;
    }

    public function getSkpiPointsAttribute(): float
    {
        return (float) ($this->skpi_points_awarded ?? 0);
    }

    public function setSkpiPointsAttribute($value): void
    {
        $this->attributes['skpi_points_awarded'] = $value;
    }

    public function getValidationStatusAttribute(): CertificateValidationStatus
    {
        return match (strtolower((string) $this->status)) {
            'verified', 'valid', 'approved' => CertificateValidationStatus::VALID,
            'rejected' => CertificateValidationStatus::REJECTED,
            default => CertificateValidationStatus::PENDING,
        };
    }

    public function setValidationStatusAttribute($value): void
    {
        $this->attributes['status'] = $value instanceof CertificateValidationStatus ? $value->value : (string) $value;
    }
}
