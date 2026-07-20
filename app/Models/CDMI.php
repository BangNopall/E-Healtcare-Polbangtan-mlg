<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CDMI extends Model
{
    use HasFactory;

    protected $with = [
        'user',
        'prodi',
        'blok',
    ];

    protected $fillable = [
        'nim',
        'user_id',
        // 'kelas',
        'no_ruangan',
        'prodi_id',
        'blok_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function blok()
    {
        return $this->belongsTo(Blok::class);
    }
}
