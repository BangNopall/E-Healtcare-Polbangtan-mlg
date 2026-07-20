<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /**
         * Default User Settings By Role
         * Role: Mahasiswa => 'cdmi' => true, 'cdmi_complete' => false, 'dmti' => true, 'dmti_complete' => false, 'senso' => false
         * Role: Karyawan => 'dmti' => true, 'dmti_complete' => false
         * Role: Dokter, Psikiater, Admin
         */
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('role', ['Mahasiswa', 'Dokter', 'Psikolog', 'Karyawan', 'Admin', 'Perawat'])->default('Mahasiswa');
            $table->string('avatar_url')->nullable()->default(null);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Complete Data Mahasiswa Informations
            // Check data mahasiswa apakah sudah lengkap atau belum => DB name : CDMI
            // CDMI = [ NIM, Prodi, Blok, No_Ruangan]
            $table->boolean('cdmi')->nullable()->default(null);
            $table->boolean('cdmi_complete')->nullable()->default(null);
            // $table->foreignId('cdmi_id')->nullable()->constrained('cdmis')->onDelete('cascade');

            // Data Medical Technical Informations
            // Check data mahasiswa dan karyawan apakah sudah lengkap atau belum => DB name : DMTI
            // DMTI = [ Tanggal_Lahir, Usia, NIK, No_BPJS, No_HP ]
            $table->boolean('dmti')->nullable()->default(null);
            $table->boolean('dmti_complete')->nullable()->default(null);
            // $table->foreignId('dmti_id')->nullable()->constrained('dmtis')->onDelete('cascade');

            $table->string('kesehatan_token')->nullable()->default(null)->unique();
            $table->string('bimbingan_token')->nullable()->default(null)->unique();
            $table->string('konsultasi_token')->nullable()->default(null)->unique();

            // expired token
            $table->timestamp('kesehatan_token_expired_at')->nullable()->default(null);
            $table->timestamp('bimbingan_token_expired_at')->nullable()->default(null);
            $table->timestamp('konsultasi_token_expired_at')->nullable()->default(null);

            // Senso Data
            $table->boolean('senso')->nullable()->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
