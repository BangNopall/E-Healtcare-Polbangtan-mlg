<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DMTI extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nik',
        'no_bpjs',
        'no_hp',
        'tempat_kelahiran',
        'tanggal_lahir',
        'jenis_kelamin',
        'golongan_darah',
        'usia',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
