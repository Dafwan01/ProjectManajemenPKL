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
        Schema::create('forum_messages', function (Blueprint $table) {
          $table->id('message_id');
    $table->foreignId('forum_id')->constrained('forums', 'forum_id')->onDelete('cascade');
    $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
    $table->text('content');
    $table->string('gambar')->nullable();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_messages');
    }
};
