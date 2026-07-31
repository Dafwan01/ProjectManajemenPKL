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
        if (!Schema::hasColumn('permohonan_izins', 'alamat_izin')) {
            Schema::table('permohonan_izins', function (Blueprint $table) {
                $table->string('alamat_izin')->nullable();
            });
        }

        if (!Schema::hasColumn('permohonan_izins', 'jumlah_hari')) {
            Schema::table('permohonan_izins', function (Blueprint $table) {
                $table->integer('jumlah_hari')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('permohonan_izins', 'jumlah_hari')) {
            Schema::table('permohonan_izins', function (Blueprint $table) {
                $table->dropColumn('jumlah_hari');
            });
        }

        if (Schema::hasColumn('permohonan_izins', 'alamat_izin')) {
            Schema::table('permohonan_izins', function (Blueprint $table) {
                $table->dropColumn('alamat_izin');
            });
        }
    }
};
