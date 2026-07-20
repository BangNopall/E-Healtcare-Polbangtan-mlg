<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class AlatLog extends Model
{
    use HasFactory;
    use HasUlids;

    protected $with = [
        'Alat',
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

    public function Alat()
    {
        return $this->belongsTo(InventoryAlat::class, 'alat_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
