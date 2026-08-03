<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class SsoTicket extends Model
{
    use HasUlids;

    protected $fillable = [
        'nonce',
        'nim',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];
}
