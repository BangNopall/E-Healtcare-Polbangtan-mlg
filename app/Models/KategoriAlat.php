<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriAlat extends Model
{
    use HasFactory;

    protected $fillable = ['nama_kategori'];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function alat()
    {
        return $this->hasMany(InventoryAlat::class);
    }
}
