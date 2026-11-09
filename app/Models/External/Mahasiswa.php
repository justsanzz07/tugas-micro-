<?php

namespace App\Models\External;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $connection = 'siakad';

    protected $table = 'mahasiswa';

    protected $primaryKey = 'nim';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'nim',
        'nama',
        'email',
        'no_hp',
        'prodi_id',
        'fakultas_id',
        'angkatan',
        'status_akademik',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id', 'id');
    }

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id', 'id');
    }
}
