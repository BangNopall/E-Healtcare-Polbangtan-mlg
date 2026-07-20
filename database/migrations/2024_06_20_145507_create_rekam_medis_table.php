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
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->unsignedBigInteger('dokter_id')->nullable()->default(null);
            $table->unsignedBigInteger('pasien_id')->nullable()->default(null);
            $table->foreign('dokter_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('pasien_id')->references('id')->on('users')->onDelete('cascade');

            $table->text('keluhan')->nullable()->default(null);
            $table->text('pemeriksaan')->nullable()->default(null);
            $table->text('diagnosa')->nullable()->default(null);
            $table->json('tindakan')->nullable()->default(null);

            $table->boolean('withObat')->default(false);
            $table->boolean('withAlat')->default(false);

            $table->text('rawatjalan')->nullable()->default(null);
            $table->text('rs_name_rujukan')->nullable()->default(null);
            $table->text('rs_name_rawatinap')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};
