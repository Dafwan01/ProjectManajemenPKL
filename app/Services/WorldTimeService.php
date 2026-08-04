<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WorldTimeService
{
    /**
     * Ambil waktu dunia dengan pendekatan "offset caching":
     * - Hit API HANYA sesekali (misal tiap 5 menit), simpan selisih (offset) antara waktu API dan waktu server.
     * - Di request-request berikutnya, waktu dihitung dari now() server + offset tersimpan.
     * - Ini jauh lebih cepat karena tidak perlu network call di setiap request,
     *   tapi tetap akurat karena offset diperbarui berkala dan drift jam server dalam 5 menit sangat kecil.
     */
    public static function now(string $timezone = 'Asia/Jakarta'): Carbon
    {
        $cacheKey = 'world_time_offset_' . $timezone;

        $offsetSeconds = Cache::remember($cacheKey, 300, function () use ($timezone) {
            try {
                $response = Http::timeout(2)->get("https://worldtimeapi.org/api/timezone/{$timezone}");

                if ($response->successful()) {
                    $data = $response->json();
                    $waktuApi = Carbon::parse($data['datetime'])->setTimezone($timezone);
                    $waktuServer = Carbon::now($timezone);

                    // Selisih dalam detik antara waktu API dan waktu server saat ini
                    return $waktuApi->getTimestamp() - $waktuServer->getTimestamp();
                }
            } catch (\Exception $e) {
                Log::warning('WorldTimeService gagal fetch API: ' . $e->getMessage());
            }

            // Kalau API gagal, anggap offset 0 (pakai waktu server apa adanya)
            return 0;
        });

        return Carbon::now($timezone)->addSeconds($offsetSeconds);
    }

    public static function isFromApi(string $timezone = 'Asia/Jakarta'): bool
    {
        // Cek berdasarkan cache offset yang sudah ada, tanpa hit API baru
        return Cache::has('world_time_offset_' . $timezone);
    }
}