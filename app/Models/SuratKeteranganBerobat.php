<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeteranganBerobat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_surat', // 'nomor_surat' is a unique string
        'dokter_id',
        'pasien_id',
        'nama_dokter',
        'nama_pasien',
        'jabatan_dokter',
        'jabatan_pasien',
        'keluhan',
        'pemeriksaan',
        'diagnosa',
        'nik',
        'no_bpjs',
        'no_hp',
        'ttl',
        'jenis_kelamin',
        'usia',
        'golongan_darah',
        'nim',
        'no_ruangan',
        'prodi_id',
        'blok_id',
        'withObat',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($surat) {
            $date = date('Ymd');
            $id = $surat->id;
            $padLength = max(3, strlen($id)); // Pastikan panjang minimal 3, sesuaikan jika diperlukan
            $paddedId = str_pad($id, $padLength, '0', STR_PAD_LEFT);
            $surat->nomor_surat = "SKB-$date-$paddedId";
            $surat->save();
        });
    }


    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    public function pasien()
    {
        return $this->belongsTo(User::class, 'pasien_id');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function blok()
    {
        return $this->belongsTo(Blok::class);
    }

    public function obatLogs()
    {
        return $this->hasMany(ObatLog::class, 'skb_id');
    }
}
