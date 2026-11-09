<?php

namespace App\Models\Submission;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionDocument extends Model
{
    use HasUlids;

    protected $connection = 'sipres_submission';

    protected $table = 'submission_documents';

    public $timestamps = false;

    protected $fillable = [
        'submission_id',
        'document_type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'document_type' => DocumentType::class,
            'uploaded_at' => 'datetime',
            'file_size' => 'integer',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
