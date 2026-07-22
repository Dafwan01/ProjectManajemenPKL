<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PresensiStatusKehadiran;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('presensis', function (Blueprint $table) {
            $table->id('presensi_id');
            $table->string('foto_masuk')->nullable();
            $table->string('foto_keluar')->nullable();
            $table->date('tanggal')->nullable();
            $table->enum('status_kehadiran', PresensiStatusKehadiran::cases())->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->time('absen_masuk')->nullable();
            $table->time('absen_keluar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
