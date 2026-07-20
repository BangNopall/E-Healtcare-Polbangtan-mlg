<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriConsumable extends Model
{
    use HasFactory;

    protected $fillable = ['nama_kategori'];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function consumable()
    {
        return $this->hasMany(InventoryConsumable::class);
    }
}
