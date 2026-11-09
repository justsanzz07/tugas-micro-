<?php

namespace App\Models\Submission;

use App\Enums\LpjAttachmentType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LpjAttachment extends Model
{
    use HasUlids;

    protected $connection = 'sipres_submission';

    protected $table = 'lpj_attachments';

    public $timestamps = false;

    protected $fillable = [
        'lpj_report_id',
        'attachment_type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'attachment_type' => LpjAttachmentType::class,
            'uploaded_at' => 'datetime',
            'file_size' => 'integer',
        ];
    }

    public function lpjReport(): BelongsTo
    {
        return $this->belongsTo(LpjReport::class);
    }
}
