<?php

namespace App\Models\Submission;

use App\Enums\ApproverRole;
use App\Enums\LetterType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class OfficialLetter extends Model
{
    protected $connection = 'sipres_workflow';

    protected $table = 'assignment_letters';

    protected $fillable = [
        'uuid',
        'reference_id',
        'letter_number',
        'subject',
        'signed_by_name',
        'signed_by_nip',
        'signed_by_position',
        'issued_date',
        'pdf_path',
        'verification_token',
    ];

    protected function casts(): array
    {
        return [
            'issued_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (OfficialLetter $letter) {
            if (empty($letter->uuid)) {
                $letter->uuid = (string) Str::uuid();
            }
        });
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'reference_id', 'uuid');
    }

    public function getLetterTypeAttribute(): LetterType
    {
        return str_contains(strtolower($this->subject ?? ''), 'dispensasi')
            ? LetterType::DISPENSASI
            : LetterType::SURAT_TUGAS;
    }

    public function getIssuedByRoleAttribute(): ApproverRole
    {
        return ApproverRole::DEKAN;
    }

    public function getFilePathAttribute(): ?string
    {
        return $this->pdf_path;
    }

    public function getIssuedAtAttribute()
    {
        return $this->issued_date ?? $this->created_at;
    }

    public function getQrCodeTokenAttribute(): ?string
    {
        return $this->verification_token;
    }
}
