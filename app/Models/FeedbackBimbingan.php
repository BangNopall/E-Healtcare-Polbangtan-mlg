<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackBimbingan extends Model
{
    use HasFactory;

    protected $with = ['jadwal', 'senso', 'siswa'];

    protected $fillable = [
        'jadwal_id',
        'senso_id',
        'siswa_id',
        'feedback',
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalBimbingan::class, 'jadwal_id');
    }

    public function senso()
    {
        return $this->belongsTo(User::class, 'senso_id');
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }
}
