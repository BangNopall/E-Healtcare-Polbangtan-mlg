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
        Schema::create('obat_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUuid('obat_id')->constrained('inventory_obats');
            $table->integer('Qty');
            $table->enum('type', ['withdraw'])->default('withdraw');
            $table->date('production_date');
            $table->date('expired_date');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('skb_id')->nullable()->constrained('surat_keterangan_berobats')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat_logs');
    }
};
