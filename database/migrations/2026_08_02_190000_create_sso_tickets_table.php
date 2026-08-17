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
        Schema::create('sso_tickets', function (Blueprint $table) {
            $table->ulid('id')->primary();
            // Nonce dari payload SSO E-Management — unique constraint inilah
            // yang mencegah replay (lihat SsoLoginController::consumeNonce()).
            $table->string('nonce')->unique();
            // Disimpan untuk audit saja (bukan untuk lookup), nullable karena
            // penyimpanan nonce terjadi sebelum user berhasil di-resolve.
            $table->string('nim')->nullable();
            $table->timestamp('used_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sso_tickets');
    }
};
