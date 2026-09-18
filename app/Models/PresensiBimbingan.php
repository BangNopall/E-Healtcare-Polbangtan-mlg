<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiBimbingan extends Model
{
    use HasFactory;

    protected $with = ['jadwal', 'senso'];

    protected $fillable = [
        'jadwal_id',
        'senso_id',
        'tanggal_presensi',
        'jam_presensi',
        'status',
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalBimbingan::class, 'jadwal_id');
    }

    public function senso()
    {
        return $this->belongsTo(User::class, 'senso_id');
    }
}
