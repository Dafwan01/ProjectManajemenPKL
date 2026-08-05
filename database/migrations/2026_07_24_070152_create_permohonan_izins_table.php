<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan_izins', function (Blueprint $table) {
            $table->id('permohonan_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->date('tanggal_permohonan');
            $table->date('tanggal_awal')->nullable();;
            $table->date('tanggal_akhir')->nullable();;
            $table->enum('jenis', ['izin', 'sakit', 'absen', 'absen pulang']);
            $table->text('alasan');
            $table->string('lampiran')->nullable();
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->string('alamat_izin')->nullable();
            $table->integer('jumlah_hari')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan_izins');
    }
};