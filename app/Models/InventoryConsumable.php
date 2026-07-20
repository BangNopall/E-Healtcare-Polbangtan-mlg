<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class InventoryConsumable extends Model
{
    use HasFactory;
    use HasUuids;

    protected $with = [
        'KategoriConsumable',
        'User',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'kode_alat',
        'nama_alat',
        'stok', 
        'kategori_id',
        'createdBy',
        'foto_consumable'
    ];

    public function KategoriConsumable()
    {
        return $this->belongsTo(KategoriConsumable::class, 'kategori_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'createdBy', 'id');
    }
}
