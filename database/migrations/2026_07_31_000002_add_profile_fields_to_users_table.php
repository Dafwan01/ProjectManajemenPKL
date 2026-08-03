<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'tempat_lahir')) {
                $table->string('tempat_lahir')->nullable()->after('skill');
            }

            if (!Schema::hasColumn('users', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            }

            if (!Schema::hasColumn('users', 'jenis_kelamin')) {
                // use string to avoid enum mismatch between existing code and data
                $table->string('jenis_kelamin')->nullable()->after('tanggal_lahir');
            }

            if (!Schema::hasColumn('users', 'jurusan')) {
                $table->string('jurusan')->nullable()->after('jenis_kelamin');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'jurusan')) {
                $table->dropColumn('jurusan');
            }
            if (Schema::hasColumn('users', 'jenis_kelamin')) {
                $table->dropColumn('jenis_kelamin');
            }
            if (Schema::hasColumn('users', 'tanggal_lahir')) {
                $table->dropColumn('tanggal_lahir');
            }
            if (Schema::hasColumn('users', 'tempat_lahir')) {
                $table->dropColumn('tempat_lahir');
            }
        });
    }
};
