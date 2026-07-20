<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatuanObat extends Model
{
    use HasFactory;

    protected $fillable = ['nama_satuan'];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function obat()
    {
        return $this->hasMany(InventoryObat::class);
    }
}
