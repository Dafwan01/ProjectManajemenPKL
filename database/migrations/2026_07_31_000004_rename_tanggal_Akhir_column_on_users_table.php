<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'tanggal_Akhir') && !Schema::hasColumn('users', 'tanggal_akhir')) {
            DB::statement('ALTER TABLE `users` CHANGE `tanggal_Akhir` `tanggal_akhir` DATE NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'tanggal_akhir') && !Schema::hasColumn('users', 'tanggal_Akhir')) {
            DB::statement('ALTER TABLE `users` CHANGE `tanggal_akhir` `tanggal_Akhir` DATE NULL');
        }
    }
};
