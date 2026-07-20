<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_obats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_obat');
            $table->string('kode_obat')->unique();
            $table->integer('stok')->default(0);
            $table->unsignedBigInteger('satuan_id')->nullable();
            $table->unsignedBigInteger('createdBy');
            $table->string('foto_obat')->nullable();
            $table->timestamps();
            
            $table->foreign('createdBy')->references('id')->on('users');
            $table->foreign('satuan_id')->references('id')->on('satuan_obats');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $inventorys = DB::table('inventory_obats')->get();

        foreach ($inventorys as $obat) {
            if ($obat->foto_obat) {
                Storage::disk('foto_obat')->delete($obat->foto_obat);
            }
        }
        Schema::dropIfExists('inventory_obats');
    }
};
