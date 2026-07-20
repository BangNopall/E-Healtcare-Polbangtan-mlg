<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surat_keterangan_berobats', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique()->nullable()->default(null);
            $table->foreignId('dokter_id')->constrained('users');
            $table->foreignId('pasien_id')->constrained('users');
            $table->string('nama_dokter');
            $table->string('nama_pasien');
            $table->enum('jabatan_dokter', ['Dokter', 'Psikolog', 'Perawat'])->default('Dokter');
            $table->enum('jabatan_pasien', ['Mahasiswa', 'Karyawan'])->default('Mahasiswa');
            $table->text('keluhan')->nullable()->default(null);
            $table->text('pemeriksaan')->nullable()->default(null);
            $table->text('diagnosa')->nullable()->default(null);
            $table->boolean('withObat')->default(false);
            // DMTI
            $table->string('nik', 16);
            $table->string('no_bpjs', 16)->nullable()->default(null);
            $table->string('no_hp', 16);
            $table->string('ttl');
            $table->enum('jenis_kelamin', ['pria', 'wanita']);
            $table->unsignedInteger('usia');
            $table->enum('golongan_darah', ['A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-']);

            // CDMI
            $table->string('nim', 16)->nullable()->default(null);
            $table->string('no_ruangan', 25)->nullable()->default(null);
            $table->unsignedBigInteger('prodi_id')->nullable();
            $table->unsignedBigInteger('blok_id')->nullable();
            $table->foreign('prodi_id')->references('id')->on('prodis')->onDelete('cascade');
            $table->foreign('blok_id')->references('id')->on('bloks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keterangan_berobats');
    }
};
