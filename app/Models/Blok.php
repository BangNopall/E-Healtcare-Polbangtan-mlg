<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blok extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function cdmi()
    {
        return $this->belongsTo(CDMI::class);
    }

    public function suratKeteranganBerobat()
    {
        return $this->hasMany(SuratKeteranganBerobat::class);
    }
}
