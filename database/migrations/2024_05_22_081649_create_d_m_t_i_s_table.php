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
        Schema::create('d_m_t_i_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nik', 16)->unique();
            $table->string('no_bpjs', 16)->unique()->nullable()->default(null);
            $table->string('no_hp', 16)->unique();
            $table->string('tempat_kelahiran');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['pria', 'wanita']);
            $table->unsignedInteger('usia');
            $table->enum('golongan_darah', ['A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-']);
            $table->timestamps();

            $table->unique(['user_id', 'nik']);
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_m_t_i_s');
    }
};
