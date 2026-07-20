<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestRujukan extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_id',
        'status',
        'alasan_penolakan',
        'rujukan_id',
    ];

    public function dataPsikolog()
    {
        return $this->belongsTo(DataPsikolog::class, 'data_id');
    }

    public function suratRujukan()
    {
        return $this->belongsTo(SuratRujukan::class, 'rujukan_id');
    }
}
