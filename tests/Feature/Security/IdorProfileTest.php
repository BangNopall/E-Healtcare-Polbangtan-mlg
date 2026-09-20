<?php

use App\Models\CDMI;
use App\Models\DMTI;
use App\Models\Prodi;
use App\Models\Blok;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('local');
});

test('mahasiswa tidak dapat mengubah avatar milik pengguna lain (IDOR Protection)', function () {
    $victim = User::factory()->create(['role' => 'Mahasiswa']);
    $attacker = User::factory()->create([
        'role' => 'Mahasiswa',
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
        'is_email_changed' => 1,
        'is_password_changed' => 1,
    ]);

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->actingAs($attacker)->post(route('profile.update-avatar', $victim->id), [
        'avatar_url' => $file,
    ]);

    $response->assertForbidden();
    $victim->refresh();
    expect($victim->avatar_url)->toBeNull();
});

test('mahasiswa tidak dapat mengubah data DMTI medis milik pengguna lain (IDOR Protection)', function () {
    $victim = User::factory()->create(['role' => 'Mahasiswa']);
    DMTI::create([
        'user_id' => $victim->id,
        'nik' => '1234567890123456',
        'no_bpjs' => '1234567890123456',
        'no_hp' => '081234567890',
        'tempat_kelahiran' => 'Malang',
        'tanggal_lahir' => '2000-01-01',
        'jenis_kelamin' => 'pria',
        'usia' => 24,
        'golongan_darah' => 'O+',
    ]);

    $attacker = User::factory()->create([
        'role' => 'Mahasiswa',
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
        'is_email_changed' => 1,
        'is_password_changed' => 1,
    ]);

    $response = $this->actingAs($attacker)->patch(route('profile.update-dmti', $victim->id), [
        'nik' => '9999999999999999',
        'no_bpjs' => '9999999999999999',
        'no_hp' => '089999999999',
        'tempat_kelahiran' => 'Hacked City',
        'tanggal_lahir' => '2000-01-01',
        'jenis_kelamin' => 'pria',
        'usia' => 25,
        'golongan_darah' => 'B+',
    ]);

    $response->assertForbidden();
    $victimDmti = DMTI::where('user_id', $victim->id)->first();
    expect($victimDmti->tempat_kelahiran)->toBe('Malang');
});

test('mahasiswa tidak dapat mengunggah RPD untuk pengguna lain (IDOR Protection)', function () {
    $victim = User::factory()->create(['role' => 'Mahasiswa']);
    $attacker = User::factory()->create([
        'role' => 'Mahasiswa',
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
        'is_email_changed' => 1,
        'is_password_changed' => 1,
    ]);

    $file = UploadedFile::fake()->create('riwayat_penyakit.pdf', 100, 'application/pdf');

    $response = $this->actingAs($attacker)->post(route('profile.create-rpd', $victim->id), [
        'file_RPD' => $file,
    ]);

    $response->assertForbidden();
    expect($victim->RPD()->count())->toBe(0);
});

test('pengguna dapat memperbarui avatar miliknya sendiri', function () {
    $user = User::factory()->create([
        'role' => 'Mahasiswa',
        'cdmi_complete' => 1,
        'dmti_complete' => 1,
        'is_email_changed' => 1,
        'is_password_changed' => 1,
    ]);

    $file = UploadedFile::fake()->image('my_avatar.png');

    $response = $this->actingAs($user)->post(route('profile.update-avatar', $user->id), [
        'avatar_url' => $file,
    ]);

    $response->assertRedirect(route('profile.edit'));
    $user->refresh();
    expect($user->avatar_url)->not->toBeNull();
});
