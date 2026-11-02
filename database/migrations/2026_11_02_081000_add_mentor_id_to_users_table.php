<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan kolom mentor_id jika belum ada
        if (!Schema::hasColumn('users', 'mentor_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('mentor_id')
                    ->nullable()
                    ->after('sekolah_id')
                    ->constrained('users', 'user_id')
                    ->nullOnDelete();
            });
        }

        // 2. Backfill data existing dari kolom 'mentor' ke 'mentor_id'
        $mentors = DB::table('users')
            ->where('role', 'mentor')
            ->select('user_id', 'nama')
            ->get();

        $mentorMapByName = [];
        $mentorValidIds = [];

        foreach ($mentors as $m) {
            $mentorMapByName[trim(strtolower($m->nama))] = $m->user_id;
            $mentorValidIds[$m->user_id] = true;
        }

        // Ambil semua user PKL yang memiliki nilai di kolom mentor
        $students = DB::table('users')
            ->where('role', 'PKL')
            ->whereNotNull('mentor')
            ->where('mentor', '!=', '')
            ->where('mentor', '!=', '-')
            ->select('user_id', 'mentor')
            ->get();

        foreach ($students as $student) {
            $rawMentor = trim((string) $student->mentor);
            $matchedMentorId = null;

            // Kasus 1: Nilai mentor sudah berupa angka (user_id)
            if (is_numeric($rawMentor) && isset($mentorValidIds[(int) $rawMentor])) {
                $matchedMentorId = (int) $rawMentor;
            } else {
                // Kasus 2: Nilai mentor berupa teks nama
                $lowerName = strtolower($rawMentor);
                if (isset($mentorMapByName[$lowerName])) {
                    $matchedMentorId = $mentorMapByName[$lowerName];
                }
            }

            if ($matchedMentorId !== null) {
                DB::table('users')
                    ->where('user_id', $student->user_id)
                    ->update(['mentor_id' => $matchedMentorId]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'mentor_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['mentor_id']);
                $table->dropColumn('mentor_id');
            });
        }
    }
};
