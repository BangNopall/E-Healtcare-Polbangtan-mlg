<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JadwalBimbingan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'materi',
        'token',
    ];

    /**
     * Generate a unique token for the jadwal bimbingan
     *
     * @return string
     */
    protected static function generateUniqueToken()
    {
        do {
            $token = Str::random(24);
        } while (JadwalBimbingan::where('token', $token)->exists());

        return $token;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($jadwal) {
            $jadwal->jam_mulai = '00:00:01';
            $jadwal->jam_selesai = '23:59:59';
            $jadwal->token = self::generateUniqueToken();
        });

        static::created(function ($jadwal) {
            $getSensos = User::where('senso', 1)->get();

            foreach ($getSensos as $senso) {
                $presensi = new PresensiBimbingan();
                $presensi->jadwal_id = $jadwal->id;
                $presensi->senso_id = $senso->id;
                $presensi->tanggal_presensi = $jadwal->tanggal;
                $presensi->status = 'Alpha';
                $presensi->save();
            }
        });

        static::deleting(function ($jadwal) {
            // Hapus semua presensi yang terkait dengan jadwal ini
            $jadwal->presensi()->delete();
        });
    }

    public function presensi()
    {
        return $this->hasMany(PresensiBimbingan::class, 'jadwal_id');
    }
}