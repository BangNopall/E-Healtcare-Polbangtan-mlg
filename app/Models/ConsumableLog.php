<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class ConsumableLog extends Model
{
    use HasFactory;
    use HasUlids;

    protected $with = [
        'Consumable',
        'User',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'alat_id',
        'type',
        'Qty',
        'description',
        'user_id',
        'date',
        'time',
    ];

    public function Consumable()
    {
        return $this->belongsTo(InventoryConsumable::class, 'consumable_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
