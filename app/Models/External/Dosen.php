<?php

namespace App\Models\External;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $connection = 'siakad';

    protected $table = 'dosen';

    protected $primaryKey = 'nidn';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'nidn',
        'nip',
        'nama',
        'email',
        'no_hp',
        'prodi_id',
        'fakultas_id',
        'jabatan_fungsional',
        'jabatan_struktural',
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
