<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPsikolog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'keluhan',
        'metode_psikologi',
        'diagnosa',
        'prognosis',
        'intervensi',
        'saran',
        'rencana_tindak_lanjut',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requestRujukan()
    {
        return $this->hasOne(RequestRujukan::class, 'data_id');
    }
}
