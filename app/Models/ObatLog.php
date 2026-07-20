<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObatLog extends Model
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
        'Qty',
        'production_date',
        'expired_date',
        'user_id',
        'skb_id',
    ];

    public function Obat()
    {
        return $this->belongsTo(InventoryObat::class);
    }

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function SKB()
    {
        return $this->belongsTo(SuratKeteranganBerobat::class, 'skb_id');
    }
}
