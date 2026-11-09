<?php

namespace App\Models\External;

use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    protected $connection = 'siakad';

    protected $table = 'prodi';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'nama',
        'kode_prodi',
        'jenjang',
        'fakultas_id',
    ];

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id', 'id');
    }
}
