<?php

namespace App\Models\Submission;

use App\Models\External\Mahasiswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionMember extends Model
{
    protected $connection = 'sipres_submission';

    protected $table = 'submission_members';

    protected $fillable = [
        'submission_id',
        'user_id',
        'nim',
        'name',
        'study_program_name',
        'role_in_team',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function mahasiswa(): ?Mahasiswa
    {
        return Mahasiswa::find($this->nim);
    }
}
