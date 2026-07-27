<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk mengubah tipe kolom payload.
     */
    public function up(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            // Mengubah tipe kolom payload dari TEXT menjadi LONGTEXT
            $table->longText('payload')->change();
        });
    }

    /**
     * Kembalikan perubahan jika migration di-rollback.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            // Mengembalikan tipe kolom payload ke TEXT
            $table->text('payload')->change();
        });
    }
};