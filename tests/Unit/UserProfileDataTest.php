<?php

use App\Models\User;

it('normalizes gender values and reads legacy completion dates', function () {
    $user = new User();

    $user->setRawAttributes([
        'jenis_kelamin' => 'laki-laki',
        'tanggal_akhir' => null,
        'tanggal_Akhir' => '2026-08-10',
    ]);

    expect($user->jenis_kelamin)->toBe('Laki-laki')
        ->and($user->tanggal_akhir?->format('Y-m-d'))->toBe('2026-08-10');
});
