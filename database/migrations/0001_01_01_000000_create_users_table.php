<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\UserRole;   // Import Enum Role
use App\Enums\UserStatus; // Import Enum Status

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('nama');
            $table->string('asal_sekolah')->nullable();
            $table->string('mentor')->nullable();
            
            // Ambil semua daftar value dari file Enum
            $table->enum('status', array_column(UserStatus::cases(), 'value'))
                  ->default(UserStatus::AKTIF->value);
                  
            $table->enum('role', array_column(UserRole::cases(), 'value'));

            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('tanggal_mulai')->useCurrent();
            $table->date('tanggal_Akhir')->nullable();
            $table->string('nilai')->nullable();
            $table->string('surat_penerimaan')->nullable();
            $table->rememberToken();
            $table->string('foto')->nullable();
            $table->text('skill')->nullable();
            $table->string('sertifikat')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};