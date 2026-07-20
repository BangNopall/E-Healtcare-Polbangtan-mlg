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
        Schema::create('data_psikologs', function (Blueprint $table) {
            $table->id();
            // Data User
            $table->foreignId('user_id')->constrained('users');

            // Data Psikolog
            $table->date('tanggal');
            $table->text('keluhan')->nullable();
            $table->text('metode_psikologi')->nullable();
            $table->text('diagnosa')->nullable();
            $table->text('prognosis')->nullable();
            $table->text('intervensi')->nullable();
            $table->text('saran')->nullable();
            $table->text('rencana_tindak_lanjut')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_psikologs');
    }
};
