<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;
    use HasUlids;

    protected $with = [
        'Obat',
        'User',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'obat_id',
        'type',
        'Qty',
        'production_date',
        'expired_date',
        'description',
        'user_id',
    ];

    public function Obat()
    {
        return $this->belongsTo(InventoryObat::class, 'obat_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
