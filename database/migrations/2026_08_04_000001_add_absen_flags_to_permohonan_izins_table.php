<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('permohonan_izins', 'absen_masuk')) {
            Schema::table('permohonan_izins', function (Blueprint $table) {
                $table->boolean('absen_masuk')->default(false)->after('jumlah_hari');
            });
        }

        if (!Schema::hasColumn('permohonan_izins', 'absen_pulang')) {
            Schema::table('permohonan_izins', function (Blueprint $table) {
                $table->boolean('absen_pulang')->default(false)->after('absen_masuk');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('permohonan_izins', 'absen_pulang')) {
            Schema::table('permohonan_izins', function (Blueprint $table) {
                $table->dropColumn('absen_pulang');
            });
        }

        if (Schema::hasColumn('permohonan_izins', 'absen_masuk')) {
            Schema::table('permohonan_izins', function (Blueprint $table) {
                $table->dropColumn('absen_masuk');
            });
        }
    }
};