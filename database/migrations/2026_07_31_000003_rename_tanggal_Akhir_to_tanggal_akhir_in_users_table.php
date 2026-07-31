<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'tanggal_Akhir') && !Schema::hasColumn('users', 'tanggal_akhir')) {
                $table->renameColumn('tanggal_Akhir', 'tanggal_akhir');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'tanggal_akhir') && !Schema::hasColumn('users', 'tanggal_Akhir')) {
                $table->renameColumn('tanggal_akhir', 'tanggal_Akhir');
            }
        });
    }
};
