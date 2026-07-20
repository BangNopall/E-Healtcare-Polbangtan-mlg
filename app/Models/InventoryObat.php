<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class InventoryObat extends Model
{
    use HasFactory;
    use HasUuids;

    protected $with = [
        'SatuanObat',
        'User',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'kode_obat',
        'nama_obat',
        'stok',
        'satuan_id',
        'createdBy',
        'foto_obat',
    ];

    public function SatuanObat()
    {
        return $this->belongsTo(SatuanObat::class, 'satuan_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'createdBy', 'id');
    }
}
