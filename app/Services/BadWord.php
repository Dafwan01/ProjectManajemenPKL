<?php

namespace App\Services;

class BadWord
{
    // Pemetaan pelesetan angka/simbol ke huruf biasa
    protected static array $leetMap = [
        '4' => 'a', '@' => 'a',
        '1' => 'i', '!' => 'i', '|' => 'i',
        '3' => 'e',
        '0' => 'o',
        '5' => 's', '$' => 's',
        '7' => 't',
        '8' => 'b',
    ];

    protected static function getBadWords(): array
    {
        $path = storage_path('app/badwords.json');

        if (!file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true) ?? [];
    }

    public static function cek(string $text): bool
    {
        $words = self::getBadWords();

        // 1. Ubah ke huruf kecil & konversi angka pelesetan (misal: 4nj1ng -> anjing)
        $normalizedText = strtolower($text);
        $normalizedText = strtr($normalizedText, self::$leetMap);

        // 2. Cek setiap kata kasar dengan pola fleksibel (mengabaikan titik, spasi, dash di antara huruf)
        foreach ($words as $word) {
            $cleanWord = strtolower(trim($word));
            if (empty($cleanWord)) continue;

            $chars = mb_str_split($cleanWord);

            // Membuat regex pattern yang mengabaikan pemisah di antara huruf
            // Contoh "kontol" menjadi pola regex: k[\s._\-*]*o[\s._\-*]*n[\s._\-*]*t[\s._\-*]*o[\s._\-*]*l
            $pattern = '/' . implode('[\s._\-*]*', array_map(fn($c) => preg_quote($c, '/'), $chars)) . '/i';

            if (preg_match($pattern, $normalizedText)) {
                return true;
            }
        }

        return false;
    }
}