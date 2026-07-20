<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriRs extends Model
{
    use HasFactory;

    protected $table = 'histori_rs';

    protected $fillable = [
        'nama_rs'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

}
