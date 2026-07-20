<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    use HasFactory;
    protected $table = 'rekam_medis';

    protected $with = [
        'dokter',
        'pasien',
    ];

    protected $fillable = [
        'tanggal',
        'dokter_id',
        'pasien_id',
        'keluhan',
        'pemeriksaan',
        'diagnosa',
        'tindakan',
        'withObat',
        'withAlat',
        'rawatjalan',
        'rs_name_rujukan',
        'rs_name_rawatinap'
    ];

    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    public function pasien()
    {
        return $this->belongsTo(User::class, 'pasien_id');
    }
}
