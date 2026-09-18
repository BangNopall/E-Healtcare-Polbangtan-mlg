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
        Schema::table('users', function (Blueprint $table) {
            // Mirrors c_d_m_i_s.nim (varchar 16) so the two stay copy-compatible.
            // Nullable because only Mahasiswa users end up with a NIM, and even
            // then only once their CDMI profile is completed.
            $table->string('nim', 16)->nullable()->unique()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nim']);
            $table->dropColumn('nim');
        });
    }
};
