<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // On MySQL, update the enum column definition
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE presensi_bimbingans MODIFY COLUMN status ENUM('Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpha', '') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE presensi_bimbingans MODIFY COLUMN status ENUM('Hadir', 'Izin', 'Sakit', 'Alpha', '') NOT NULL");
        }
    }
};
