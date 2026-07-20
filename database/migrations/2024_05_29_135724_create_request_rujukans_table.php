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
        Schema::create('request_rujukans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_id')->constrained('data_psikologs');
            $table->enum('status', ['completed', 'rejected', 'canceled', 'submitted'])->default('submitted');
            $table->text('alasan_penolakan')->nullable()->default(null);

            // Surat Rujukan
            $table->unsignedBigInteger('rujukan_id')->nullable()->default(null);

            $table->foreign('rujukan_id')->references('id')->on('surat_rujukans')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_rujukans');
    }
};
