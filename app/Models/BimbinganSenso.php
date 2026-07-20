<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BimbinganSenso extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'senso_id',
    ];

    protected $with = ['siswa', 'senso'];

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function senso()
    {
        return $this->belongsTo(User::class, 'senso_id');
    }
}
