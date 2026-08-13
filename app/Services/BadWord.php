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

            // Membuat regex pattern per-huruf dengan kuantifier '+' (satu huruf boleh
            // muncul berkali-kali berturut-turut) + pemisah opsional di antaranya.
            // Ini menangani DUA teknik evasion sekaligus:
            //  - Pemisah antar huruf, misal "k.o.n.t.o.l" atau "k-o-n-t-o-l"
            //  - Penggandaan huruf, misal "annjing" atau "gooblok" tetap terbaca
            //    sebagai representasi dari "anjing" / "goblok"
            // Contoh "goblok" -> g+[\s._\-*]*o+[\s._\-*]*b+[\s._\-*]*l+[\s._\-*]*o+[\s._\-*]*k+
            $innerPattern = implode('', array_map(
                fn($c) => preg_quote($c, '/') . '+[\s._\-*]*',
                $chars
            ));

            // Word boundary berbasis Unicode (\p{L} = karakter huruf apapun).
            // (?<!\p{L}) -> karakter tepat sebelum match TIDAK BOLEH huruf
            // (?!\p{L})  -> karakter tepat sesudah match TIDAK BOLEH huruf
            // Ini mencegah kata kasar terdeteksi jika ia hanya menjadi BAGIAN
            // dari kata lain yang sah, misalnya "gelo" di dalam "pengelolaan"
            // atau "gelombang", tapi tetap mendeteksi "gelo" yang berdiri sendiri
            // (termasuk yang dipisah leetspeak seperti "g.e.l.o" atau "g-e-l-o").
            $pattern = '/(?<!\p{L})' . $innerPattern . '(?!\p{L})/iu';

            if (preg_match($pattern, $normalizedText)) {
                return true;
            }
        }

        return false;
    }
}
