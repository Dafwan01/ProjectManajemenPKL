<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilais', function (Blueprint $table) {
            $table->id('nilai_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->unsignedTinyInteger('kedisiplinan')->nullable(); // Kedisiplinan & Profesionalisme
            $table->unsignedTinyInteger('kemampuan_teknis')->nullable(); // Hard Skills
            $table->unsignedTinyInteger('problem_solving')->nullable(); // Problem Solving
            $table->unsignedTinyInteger('komunikasi_kerjasama')->nullable(); // Soft Skills
            $table->unsignedTinyInteger('kualitas_ketepatan')->nullable(); // Deliverables
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilais');
    }
};