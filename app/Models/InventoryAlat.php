<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class InventoryAlat extends Model
{
    use HasFactory;
    use HasUuids;

    protected $with = [
        'KategoriAlat',
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
        'foto_alat'
    ];

    public function KategoriAlat()
    {
        return $this->belongsTo(KategoriAlat::class, 'kategori_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'createdBy', 'id');
    }
}
