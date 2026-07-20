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
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUuid('obat_id')->constrained('inventory_obats');
            $table->enum('type', ['deposit', 'withdraw', 'destroy', 'expired', 'created']);
            $table->integer('Qty')->default(0);
            $table->date('production_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
