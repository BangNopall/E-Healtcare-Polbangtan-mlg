<?php

namespace App\Models;

use App\Models\ObatLog;
use Illuminate\Support\Str;
use App\Models\InventoryLog;
use App\Models\InventoryObat;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
// class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'avatar_url',
        'cdmi',
        'cdmi_complete',
        'dmti',
        'dmti_complete',
        'senso',
        'kesehatan_token',
        'bimbingan_token',
        'konsultasi_token',
        'kesehatan_token_expired_at',
        'bimbingan_token_expired_at',
        'konsultasi_token_expired_at',
    ];

    /**
     * Generate a unique kesehatan_token
     *
     * @return string
     */
    protected static function generateUniqueToken()
    {
        do {
            $token = Str::random(24);
        } while (User::where('kesehatan_token', $token)->exists());

        return $token;
    }
    protected static function generateUniqueTokenBimbingan()
    {
        do {
            $token = Str::random(24);
        } while (User::where('bimbingan_token', $token)->exists());

        return $token;
    }
    protected static function generateUniqueTokenKonsultasi()
    {
        do {
            $token = Str::random(24);
        } while (User::where('konsultasi_token', $token)->exists());

        return $token;
    }

    protected static function generateExpiredToken()
    {
        $now = now();
        $now = now()->addMinutes(60);
        return $now;
    }

    protected static function UpdateTokenKesehatan($id)
    {
        $user = User::find($id);
        $user->kesehatan_token = self::generateUniqueToken();
        $user->kesehatan_token_expired_at = self::generateExpiredToken();
        $user->save();
    }
    protected static function UpdateTokenBimbingan($id)
    {
        $user = User::find($id);
        $user->bimbingan_token = self::generateUniqueToken();
        $user->bimbingan_token_expired_at = self::generateExpiredToken();
        $user->save();
    }

    protected static function UpdateTokenKonsultasi($id)
    {
        $user = User::find($id);
        $user->konsultasi_token = self::generateUniqueToken();
        $user->konsultasi_token_expired_at = self::generateExpiredToken();
        $user->save();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if ($user->role == 'Mahasiswa') {
                $user->cdmi = 1;
                $user->cdmi_complete = 0;
                $user->dmti = 1;
                $user->dmti_complete = 0;
                $user->kesehatan_token = self::generateUniqueToken();
                $user->bimbingan_token = self::generateUniqueTokenBimbingan();
                $user->konsultasi_token = self::generateUniqueTokenKonsultasi();
                $user->kesehatan_token_expired_at = self::generateExpiredToken();
                $user->bimbingan_token_expired_at = self::generateExpiredToken();
                $user->konsultasi_token_expired_at = self::generateExpiredToken();
            } elseif ($user->role == 'Karyawan') {
                $user->dmti = 1;
                $user->dmti_complete = 0;
                $user->kesehatan_token = self::generateUniqueToken();
                $user->kesehatan_token_expired_at = self::generateExpiredToken();
            } else {
                $user->kesehatan_token = null;
                $user->kesehatan_token_expired_at = null;
                $user->bimbingan_token = null;
                $user->bimbingan_token_expired_at = null;
                $user->konsultasi_token = null;
                $user->konsultasi_token_expired_at = null;
            }
            // Other roles do not need defaults
            // $user->password = Hash::make(Str::substr($user->name, 0, 5));
        });

        static::updating(function ($user) {
            if ($user->role == 'Mahasiswa' || $user->role == 'Karyawan') {
                // $user->kesehatan_token = self::generateUniqueToken();
                // $user->kesehatan_token_expired_at = self::generateExpiredToken();
            } elseif($user->role == 'Mahasiswa'){
                // $user->bimbingan_token = self::generateUniqueTokenBimbingan();
                // $user->konsultasi_token = self::generateUniqueTokenKonsultasi();
                // $user->bimbingan_token_expired_at = self::generateExpiredToken();
                // $user->konsultasi_token_expired_at = self::generateExpiredToken();
            } else {
                $user->kesehatan_token = null;
                $user->kesehatan_token_expired_at = null;
                $user->bimbingan_token = null;
                $user->bimbingan_token_expired_at = null;
                $user->konsultasi_token = null;
                $user->konsultasi_token_expired_at = null;
            }
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'avatar_url',
        'created_at',
        'updated_at',
        'role',
        'cdmi',
        'cdmi_complete',
        'dmti',
        'dmti_complete',
        'senso',
        'kesehatan_token',
        'bimbingan_token',
        'konsultasi_token',
        'kesehatan_token_expired_at',
        'bimbingan_token_expired_at',
        'konsultasi_token_expired_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user has a role
     *
     * role user [ 'Admin', 'Mahasiswa', 'Dokter', 'Psikiater', 'Karyawan' ]
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function inventoryObat()
    {
        return $this->hasMany(InventoryObat::class);
    }

    public function obatLog()
    {
        return $this->hasMany(ObatLog::class);
    }

    public function inventoryLog()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function getDataUser($role)
    {
        if ($this->role === 'Mahasiswa') {
            // return $this->hasOne(Cdmi::class, 'id', 'cdmi_id');
            // return $this->hasOne(Dmti::class, 'id', 'dmti_id');
            // return $this->hasOne(Rpd::class, 'id', 'rpd_id');
        } elseif ($this->role === 'Karyawan') {
            // return $this->hasOne(Dmti::class, 'id', 'dmti_id');
            // return $this->hasOne(Rpd::class, 'id', 'rpd_id');
        } else {
            return null;
        }
    }

    public function getDMTI()
    {
        return $this->hasOne(DMTI::class);
    }

    public function getCDMI()
    {
        return $this->hasOne(CDMI::class);
    }

    public function RPD()
    {
        return $this->hasMany(RPD::class);
    }

    public function Senso()
    {
        return $this->hasOne(BimbinganSenso::class);
    }

    public function Feedback()
    {
        return $this->hasMany(FeedbackBimbingan::class);
    }

    public function BimbinganSenso()
    {
        return $this->hasMany(BimbinganSenso::class);
    }

    public function PresensiBimbingan()
    {
        return $this->hasMany(PresensiBimbingan::class);
    }

    public function DataPsikolog()
    {
        return $this->hasMany(DataPsikolog::class);
    }

    public function suratKeteranganBerobat()
    {
        return $this->hasMany(SuratKeteranganBerobat::class, 'pasien_id', 'dokter_id');
    }

    public function SuratKeteranganSakit()
    {
        return $this->hasMany(SuratKeteranganSakit::class);
    }

    public function SuratRujukan()
    {
        return $this->hasMany(SuratRujukan::class);
    }

    public function DokterRM()
    {
        return $this->hasMany(RekamMedis::class, 'dokter_id');
    }

    public function PasienRM()
    {
        return $this->hasMany(RekamMedis::class, 'pasien_id');
    }
}
