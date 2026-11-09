<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicProfile extends Model
{
    protected $table = 'academic_profiles';

    protected $fillable = [
        'user_id',
        'identifier_number',
        'faculty_id',
        'faculty_name',
        'study_program_id',
        'study_program_name',
        'phone_number',
        'semester',
        'academic_year',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
