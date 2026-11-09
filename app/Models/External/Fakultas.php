<?php

namespace App\Models\External;

use Illuminate\Database\Eloquent\Model;

class Fakultas extends Model
{
    protected $connection = 'siakad';

    protected $table = 'fakultas';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'nama',
        'kode_fakultas',
        'dekan_nidn',
    ];

    public function prodis()
    {
        return $this->hasMany(Prodi::class, 'fakultas_id', 'id');
    }

    public function dekan()
    {
        return $this->belongsTo(Dosen::class, 'dekan_nidn', 'nidn');
    }
}
