<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataPsikolog extends Model
{
    use HasFactory, SoftDeletes;

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
        'deleted_by',
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
