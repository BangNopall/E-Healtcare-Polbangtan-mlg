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
        Schema::create('consumable_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUuid('alat_id')->constrained('inventory_consumables');
            $table->enum('type', ['deposit', 'withdraw', 'destroy', 'created', 'Deleted']);
            $table->integer('Qty')->default(0);
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->date('date');
            $table->time('time');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumable_logs');
    }
};
