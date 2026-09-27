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
        Schema::table('c_d_m_i_s', function (Blueprint $table) {
            $table->unique('user_id');
        });

        Schema::table('d_m_t_i_s', function (Blueprint $table) {
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('c_d_m_i_s', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });

        Schema::table('d_m_t_i_s', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });
    }
};
